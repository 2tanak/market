<template>
  <div class="chat-parent" @mousedown="activity">
    <!-- Шапка -->
    <header class="chat-header">
      <div v-if="partnerName" class="avatar"></div>
      <div class="user-info">
        <span class="user-name">{{ partnerName }}</span>
        <span v-if="user?.online === 'online'" class="user-status">в сети</span>
        <span v-if="user?.offline" class="user-status">был(а) недавно</span>
        <div v-if="user?.typing === true" class="user-typing">печатает ...</div>
      </div>
    </header>

    <!-- Область сообщений -->
    <main
      ref="messagesArea"
      class="messages-area chat-background"
      @scroll="handleScroll"
    >
      <div ref="textBox" class="text-box">
        <div
          v-for="msg in messages"
          :key="msg.uuid"
          :class="[
            'msg-row',
            msg.sender_id == authUser?.id ? 'is-me' : 'is-them',
            { 'msg-appear': msg.isNew }
          ]"
        >
          <div class="msg-bubble">
            <div class="msg-text">{{ msg.text }}</div>
            <div class="msg-meta">
              <span class="msg-time">{{ formatTime(msg?.created_at) }}</span>

              <span
                v-if="msg.sender_id == authUser?.id"
                class="msg-status"
                :class="{ 'is-read': msg.status == 3 }"
              >
                {{ msg.status === 1 ? '✓' : (msg.status >= 2 ? '✓✓' : '🕒') }}
              </span>
            </div>
          </div>
        </div>

        <!-- Лоадер наверх истории -->
        <div v-if="loading && loadPartition" class="top-loader-row">
          <div class="spinner"></div>
        </div>
      </div>
    </main>

    <!-- Футер (текстовая область и кнопка отправить) -->
    <footer v-if="user?.id" class="chat-footer">
      <div class="input-wrapper">
        <textarea
          v-model="text"
          rows="2"
          @input="handleTyping"
          @keydown.enter.exact.prevent="send"
          :placeholder="partnerName ? `Введите сообщение для ${partnerName}...` : 'Введите сообщение...'"
          class="chat-textarea"
        ></textarea>
        <button
          type="button"
          @click="send"
          :disabled="isSending || !text.trim()"
          :class="['send-btn', { 'is-sending': isSending, 'is-ready': !!text.trim() }]"
          aria-label="Отправить сообщение"
        >
          <span class="send-icon-wrap">
            <svg class="send-svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
              <path d="M1.101 21.757L23.8 12.028 1.101 2.3l.011 7.912 13.623 1.816-13.623 1.817-.011 7.912z"></path>
            </svg>
            <span class="send-spinner"></span>
          </span>
        </button>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed, onUnmounted, nextTick,toRaw } from 'vue';

import { initSocket, joinRoom, disconnectSocket } from '@socket/socketService';
import { v4 as uuidv4 } from 'uuid';
import useUserStore from '@chat/stores/userStore';
import useMessagesStore from '@chat/stores/MessagesStore';

const props = defineProps(['user']);

// Текущий авторизованный пользователь (из пропсов, window или мок)
const authUser = ref(
  props.authUser || window.authUser || { id: 1, name: 'admin', email: 'admin@mail.ru' }
);

//store
const userStore = useUserStore();
const messagesStore = useMessagesStore();



//computed
const user = computed(() => props.user ?? userStore?.user_partner);

const partnerName = computed(() => {
  const u = user.value;
  if (!u) return '';
  const fullName = [u.first_name, u.last_name].filter(Boolean).join(' ');
  return u.name || fullName || u.email || (u.id ? `User #${u.id}` : '');
});
const messages = computed(() => messagesStore?.selectedMessages || []);
const hasMore = computed(() => Boolean(messagesStore?.hasMore));
let currentPage = 1; 
const isAllLoaded = ref(false); 
const isScreenFilled = ref(false); 
const text = ref('');
const isSending = ref(false);
const socket = ref(null);
const socketConnected = ref(false);
const loading = ref(false);
const loadPartition = ref(false);
const messagesArea = ref(null);
const textBox = ref(null);

const MIN_SEND_ANIM_MS = 400;
let pendingUuid = null;
let sendStartedAt = 0;
let unlockTimer = null;

const finishSending = () => {
  const elapsed = Date.now() - sendStartedAt;
  const remaining = Math.max(0, MIN_SEND_ANIM_MS - elapsed);
  clearTimeout(unlockTimer);
  unlockTimer = setTimeout(() => {
    isSending.value = false;
    pendingUuid = null;
  }, remaining);
};

const formatTime = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  if (Number.isNaN(date.getTime())) return '';
  return date.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
};

const activity = () => {};
const handleTyping = () => {};
const handleScroll = () => {};


const fetchAndSyncMessages = async () => {
  const chat_id = user.value?.chat_id;
  if (!chat_id) {
    messagesStore.setMessages([]);
    return;
  }

  currentPage = 1;
  isAllLoaded.value = false;

  try {
    const res = await messagesStore.fetchMessages({
      chat_id,
      created_at: 0,
      newm: 1,
      page: currentPage,
    });

    // Защита от гонки при быстром переключении между чатами
    if (user.value?.chat_id !== chat_id) return;

    messagesStore.setMessages(res?.data || []);
  } catch (err) {
    if (user.value?.chat_id === chat_id) {
      messagesStore.setMessages([]);
    }
  }
};


const handleNewMessage = (incomingMsg) => {
  if (!incomingMsg) return;
  const currentList = messages.value || [];
  const exists = currentList.some((m) => m.uuid === incomingMsg.uuid);
  if (!exists) {
    messagesStore.setMessages([{ ...incomingMsg, isNew: true }, ...currentList]);
  }
  if (pendingUuid && incomingMsg.uuid === pendingUuid) {
    finishSending();
  }
};

const send = () => {
  if (isSending.value) return;

  const trimmed = text.value.trim();
  if (!trimmed) return;

  if (!socket.value || !socket.value.connected) {
    console.warn('Сокет ещё не подключен к server.js');
    return;
  }

  isSending.value = true;
  sendStartedAt = Date.now();

  const uuid = uuidv4();
  pendingUuid = uuid;

  const payload = {
    uuid,
    text: trimmed,
    sender_id: authUser.value?.id || 1,
    receiver_id: user.value?.id || 2,
    chat_id: user.value?.chat_id || null,
    status: 1,
    created_at: new Date().toISOString(),
  };

  socket.value.emit('send_message', payload);
  text.value = '';

  // Страховочный разблок, если ответ от сервера задержался
  clearTimeout(unlockTimer);
  unlockTimer = setTimeout(() => {
    isSending.value = false;
    pendingUuid = null;
  }, 3000);
};

onMounted(() => {
  const token = window.apiToken;
  if (!token) {
    console.warn('Нет window.apiToken — для подключения к сокету перезайдите в аккаунт.');
    return;
  }

  const s = initSocket(token, authUser.value?.email);
  socket.value = s;

  if (s.connected) {
    socketConnected.value = true;
    joinRoom(s, authUser.value?.id);
  }

  s.on('connect', () => {
    socketConnected.value = true;
    console.log('Chat.vue: подключено к server.js');
    joinRoom(s, authUser.value?.id);
  });

  s.on('disconnect', () => {
    socketConnected.value = false;
  });

  s.on('connect_error', (err) => {
    socketConnected.value = false;
    console.error('Ошибка подключения к server.js:', err.message);
  });

  s.off('new_message', handleNewMessage);
  s.on('new_message', handleNewMessage);
});

onUnmounted(() => {
  clearTimeout(unlockTimer);
  if (socket.value) {
    socket.value.off('new_message', handleNewMessage);
  }
  disconnectSocket();
});





watch(() => user.value?.chat_id, (newChatId) => {
  isScreenFilled.value = false;
  loadPartition.value = false;
  isAllLoaded.value = false;
  currentPage = 1;

  if (newChatId) {
    const userId = user.value?.id;
    messagesStore.setIsOpen(userId);
    //startReadTimer(userId);
    fetchAndSyncMessages();
  } else {
    messagesStore.setIsOpen(null);
    messagesStore.setMessages([]);
  }
}, { immediate: true });
watch(messages, async () => {
  //выключение автоматического заполнителя 
  if (isScreenFilled.value || !hasMore.value || loadPartition.value) return;
  await nextTick();
  
  if (!messagesArea.value || !textBox.value) return;

  
  const areaRect = messagesArea.value.getBoundingClientRect();
  const textBoxRect = textBox.value.getBoundingClientRect();


  // Чистая физическая высота контейнера на экране (без учета внутренних прокруток)
  const areaHeightWithoutScroll = areaRect.height; 
  
   // Реальная высота контента, которую занял text-box со всеми сообщениями
  const currentTextBoxHeight = textBoxRect.height;




  console.log('--- Точные замеры JS ---');
  console.log('Высота окна messages-area:', areaHeightWithoutScroll);
  console.log('Текущая высота text-box:', currentTextBoxHeight);

  // Сравниваем физические размеры блоков
  if (currentTextBoxHeight > areaHeightWithoutScroll) {
    console.log('Контент сравнялся или стал больше окна. Выключаем автомат.');
	 //await loadMore();
    isScreenFilled.value = true;
  } else {
    console.log('Место еще есть, вызываем следующую страницу...');
    //await loadMore();
  }
 
 
 
 
}, { deep: true });


</script>



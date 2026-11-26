<template>
  <div class="chat-parent" @mousedown="activity">
    <!-- Шапка -->
    <header class="chat-header">
      <div v-if="user?.name" class="avatar"></div>
      <div class="user-info">
        <span class="user-name">{{ user?.name }}</span>
        <span class="user-status">{{ socketConnected ? 'в сети' : 'был(а) недавно' }}</span>
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
    <footer class="chat-footer">
      <div class="input-wrapper">
        <textarea
          v-model="text"
          rows="2"
          @input="handleTyping"
          @keydown.enter.exact.prevent="send"
          placeholder="Введите сообщение для Юлии..."
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
import { ref, onMounted, onUnmounted } from 'vue';
import { initSocket, joinRoom, disconnectSocket } from '@socket/socketService';
import { v4 as uuidv4 } from 'uuid';

const props = defineProps(['authUser']);

// Текущий авторизованный пользователь (из пропсов, window или мок)
const authUser = ref(
  props.authUser || window.authUser || { id: 1, name: 'admin', email: 'admin@mail.ru' }
);

// Моковый собеседник — Юлия (id = 2)
const user = ref({
  id: 2,
  name: 'Юлия',
});

// Моковый массив сообщений (индекс 0 отображается внизу из-за flex-direction: column-reverse)
const messages = ref([
  {
    uuid: 'mock-msg-2',
    sender_id: authUser.value.id,
    receiver_id: 2,
    text: 'Привет, Юлия! Проверяю отправку сообщений через server.js.',
    status: 2,
    created_at: '2026-11-26T10:05:00.000Z',
  },
  {
    uuid: 'mock-msg-1',
    sender_id: 2,
    receiver_id: authUser.value.id,
    text: 'Привет! Напиши что-нибудь для проверки сокета.',
    status: 3,
    created_at: '2026-11-26T10:00:00.000Z',
  },
]);

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

const handleNewMessage = (incomingMsg) => {
  console.log('Получено сообщение из server.js:', incomingMsg);
  const exists = messages.value.some((m) => m.uuid === incomingMsg.uuid);
  if (!exists) {
    messages.value.unshift({ ...incomingMsg, isNew: true });
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
    receiver_id: 2, // отправляем Юлии (id = 2)
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
</script>



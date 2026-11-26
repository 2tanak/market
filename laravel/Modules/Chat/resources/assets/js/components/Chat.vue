<template>
  <div class="chat-parent" @mousedown="activity">
    <!-- Шапка -->
    <header class="chat-header">
      <div v-if="user?.name" class="avatar"></div>
      <div class="user-info">
        <span class="user-name">{{ user?.name || '' }} (ID: {{ user?.id }})</span>
        <span class="user-status">{{ socketConnected ? 'Сокет подключен' : 'Сокет не подключен' }}</span>
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
          :class="['msg-row', msg.sender_id == authUser?.id ? 'is-me' : 'is-them']"
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
          class="chat-input chat-textarea"
        ></textarea>
        <button type="button" @click="send" class="send-btn send-action-btn">
          Отправить
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
const socket = ref(null);
const socketConnected = ref(false);
const loading = ref(false);
const loadPartition = ref(false);
const messagesArea = ref(null);
const textBox = ref(null);

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
    messages.value.unshift(incomingMsg);
  }
};

const send = () => {
  const trimmed = text.value.trim();
  if (!trimmed) return;

  if (!socket.value || !socket.value.connected) {
    console.warn('Сокет ещё не подключен к server.js');
    return;
  }

  const payload = {
    uuid: uuidv4(),
    text: trimmed,
    sender_id: authUser.value?.id || 1,
    receiver_id: 2, // отправляем Юлии (id = 2)
    status: 1,
    created_at: new Date().toISOString(),
  };

  socket.value.emit('send_message', payload);
  text.value = '';
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
  if (socket.value) {
    socket.value.off('new_message', handleNewMessage);
  }
  disconnectSocket();
});
</script>

<style scoped>
.messages-area {
  flex-grow: 1;
  overflow-y: auto !important;
  min-height: 0;
  display: flex;
  flex-direction: column-reverse;
}

.text-box {
  padding: 20px 5%;
  display: flex;
  flex-direction: column-reverse;
}

.chat-parent {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  background-color: #efe7de;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
}

.chat-header {
  flex: 0 0 60px;
  background: #f0f2f5;
  display: flex;
  align-items: center;
  padding: 0 16px;
  border-bottom: 1px solid #d1d7db;
  position: sticky;
  top: 0;
  z-index: 10;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #ccc;
  margin-right: 12px;
}

.user-name {
  display: block;
  font-weight: 500;
  color: #111b21;
}

.user-status {
  font-size: 12px;
  color: #667781;
}

.msg-row {
  display: flex;
  width: 100%;
  flex-direction: row;
  margin-bottom: 8px;
}

.is-me {
  justify-content: flex-end;
}

.is-them {
  justify-content: flex-start;
}

.msg-bubble {
  max-width: 65%;
  border-radius: 8px;
  font-size: 14.5px;
  position: relative;
  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  word-wrap: break-word;
  min-width: 100px;
  padding: 8px 12px;
}

.is-me .msg-bubble {
  background: #d9fdd3;
}

.is-them .msg-bubble {
  background: #ffffff;
}

.msg-time {
  font-size: 11px;
  color: #667781;
  align-self: flex-end;
}

.chat-footer {
  flex: 0 0 auto;
  background: #f0f2f5;
  padding: 10px 16px;
  position: sticky;
  bottom: 0;
  z-index: 10;
}

.input-wrapper {
  display: flex;
  align-items: center;
  gap: 12px;
  background: white;
  padding: 8px 12px;
  border-radius: 8px;
}

.chat-input {
  flex-grow: 1;
  border: none;
  outline: none;
  font-size: 15px;
  padding: 6px 0;
  background: transparent;
}

.chat-textarea {
  resize: none;
  font-family: inherit;
  line-height: 1.4;
}

.icon-btn,
.send-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 20px;
  color: #54656f;
}

.send-action-btn {
  background-color: #00a884;
  color: #ffffff;
  font-size: 14px;
  font-weight: 500;
  padding: 8px 16px;
  border-radius: 6px;
  white-space: nowrap;
}

.send-action-btn:hover {
  background-color: #008f72;
}

.msg-text {
  padding-bottom: 4px;
  line-height: 1.4;
  white-space: pre-wrap;
}

.msg-meta {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 4px;
  margin-top: -4px;
  height: 15px;
}

.msg-status {
  font-size: 14px;
  line-height: 1;
  color: #8696a0;
  display: inline-flex;
}

.msg-bubble .msg-status.is-read {
  color: #53bdeb !important;
}

.msg-meta .msg-status .is-read {
  color: #53bdeb !important;
}

.user-typing {
  font-size: 14px;
  color: #8696a0;
  margin-bottom: 2px;
  font-weight: bold;
}

.top-loader-row {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  padding: 10px 0;
  flex-shrink: 0;
}

.spinner {
  width: 24px;
  height: 24px;
  border: 2.5px solid rgba(0, 0, 0, 0.1);
  border-top-color: #54656f;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>

<template>
  <div class="scroll-area" @scroll="handleScroll">
    <ul class="navigation navigation-main navigation-accordion">
      <li
        v-for="contact in users"
        :key="contact.chat_id || contact.id"
        class="contact-item"
      >
        <a
          href="javascript:void(0)"
          class="contact-link"
          @click.prevent="someMethod(contact)"
        >
          <!-- Лево: Аватар -->
          <div class="avatar-wrapper">
            <div class="avatar-circle">{{ getInitials(contact) }}</div>
          </div>

          <div class="contact-content">
            <!-- Верхняя строка: Имя + Дата -->
            <div class="contact-row">
              <span class="contact-name">{{ getDisplayName(contact) }}</span>
              <span class="contact-date">{{ getDisplayDate(contact) }}</span>
            </div>

            <!-- В сети -->
            <span v-if="contact.online === 'online'" class="status-online">
              в сети
            </span>

            <!-- Нижняя строка: Статус/Текст + Счетчики -->
            <div class="contact-row text-block">
              <p v-if="contact.typing" class="last-msg-text status-text">
                печатает...
              </p>
              <p v-else class="last-msg-text">
                {{ contact.last_message?.text || 'Нет сообщений' }}
              </p>

              <span v-if="contact.unread_count > 0" class="unread-count">
                {{ contact.unread_count }}
              </span>
            </div>

            <div class="border"></div>
          </div>
        </a>
      </li>

      <!-- Лоадер -->
      <li v-if="loading" class="loader-item">
        <div class="spinner"></div>
        <span></span>
      </li>
    </ul>
  </div>
</template>

<script>
import useUserStore from '@chat/stores/userStore';
// import { getSocket } from '@socket/socketService';
// import useOnlineStore from '@chat/stores/onlineStore.js';
// import { handleActivity } from '@chat/utils/chat.js';

export default {
  props: ['user', 'token', 'authUser'],

  setup() {
    const userStore = useUserStore();
    // const isOnlineStore = useOnlineStore();
    return { userStore };
  },

  data() {
    return {
      page: 1,
      socket: null,
    };
  },

  computed: {
    loading() {
      return this.userStore.loading;
    },
    hasMore() {
      return this.userStore.hasMore;
    },
    users() {
      return this.userStore.users;
    },
  },

  mounted() {
    const token = this.token || window.apiToken;
    this.userStore.setToken(token);

    if (this.user?.id) {
      this.userStore.setUser(this.user);
    }

    this.userStore.fetchUsers(this.page);
    // this.socket = getSocket();
  },

  methods: {
    getDisplayName(contact) {
      const fullName = [contact.first_name, contact.last_name].filter(Boolean).join(' ');
      return contact.name || fullName || contact.email || `User #${contact.id}`;
    },

    getInitials(contact) {
      if (contact.first_name || contact.last_name) {
        const first = contact.first_name ? contact.first_name[0] : '';
        const last = contact.last_name ? contact.last_name[0] : '';
        return (first + last).toUpperCase();
      }

      const name = this.getDisplayName(contact).trim();
      const parts = name.split(/\s+/);
      if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
      }

      return name.slice(0, 2).toUpperCase();
    },

    getDisplayDate(contact) {
      if (!contact.last_message) {
        return contact.created_at || '';
      }

      const now = new Date();
      const today = [
        String(now.getDate()).padStart(2, '0'),
        String(now.getMonth() + 1).padStart(2, '0'),
        now.getFullYear(),
      ].join('.');

      return contact.last_message.date === today
        ? contact.last_message.time
        : contact.last_message.date;
    },

    someMethod(contact) {
      // handleActivity(this.socket, this.isOnlineStore);
      this.userStore.setUser(contact);
      // this.$router.push(`/chat/${contact.id}`);
    },

    handleScroll(e) {
      // handleActivity(this.socket, this.isOnlineStore);
      const { scrollTop, scrollHeight, clientHeight } = e.target;
      if (scrollHeight - scrollTop <= clientHeight + 20 && this.hasMore && !this.loading) {
        this.loadMore();
      }
    },

    async loadMore() {
      this.page++;
      try {
        await this.userStore.fetchUsers(this.page);
      } catch (error) {
        console.error('Ошибка скролла:', error);
      }
    },
  },
};
</script>

<style scoped>
</style>

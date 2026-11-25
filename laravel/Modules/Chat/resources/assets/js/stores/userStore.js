import { defineStore } from 'pinia';
import axios from 'axios';

export default defineStore('user', {
  state: () => ({
    selectedUser: null,
    authUser: null,
    totalUnread: 0,
    user: null,
    user_partner: null,
    users: [],
    loading: false,
    currentPage: 1,
    hasMore: false,
  }),
  actions: {
    async fetchUsers(page = 1) {
      this.loading = true;
      try {
        const response = await axios.get('/api/users', {
          params: { page: page },
          headers: {
            'Accept': 'application/json',
          },
        });

        if (response.data && response.data.success !== false) {
          const newUsers = response.data.data;
          const pagination = response.data.pagination;

          if (page === 1) {
            this.setUsers(newUsers);
          } else {
            this.setUsers([...this.users, ...newUsers]);
          }

          this.currentPage = pagination.page || page;
          this.hasMore = pagination.has_more || false;
        }
      } catch (error) {
        console.error('Ошибка при загрузке списка чатов:', error);
      } finally {

        this.loading = false;
        if (this.users.length > 0 && this.user_partner?.id) {
          const indexUser = this.users.findIndex(u => u.id === this.user_partner.id);
          if (indexUser !== -1) {
            this.user_partner = this.users[indexUser];
          }
        }

      }
    },

    setUser(p) {
      this.user_partner = p;
    },
    setUsers(users) {
      this.users = users;
    },
    setSelectedUser(user) {
      this.selectedUser = user;
    },
    setAuthUser(user) {
      this.authUser = user;
    },
    setTotalUnreadPlus(param) {
      this.totalUnread += param;
    },
    updateUserUnread(msg) {
      console.log('updateUserUnread');
      //console.log(JSON.parse(JSON.stringify(this.users)));
      const { sender_id, message, sender } = msg;
      const existingUser = this.users.find(u => u.id === sender_id);

      const currentUnread = (existingUser?.unread_count || 0) + 1;
      const updatedUser = {
        ...(existingUser || sender),
        unread_count: currentUnread,
        last_message: message,
      };
      console.log(updatedUser);
      this.users = [
        updatedUser,
        ...this.users.filter(u => u.id !== sender_id),
      ];
    },
    updateUserOnlineStatus(data) {
      const { userId, status } = data;
      const indexUser = this.users.findIndex(u => u.id === userId);
      if (indexUser !== -1) {
        this.users[indexUser].online = status;

      }
      //console.log('isonline');
      //console.log(this.users[indexUser]);
    },
    resetUnread(receiverId) {

      const indexUser = this.users.findIndex(u => u.id === receiverId);

      if (indexUser !== -1) {
        const user = this.users[indexUser];

        // 1. Если были непрочитанные — чистим счетчики
        if (user.unread_count > 0) {
          /*
              state.totalUnread = Math.max(
                0,state.totalUnread - user.unread_count,
              );
          */
          user.unread_count = 0; // Обнулили прямо в стейте
        }

        // 2. ОДИН ПРОХОД: Разделяем всех по двум корзинам
        const active = [];
        const inactive = [];

        this.users.forEach(u => {
          if (u.unread_count > 0) {
            active.push(u);
          } else {
            inactive.push(u);
          }
        });


        this.users = [...active, ...inactive];
      }
    },
    updateUserTypingStatus(payload) {
      const { userId, status } = payload;
      console.log('updateUserTypingStatus');
      console.log(userId);
      console.log(status);

      const indexUser = this.users.findIndex(u => u.id === userId);
      //console.log(this.users[indexUser]);
      if (indexUser !== -1) {
        const user = this.users[indexUser];

        if (status === true) {
          user.typing = status;

        }
        if (status === false) {
          user.typing = status;

        }


      }

    },
  }
});

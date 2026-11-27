import { defineStore } from 'pinia';
import axios from 'axios';

export default defineStore('messages', {
  state: () => ({
    selectedMessages: [],
    isOpen: null,
    unreadCounts: {},
    loading: true,
	hasMore: true,
	currentPage: 1,
  }),
  actions: {
    async fetchMessages({ chat_id, created_at = 0, newm = 1, page = 1 }) {
      if (!chat_id) {
        return { data: [], pagination: { current_page: 1, has_more: false } };
      }

      const pageNum = Number(page) || 1;
      this.loading = true;
      try {
        const response = await axios.get(
          `/api/messages/${chat_id}/${encodeURIComponent(created_at)}/${newm}/${pageNum}`
        );

        const data = Array.isArray(response?.data?.data) ? response.data.data : [];
        const pagination = response?.data?.pagination || {};

        this.currentPage = pagination.current_page || pageNum;
        this.hasMore = data.length > 0 ? Boolean(pagination.has_more) : false;

        return {
          ...(response?.data || {}),
          data,
          pagination,
        };
      } catch (err) {
        console.error('Ошибка загрузки сообщений:', err);
        this.hasMore = false;
        throw err;
      } finally {
        this.loading = false;
      }
    },
	updateMessagesStatus(message) {
    const { message_ids, status} = message;
    message_ids.forEach(id => {
	const msgToUpdate = this.selectedMessages.find(m => m.server_id === id);
        
        if (msgToUpdate) {
            msgToUpdate.status = status;
            console.log(`Статус сообщения ${id} обновлен на ${status}`);
        }
    });
},

    updateMessageStatus(message) {
      console.log('updateMessageStatus');
      const { uuid, status, id, created_at, server_id, sender } = message;
      const index = this.selectedMessages.findIndex(m => m.uuid === uuid);
      if (index !== -1) {
        console.log('нашли по uuid');
        this.selectedMessages[index].status = status;
        this.selectedMessages[index].created_at = created_at;
        this.selectedMessages[index].server_id = server_id;
        console.log('обновили по uuid');

      }


    },

    setIsOpen(param) {
      this.isOpen = param;
    },

    setMessages(messages) {
      if (!Array.isArray(messages)) {
        this.selectedMessages = [];
        return;
      }
      this.selectedMessages = [...messages];
    },
    setMessage(message) {
		
      this.selectedMessages = [message, ...this.selectedMessages];
    },
  }
});

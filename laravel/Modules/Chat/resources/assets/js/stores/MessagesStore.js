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
		
      if (!chat_id) return;

      this.loading = true;
      try {
		
        const response = await axios.get(`/messages/${chat_id}/${created_at}/${newm}/${Number(page)}`);
        if (page === 1) {
          console.log('page-1');
          //const result = response.data.data.reverse();
          //console.log(result);
		  console.log(88888888888888888);
		  if(response.data){
		    const {data, pagination} = response.data;
			if(data.length > 0){
				this.hasMore = pagination.has_more;
				this.loading = false;
				return response.data;
				//this.selectedMessages = response.data.data || [];
			}else{
				this.hasMore = false;
			}
			
		  }
		  
          

        } else {
			this.hasMore = false;
          //this.selectedMessages = [...response.data, ...this.selectedMessages];
        }

        return response.data;
      } catch (err) {
        console.error('Ошибка загрузки сообщений:', err);
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
      if (!messages) {
        console.log('messages==0');
        return;
      }
      console.log('addMessage сообщение пришло');
      console.log(messages);

      this.selectedMessages = [...messages];


      //console.log('новвй сокет добавлен в стейт messages:');
      //console.log(this.selectedMessages);
    },

  }
});

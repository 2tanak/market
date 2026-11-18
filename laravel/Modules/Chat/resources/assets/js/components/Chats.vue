<template>
  <div>
   ddddddddddddddddddddddddddddd
  </div>
</template>


<script>
import { v4 as uuidv4 } from 'uuid';
import axios from 'axios';
//import useUserStore from '@chat/stores/userStore';
//import { getSocket } from '@socket/socketService';
//import useOnlineStore from '@chat/stores/onlineStore.js';
//import { handleActivity } from '@chat/utils/chat.js';

export default {
  setup() {
    //const userStore = useUserStore();
	//const isOnlineStore = useOnlineStore();
    //return { userStore,isOnlineStore };
  },


  props: ["user", "authUser"],
  data() {
    return {
      //page: this.pagination.current_page,
      //hasMore: this.pagination.has_more,
	  page: 1,
      socket:null,
	  token:null
    };
  },
  computed: {
    loading(){
	   //return this.userStore.loading
	},
	hasMore(){
	   //return this.userStore.hasMore;
	},
    users() {
      //const users = this.userStore.users;
      //return users;
    }
  },
  methods: {
   
    someMethod(contact) {
		//handleActivity(this.socket,this.isOnlineStore);
        //this.userStore.setUser(contact);
        //this.$router.push(`/chat/${contact.id}`)
    },


    handleScroll(e) {
	  handleActivity(this.socket,this.isOnlineStore);
      const { scrollTop, scrollHeight, clientHeight } = e.target;
      // Если дошли до низа (с запасом 20px)
      if (scrollHeight - scrollTop <= clientHeight + 20 && this.hasMore && !this.loading) {
        this.loadMore();
      }
    },
   async loadMore() {
      this.loading = true;
      this.page++;

      try {
        this.userStore.fetchUsers(this.page);
        } catch (error) {
        console.error("Ошибка скролла:", error);
        } 
      
    }
    
  },
  mounted() {
   
	 const token = window.apiToken;
	 //this.userStore.setToken(token);
	 if(this.user){
		this.userStore.setUser(this.user);
	 }
     //this.userStore.fetchUsers(this.page);
	 //this.socket = getSocket();
	
  }
};
</script>
<style scoped>
.scroll-area {
  /* Фиксируем высоту. Если не сработает 80vh, поставь 400px для теста */
  height: 70vh;
  overflow-y: auto !important;
  /* Принудительно включаем скролл */
  display: block;
}

/* Стилизация полосы прокрутки, чтобы её было видно */
.scroll-area::-webkit-scrollbar {
  width: 6px;
}

.scroll-area::-webkit-scrollbar-thumb {
  background-color: #444;
  /* Темный цвет, чтобы заметить */
  border-radius: 10px;
}

.scroll-area::-webkit-scrollbar-track {
  background: #f1f1f1;
}

/* Твои старые стили .scroll-area ... */

.loader-item {


  display: flex;
  align-items: center;
  justify-content: center;

  color: #666;
  font-size: 12px;
}

.spinner {
  width: 18px;
  height: 18px;
  border: 2px solid #f3f3f3;
  border-top: 2px solid #3498db;
  /* Цвет спиннера */
  border-bottom: 2px solid #3498db;
  /* Цвет спиннера */
  border-radius: 50%;
  animation: spin 0.8s linear infinite;

}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}










/* Зеленая точка на аватаре */
.online-badge {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 10px;
  height: 10px;
  background-color: #25D366;
  /* WhatsApp зеленый */
  border: 2px solid #fff;
  border-radius: 50%;
}

/* Контент справа от аватара */
.contact-content {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
  /* Чтобы длинный текст не распирал */
}







/* Эффект при наведении */
.contact-item:hover {
  background-color: #f5f5f5;
}




/* Шапка: Имя + Дата */
.contact-header {
  display: flex;
  justify-content: space-between;
  /* Раскидывает имя и дату по краям */
  align-items: baseline;
  margin-bottom: 4px;
}


/* Футер: Текст сообщения + Статус */
.contact-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}






.contact-link {
  display: flex !important;
  align-items: center;
  padding: 10px 15px;
  gap: 12px;
  text-decoration: none;
}

/* Центральная часть с именем и текстом */
.contact-main-info {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
  /* Чтобы текст обрезался, а не толкал правую колонку */
}

.contact-name {
  font-weight: 500;
  color: #111b21;
  font-size: 15px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 2px;
}

.last-msg {
  font-size: 13px;
  color: #667781;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Правая колонка: дата сверху, статус снизу */
.contact-right-meta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  /* Прижимаем всё вправо */
  justify-content: center;
  flex-shrink: 0;
  gap: 4px;
  /* Расстояние между датой и статусом */
}

.contact-time {
  font-size: 11px;
  color: #8696a0;
}

.status-text {
  font-size: 11px;
  color: #25D366;
  /* Тот самый зеленый */
  font-weight: 500;
}

/* Аватар (без изменений) */
.avatar-wrapper {
  width: 45px;
  height: 45px;
  background: #eee;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.contact-item {
  list-style: none;
  background: #fff;
}

.contact-link {
  display: flex;
  padding: 10px 12px;
  text-decoration: none;
  align-items: center;
  gap: 12px;
}

.avatar-circle {
  width: 48px;
  height: 48px;
  background: #517da2;
  color: #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}

.contact-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
  /* Важно для обрезки текста */
}

.contact-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2px;
}

.contact-name {
  font-weight: 600;
  color: #000;
  font-size: 15px;
}

.contact-date {
  font-size: 12px;
  color: #8e8e93;
}

.text-block {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

/* Статус "в сети" над текстом сообщения */
.status-online {
  font-size: 12px;
  color: #0088cc;
  margin-bottom: 2px;
}

.last-msg-text {
  margin: 0;
  font-size: 14px;
  color: #8e8e93;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Синий кружок с цифрой */
.unread-count {
  background: #4ca5ff;
  color: #fff;
  font-size: 12px;
  font-weight: bold;
  padding: 2px 8px;
  border-radius: 12px;
  min-width: 18px;
  text-align: center;
}

.contact-item:hover {
  background-color: #f4f4f5;
}
</style>

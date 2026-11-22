<template>
  <div class="scroll-area">
    <ul class="navigation navigation-main navigation-accordion">
      <li class="contact-item">

        <a href="javascript:void(0)" class="contact-link">
		<!-- Лево: Аватар -->
          <div class="avatar-wrapper">
            <div class="avatar-circle">БХ</div>
          </div>

          <div class="contact-content">

            <!-- Верхняя строка: Имя + Дата -->
            <div class="contact-row">
              <span class="contact-name">Alex</span>
              <span class="contact-date">14:20</span>
            </div>
			
			
               <!-- В сети-->
                <span class="status-online">в сети</span>
              
			 
			   <!-- Нижняя строка: Статус/Текст + Счетчики -->
			   <div class="contact-row text-block">
			   
				    <p class="last-msg-text">
                  ffth7u7u7u7u7
                </p>
                 
                <span class="unread-count">40</span>
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
//import { v4 as uuidv4 } from 'uuid';
import axios from 'axios';
import useUserStore from '@chat/stores/userStore';
//import { getSocket } from '@socket/socketService';
//import useOnlineStore from '@chat/stores/onlineStore.js';
//import { handleActivity } from '@chat/utils/chat.js';

export default {
  setup() {
    const userStore = useUserStore();
    //const isOnlineStore = useOnlineStore();
    //return { userStore,isOnlineStore };
  },


  props: ["user", "authUser"],
  data() {
    return {
      //page: this.pagination.current_page,
      //hasMore: this.pagination.has_more,
      page: 1,
      socket: null,
      token: null
    };
  },
  computed: {
    loading() {
      //return this.userStore.loading
    },
    hasMore() {
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
      handleActivity(this.socket, this.isOnlineStore);
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
    if (this.user) {
      this.userStore.setUser(this.user);
    }
    //this.userStore.fetchUsers(this.page);
    //this.socket = getSocket();

  }
};
</script>
<style scoped>

</style>

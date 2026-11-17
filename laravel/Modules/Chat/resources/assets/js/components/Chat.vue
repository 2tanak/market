<template>
  <div class="chat-parent" @mousedown="activity">
    <!-- Шапка -->
    <header class="chat-header">
      <div v-if="user?.name" class="avatar"></div>
      <div class="user-info">
        <span class="user-name">{{ user?.name || '' }}</span>

        <span v-if="user?.online === 'online'" class="user-status">в сети</span>
        <span v-if="user?.offline" class="user-status">был(а) недавно</span>

        <div v-if="user?.typing === true" class="user-typing">печатает ...</div>
      </div>
    </header>

    <!-- ОбластAFь сообщений -->
    <main 
	  ref="messagesArea" 
	  class="messages-area chat-background"
	  @scroll="handleScroll"
	  >
	<div ref="textBox" class="text-box">
	
      <div v-for="msg in messages" :key="msg.uuid"
        :class="['msg-row', msg.sender_id == authUser?.id ? 'is-me' : 'is-them']">
        <div class="msg-bubble">
          <div class="msg-text">{{ msg.text }}</div>
          <div class="msg-meta">
            <span class="msg-time">{{ formatTime(msg?.created_at) }}</span>

            <span v-if="msg.sender_id == authUser?.id" class="msg-status" :class="{ 'is-read': msg.status == 3 }">
              <!-- Просто меняем текст внутри одного спана -->
              {{ msg.status === 1 ? '✓' : (msg.status >= 2 ? '✓✓' : '🕒') }}
            </span>


          </div>
        </div>
      </div>
	  
	 <!-- ДОБАВИЛИ ЛОАДЕР НАВЕРХ ИСТОРИИ -->
    <div v-if="loading && loadPartition" class="top-loader-row">
      <div class="spinner"></div>
    </div>
    </div>
    </main>

    <!-- Футер (инпут) -->
    <footer class="chat-footer">
      <div class="input-wrapper">
        <button class="icon-btn">📎</button>
        <input v-model="text" @input="handleTyping" @keyup.enter="send" placeholder="Введите сообщение"
          class="chat-input">
        <button @click="send" class="send-btn">➤</button>
      </div>
    </footer>
  </div>
</template>

<script setup>
import axios from 'axios';
import { ref, onMounted, watch, computed, onUnmounted, nextTick,toRaw } from 'vue';
import { getSocket } from '@socket/socketService';

//store import
import useUserStore from '@chat/stores/userStore';
import useMessagesStore from '@chat/stores/MessagesStore';
import useOnlineStore from '@chat/stores/onlineStore.js';
import { handleActivity } from '@chat/utils/chat.js';
import { formatTime, formatDateLabel } from '@chat/utils/dateHelpers';
import { uniqueArray } from '@chat/utils/unigueArray';
import { v4 as uuidv4 } from 'uuid';

//store
const userStore = useUserStore();
const messagesStore = useMessagesStore();
const isOnlineStore = useOnlineStore();

//у кого открыт чат
let timer = null;

//props
const props = defineProps(['authUser']);
if (props?.authUser) {
  userStore.setAuthUser(props.authUser);
}


//computed
const user = computed(() => userStore?.user_partner);
const loading = computed(() => messagesStore?.loading);
const hasMore = computed(() => messagesStore?.hasMore);




//const user_r = computed(() => userStore?.selectedUser);
const authUser = computed(() => userStore?.authUser);
const messages = computed(() => messagesStore?.selectedMessages);



const text = ref('');
const socket = ref(null);


//typing
const isTyping = ref(false);// Печатает ли текущий пользователь
const remoteIsTyping = ref(false);//Печатает ли собеседник 
let typingTimeout = null;// Таймер для сброса статуса «печатает»

const messagesArea = ref(null); 
const isAllLoaded = ref(false); 
const textBox = ref(null); 
const isScreenFilled = ref(false); 


let currentPage = 1;  

const loadPartition = ref(false);

const handleTyping = () => {
  if (!socket.value || !user.value?.id) return;
  console.log('юзер локально печатает');
  // Если еще не отправляли статус, отправляем
  if (!isTyping.value) {
    isTyping.value = true;
    socket.value.emit('typing', {
      userId: authUser.value.id,
      receiverId: user.value.id,
      status: true
    });
  }

  // Сбрасываем старый таймер и ставим новый
  if (typingTimeout) clearTimeout(typingTimeout);

  typingTimeout = setTimeout(() => {
    isTyping.value = false;
    socket.value.emit('typing', {
      userId: authUser.value.id,
      receiverId: user.value.id,
      status: false
    });
  }, 2000);
};



const startReadTimer = (contactId) => {
  if (timer) clearTimeout(timer);
  const unread_count = user?.value?.unread_count;
  const receiverId = contactId;
  if (unread_count > 0) {
    //console.log('работа с таймером');
    //console.log('unread_coun', unread_count);
    timer = setTimeout(() => {
      //console.log('Прошла минута, обнуляем счетчик для:', receiverId)
      const idsToUpdate = [];

      for (let i = 0; i < messages.value.length; i++) {
        const m = messages.value[i];

        if (m.sender_id === receiverId && m.status === 2) {
          idsToUpdate.push(m.server_id);
        }
        if (m.status === 3) {
          break;
        }
        if (i > 50) break;
      }

      if (idsToUpdate.length > 0) {
        try {
          axios({
            method: "post", //you can set what request you want to be
            url: "/mark-as-read",
            data: {
              channels: 'action',
              message_ids: idsToUpdate,
              sender_id: receiverId,
              status: 3,
			  chat_id:user.value.chat_id,
            },

            headers: {
              "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
              //'X-Requested-With': 'XMLHttpRequest'
            }
          }).then(response => {
            console.log(
              `Чат с ${receiverId} помечен как прочитанный ${response.ids}`,
            );
            userStore?.resetUnread(receiverId);
            // dispatch(resetUnread(receiverId));

          });

        }
        catch (err) {
          console.error('Не удалось обновить статус "прочитано"', err);

        }
      }
  }, 600);
  }
};

const activity = () => {
 handleActivity(socket.value,isOnlineStore);
};



const send = () => {
  if (text.value.trim()) {
    // 1. Формируем объект сообщения
    console.log('send');
    const userData = user?.value;
	
	
    const message = {
      uuid: uuidv4(),
      channels: 'action',
      text: text.value,
      chat_id: user?.value?.id,
      status: 1,
	  seller_id: userData?.id,
      sender_id: authUser?.value?.id,
      created_at: new Date().toISOString(),
	   ...(userData?.chat_id 
        ? { chat_id: userData.chat_id } 
        : { product_id: product.value?.id || null }
      )
    };
   
    messagesStore.setMessages(message);
    try {
      axios({
        method: "post", //you can set what request you want to be
        url: "/privat-chat-ajax",
        data: message,

        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          //'X-Requested-With': 'XMLHttpRequest'
        }
      }).then(response => {
        console.log(888888);
        console.log(response.data);
        text.value = '';


      });

    }
    catch (error) {
      console.error('Ошибка отправки:', error);
      //setTextinput(messageToSend);
    }

  }
};
const loadMore = async () => {
  if (!hasMore.value)  {
	  console.log('сообщения закончились');
   return;
  }	  
  //if (isScreenFilled.value) return; 
  const chat_id = user?.value?.chat_id;
  if (!chat_id) return;
  
  if (loadPartition.value) return;
  console.log(22222);
  if (messages.value.length === 0) return;
  
  const lastTime = messages.value[messages.value.length - 1].created_at;
  //console.log(messages.value[messages.value.length - 1]);
  
  loadPartition.value = true;
   messagesStore.fetchMessages({
    chat_id,
    created_at: lastTime,
	newm:0,
  }).then(response =>{
	  const plainMessages = toRaw(messages.value).map(m => toRaw(m));
	  uniqueArray(response.data, plainMessages, data => {
		  messagesStore.setMessages(data)
		  
	  });
	  if(response.pagination.has_more === false){
			  loadPartition.value = true;
		  }else{
			  loadPartition.value = false;
		  }
	   
	 
  });
 
};
const handleScroll = () => {
  if (!messagesArea.value) return;

  // Если на сервере больше ничего нет или уже идет загрузка — игнорируем
  if (!hasMore.value || loadPartition.value){
	  console.log('заблокировано на уровне скрола');
	  console.log('hasMore',hasMore.value);
	  console.log('loadPartition',loadPartition.value);
    return;
  }	  

  const { scrollTop, clientHeight, scrollHeight } = messagesArea.value;


  
  
  const currentScroll = Math.abs(scrollTop); 
  console.log(currentScroll);
  
  const isAtTop = currentScroll + clientHeight >= scrollHeight - 10; 

  if (isAtTop) {
    console.log('Пользователь докрутил до верха истории! Подгружаем старые сообщения...');
    loadMore();
  }
};



const fetchAndSyncMessages = async () => {
const chat_id = user?.value?.chat_id;
if(!chat_id) return;
currentPage = 1;
isAllLoaded.value = false;
console.log('отправляем fetch');
  const res = await messagesStore.fetchMessages({
    chat_id,
    created_at: 0,
    newm: 1,
    page: currentPage
  });
   messagesStore.setMessages(res.data);

};
// Следим за наполнением массива сообщений
watch(messages, async () => {
 
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
    await loadMore();
  }
 
 
 
 
}, { deep: true });

onMounted(() => {
socket.value = getSocket();
  //console.log(authUser.value.id);
});
onUnmounted(() => {
  if (timer) clearTimeout(timer);
  messagesStore.setIsOpen(null);
  console.log('Чат закрыт, таймеры очищены');
});
watch(() => user?.value?.chat_id, (newChatId) => {
  if (newChatId) {
	isScreenFilled.value = false;  
    loadPartition.value = false;   
    isAllLoaded.value = false;      
    currentPage = 1;              
	  
	const userId = user?.value?.id;
    messagesStore.setIsOpen(userId);
    startReadTimer(userId);
    fetchAndSyncMessages(); 
    console.log('WATCH: ID собеседника:', userId);
  }
}, { immediate: true })



</script>
<style scoped>
.messages-area {
  flex-grow: 1;
  
  
  /* ВКЛЮЧАЕМ СКРОЛЛ ТУТ */
  overflow-y: auto !important; 
  min-height: 0;
  
  /* ПЕРЕНОСИМ РЕВЕРС СЮДА */
  display: flex;
  flex-direction: column-reverse; 
}

.text-box {
  padding: 20px 5%;
  /* УБИРАЕМ ОТСЮДА С КОРНЕМ ВСЕ ФЛЕКСЫ И РЕВЕРСЫ */
 
  
  /* Делаем его обычным блоком, чтобы он просто рос */
  
   display: flex;
  flex-direction: column-reverse;
}




/* Родитель на весь экран */

.chat-parent {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  background-color: #efe7de;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;

}

/* Шапка всегда сверху */
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



.chat-background {
  background-image: url('https://githubusercontent.com');
  background-repeat: repeat;
}

/* Баблы */
.msg-row {
  display: flex;
  width: 100%;
  flex-direction: row;
  margin-bottom:8px;
}

.is-me {
  justify-content: flex-end;
}

.is-them {
  justify-content: flex-start;
}

.msg-bubble {

  max-width: 65%;

  /* Снизу чуть больше места для времени */
  border-radius: 8px;
  font-size: 14.5px;
  position: relative;
  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
  /* Чтобы время не наезжало на текст, если он короткий */


  flex-direction: column;
  word-wrap: break-word;


  display: flex;
  flex-direction: column;
  min-width: 100px;
  /* Чтобы было куда двигать время */
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

/* Футер прилипает к низу */








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
  padding: 5px 12px;
  border-radius: 8px;
}

.chat-input {
  flex-grow: 1;
  border: none;
  outline: none;
  font-size: 15px;
  padding: 8px 0;
  background: transparent;
}

.icon-btn,
.send-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 20px;
  color: #54656f;
}




/*
.messages-area{
	flex-grow:1;
	background:green;
	
	 overflow-y: auto;
    overflow-y: auto !important;
	
}
.text-box {
  padding: 20px 5%;
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex-direction: column-reverse;
  background:red;

}
*/
.msg-text {
  padding-bottom: 4px;
  line-height: 1.4;
}

.msg-meta {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 4px;
  margin-top: -4px;
  /* Подтягиваем ближе к тексту */
  height: 15px;
}

.msg-status {
  font-size: 14px;
  /* Галочки чуть крупнее */
  line-height: 1;
  color: #8696a0;
  /* Цвет для отправлено (серый) */
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


/* Ряд для лоадера, центрируем его */
.top-loader-row {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  padding: 10px 0;
  /* Важно, чтобы при реверсе он не сжался */
  flex-shrink: 0; 
}

/* Сам крутящийся спиннер */
.spinner {
  width: 24px;
  height: 24px;
  border: 2.5px solid rgba(0, 0, 0, 0.1);
  border-top-color: #54656f; /* Цвет под стать иконкам в футере */
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

/* Анимация вращения */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

</style>

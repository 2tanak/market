<template>
    <!-- Ничего не рендерим -->
</template>

<script setup>
import { onMounted, onUnmounted, watch, ref,computed } from 'vue';
import { initSocket, disconnectSocket, listenSoket, joinRoom } from '@base/socketService';

//import store
import useMessagesStore from '@chat/stores/MessagesStore';
import useUserStore from '@chat/stores/userStore';
import useOnlineStore from '@chat/stores/onlineStore.js';

import { uniqueArray } from '@chat/utils/unigueArray';
import { toRaw } from 'vue';
const socket = ref(null);
const isConnecting = ref(false);
let unsubscribe = null;

//props
const props = defineProps(['user','apitoken','isReady','authUser']);

//store
const messagesStore = useMessagesStore();
const userStore = useUserStore();
const isOnlineStore = useOnlineStore();

//computed
const isOnline = computed(() => useOnlineStore?.isOnline);
const timerId = computed(() => useOnlineStore?.timerId);


const handleIncomingMessage = (newMessage, ack) => {
	//return false;
    console.log('listenSoket start');
    const userId = props.user.id;
	
	const message = [{...newMessage}];
    if (newMessage.sender_id == userId) {
        console.log('Мое сообщение');
		console.log(message);
		if(newMessage.event_type === 'messages_read'){
			messagesStore.updateMessagesStatus(newMessage);
		}else{
          messagesStore.updateMessageStatus(newMessage);
        }
    } else {
        console.log('Сообщение от друга');
        if (messagesStore?.isOpen) {
            console.log('у кого открыт чат');
            if (messagesStore?.isOpen === newMessage.sender_id) {
                console.log(newMessage.sender_id);
                console.log('чат открыт');
				const plainMessages = toRaw(messagesStore.selectedMessages).map(m => toRaw(m));
				 if (typeof ack === 'function') {
                            ack({ status: 3 });
                        }
						
				uniqueArray(message, plainMessages, data => {

                    const uuid = data[data.length - 1].uuid;
                    if (uuid === newMessage.uuid) {

                        console.log('новое сообщение уникально и добавлено');
                        messagesStore.setMessages(message);
                    }
                });
             }
			 else {
                 console.log('чат закрыт');
			     if (typeof ack === 'function') {
				   //сообщение получено клиентом
		           ack({status: 2});
				   message[0].status=2;
                 }
				 userStore.setTotalUnreadPlus(1);
				 userStore.updateUserUnread({
                   sender_id: newMessage.sender_id,
                   message: message[0],
                   sender: newMessage.sender,
                 });
			 }
			 
			 
			 
			 
			 
        }
        // Здесь проверка: открыт ли чат с этим юзером сейчас (берем из стора)
        // if (store.state.isOpen == newMessage.sender_id) { ... }

        if (typeof ack === 'function') {
            ack({ status: 3 }); // Прочитано
        }
    }
    // Сохранение в IndexedDB или локальную БД
    // insertUpdate(newMessage);
};

const startSocket = async () => {
    if (socket.value?.connected || isConnecting.value) return;
     console.log('startSocket');
    isConnecting.value = true;
    const s = await initSocket(props?.apitoken,props?.user?.email);
    socket.value = s;

    s.on('connect', () => {
        console.log('Socket Connected');
        joinRoom(s, props.user.id);
      
        
       
    });

    s.on('status_update', data => {
		console.log('online');
		console.log(data);
		userStore.updateUserOnlineStatus(data);
	});

    s.on('status_typing', data => {
		//console.log('собеседедник печатает');
		//console.log(data);
		userStore.updateUserTypingStatus(data);
       
    });

    // Подписка на основной канал
    unsubscribe = listenSoket(s, "laravel_database_action:privat", (data, ack) => {
        handleIncomingMessage(data.message, ack);
    });

    isConnecting.value = false;
};

const stopSocket = () => {
    if (unsubscribe) unsubscribe();
    disconnectSocket();
    socket.value = null;
    //if (timerId.value) clearTimeout(timerId.value);
};


watch(() => props.isReady, (val) => {
    console.log('socketManager: watch запущен');
    if (val && props.user?.id) {
        console.log('socketManager: startSocket запущен');
        startSocket();
    } else {
        stopSocket();
    }
}, { immediate: true });

// Вместо AppState (React Native) используем Visibility API браузера
const handleVisibility = () => {
    if (document.hidden) {
        console.log('Вкладка скрыта');
        // stopSocket(); // если хочешь рвать соединение как в мобилке
    } else {
        if (props.isReady) startSocket();
    }
};

onMounted(() => {
//timerOnline.value = 1* 60 * 1000;

//timerOnline.value =1000;
    document.addEventListener('visibilitychange', handleVisibility);
});

onUnmounted(() => {
    document.removeEventListener('visibilitychange', handleVisibility);
    stopSocket();
});
</script>

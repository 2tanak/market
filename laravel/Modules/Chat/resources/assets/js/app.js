import '@base_resources/bootstrap.js'; 
import { createApp } from 'vue';
import ChatComponent from '@chat/components/Chat.vue';
import ChatsComponent from '@chat/components/Chats.vue';
//import { createRouter, createWebHistory,createMemoryHistory } from 'vue-router';
import { createPinia } from 'pinia';
const pinia = createPinia();
const app = createApp({});

app.component("chats", ChatsComponent);
app.component("chat", ChatComponent);

//import Socket from '@socket/SocketManager.vue';
//app.component("socket",Socket);
/*---
const routes = [
    // props: true — это и есть та самая фича, которая прокинет :id в компонент
    //{ path: '/chats', component: ChatsComponent, props: true }, 
	{ path: '/chat/:id?', component: ChatComponent, props: true } 
];

const router = createRouter({
    history: createWebHistory(),
    routes
});
----*/
//app.use(router);
app.use(pinia);

app.mount('#app');
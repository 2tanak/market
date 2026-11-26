//import { io } from 'socket.io-client';

let socketInstance = null;

export const initSocket = (apitoken,userEmail) => {
    if (socketInstance && socketInstance.connected) return socketInstance;

    socketInstance = io( "http://192.168.8.8:3000", {
		//withCredentials: true,
        auth: {
          token: apitoken, // Берем из глобальной области
          email: userEmail
        }
    });

    return socketInstance;
};

export const getSocket = () => socketInstance;
export const disconnectSocket = () => {
    if (socketInstance) {
        socketInstance.disconnect();
        socketInstance = null;
    }
};
export const listenSoket = (currentSocket, channelName, updateState) => {
    const handler = (data, ack) => {
        console.log('listenSoket: Сработал слушатель на канале', channelName);
        updateState(data, ack);
    };
    currentSocket.on(channelName, handler);
    return () => currentSocket.off(channelName, handler);
};

export const joinRoom = (socket, user_id) => {
    console.log('Отправляем join для ID: ' + user_id);
    socket.emit('join', { id: user_id });
};
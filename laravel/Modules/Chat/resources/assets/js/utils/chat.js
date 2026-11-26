
export const handleActivity = (socket, store) => {
  if (!socket || !store) return;

  // 1. Если юзер был оффлайн — будим его и отправляем сокет
  if (!store.isOnline) {
    console.log("Vue: Юзер проснулся, отправляем online");
    socket.emit('status', 'online');
    store.setIsOnline(true);
  }

  // 2. Просто просим стор перезапустить таймер оффлайна
  store.startOfflineTimer(socket);
};

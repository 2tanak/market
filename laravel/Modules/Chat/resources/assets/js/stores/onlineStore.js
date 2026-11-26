import { defineStore } from 'pinia';

// Храним ID таймера в глобальном замыкании этого файла.
// Он общий для всего приложения, не умирает и не грузит реактивность.
let globalTimerId = null;

export default defineStore('online', {
  state: () => ({
    timeOnline: 20000, 
    isOnline: false,
  }),
  actions: {
    setIsOnline(payload) {
      console.log('Смена статуса в сторе:', payload);
      this.isOnline = payload;
    },
    // Экшен для безопасного запуска таймера
    startOfflineTimer(socket) {
      // 1. Если старый таймер тикает — гасим его
      if (globalTimerId) {
        clearTimeout(globalTimerId);
      }

      // 2. Запускаем новый таймаут бездействия
      globalTimerId = setTimeout(() => {
        console.log('Vue: Время вышло, юзер уснул');
        if (socket && typeof socket.emit === 'function') {
          socket.emit('status', 'offline');
        }
        this.setIsOnline(false);
        globalTimerId = null;
      }, this.timeOnline);
    },
    // Экшен на случай, если надо принудительно потушить таймер извне
    clearGlobalTimer() {
      if (globalTimerId) {
        clearTimeout(globalTimerId);
        globalTimerId = null;
      }
    }
  }
});

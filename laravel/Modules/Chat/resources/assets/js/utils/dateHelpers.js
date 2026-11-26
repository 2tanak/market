export const formatDateLabel = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const now = new Date();
  
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
  const yesterday = today - 86400000;
  const itemDate = new Date(date.getFullYear(), date.getMonth(), date.getDate()).getTime();

  if (itemDate === today) return 'Сегодня';
  if (itemDate === yesterday) return 'Вчера';

  // Настройки: число и месяц строкой
  const options = { day: 'numeric', month: 'long' };
  
  // Если год не совпадает с текущим — добавляем его в вывод
  if (date.getFullYear() !== now.getFullYear()) {
    options.year = 'numeric';
  }
  
  return date.toLocaleDateString('ru-RU', options);
};


export const formatTime = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const now = new Date();

  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
  const yesterday = today - 86400000;
  const itemDate = new Date(date.getFullYear(), date.getMonth(), date.getDate()).getTime();

  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  const timeStr = `${hours}:${minutes}`;

  // 1. Если сегодня — просто время
  if (itemDate === today) return timeStr;

  // 2. Если вчера — пишем "Вчера" и время
  if (itemDate === yesterday) return `Вчера, ${timeStr}`;

  // 3. Если старое — время + число + месяц строкой
  const options = { day: 'numeric', month: 'long' };
  // Если год старый — тоже добавим для ясности
  if (date.getFullYear() !== now.getFullYear()) options.year = 'numeric';
  
  const dateStr = date.toLocaleDateString('ru-RU', options);
  
  return `${timeStr}, ${dateStr}`; 
  // Пример: "14:20, 10 октября" или "09:15, 5 мая 2023"
};


export const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const now = new Date();
  
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
   const itemDate = new Date(date.getFullYear(), date.getMonth(), date.getDate()).getTime();
  if (itemDate === today) return `${hours}.${minutes}`;
  
  const month = String(date.getMonth()).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const year = String(date.getFullYear()); 
  
  
  const options = { year: 'numeric' };
  const yearStr = date.toLocaleDateString('ru-RU', options);
  
  return `${day}.${month}.${year}`; 
  // Пример: "14:20, 10 октября" или "09:15, 5 мая 2023"
};
export const formatMinute = (dateString) => {
 if (!dateString) return '';
  const date = new Date(dateString);
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${hours}.${minutes}`; 
};
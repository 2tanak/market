const colors = [
  { bg: '#FFE2E2', text: '#FF5C5C' }, // Красный
  { bg: '#E2F0FF', text: '#2E8CFF' }, // Синий
  { bg: '#E7FFE2', text: '#3EAD2A' }, // Зеленый
  { bg: '#FFF5E2', text: '#FF9F00' }, // Оранжевый
  { bg: '#F2E2FF', text: '#9747FF' }, // Фиолетовый
  { bg: '#E2FBFF', text: '#00B5D8' }, // Бирюзовый
  { bg: '#FFE2F5', text: '#E03396' }, // Розовый
];

const getLetterColor = (name = '') => {
  if (!name) return { background: '#DAD5F8', color: '#81C6FF' };

  // Простая функция хэширования имени
  let hash = 0;
  for (let i = 0; i < name.length; i++) {
    hash = name.charCodeAt(i) + ((hash << 5) - hash);
  }

  // Выбираем индекс из массива цветов
  const index = Math.abs(hash) % colors.length;
  const chosen = colors[index];

  return {
    background: chosen.bg,
    color: chosen.text
  };
};

export default getLetterColor;

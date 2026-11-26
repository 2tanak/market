export const uniqueArray = (data, messages = [], callback) => {
  
 const combinedMap = new Map(
    messages.map(item => [item.uuid, item])
  );
 

 data.forEach(item => {
  if (combinedMap.has(item.uuid)) {
    const oldItem = combinedMap.get(item.uuid);
    const updatedItem = { ...oldItem, ...item };
    combinedMap.set(item.uuid, updatedItem);
    //console.log(`UUID ${item.uuid} обновлен внутри блока`);
  } else {
    combinedMap.set(item.uuid, item);
    //console.log(`UUID ${item.uuid} добавлен как новый`);
  }
  
});
 
 // Превращаем Map обратно в массив объектов
  const filteredData = Array.from(combinedMap.values());

  callback(filteredData);
};

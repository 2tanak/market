const sendTypingStatus = async (isTyping,socket,senderId,receiverId) => {
  try {
    socket.emit('typing', { 
	userId: senderId, 
	receiverId: receiverId,
	status:isTyping 
	});
	
    } catch (e) {
    console.log('Typing status error', e);
  }
};

export const handleInputChange = (socket,senderId,receiverId,typingTimer) => {
  
  if (!typingTimer.current) {
    sendTypingStatus(true,socket,senderId,receiverId); 
  } else {
    
    clearTimeout(typingTimer.current);
  }


  typingTimer.current = setTimeout(() => {
    sendTypingStatus(false,socket,senderId,receiverId);
    typingTimer.current = null; 
  }, 2000);
};

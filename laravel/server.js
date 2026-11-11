import express from 'express';
const app = express();
import http from 'http';


import {Server, Socket} from 'socket.io';

const port = process.env.PORT;

const redis_port = process.env.REDIS_PORT || 6379;

const url = process.env.APP_URL;

const message = (name, text, id) => ({name, text, id});

const server = http.createServer(app);

import redis from 'ioredis';
import axios from 'axios';

console.log(`Запускаюсь на порту: ${port}`);

const client = redis.createClient({
  host: 'redis',
  port: redis_port,
});
const subClient = redis.createClient({
    host: 'redis',
    port: redis_port,
});

app.use((req, res, next) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
  res.setHeader(
    'Access-Control-Allow-Headers',
    'Origin, X-Requested-With, Content-Type, Accept',
  );
  next();
});
app.get('/test', function (req, res) {
  console.log(req.params);
  return res.send('11111111');
});

const io = new Server(server, {
  cors: {
    origin: '*',
    methods: ['GET', 'POST'],
  },
});

io.use(async (socket, next) => {
  try {
    //console.log('=== Проверка токена ===');
    const token = socket.handshake?.auth?.token;
    const email = socket.handshake?.auth?.email;

    if (!token) {
      //console.log('Доступ запрещен: Токен не прилетел');
      return next(new Error('Authentication error: No token'));
    }

    //console.log('Token принадлежит: ' + email);
    //console.log('Token: ' + token);

    try {
      const response = await axios.post(
        `${url}/api/verify-token`,
        {},
        {
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: 'application/json',
          },
        },
      );

      if (response.data.success) {
        socket.verifiedUserId = response.data.user_id;
        //console.log(`Юзер проверен! ID в базе: ${socket.verifiedUserId}`);

        return next();
      } else {
        console.log('Laravel вернул успех false');
        return next(new Error('Invalid token'));
      }
    } catch (e) {
      console.error('Ошибка связи с Laravel или токен просрочен (401)');
      return next(new Error('Auth failed on server side'));
    }
  } catch (error) {
    console.error('Глобальная ошибка Middleware:', error.message);
    return next(new Error('Internal Auth Error'));
  }
});

io.on('connection', socket => {
  const userId = socket.verifiedUserId;
  console.log('Новое подключение:', userId);

/*
  socket.broadcast.emit('status_update', {
    userId: userId,
    status: 'online',
	join:   true
	
  });
*/
  socket.on('join', () => {
    const roomName = `user_room_${userId}`;
    if (roomName) {
      socket.join(roomName);
     // console.log(`Сокет ${userId} вошел в комнату ${roomName}`);
    }
  });

  socket.on('status', data => {
    const userId = socket.verifiedUserId;

    console.log(`Юзер ${userId} сменил статус на: ${data}`);

    socket.broadcast.emit('status_update', {
      userId: userId,
      status: data,
      lastSeen: data === 'offline' ? new Date() : null,
    });
	
  });

  socket.on('typing', data => {
    const userId = socket.verifiedUserId;
    const receiverId = data.receiverId;

    console.log(`Юзер ${userId} сменил статус печатает на: ${data.status}`);
	console.log(data)
    socket.to(`user_room_${receiverId}`).emit('status_typing', {
      userId: userId,
      status: data.status,
    });
  });

  socket.on('disconnect', () => {
    console.log('Пользователь отключился:', userId);

    socket.broadcast.emit('status_update', {
      userId: userId,
      status: 'offline',
      lastSeen: new Date(),
    });
	
  });
  
});

subClient.psubscribe('laravel_database_action*');

subClient.on('pmessage', async function (pattern, channel, message) {
  //console.log('send start success1');

  console.log('--- ПОЛУЧЕНО ИЗ REDIS ---');
  console.log('Канал:', channel);
  console.log('Данные:', message);

  const parsed = JSON.parse(message);
  const eventName = channel + ':' + parsed.event;
  
  const receiverId = parsed.data?.message?.seller_id;


  const sender_id = parsed.data.message.sender_id;
  const messages_read = parsed.data?.message?.event_type;
  if(messages_read){
	  //отправлем отправителю уведомление что сообщение получено
	  console.log('уведомление что сообщение прочитано статус 3');
	  io.to(`user_room_${sender_id}`).emit(eventName, parsed.data);
	  return false;
  }
  //console.log('sender_id',sender_id);
  const msgId = parsed.data.message.id;

 

  const targetRoom = `user_room_${receiverId}`;

  const senderSocket = [...io.sockets.sockets.values()].find(
    s => s.verifiedUserId === sender_id,
  );
  //const token = senderSocket?.handshake?.auth?.token;
  
  const token = await client.get(`laravel_database_user:${sender_id}:token`);
  //console.log('sender_id',sender_id);
  
 // console.log('токен отправителя');
  //console.log(token);
  if (!token) {
    console.error("Нет токена для запроса к Laravel");
    return;
  }
  const clientsInRoom = io.sockets.adapter.rooms.get(targetRoom);
  if (clientsInRoom && clientsInRoom.size > 0) {
    console.log(`Юзер ${receiverId} в сети. Шлем сокет...`);

    io.to(targetRoom)
      .timeout(5000)
      .emit(eventName, parsed.data, async (err, responses) => {
        if (err) {
          //console.log(err);

          console.log(
            `Таймаут подтверждения для сообщения ${msgId}. Запускаем Job в Laravel.`,
          );
          triggerLaravelJob(msgId, token);
        } else {
			const clientResponse = responses[0];
            const statusFromClient = clientResponse?.status;
			console.log(`Клиент вернул статус: ${statusFromClient}`);
          console.log(
            `Сообщение ${msgId} получено клиентом. Очередь не нужна.`,
          );
          triggerLaravelStatusUpdate(msgId, statusFromClient, token,sender_id,eventName,parsed);
		  
		   // 2. СРАЗУ шлем отправителю обновленный статус
		       //io.to(`user_room_${sender_id}`).emit(eventName, parsed.data);

   
		  
		  
		  
        }
      });
  } else {
	  
	  
	  
	  
    console.log(`Юзер ${receiverId} оффлайн. Сразу запускаем Job.`);
    triggerLaravelJob(msgId, token);
  }

  if (sender_id) {
    //io.to(`user_room_${sender_id}`).emit(eventName, parsed.data);
  }
  console.log(`Событие ${eventName} отправлено от юзера с id ${sender_id}`);
  console.log(`Событие ${eventName} отправлено юзеру с id ${receiverId}`);
});

async function triggerLaravelJob(messageId, token) {
  try {
    await axios.post(
      `${url}/api/ping`,
      {message_id: messageId},
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json',
        },
      },
    );
  } catch (e) {
    console.error('Не удалось запустить очередь в Laravel');
  }
}

async function triggerLaravelStatusUpdate(messageId, status, token,sender_id,eventName,parsed) {
  try {
    console.log('messageId', messageId);
    console.log('status', status);
    console.log('token', token);
    await axios.post(
      `${url}/api/ping_success`,
      {
        message_id: messageId,
        status: status,
      },
      {
        headers: {Authorization: `Bearer ${token}`},
      },
    );
    console.log(`[Laravel] Статус 2 для сообщения ${messageId} подтвержден`);
	 if (sender_id) {
		 parsed.data.message.status = status;
		 console.log(`Статус ${status} для отправителя ${sender_id}`);
        io.to(`user_room_${sender_id}`).emit(eventName, parsed.data);
		console.log(`отправлена обратная связь для для отправителя ${sender_id}`);
    }
	
  } catch (e) {
    console.error('Не удалось достучаться до Laravel для обновления статуса');
  }
}

server.listen(port, () => {
  console.log('Сервер запущен на порту ' + port);
});

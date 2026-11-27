import test from 'node:test';
import assert from 'node:assert/strict';
import Redis from 'ioredis';
import axios from 'axios';
import { io } from 'socket.io-client';

const API_URL = process.env.INTERNAL_API_URL || 'http://nginx';
const SOCKET_URL = `http://127.0.0.1:${process.env.PORT || 3000}`;
const REDIS_PORT = Number(process.env.REDIS_PORT || 6379);

async function loginUser(email, password = '12345678') {
  const csrfRes = await axios.get(`${API_URL}/login`, {
    headers: { Referer: 'http://127.0.0.1' },
  });
  const setCookies = csrfRes.headers['set-cookie'] || [];
  const cookieHeader = setCookies.map((c) => c.split(';')[0]).join('; ');
  const xsrfMatch = cookieHeader.match(/XSRF-TOKEN=([^;]+)/);
  const xsrfToken = xsrfMatch ? decodeURIComponent(xsrfMatch[1]) : '';

  const response = await axios.post(
    `${API_URL}/api/login`,
    { email, password },
    {
      headers: {
        Accept: 'application/json',
        Referer: 'http://127.0.0.1',
        Cookie: cookieHeader,
        'X-XSRF-TOKEN': xsrfToken,
      },
    }
  );
  return response.data;
}

test('Тест 5: доставка через Privates со status=1 и срабатывание forceQueue по ветке err в Node', async () => {
  const senderAuth = await loginUser('admin@mail.ru');
  const receiverAuth = await loginUser('ulia@mail.ru');

  const subRedis = new Redis({ host: 'redis', port: REDIS_PORT });
  const cmdRedis = new Redis({ host: 'redis', port: REDIS_PORT });

  const testUuid = `test-err-queue-${Date.now()}`;
  const testText = 'Проверка ветки err -> forceQueue при status=1';

  // 1. Слушаем канал Redis laravel_database_action (проверяем приход от события Privates)
  const redisChannelPromise = new Promise((resolve, reject) => {
    const timer = setTimeout(() => {
      reject(new Error('Таймаут ожидания события Privates в канале laravel_database_action'));
    }, 7000);

    subRedis.on('message', (channel, rawMessage) => {
      try {
        const parsed = JSON.parse(rawMessage);
        if (parsed?.data?.message?.uuid === testUuid) {
          clearTimeout(timer);
          resolve({ channel, parsed });
        }
      } catch (err) {
        clearTimeout(timer);
        reject(err);
      }
    });
  });

  await subRedis.subscribe('laravel_database_action');

  // 2. Подключаем получателя в комнату user_room_<id>, чтобы server.js пошёл в онлайн-ветку .timeout(5000)
  const receiverSocket = io(SOCKET_URL, {
    auth: {
      token: receiverAuth.token,
      email: receiverAuth.user.email,
    },
  });

  try {
    await new Promise((resolve, reject) => {
      const timer = setTimeout(() => reject(new Error('Таймаут подключения к server.js')), 5000);
      receiverSocket.once('connect', () => {
        clearTimeout(timer);
        receiverSocket.emit('join', { id: receiverAuth.user.id });
        setTimeout(resolve, 150);
      });
      receiverSocket.once('connect_error', (err) => {
        clearTimeout(timer);
        reject(err);
      });
    });

    // Слушаем сокет получателя, но СПЕЦИАЛЬНО НЕ вызываем ack(), чтобы статус остался 1 и в server.js сработал if (err)
    const socketReceivedWithoutAckPromise = new Promise((resolve, reject) => {
      const timer = setTimeout(() => {
        reject(new Error('Сообщение не пришло получателю от server.js'));
      }, 5000);

      receiverSocket.on('laravel_database_action:privat', (payload /* , ack */) => {
        if (payload?.message?.uuid === testUuid) {
          clearTimeout(timer);
          // Намеренно НЕ вызываем ack(), чтобы через 5 секунд в Node сработал if (err) -> triggerLaravelJob -> forceQueue
          resolve(payload);
        }
      });
    });

    // Отправляем сообщение со специально заданным status = 1 (через маршрут /api/message -> event(new Privates(...)))
    const postRes = await axios.post(
      `${API_URL}/api/message`,
      {
        uuid: testUuid,
        text: testText,
        status: 1,
        listing_id: 1,
        seller_id: receiverAuth.user.id,
      },
      {
        headers: {
          Authorization: `Bearer ${senderAuth.token}`,
          Accept: 'application/json',
        },
      }
    );

    assert.equal(postRes.status, 200);
    const msgId = postRes.data?.message?.id;
    assert.ok(msgId, 'Должен вернуться ID созданного сообщения');

    // Проверяем пункт 1 и 2: событие Privates дошло по каналу и в сокет со status = 1
    const [redisEvent, socketPayload] = await Promise.all([
      redisChannelPromise,
      socketReceivedWithoutAckPromise,
    ]);

    assert.equal(redisEvent.channel, 'laravel_database_action');
    assert.equal(redisEvent.parsed.event, 'privat');
    assert.equal(redisEvent.parsed.data.message.status, 1);
    assert.equal(socketPayload.message.status, 1);

    // До истечения 5-секундного таймаута сокета ключ в Redis ещё должен отсутствовать
    // (доказывает, что сработает именно ветка if (err) по таймауту, а не ветка оффлайна)
    const statusBeforeTimeout = await cmdRedis.get(`laravel_database_msg_status:${msgId}`);
    assert.equal(
      statusBeforeTimeout,
      null,
      'До таймаута сокета (5 сек) forceQueue не должен был вызываться'
    );

    // 3. Ждём срабатывания таймаута 5000мс в server.js -> if (err) -> triggerLaravelJob -> /api/ping (forceQueue)
    let redisStatusAfterErr = null;
    const deadline = Date.now() + 7500;
    while (Date.now() < deadline) {
      redisStatusAfterErr = await cmdRedis.get(`laravel_database_msg_status:${msgId}`);
      if (redisStatusAfterErr !== null) break;
      await new Promise((r) => setTimeout(r, 250));
    }

    assert.equal(
      redisStatusAfterErr,
      '1',
      'После таймаута (err в Node) forceQueue должен записать msg_status = 1 в Redis'
    );
  } finally {
    receiverSocket.disconnect();
    await subRedis.quit();
    await cmdRedis.quit();
  }
});

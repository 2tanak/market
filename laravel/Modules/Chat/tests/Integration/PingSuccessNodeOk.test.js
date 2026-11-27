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

function connectAndJoin(authData) {
  const socket = io(SOCKET_URL, {
    auth: {
      token: authData.token,
      email: authData.user.email,
    },
  });

  const ready = new Promise((resolve, reject) => {
    const timer = setTimeout(() => reject(new Error('Таймаут подключения к server.js')), 5000);
    socket.once('connect', () => {
      clearTimeout(timer);
      socket.emit('join', { id: authData.user.id });
      setTimeout(resolve, 150);
    });
    socket.once('connect_error', (err) => {
      clearTimeout(timer);
      reject(err);
    });
  });

  return { socket, ready };
}

test('Тест 6: доставка через Privates, возврат status=2 и отработка /api/ping_success по ветке ok в Node', async () => {
  const senderAuth = await loginUser('admin@mail.ru');
  const receiverAuth = await loginUser('ulia@mail.ru');

  const subRedis = new Redis({ host: 'redis', port: REDIS_PORT });
  const cmdRedis = new Redis({ host: 'redis', port: REDIS_PORT });

  const testUuid = `test-ok-ping-${Date.now()}`;
  const testText = 'Проверка ветки ok -> /api/ping_success со status=2';

  // 1. Слушаем канал Redis laravel_database_action (событие Privates)
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

  const { socket: senderSocket, ready: senderReady } = connectAndJoin(senderAuth);
  const { socket: receiverSocket, ready: receiverReady } = connectAndJoin(receiverAuth);

  try {
    await Promise.all([senderReady, receiverReady]);

    // 2. Получатель принимает сообщение от server.js и СПЕЦИАЛЬНО подтверждает ack({ status: 2 })
    const receiverAckedPromise = new Promise((resolve, reject) => {
      const timer = setTimeout(() => {
        reject(new Error('Сообщение не пришло получателю от server.js'));
      }, 5000);

      receiverSocket.on('laravel_database_action:privat', (payload, ack) => {
        if (payload?.message?.uuid === testUuid) {
          clearTimeout(timer);
          assert.equal(typeof ack, 'function', 'server.js должен передать callback ack');
          ack({ status: 2 });
          resolve(payload);
        }
      });
    });

    // 3. Отправитель ждёт обратную связь от triggerLaravelStatusUpdate (после успешного POST /api/ping_success)
    const senderFeedbackPromise = new Promise((resolve, reject) => {
      const timer = setTimeout(() => {
        reject(new Error('Отправитель не получил обновлённый status=2 после /api/ping_success'));
      }, 5000);

      senderSocket.on('laravel_database_action:privat', (payload) => {
        if (payload?.message?.uuid === testUuid && Number(payload?.message?.status) === 2) {
          clearTimeout(timer);
          resolve(payload);
        }
      });
    });

    // Отправляем сообщение через /api/message -> event(new Privates(...))
    const postRes = await axios.post(
      `${API_URL}/api/message`,
      {
        uuid: testUuid,
        text: testText,
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

    const [redisEvent, receiverPayload, senderFeedback] = await Promise.all([
      redisChannelPromise,
      receiverAckedPromise,
      senderFeedbackPromise,
    ]);

    // Проверяем пункт 1: пришло по каналу через событие Privates
    assert.equal(redisEvent.channel, 'laravel_database_action');
    assert.equal(redisEvent.parsed.event, 'privat');
    assert.equal(receiverPayload.message.uuid, testUuid);

    // Проверяем пункт 2 и 3: ветка ok в Node вызвала /api/ping_success и вернула отправителю status = 2
    assert.equal(Number(senderFeedback.message.status), 2);

    // Дожидаемся завершения dispatch(...)->afterResponse() в updateStatus (/api/ping_success)
    let redisStatus = null;
    const deadline = Date.now() + 3000;
    while (Date.now() < deadline) {
      redisStatus = await cmdRedis.get(`laravel_database_msg_status:${msgId}`);
      if (redisStatus === '2') break;
      await new Promise((r) => setTimeout(r, 100));
    }

    assert.equal(
      redisStatus,
      '2',
      'Экшен /api/ping_success (updateStatus) должен записать msg_status = 2 в Redis'
    );
  } finally {
    senderSocket.disconnect();
    receiverSocket.disconnect();
    await subRedis.quit();
    await cmdRedis.quit();
  }
});

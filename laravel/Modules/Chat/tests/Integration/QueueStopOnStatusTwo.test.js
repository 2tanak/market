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

test('Тест 7: статус=2 при повторе из очереди сам останавливает CheckMessageDelivery и очищает jobs и Redis', async () => {
  const senderAuth = await loginUser('admin@mail.ru');
  const receiverAuth = await loginUser('ulia@mail.ru');

  const cmdRedis = new Redis({ host: 'redis', port: REDIS_PORT });

  const testUuid = `test-stop-queue-${Date.now()}`;
  const testText = 'Проверка самостоятельной остановки очереди при status=2';

  let receiverSocket = null;

  try {
    // 1. Отправляем сообщение, пока получатель ОФФЛАЙН -> server.js сразу вызывает /api/ping -> стартует очередь CheckMessageDelivery
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
    assert.ok(msgId, 'Должен вернуться ID сообщения');

    // Убеждаемся, что forceQueue поставил msg_status = 1 в Redis
    let statusInRedis = null;
    const waitOfflineDeadline = Date.now() + 4000;
    while (Date.now() < waitOfflineDeadline) {
      statusInRedis = await cmdRedis.get(`laravel_database_msg_status:${msgId}`);
      if (statusInRedis === '1') break;
      await new Promise((r) => setTimeout(r, 100));
    }
    assert.equal(statusInRedis, '1', 'Очередь должна стартовать с msg_status = 1 в Redis');

    // 2. Получатель выходит в ОНЛАЙН и готов ответить ack({ status: 2 }) на повтор из очереди
    receiverSocket = io(SOCKET_URL, {
      auth: {
        token: receiverAuth.token,
        email: receiverAuth.user.email,
      },
    });

    await new Promise((resolve, reject) => {
      const timer = setTimeout(() => reject(new Error('Таймаут подключения получателя к server.js')), 5000);
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

    const retryDeliveredAndAcked = new Promise((resolve, reject) => {
      const timer = setTimeout(() => {
        reject(new Error('Таймаут ожидания повторного сообщения от воркера CheckMessageDelivery'));
      }, 6000);

      receiverSocket.on('laravel_database_action:privat', (payload, ack) => {
        if (payload?.message?.uuid === testUuid) {
          clearTimeout(timer);
          ack({ status: 2 });
          resolve(payload);
        }
      });
    });

    // Пробуждаем отложенную задачу №1 (вместо ожидания 30 сек), чтобы laravel-worker переотправил сообщение
    await cmdRedis.rpush('laravel_database_test7:wakeup_job', String(msgId));

    await retryDeliveredAndAcked;

    // Дожидаемся, пока /api/ping_success обновит статус в Redis на '2'
    const waitStatusTwoDeadline = Date.now() + 4000;
    while (Date.now() < waitStatusTwoDeadline) {
      statusInRedis = await cmdRedis.get(`laravel_database_msg_status:${msgId}`);
      if (statusInRedis === '2') break;
      await new Promise((r) => setTimeout(r, 100));
    }
    assert.equal(statusInRedis, '2', 'После ack({ status: 2 }) в Redis должен установиться статус 2');

    // 3. Пробуждаем следующий цикл CheckMessageDelivery в laravel-worker:
    // увидев status == 2, воркер САМ должен удалить ключ из Redis и завершить джобу (стереть из таблицы jobs)
    await cmdRedis.rpush('laravel_database_test7:wakeup_job', String(msgId));

    let wipedByWorker = false;
    const waitWipeDeadline = Date.now() + 5000;
    while (Date.now() < waitWipeDeadline) {
      const current = await cmdRedis.get(`laravel_database_msg_status:${msgId}`);
      if (current === null) {
        wipedByWorker = true;
        break;
      }
      await new Promise((r) => setTimeout(r, 150));
    }

    assert.equal(
      wipedByWorker,
      true,
      'CheckMessageDelivery при status=2 должен САМ удалить ключ msg_status из Redis и остановить очередь'
    );
  } finally {
    await cmdRedis.setex('laravel_database_test7:done', 10, '1');
    if (receiverSocket) receiverSocket.disconnect();
    await cmdRedis.quit();
  }
});

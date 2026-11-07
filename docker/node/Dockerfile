FROM node:23.0.0


ARG USER_UID=1000
ARG USER_GID=1000

#удаляем дефолтного пользователя node с UID 1000, чтобы не было конфликта
RUN if id -u node >/dev/null 2>&1; then userdel -r node; fi

# Создаем группу www и пользователя www
RUN groupadd -g ${USER_GID} www && \
    useradd -u ${USER_UID} -g www -m -s /bin/bash www

# Настраиваем папки для глобальных пакетов и кэша npm внутри домашней директории www
ENV NPM_CONFIG_PREFIX=/home/www/.npm-global
ENV PATH=$PATH:/home/www/.npm-global/bin

# Задаем рабочую директорию и меняем её владельца на www
WORKDIR /var/www/app
RUN chown -R www:www /var/www/app

# Переключаемся на созданного пользователя www
USER www





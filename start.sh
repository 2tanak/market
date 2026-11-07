
#!/bin/bash

# 1. Если файла .env вообще нет, создаем его из шаблона
if [ ! -f .env ]; then
    cp .env.example .env
fi

# 2. Гарантируем, что переменные USER_UID и USER_GID есть в файле (если их не было)
if ! grep -q "USER_UID=" .env; then echo "USER_UID=" >> .env; fi
if ! grep -q "USER_GID=" .env; then echo "USER_GID=" >> .env; fi

# 3. Находим старые значения и заменяем их на актуальные ID пользователя
sed -i "s/^USER_UID=.*/USER_UID=$(id -u)/" .env
sed -i "s/^USER_GID=.*/USER_GID=$(id -g)/" .env

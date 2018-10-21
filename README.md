# www-upyachka/site-api

## Установка
1. Клонируем репозиторий в директорию домена API
	1. `cd /var/www/api && git clone https://github.com/www-upyachka/site-api`, вместо `/var/www/api` может быть другая директория, в зависимости от настроек Вашего вёб-сервера
	2. Или в случае отсутствия прямого доступа к серверу (если API будет на говнохостинге без root-доступа по SSH) [берем zip-архив](https://github.com/www-upyachka/site-api) с содержимым репозитория и распаковываем в корень директории домена. Например `/public_html/api` или `/api.your-site.net`
2. Создаем базу данных с помощью консоли MySQL или PHPMyAdmin, например
3. Развертываем БД SQL-запросом из `dump.sql`
4. Создаем ~~админа~~ глобального модератора с помощью SQL-запроса 
```sql
INSERT INTO otake_users (login, passwd, joindate, join_ip, ugroup, email, parent_user) VALUES ('YOUR USERNAME', MD5('YOUR PASSWORD'), UNIX_TIMESTAMP(), '127.0.0.1', 'admin', 'this-email-is-useless@fuckyou.wtf', 'root')
```
5. Переименовываем `core/includes/includes.example.php` в `core/includes/includes.php`
6. Правим `$config` под себя
7. Поднимаем фронтенд к движку ([этот](https://github.com/www-upyachka/site-frontend), например)
8. Логинимся на фронтенде
9. Создаем раздел `main`, Глагне, то бишь

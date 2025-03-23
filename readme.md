# Лабораторная работа №5: Запуск сайта в контейнере

## Цель работы

Выполнив данную работу студент сможет подготовить образ контейнера для запуска веб-сайта на базе Apache HTTP Server + PHP (mod_php) + MariaDB.

## Задание

Создать Dockerfile для сборки образа контейнера, который будет содержать веб-сайт на базе Apache HTTP Server + PHP (mod_php) + MariaDB. База данных MariaDB должна храниться в монтируемом томе. Сервер должен быть доступен по порту 8000.

Установить сайт WordPress. Проверить работоспособность сайта.

## Подготовка

Для выполнения данной работы необходимо иметь установленный на компьютере Docker.

Для выполнения работы необходимо иметь опыт выполнения лабораторной работы №3.

## Выполнение

Создаю репозиторий containers05 и копирую его себе на компьютер.

### Извлечение конфигурационных файлов apache2, php, mariadb из контейнера

Создаю в папке `containers05` папку `files`, а также

- папку `files/apache2` - для файлов конфигурации `apache2`;
- папку `files/php` - для файлов конфигурации `php`;
- папку `files/mariadb` - для файлов конфигурации `mariadb`.

Создаю в папке `containers05` файл `Dockerfile` со следующим содержимым:

```Dockerfile
FROM debian:latest

# install apache2, php, mod_php for apache2, php-mysql and mariadb
RUN apt-get update && \
    apt-get install -y apache2 php libapache2-mod-php php-mysql mariadb-server && \
    apt-get clean
```

Строю образ контейнера c именем `apache2-php-mariadb` при помощи

```sh
docker image build -t apache2-php-mariadb .
```

Создаю контейнер `apache2-php-mariadb` из образа `apache2-php-mariadb` и запускаю его в фоновом режиме с командой запуска bash.

```sh
docker container run -d --name apache2-php-mariadb apache2-php-mariadb bash
```

Копирую из контейнера файлы конфигурации apache2, php, mariadb в папку files/ на компьютере. Для этого, в контексте проекта, выполняю команды

```sh
docker cp apache2-php-mariadb:/etc/apache2/sites-available/000-default.conf files/apache2/
docker cp apache2-php-mariadb:/etc/apache2/apache2.conf files/apache2/
docker cp apache2-php-mariadb:/etc/php/8.2/apache2/php.ini files/php/
docker cp apache2-php-mariadb:/etc/mysql/mariadb.conf.d/50-server.cnf files/mariadb/

$ Successfully copied 3.07kB to /.../containers05/files/apache2
$ Successfully copied 9.22kB to /.../containers05/files/apache2
$ Successfully copied 75.8kB to /.../containers05/files/php
$ Successfully copied 5.63kB to /.../containers05/files/mariadb
```

Проверяю наличие конфигураций, останавливаю и удаляю контейнер `apache2-php-mariadb`

```sh
docker container stop apache2-php-mariadb
docker container remove apache2-php-mariadb
```

### Настройка конфигурационных файлов

#### Конфигурационный файл apache2

Открываю файл `files/apache2/000-default.conf`, нахожу строку `#ServerName www.example.com` и заменяю её на `ServerName localhost`

Нахожу строку ServerAdmin `webmaster@localhost` и заменяю в ней почтовый адрес на свой.

После строки `DocumentRoot /var/www/html` добавляю следующие строки:

```apacheconf
DirectoryIndex index.php index.html
```

В конце файла `files/apache2/apache2.conf` добавляю следующую строку:

```apacheconf
ServerName localhost
```

#### Конфигурационный файл php

Открываю файл `files/php/php.ini`, нахожу строку `;error_log = php_errors.log` и заменяю её на `error_log = /var/log/php_errors.log`

Настраиваю параметры `memory_limit`, `upload_max_filesize`, `post_max_size` и `max_execution_time` следующим образом:

```ini
memory_limit = 128M
upload_max_filesize = 128M
post_max_size = 128M
max_execution_time = 120
```

#### Конфигурационный файл mariadb

Открываю файл `files/mariadb/50-server.cnf`, нахожу строку `#log_error = /var/log/mysql/error.log` и раскомментирую её.

### Создание скрипта запуска

Создаю в папке `files` папку `supervisor` и файл `supervisord.conf` со следующим содержимым

```ini
[supervisord]
nodaemon=true
logfile=/dev/null
user=root

# apache2
[program:apache2]
command=/usr/sbin/apache2ctl -D FOREGROUND
autostart=true
autorestart=true
startretries=3
stderr_logfile=/proc/self/fd/2
user=root

# mariadb
[program:mariadb]
command=/usr/sbin/mariadbd --user=mysql
autostart=true
autorestart=true
startretries=3
stderr_logfile=/proc/self/fd/2
user=mysql
```

### Создание Dockerfile

Открываю файл `Dockerfile` и добавляю монтирование томов

```Dockerfile
VOLUME /var/lib/mysql
VOLUME /var/log
```

Добавляю установку пакета `supervisor`

```Dockerfile
RUN apt-get update && \
    apt-get install -y apache2 php libapache2-mod-php php-mysql mariadb-server supervisor && \
    apt-get clean
```

Добавляю копирование и распаковку сайта WordPress

```Dockerfile
# add wordpress files to /var/www/html
ADD https://wordpress.org/latest.tar.gz /var/www/html/
RUN tar xf /var/www/html/latest.tar.gz -C /var/www/html/
```

Добавляю копирование конфигурационных файлов `apache2`, `php`, `mariadb`, а также скрипта запуска

```Dockerfile
# copy the configuration file for apache2 from files/ directory
COPY files/apache2/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY files/apache2/apache2.conf /etc/apache2/apache2.conf

# copy the configuration file for php from files/ directory
COPY files/php/php.ini /etc/php/8.2/apache2/php.ini

# copy the configuration file for mysql from files/ directory
COPY files/mariadb/50-server.cnf /etc/mysql/mariadb.conf.d/50-server.cnf

# copy the supervisor configuration file
COPY files/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# start supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

Для функционирования mariadb создаю папку `/var/run/mysqld` и установите права на неё

```Dockerfile
# create mysql socket directory
RUN mkdir /var/run/mysqld && chown mysql:mysql /var/run/mysqld
```

Открываю порт `80`

```Dockerfile
EXPOSE 80
```

Собираю образ контейнера с именем `apache2-php-mariadb` и запускаю контейнер `apache2-php-mariadb` из образа `apache2-php-mariadb`

```sh
docker image build -t apache2-php-mariadb .
docker container run -d -p 80:80 --name apache2-php-mariadb apache2-php-mariadb
```

Проверяю наличие сайта WordPress в папке `/var/www/html/`

### Создание базы данных и пользователя

Создаю базу данных `wordpress` и пользователя `wordpress` с паролем `wordpress` в контейнере `apache2-php-mariadb`. Для этого, в контейнере `apache2-php-mariadb`, выполняю команды

```sh
mysql
```

```sql
CREATE DATABASE wordpress;
CREATE USER 'wordpress'@'localhost' IDENTIFIED BY 'wordpress';
GRANT ALL PRIVILEGES ON wordpress.* TO 'wordpress'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Создание файла конфигурации WordPress

Открываю в браузере сайт `WordPress` по адресу `http://localhost/wordpress`. Указываю параметры подключения к базе данных:

- Имя базы данных: `wordpress`;
- Имя пользователя: `wordpress`;
- Пароль: `wordpress`;
- Адрес сервера базы данных: `localhost`;
- Префикс таблиц: `wp_`.

Копирую содержимое файла конфигурации в файл `files/wp-config.php` на компьютере.

### Добавление файла конфигурации WordPress в Dockerfile

Добавляю в файл `Dockerfile` следующие строки

```Dockerfile
# copy the configuration file for wordpress from files/ directory
COPY files/wp-config.php /var/www/html/wordpress/wp-config.php
```

## Запуск и тестирование

Пересобираю образ контейнера с именем `apache2-php-mariadb` и запуская контейнер `apache2-php-mariadb` из образа `apache2-php-mariadb`.

Проверяю работоспособность сайта `WordPress`

## Ответы на вопросы

> **Q:** Какие файлы конфигурации были изменены?  
> **A:**
>
> - Конфигурация `apache`
> - Конфигурация `vhost` (`default` виртуального хоста)
> - Конфигурация `php`
> - Конфигурация `mariadb`
> - Конфигурацья `supervisor` была создана
> - Конфигурация `wordpress` была сгенерирована

---

> **Q:** За что отвечает инструкция DirectoryIndex в файле конфигурации apache2?  
> **A:** Указывает дефолтный файл для загрузки при обращении к каталогу

---

> **Q:** Зачем нужен файл wp-config.php?  
> **A:** Он содержит в себе базовые настройки сайта, а также параметры подключения к базе данных

---

> **Q:** За что отвечает параметр post_max_size в файле конфигурации php?  
> **A:** Максимальный размер данных, которые можно отправить по `POST` запросу

---

> **Q:** Укажите, на ваш взгляд, какие недостатки есть в созданном образе контейнера?  
> **A:**
>
> - Нстройку базы данных нужно делать вручную
> - Папка `php/8.2` указана, хоть нигде до этого мы не указывали конкретную версию `php` которую устанавливаем

## Вывод

В ходе лабораторной работы был создан Docker-контейнер с установленным веб-сервером Apache, PHP и базой данных MariaDB. Для этого были подготовлены конфигурационные файлы, скрипты и реализован автоматический запуск необходимых сервисов. В качестве веб-приложения был развернут WordPress, настроена база данных и создан пользователь.

CREATE TABLE post (
   id VARCHAR(36) PRIMARY KEY,
   authorId VARCHAR(36) NOT NULL,
   content MEDIUMTEXT NOT NULL,
   likes INT UNSIGNED DEFAULT 0,
   createdAt INT UNSIGNED NOT NULL
);
CREATE TABLE image (
   postId VARCHAR(36) NOT NULL,
   path VARCHAR(255) NOT NULL,
   PRIMARY KEY (postId, path)
);
CREATE TABLE user (
   id VARCHAR(36) PRIMARY KEY,
   name VARCHAR(255) NOT NULL,
   profileStatus VARCHAR(255),
   avatar VARCHAR(255),
   email VARCHAR(254) UNIQUE,
   password VARCHAR(255),
   registeredAt INT UNSIGNED NOT NULL
);
/*
запуск mysql
   mariadb -u root -p

использование бд
   use blog;

показать все таблицы
   SHOW TABLES;

показать конкретную таблицу 
   SELECT * FROM post;
   SELECT * FROM user;
   SELECT * FROM image;

очистить таблицу
   DELETE FROM post;
   DELETE FROM user;
   DELETE FROM image;

вставка в таблицу
   *файлы posts.sql, users.sql*

удаление таблицы
   DROP TABLE post;
   DROP TABLE user;
   DROP TABLE image;

создание таблиц
   файл table.sql
*/
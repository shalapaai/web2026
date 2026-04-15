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
INSERT INTO
    post (
        id,
        authorId,
        likes,
        content,
        createdAt
    )
VALUES (
        '07f716e9-5e29-452b-897f-0baa5c790d3e',
        '0874af11-e313-4e09-8c10-b233f293bf70',
        100,
        'Так красиво сегодня на улице! Настоящая зима)) Вспоминается Бродский: «Поздно ночью, в уснувшей долине, на самом дне, в городке, занесенном снегом по ручку двери...» ',
        1775478841
    ),
    (
        '37f889a3-a728-4a91-b4b0-36d657e93971',
        '0874af11-e313-4e09-8c10-b233f293bf70',
        100000,
        'Я хочу питсы',
        1775478841
    ),
    (
        'b8a67386-0d49-42f5-a03d-7c817b5391ba',
        '0874af11-e313-4e09-8c10-b233f293bf70',
        1,
        'Русские вперед',
        1775478841
    ),
    (
        '7e9cd21b-625c-45e6-8b1f-0948fb890a6d',
        '0874af11-e313-4e09-8c10-b233f293bf70',
        0,
        'Я слушаю Кристину Орбакайте',
        1775478841
    ),
    (
        '32a8a94f-ec98-41a8-bcbb-2bc750c73046',
        '0874af11-e313-4e09-8c10-b233f293bf70',
        5000000,
        'Равшан раскривушка нада',
        1775478841
    ),
    (
        '2981506d-e9c5-4302-a552-ce29af4cb972',
        '0874af11-e313-4e09-8c10-b233f293bf70',
        0,
        'Первый раскривушка - Джамшут спать, второй раскривушка - Равшан спать, третий раскривушка оба спат',
        1775478841
    ),
    (
        '7da92648-6ce2-4253-827d-8df72d5b836c',
        'c49163a6-7bf6-49db-8914-9a959671c6bc',    
        5,
        'Привет мир',
        1775478841
    ),
    (
        '2d61e17d-1e2e-4e47-a2e3-acfd81eddc46',
        '446523d5-d483-4ef0-b4cb-204de397b59c',
        0,
        'йоу',
        1775478841
    );
INSERT INTO
    user (
        id,
        name,
        avatar,
        profileStatus,
        email,
        password,
        registeredAt
    )
VALUES (
        '0874af11-e313-4e09-8c10-b233f293bf70',
        'Ваня Денисов',
        '/avatar1.jpg',
        'Привет! Я системный аналитик в ACME :) Тут моя жизнь только для самых классных!',
        'vanya@email.com',
        'Qwerty_123',
        1775478841
    ),
    (
        'c49163a6-7bf6-49db-8914-9a959671c6bc',
        'Лиза Дёмина',
        '/avatar2.jpg',
        'Sigma-sigma on the wall, who''s the skibidiest of them all?',
        'liza@email.com',
        'Qwerty_123',
        1775478841
    ),
    (
        '446523d5-d483-4ef0-b4cb-204de397b59c',
        'Аноним',
        '',
        '',
        'anonim@gmail.com',
        'Qwerty_123',
        1775478841
    );
INSERT INTO
    image (
        postId,
        path
    )
VALUES (
        '07f716e9-5e29-452b-897f-0baa5c790d3e',
        '/photo1.jpg'
    ),
    (
        '07f716e9-5e29-452b-897f-0baa5c790d3e',
        '/photo2.jpg'
    ),
    (
        '37f889a3-a728-4a91-b4b0-36d657e93971',
        '/photo3.jpg'
    ),
    (
        'b8a67386-0d49-42f5-a03d-7c817b5391ba',
        '/photo4.jpg'
    ),
    (
        '7e9cd21b-625c-45e6-8b1f-0948fb890a6d',
        '/photo5.jpg'
    ),
    (
        '32a8a94f-ec98-41a8-bcbb-2bc750c73046',
        '/photo6.jpg'
    ),
    (
        '2981506d-e9c5-4302-a552-ce29af4cb972',
        '/photo2.jpg'
    ),
    (
        '2d61e17d-1e2e-4e47-a2e3-acfd81eddc46',
        '/69c3cd6417262_1774439780_0.jpg'
    ),
    (
        '2d61e17d-1e2e-4e47-a2e3-acfd81eddc46',
        '/69c3cd64174a4_1774439780_1.jpg'
    ),
    (
        '7da92648-6ce2-4253-827d-8df72d5b836c',
        '/photo2.jpg'
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

удаление таблицы
   DROP TABLE post;
   DROP TABLE user;
   DROP TABLE image;
*/
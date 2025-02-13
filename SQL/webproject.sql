-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3307
-- Время создания: Фев 13 2025 г., 02:08
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `webproject`
--

-- --------------------------------------------------------

--
-- Структура таблицы `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `breed` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `description` text NOT NULL,
  `photo` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `pets`
--

INSERT INTO `pets` (`id`, `name`, `breed`, `age`, `description`, `photo`, `created_at`, `category_id`) VALUES
(1, 'Барсик', 'Сиамский кот', 3, 'Дружелюбный и игривый кот.', 'https://sun9-14.userapi.com/impg/RchUrngcdv9qHrlIr4GZSGkKgnAhtiZ8LhEyUA/LbAdtHv6Yd4.jpg?size=300x300&quality=95&sign=3206653bb718bbc6cf20f63dfec1738a&type=album', '2025-01-31 01:55:24', 1),
(2, 'Шарик', 'Немецкая овчарка', 5, 'Умная и преданная собака.', 'https://sun9-31.userapi.com/impg/Ir7WvEJ6fuYP1v21546DFsrjNbP_Ee7MCVjLUg/NrjVdxq9jYE.jpg?size=300x300&quality=95&sign=0c675ebb230a583c517eec87d1a50bef&type=album', '2025-01-31 01:55:24', 2),
(3, 'Мурка', 'Дворовая кошка', 2, 'Ласковая и спокойная кошка.', 'https://sun9-75.userapi.com/impg/Lz55XbsyJXSy1xwJFZ5o6S-VYcUGwoyChEvNWw/FUyUDT9FKQg.jpg?size=300x300&quality=95&sign=c8253172d8acabd48294e16972d9df4f&type=album', '2025-01-31 01:55:24', 1),
(12, 'Черныш', 'Лабрадор', 3, 'Умная, добрая, общительная и послушная собака.', 'https://sun9-77.userapi.com/impg/HIOJjqC1m2XzRdV8XESVKzQ7OQPXvVB2E_Jmiw/T2sJ-sZKXXI.jpg?size=1279x1279&quality=95&sign=c7d0800af68f2dfee1a36c43a2f2bccb&type=album', '2025-02-11 16:55:45', 2),
(13, 'Том', 'Дворовой кот', 52, 'Говорящий кот!!!', 'https://sun9-22.userapi.com/impg/MgSJsSki0xIUvff6AYGrpnv-B1pIb18AtvEHLw/QDhzp91pWWI.jpg?size=443x433&quality=95&sign=1870a308303c5ed1e3987cb35d5b61ba&type=album', '2025-02-11 17:08:11', 1),
(14, 'Тейп', 'Домашний', 2, 'Как бигбебитейп только без бигбеби', 'https://sun9-33.userapi.com/impg/u7vc4xKI_TlR4KaTpoqq0wIdFx8wvoLZJ6XdbA/zxjHROcHZ04.jpg?size=960x960&quality=95&sign=2b452f246bb0167c2ea1464f7f90f693&type=album', '2025-02-11 17:19:01', 1),
(15, 'Мага', 'Хулиган', 47, 'Ассаламуалейкум брат, 2700 рублей одолжи на лирику и 2 тропа', 'https://sun9-56.userapi.com/impg/OjKWid9Nd3JDUK1vhUouNKZTjKijolKPHkTntA/dL549kkfeFs.jpg?size=801x801&quality=95&sign=e79a7acacba9e0a5c79c5251f0972732&type=album', '2025-02-11 17:23:28', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `pet_categories`
--

CREATE TABLE `pet_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `pet_categories`
--

INSERT INTO `pet_categories` (`id`, `name`) VALUES
(1, 'Коты'),
(2, 'Собаки');

-- --------------------------------------------------------

--
-- Структура таблицы `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`) VALUES
(2, 'Аксессуары'),
(3, 'Сувениры'),
(1, 'Футболка');

-- --------------------------------------------------------

--
-- Структура таблицы `shop_products`
--

CREATE TABLE `shop_products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `photo_path` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `shop_products`
--

INSERT INTO `shop_products` (`id`, `name`, `description`, `price`, `stock_quantity`, `photo_path`, `category_id`, `created_at`, `updated_at`) VALUES
(2, 'Футболка \"Лапка\"', 'Футболка хакки с рисунком \"Лапка\", \r\nразмер L, \r\n100% хлопок ', 1500.00, 58, 'https://sun9-45.userapi.com/impg/9kyD2FI28Ow4TAKX_n8-r7hBzOc90Maq5FNbRg/d_ZzdurLpuk.jpg?size=500x500&quality=95&sign=dfe3ad4601e84f688b6d7de8d4aee494&type=album', 1, '2025-02-10 23:02:45', '2025-02-11 00:45:56'),
(3, 'Футболка \"Лапка\"', 'Футболка белая с рисунком \"Лапка\", размер M, 100% хлопок', 1500.00, 51, 'https://sun9-79.userapi.com/impg/1NJ1RO8g3Jo40ouBX64L3j_4GblYJRI3mSQ6Lg/iD3YteXscyA.jpg?size=500x500&quality=95&sign=2a1a69f0a2345be348a1ae7a28fff1a4&type=album', 1, '2025-02-10 23:04:06', '2025-02-11 00:46:01'),
(4, 'Браслет ', 'Силиконовый браслет с лапками, белый', 250.00, 100, 'https://sun9-35.userapi.com/impg/NeivIQffikdFKlv60Be47nIVvDj1NFhijb6mrA/BgqwzP_BB9k.jpg?size=640x640&quality=95&sign=32dd7f405a9d256c9e8c18c8e49496cd&type=album', 2, '2025-02-10 23:09:18', '2025-02-11 00:43:57'),
(5, 'Браслет ', 'Силиконовый браслет с лапками, черный', 250.00, 117, 'https://sun9-67.userapi.com/impg/ndwkMVbB1KuTYB9bgqwy1J0vhOXl4lKasZZV7Q/xok1vbR7Z78.jpg?size=800x800&quality=95&sign=7904caeeb8245e72cf7b27dae45c4696&type=album', 2, '2025-02-10 23:09:49', '2025-02-11 00:44:18'),
(6, 'Кепка \"Лапка\"', 'Кепка универсальная с рисунком \"Лапка\", белая', 1000.00, 40, 'https://sun9-66.userapi.com/impg/OhdLkQxfbUA9q2YlnEpQfmMhzLtFvhz0Ycuhkw/o7QfnRFn9fk.jpg?size=500x500&quality=95&sign=8f00e730b427dfcda8c8b62e6b8d6d9b&type=album', 2, '2025-02-10 23:14:23', '2025-02-11 00:46:08'),
(7, 'Магнит ', 'Плоский магнит из дерева на холодильник', 150.00, 150, 'https://sun9-62.userapi.com/impg/8t8u0n9KSPy-G_SOLVG7rKwhBz3rIxcsc03BHA/iVdStuPE3EM.jpg?size=755x755&quality=95&sign=f3039286e7210b3af7f10931ff68746e&type=album', 3, '2025-02-10 23:24:55', '2025-02-11 02:17:53'),
(8, 'Наклейки ', 'Наклейки с рисунком лапка', 100.00, 300, 'https://sun9-78.userapi.com/impg/HM69_nW82HBWAH6sjI1vYaasLZ4VorNndrd4XQ/CW54chfiEjE.jpg?size=1001x1001&quality=95&sign=bfb03c3556840cbdf69f88feae8048c0&type=album', 3, '2025-02-10 23:25:51', '2025-02-11 00:47:36'),
(9, 'Ольга', '300м от Вас', 5000.00, 1, 'https://sun9-21.userapi.com/impg/zmopne2Y-WaSwYXDBvmw6hjuKnTCR7fhgFXdFQ/XVb6bbwwSs8.jpg?size=460x460&quality=95&sign=2a4d17540ef60ebf19d0bb5915854bc8&type=album', 3, '2025-02-11 17:25:49', '2025-02-11 17:25:49');

-- --------------------------------------------------------

--
-- Структура таблицы `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `pet_id`, `user_id`, `amount`, `start_date`, `end_date`, `status`) VALUES
(1, 2, 1, 1.00, '2025-02-13', NULL, 'active'),
(2, 14, 1, 100.00, '2025-02-13', NULL, 'active');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `password`, `created_at`, `role`) VALUES
(1, 'admin', 'admin@site.com', '9961073260', '$2y$10$p5Nc9xAvyeLwOvG88DGC/uuZzMNGFUssLDg7vWKFEsfpxxPlJ1tcC', '2025-02-12 21:47:28', 'admin'),
(2, 'Марсель Исмагилов', 'marsel@gmail.com', '89177581595', '$2y$10$9iLk5NvtN2AN8O.ffDiDweR9NkfRG.3hGhT/tcYpNRN2a.LE3UMHC', '2025-02-12 21:49:38', 'user');

-- --------------------------------------------------------

--
-- Структура таблицы `user_suggestions`
--

CREATE TABLE `user_suggestions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('new','in_progress','completed','rejected') NOT NULL DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `user_suggestions`
--

INSERT INTO `user_suggestions` (`id`, `user_id`, `title`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 'Добавить возможность фильтрации товаров по цене', 'Было бы удобно добавить возможность фильтрации товаров по ценовому диапазону на странице магазина.', 'in_progress', '2025-02-12 21:50:10', '2025-02-12 21:56:30'),
(3, 1, 'gdfg', 'dfgdfgs', 'new', '2025-02-12 22:29:35', '2025-02-12 22:29:35');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `pet_categories`
--
ALTER TABLE `pet_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `shop_products`
--
ALTER TABLE `shop_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_category` (`category_id`);

--
-- Индексы таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `user_suggestions`
--
ALTER TABLE `user_suggestions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `pet_categories`
--
ALTER TABLE `pet_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `shop_products`
--
ALTER TABLE `shop_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `user_suggestions`
--
ALTER TABLE `user_suggestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `pet_categories` (`id`);

--
-- Ограничения внешнего ключа таблицы `shop_products`
--
ALTER TABLE `shop_products`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`);

--
-- Ограничения внешнего ключа таблицы `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`),
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `user_suggestions`
--
ALTER TABLE `user_suggestions`
  ADD CONSTRAINT `user_suggestions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

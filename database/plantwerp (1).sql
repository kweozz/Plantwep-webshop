-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 27 nov 2024 om 07:43
-- Serverversie: 10.4.32-MariaDB
-- PHP-versie: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `plantwerp`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `category`
--

INSERT INTO `category` (`id`, `name`, `image`) VALUES
(1, 'Vet planten', 'images/uploads/vetplant.png'),
(2, 'Cactussen', 'images/uploads/cactus.png'),
(3, 'Groene planten', 'images/uploads/groenekamerplant.png'),
(5, 'Hang planten', 'images/uploads/hangplant.png');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category_id`, `stock`, `created_at`, `updated_at`) VALUES
(13, 'Allocasia', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur quibusdam inventore doloribus molestias laborum a, ipsam error, atque repellendus ratione natus odit ducimus possimus labore veritatis! Alias, recusandae et! Doloremque?', 15, 'images/uploads/allocasia.png', 3, 5, '2024-11-26 12:52:30', '2024-11-26 12:52:30'),
(14, 'Calathea', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsa laudantium iste dolores atque, fugiat, recusandae ducimus sint maiores in hic minima rem obcaecati praesentium quia corporis provident architecto excepturi. Incidunt.', 20, 'images/uploads/calathea.png', 3, 5, '2024-11-26 13:03:41', '2024-11-26 14:19:01'),
(15, 'Monstera', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi at deserunt rerum quam est repellat ipsam. Saepe assumenda vel nemo accusamus, et veritatis consequatur laudantium laboriosam rerum sunt at exercitationem!', 15, 'images/uploads/monstera.png', 3, 10, '2024-11-26 13:38:42', '2024-11-26 13:38:42'),
(16, 'Dieffenbachia', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Aperiam veniam voluptas asperiores officiis! Molestias ipsum dolorem ipsam debitis magnam aperiam doloribus voluptas, libero repellendus earum iure quae doloremque quas ab.', 50, 'images/uploads/diffnbachia.png', 3, 3, '2024-11-26 14:36:47', '2024-11-26 14:36:47');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `product_options`
--

CREATE TABLE `product_options` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `option_name` varchar(255) NOT NULL,
  `option_value` varchar(255) NOT NULL,
  `extra_price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(300) NOT NULL,
  `lastname` varchar(300) NOT NULL,
  `email` varchar(300) NOT NULL,
  `password` varchar(300) NOT NULL,
  `role` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `currency` int(11) DEFAULT 1000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`, `role`, `created_at`, `currency`) VALUES
(22, 'Ilian', 'Bosserez', 'ilian@test.com', '$2y$10$af0tckgPcNKmNwEnSwax6u5/aju50D4fP19L9uTgLXVNFVrZBPJVe', 1, '2024-11-23 13:12:01', 1000),
(23, 'tony', 'stark', 'ironman@avengers.com', '$2y$10$3Cy8tYUrXnEKooclAwrACelifW7U664Z0lPEB79c4g5NmXNoQCR6O', 0, '2024-11-23 13:12:01', 1000);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `product_options`
--
ALTER TABLE `product_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_id` (`product_id`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT voor een tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT voor een tabel `product_options`
--
ALTER TABLE `product_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `product_options`
--
ALTER TABLE `product_options`
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

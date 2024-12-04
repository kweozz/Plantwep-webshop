-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 04 dec 2024 om 14:15
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
(1, 'Vette planten', 'images/uploads/vetplant.png'),
(2, 'Cactussen', 'images/uploads/cactus.png'),
(3, 'Groene planten', 'images/uploads/groenekamerplant.png'),
(9, 'Hang planten', 'images/uploads/hangplant.png');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('size','pot') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `options`
--

INSERT INTO `options` (`id`, `name`, `type`) VALUES
(1, 'Small', 'size'),
(2, 'Medium', 'size'),
(3, 'Large', 'size'),
(4, 'Pot beschikbaar', 'pot');

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
(14, 'Allocasia', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsa laudantium iste dolores atque, fugiat, recusandae ducimus sint maiores in hic minima rem obcaecati praesentium quia corporis provident architecto excepturi. Incidunt.', 20, 'images/uploads/allocasia.png', 3, 5, '2024-11-26 13:03:41', '2024-12-02 21:38:59'),
(16, 'Dieffenbachia', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Aperiam veniam voluptas asperiores officiis! Molestias ipsum dolorem ipsam debitis magnam aperiam doloribus voluptas, libero repellendus earum iure quae doloremque quas ab.', 50, 'images/uploads/diffnbachia.png', 3, 3, '2024-11-26 14:36:47', '2024-11-26 14:36:47'),
(31, 'Monstera', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Sapiente aliquid voluptatem repellat, aut magni animi recusandae accusamus alias, fugiat placeat doloribus ad id in, laborum quos reprehenderit quis dolorum deserunt.', 15, 'images/uploads/monstera.png', 3, 20, '2024-12-02 21:50:33', '2024-12-02 21:50:33'),
(32, 'Calathea', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Sapiente aliquid voluptatem repellat, aut magni animi recusandae accusamus alias, fugiat placeat doloribus ad id in, laborum quos reprehenderit quis dolorum deserunt.', 20, 'images/uploads/calathea.png', 3, 5, '2024-12-02 21:58:02', '2024-12-02 22:23:31'),
(33, 'test met optie', 'sssss', 15, 'images/uploads/store.jpg', 1, 2, '2024-12-04 13:00:20', '2024-12-04 13:00:20'),
(34, 'test met optie', 'sssss', 15, 'images/uploads/store.jpg', 1, 2, '2024-12-04 13:00:20', '2024-12-04 13:00:20'),
(35, 'test optie test', 'ddddd', 50, 'images/uploads/logo-plantwerp.png', 9, 5, '2024-12-04 13:11:31', '2024-12-04 13:11:31'),
(36, 'lalala', 'xxxx', 25, 'images/uploads/plantstore.jpg', 1, 11, '2024-12-04 13:19:05', '2024-12-04 13:19:05'),
(37, 'test opties', 'ddddd', 15, 'images/uploads/store.jpg', 1, 5, '2024-12-04 13:25:21', '2024-12-04 13:25:21'),
(38, 'finale', 'xxxx', 15, 'images/uploads/plantstore.jpg', 1, 15, '2024-12-04 14:08:29', '2024-12-04 14:08:29');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `product_options`
--

CREATE TABLE `product_options` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL
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
-- Indexen voor tabel `options`
--
ALTER TABLE `options`
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
  ADD KEY `product_id` (`product_id`),
  ADD KEY `option_id` (`option_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT voor een tabel `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT voor een tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT voor een tabel `product_options`
--
ALTER TABLE `product_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  ADD CONSTRAINT `product_options_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_options_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

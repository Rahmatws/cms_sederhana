-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Bulan Mei 2025 pada 13.16
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cms_sederhana`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Teknologi', '2025-05-07 08:04:51'),
(2, 'Edukasi', '2025-05-07 08:04:51'),
(3, 'Hiburan', '2025-05-07 08:04:51'),
(4, 'Olahraga', '2025-05-07 08:04:51'),
(5, 'Kesehatan', '2025-05-07 08:04:51'),
(6, 'Bisnis', '2025-05-07 08:04:51'),
(7, 'Sains', '2025-05-07 08:04:51'),
(8, 'Travel', '2025-05-07 08:04:51'),
(9, 'Kuliner', '2025-05-07 08:04:51'),
(10, 'Gaya Hidup', '2025-05-07 08:04:51'),
(12, 'Other', '2025-05-07 09:00:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `logins`
--

CREATE TABLE `logins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `logins`
--

INSERT INTO `logins` (`id`, `user_id`, `login_time`) VALUES
(1, 1, '2025-05-07 16:58:00'),
(2, 4, '2025-05-07 16:59:07'),
(3, 1, '2025-05-07 16:59:47'),
(4, 1, '2025-05-07 17:02:32'),
(5, 1, '2025-05-07 17:07:17'),
(6, 1, '2025-05-07 17:21:14'),
(7, 1, '2025-05-07 17:37:36'),
(8, 1, '2025-05-07 18:15:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `status` enum('Published','Draft','Archived') DEFAULT 'Draft',
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `user_id`, `created_at`, `updated_at`, `category_id`, `status`, `image`) VALUES
(1, 'Honkai Star Rail juara game mobile', 'awdadwadwadwadawdadada', 1, '2025-05-07 08:11:44', '2025-05-07 08:11:44', 3, 'Published', 'img_681b15c040a54.png'),
(2, 'Honkai Star Rail juara game mobile', 'awdadwadwadwadawdadada', 1, '2025-05-07 08:14:26', '2025-05-07 08:14:26', 3, 'Published', 'img_681b1662bd78e.png'),
(3, 'Honkai Star Rail juara game mobile', 'adwadawdawdwad', 1, '2025-05-07 08:14:47', '2025-05-07 08:14:47', 2, 'Published', NULL),
(4, 'adwadawdawdwad', 'hhhhhhhhhhhhhhhhhhhhhhh', 1, '2025-05-07 08:19:17', '2025-05-07 08:19:17', 9, 'Published', 'img_681b1785ca56b.png'),
(5, 'Rekomendasi Game', 'cmssederhana................', 1, '2025-05-07 08:29:45', '2025-05-07 09:02:00', 12, 'Published', 'img_681b21888928c.png'),
(6, 'Berita cara membuat CMS sederhana', 'CMS adalah Content Management System', 1, '2025-05-07 10:01:16', '2025-05-07 10:01:16', 2, 'Published', 'img_681b2f6c69662.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `post_categories`
--

CREATE TABLE `post_categories` (
  `post_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','editor','penulis','viewer') NOT NULL DEFAULT 'viewer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`, `role`) VALUES
(1, 'admin', '$2y$10$NHpw6U1fZR/8o61vTTaU1e6pFZLhGgKiW1WPz36lwwXox1AcwYGhS', 'admin@example.com', '2025-05-06 09:29:42', 'admin'),
(2, 'edit', '$2y$10$O0DV/0G5f6Eg5xYKJgXdIuZ8fHJ.pVHgBCxHt5ndn5.1z80jhRcLq', 'edit@mail.com', '2025-05-07 07:32:44', 'editor'),
(3, 'view', '$2y$10$fQ/UW4XQGmJB8/wJqD/rXO9vhVrFX8UPR25txKSukVQLt7JoRQwpW', 'view@mail.com', '2025-05-07 07:34:25', 'viewer'),
(4, 'penulis', '$2y$10$ArbHUzxymXiuwXYCoXPlt.U2IeBhksb2VczyHzPPevni9ErZrJgkS', 'penulis@mail.com', '2025-05-07 09:58:54', 'penulis');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indeks untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `logins`
--
ALTER TABLE `logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_category` (`category_id`);

--
-- Indeks untuk tabel `post_categories`
--
ALTER TABLE `post_categories`
  ADD PRIMARY KEY (`post_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `logins`
--
ALTER TABLE `logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `logins`
--
ALTER TABLE `logins`
  ADD CONSTRAINT `logins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `post_categories`
--
ALTER TABLE `post_categories`
  ADD CONSTRAINT `post_categories_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

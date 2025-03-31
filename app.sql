-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Mar 31, 2025 at 08:37 PM
-- Server version: 8.1.0
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250331202556', '2025-03-31 20:26:23', 1404);

-- --------------------------------------------------------

--
-- Table structure for table `invitation`
--

CREATE TABLE `invitation` (
  `id` int NOT NULL,
  `accepted` tinyint(1) DEFAULT NULL,
  `wishlist_id` int NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` double NOT NULL,
  `has_purchased` tinyint(1) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `wishlist_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`id`, `name`, `description`, `price`, `has_purchased`, `url`, `wishlist_id`) VALUES
(51, 'eum', 'Consequatur mollitia a est ea est aut a.', 486.7, 1, 'http://sauer.net/', 11),
(52, 'doloribus', 'Illum ducimus earum provident ipsam rem molestias eum.', 341.01, 0, 'http://www.russel.com/quo-cumque-quisquam-et-culpa', 11),
(53, 'perferendis', 'Quasi architecto debitis quo sit ut.', 157.99, 0, 'http://sawayn.com/', 11),
(54, 'provident', 'Aliquam iste ea hic aut non.', 283.97, 0, 'http://gutkowski.biz/aliquam-numquam-id-amet', 11),
(55, 'tempora', 'Harum doloremque consequatur ut necessitatibus nulla autem voluptas.', 264.27, 1, 'http://www.rohan.com/et-omnis-quia-non-vero-consequatur-odio-sed', 11),
(56, 'nisi', 'Corporis nihil eaque magni voluptatem illo.', 64.31, 0, 'http://quigley.biz/ullam-aut-alias-asperiores-aut-aliquam-quia-ea.html', 12),
(57, 'in', 'Deserunt hic est in velit eos dolorem labore.', 328.38, 0, 'https://www.senger.info/qui-explicabo-quo-velit-ducimus-necessitatibus-perspiciatis-possimus', 12),
(58, 'culpa', 'Odit quo culpa quo doloribus est.', 403.2, 0, 'http://kris.com/commodi-nihil-quia-voluptatem-aut-ea-aut-quasi-quaerat', 12),
(59, 'et', 'Ipsam omnis necessitatibus blanditiis autem.', 32.9, 1, 'http://www.schultz.com/amet-suscipit-corporis-nisi-atque-commodi-fuga-assumenda', 12),
(60, 'reiciendis', 'Sint voluptate eos molestiae aut vel molestiae.', 97.76, 1, 'https://www.kemmer.net/nemo-quia-sunt-qui-odit-molestiae-et-rem', 12),
(61, 'distinctio', 'Nisi quidem et deserunt quae omnis omnis.', 235.01, 0, 'http://huels.biz/eligendi-non-repellendus-quia-qui-sed.html', 13),
(62, 'et', 'Magni ea dolor voluptatem.', 89.62, 0, 'http://cole.biz/minus-dolores-nihil-incidunt-similique-quas-corporis-consequatur-quia', 13),
(63, 'ea', 'Eos ut delectus odit molestias sit corrupti.', 115.14, 0, 'http://beahan.net/voluptatum-qui-unde-enim-incidunt-laboriosam-minus', 13),
(64, 'quibusdam', 'Soluta occaecati et ut.', 57.84, 1, 'http://www.mann.com/', 13),
(65, 'excepturi', 'In earum qui sit dolorem commodi quia iste quaerat.', 419.23, 1, 'http://www.fahey.com/quod-et-et-suscipit-dolores', 13),
(66, 'sint', 'Est vel autem et quod.', 393.8, 0, 'http://www.bruen.com/eos-maxime-et-quidem', 14),
(67, 'aliquam', 'Ullam quibusdam autem enim voluptates est.', 187.83, 1, 'http://www.pouros.net/dolores-voluptas-iusto-a-necessitatibus-nam-libero-distinctio.html', 14),
(68, 'libero', 'Quia sequi voluptas corrupti minus cupiditate molestiae reprehenderit.', 181.03, 0, 'http://volkman.com/et-blanditiis-temporibus-iusto-error-ea-architecto', 14),
(69, 'adipisci', 'Ullam commodi quo alias ad.', 235.05, 1, 'http://www.kuhn.com/fugiat-rerum-ipsa-minima-quia.html', 14),
(70, 'ut', 'Rem sunt accusamus cumque aspernatur.', 139.94, 0, 'http://west.com/sed-molestiae-adipisci-qui-qui-aut-velit', 14),
(71, 'repellendus', 'Aut quo id facere et error temporibus.', 224.94, 1, 'http://www.walter.com/repellendus-distinctio-eum-commodi-quia-id-id-et-qui', 15),
(72, 'nulla', 'Et mollitia a dolores voluptates facilis ratione dignissimos.', 214.43, 0, 'https://thiel.com/doloribus-illo-iste-a-tempore-fugit.html', 15),
(73, 'in', 'Quasi maiores ipsam quaerat omnis rerum.', 312.16, 0, 'https://www.howe.biz/laudantium-quo-consequatur-iusto-eum-aliquid-natus', 15),
(74, 'hic', 'Debitis pariatur quisquam repudiandae quis molestiae ea.', 394.06, 0, 'http://haag.com/eligendi-quae-a-velit-doloremque-voluptas-unde-deserunt', 15),
(75, 'aliquam', 'Est eos eveniet quisquam reiciendis aliquid.', 192.16, 1, 'http://turcotte.com/ipsa-eaque-sint-ea', 15),
(76, 'et', 'Sed molestiae est dolor odio ut quia.', 58.7, 1, 'https://www.johnston.com/minus-perferendis-autem-at', 16),
(77, 'laboriosam', 'Molestias sed voluptatibus ut quis.', 292.49, 1, 'http://auer.org/vel-ut-voluptatem-ut-eos-quia-et', 16),
(78, 'aut', 'Iure vel rem iure neque eos sequi quod.', 224.16, 0, 'https://feeney.net/quis-placeat-nihil-quas-velit-quis.html', 16),
(79, 'eius', 'Harum non atque hic sunt.', 498.61, 1, 'http://emard.com/nesciunt-et-quo-velit-quo-consequatur-repellendus', 16),
(80, 'quasi', 'Iste illum labore consequatur quisquam natus qui.', 315.4, 0, 'http://www.swift.com/et-ad-rerum-dolorem-sed-eius-eos-in', 16),
(81, 'minus', 'Omnis qui dicta laborum et.', 254.1, 1, 'http://www.murphy.com/mollitia-laborum-est-nihil-nam-aut', 17),
(82, 'id', 'Qui quam repellendus minima porro exercitationem magnam et.', 280.79, 0, 'https://www.bosco.com/illum-ad-soluta-totam-enim-dolorum-necessitatibus-officia', 17),
(83, 'amet', 'Perspiciatis est aut magnam mollitia.', 70.69, 1, 'https://fay.info/placeat-error-pariatur-a-suscipit-in-quod.html', 17),
(84, 'aspernatur', 'Esse voluptatum consectetur animi qui quia.', 93.01, 0, 'http://www.cassin.com/et-blanditiis-deserunt-quaerat-dolore-alias-rerum-sint', 17),
(85, 'et', 'Doloremque ut eos id delectus voluptatem harum consectetur.', 440.37, 0, 'http://www.hammes.com/', 17),
(86, 'omnis', 'Odit accusantium tempore et est eveniet placeat quos.', 362.08, 0, 'https://ratke.com/dolores-deleniti-cumque-voluptatem-facilis.html', 18),
(87, 'non', 'Dolorem fugiat quae eum iusto.', 376.61, 1, 'https://www.pagac.com/suscipit-odit-nesciunt-voluptatibus', 18),
(88, 'est', 'Possimus ad ex sequi repudiandae id aliquid et.', 499.65, 0, 'http://cassin.net/dolores-eveniet-quia-voluptas-sequi-ipsa', 18),
(89, 'blanditiis', 'Non et numquam ut at id laudantium.', 87.49, 0, 'http://www.sanford.com/aut-quis-sit-sunt-consequatur.html', 18),
(90, 'ea', 'Voluptatem magni et non sunt libero voluptatem non.', 29.5, 0, 'http://www.boehm.com/incidunt-cum-a-et-dolorem', 18),
(91, 'facilis', 'Eos dicta vel a rerum quis totam velit.', 77.72, 0, 'https://www.sipes.biz/natus-ad-architecto-exercitationem-a-nobis-rerum-dolore-omnis', 19),
(92, 'adipisci', 'Soluta adipisci et officia et vitae.', 103.2, 1, 'http://brekke.com/', 19),
(93, 'dolores', 'Et praesentium architecto minus aspernatur placeat necessitatibus.', 109.21, 0, 'https://rippin.info/ipsum-in-quas-odit-eum-incidunt-quos.html', 19),
(94, 'recusandae', 'Praesentium est laborum molestiae iusto ipsum veritatis.', 443.38, 0, 'http://parisian.com/officia-quod-numquam-aliquam-veniam.html', 19),
(95, 'dolorem', 'Repellendus sed necessitatibus dolores quas dicta eius.', 309.28, 0, 'https://www.breitenberg.biz/ea-occaecati-architecto-ratione-voluptas-sed', 19),
(96, 'earum', 'Voluptates est quasi sunt dolorem voluptas ad.', 235.08, 1, 'http://www.ruecker.org/', 20),
(97, 'consequatur', 'Et incidunt expedita et accusamus.', 471.28, 0, 'http://fritsch.com/eos-est-odit-facere-molestiae-qui', 20),
(98, 'autem', 'Tempore ad voluptas vel.', 414.02, 1, 'http://hodkiewicz.com/maxime-beatae-dolorum-quo-quo-qui-quis', 20),
(99, 'omnis', 'Dolorem molestias commodi nulla maiores.', 75.09, 1, 'http://www.feest.com/ea-quam-ipsam-velit-quis', 20),
(100, 'est', 'Eos deserunt eum saepe voluptatum accusantium quia quidem.', 199.39, 1, 'https://www.hansen.net/rerum-enim-provident-facere-magni-sapiente-quam', 20);

-- --------------------------------------------------------

--
-- Table structure for table `proof`
--

CREATE TABLE `proof` (
  `id` int NOT NULL,
  `congrats_message` varchar(255) NOT NULL,
  `proof_image_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `buyer_id` int DEFAULT NULL,
  `item_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(180) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `lock_status` tinyint(1) NOT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `username`, `email`, `created_at`, `updated_at`, `password`, `photo`, `lock_status`, `type`) VALUES
(6, 'Easton', 'Walker', 'friesen.leone', 'lenny73@fisher.com', '2025-01-07 00:44:46', '2025-02-10 08:08:41', '$2y$13$S3uUFrYd6qWMdCDa/i.Phuz3AEj4qYaK2mkDmuBJdijOBsOCBmO9y', NULL, 0, 'regular'),
(7, 'Hailie', 'Rowe', 'wilkinson.kamille', 'nshanahan@stamm.com', '2025-02-22 00:58:21', '2025-01-15 17:50:13', '$2y$13$mKBrIkWokfDnvqhwyzx2reRnv4oX0WtmQs/3fFql8Jpv0fXbErBva', NULL, 0, 'regular'),
(8, 'Ian', 'Boehm', 'mante.chloe', 'junior35@lockman.com', '2025-01-14 16:43:51', '2025-03-24 00:41:50', '$2y$13$UKFvbUjPdtfyFAneCgsRveg9r4JPzmKp9Ku4lV6j1EAIloQaoEWwq', NULL, 0, 'regular'),
(9, 'Flavio', 'Rice', 'peter.ondricka', 'deborah.dooley@gmail.com', '2025-03-10 02:19:18', '2025-03-14 15:07:50', '$2y$13$cz4NASewW.xtgduLszZ9ZuOYlaFMuM/Dp67n2rNZG9fZCPTPqIYFm', NULL, 0, 'regular'),
(10, 'Lindsey', 'Sporer', 'jordy50', 'howe.jeffrey@rutherford.com', '2025-01-11 22:56:42', '2025-03-13 12:23:49', '$2y$13$ZIkpHjq8Wdl29zBEwB474OXNLDb7rXN7TMBO7/WspX/3v65uVeiDO', NULL, 0, 'regular');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `deadline` date DEFAULT NULL,
  `collaboration_token` varchar(36) DEFAULT NULL,
  `public_token` varchar(36) DEFAULT NULL,
  `owner_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `name`, `deadline`, `collaboration_token`, `public_token`, `owner_id`) VALUES
(11, 'magnam', '2025-08-30', NULL, NULL, 6),
(12, 'praesentium', '2025-10-13', NULL, NULL, 6),
(13, 'sequi', '2025-11-25', NULL, NULL, 7),
(14, 'et', '2026-03-06', NULL, NULL, 7),
(15, 'tempore', '2026-01-23', NULL, NULL, 8),
(16, 'ea', '2025-04-30', NULL, NULL, 8),
(17, 'ipsum', '2025-09-24', NULL, NULL, 9),
(18, 'odit', '2025-06-01', NULL, NULL, 9),
(19, 'dolor', '2026-02-21', NULL, NULL, 10),
(20, 'qui', '2025-07-27', NULL, NULL, 10);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist_user`
--

CREATE TABLE `wishlist_user` (
  `wishlist_id` int NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `invitation`
--
ALTER TABLE `invitation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F11D61A2FB8E54CD` (`wishlist_id`),
  ADD KEY `IDX_F11D61A2F624B39D` (`sender_id`),
  ADD KEY `IDX_F11D61A2CD53EDB6` (`receiver_id`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1F1B251EFB8E54CD` (`wishlist_id`);

--
-- Indexes for table `proof`
--
ALTER TABLE `proof`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_FBF940DD6C755722` (`buyer_id`),
  ADD KEY `IDX_FBF940DD126F525E` (`item_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_9CE12A3124CB2F13` (`collaboration_token`),
  ADD UNIQUE KEY `UNIQ_9CE12A31AE981E3B` (`public_token`),
  ADD KEY `IDX_9CE12A317E3C61F9` (`owner_id`);

--
-- Indexes for table `wishlist_user`
--
ALTER TABLE `wishlist_user`
  ADD PRIMARY KEY (`wishlist_id`,`user_id`),
  ADD KEY `IDX_F6AC07BFFB8E54CD` (`wishlist_id`),
  ADD KEY `IDX_F6AC07BFA76ED395` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `invitation`
--
ALTER TABLE `invitation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `proof`
--
ALTER TABLE `proof`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invitation`
--
ALTER TABLE `invitation`
  ADD CONSTRAINT `FK_F11D61A2CD53EDB6` FOREIGN KEY (`receiver_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_F11D61A2F624B39D` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_F11D61A2FB8E54CD` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlist` (`id`);

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `FK_1F1B251EFB8E54CD` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlist` (`id`);

--
-- Constraints for table `proof`
--
ALTER TABLE `proof`
  ADD CONSTRAINT `FK_FBF940DD126F525E` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_FBF940DD6C755722` FOREIGN KEY (`buyer_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `FK_9CE12A317E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `wishlist_user`
--
ALTER TABLE `wishlist_user`
  ADD CONSTRAINT `FK_F6AC07BFA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_F6AC07BFFB8E54CD` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlist` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

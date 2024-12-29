-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : dim. 29 déc. 2024 à 20:10
-- Version du serveur : 8.0.30
-- Version de PHP : 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `colmarket`
--

-- --------------------------------------------------------

--
-- Structure de la table `contenu_panier`
--

CREATE TABLE `contenu_panier` (
  `id` int NOT NULL,
  `produit_id` int NOT NULL,
  `panier_id` int NOT NULL,
  `quantite` int NOT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `contenu_panier`
--

INSERT INTO `contenu_panier` (`id`, `produit_id`, `panier_id`, `quantite`, `date`) VALUES
(3, 1, 1, 1, NULL),
(4, 2, 1, 3, NULL),
(7, 2, 8, 9, NULL),
(8, 2, 15, 1, '2024-12-28 11:45:13'),
(9, 2, 17, 1, '2024-12-28 11:47:54'),
(11, 1, 19, 1, '2024-12-28 14:50:13'),
(12, 2, 19, 1, '2024-12-28 14:50:31'),
(13, 1, 20, 1, '2024-12-28 14:53:10'),
(14, 2, 21, 1, '2024-12-28 14:55:00'),
(22, 1, 39, 3, '2024-12-29 11:29:21'),
(23, 2, 39, 1, '2024-12-29 11:29:40'),
(24, 1, 26, 1, '2024-12-29 19:34:05');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20241226173312', '2024-12-26 17:36:23', 18),
('DoctrineMigrations\\Version20241226174113', '2024-12-26 17:41:21', 22),
('DoctrineMigrations\\Version20241226174411', '2024-12-26 17:44:16', 27),
('DoctrineMigrations\\Version20241227185043', '2024-12-27 18:50:54', 25),
('DoctrineMigrations\\Version20241227190022', '2024-12-27 19:00:26', 11),
('DoctrineMigrations\\Version20241228153118', '2024-12-28 15:32:55', 8);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL,
  `body` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `headers` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `queue_name` varchar(190) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `date_achat` datetime DEFAULT NULL,
  `etat` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `panier`
--

INSERT INTO `panier` (`id`, `user_id`, `date_achat`, `etat`) VALUES
(1, 1, '2024-12-27 19:35:33', 1),
(8, 2, '2024-12-28 11:24:04', 1),
(15, 2, '2024-12-28 11:45:20', 1),
(17, 2, '2024-12-28 11:47:57', 1),
(19, 2, '2024-12-28 14:50:34', 1),
(20, 2, '2024-12-28 14:53:45', 1),
(21, 2, '2024-12-28 14:55:02', 1),
(23, 1, '2024-12-28 23:28:34', 1),
(24, 1, '2024-12-28 23:33:23', 1),
(25, 1, '2024-12-28 23:34:14', 1),
(26, 1, NULL, 0),
(39, 7, '2024-12-29 18:21:21', 1),
(40, 7, NULL, 0),
(41, 2, NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE `produit` (
  `id` int NOT NULL,
  `nom` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `prix` double NOT NULL,
  `stock` int NOT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id`, `nom`, `description`, `prix`, `stock`, `photo`) VALUES
(1, 'Alcool 1', 'Description', 99, 15, NULL),
(2, 'Alcool 2', 'DESCRIPTION', 98, 10, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(180) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nom` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `prenom` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `nom`, `prenom`, `created_at`) VALUES
(1, 'paul2005832@gmail.com', '[\"ROLE_ADMIN\", \"ROLE_SUPER_ADMIN\"]', '$2y$13$T5gdrpmw2BTj/hX6k4L7QO/uLEX0MS8ynUZB9Zj6UR8SqidwSno8K', 'Bruder', 'Paul', NULL),
(2, 'dearwilliam832@gmail.com', '[]', '$2y$13$3fn479WP1UAkbHglID3sV.QdPQOEAulWEJv1/FFVaXCOLKUItpjty', 'Free', 'William', NULL),
(6, 'truc@gmail.com', '[]', '$2y$13$DN0MWQEVW1ODZAZ9GYt4re9GTUULN9KFLvIC4yP7OQ2.71GGURUW.', 'qdfg', 'qzfg', '2024-12-28 15:52:41'),
(7, 'pl.baillion@gmail.com', '[]', '$2y$13$x/Giw5JbqqH.TqgWmlJR1.b2Wv4tK3M8cSxUMrZoLS4RqZsfydxGe', 'Baillion', 'Paul', '2024-12-28 23:49:33'),
(8, 'machin@gmail.com', '[]', '$2y$13$CPPyWA9ZV08OoxteCWi13eFElHLiLWqrFlUJ26E0vRU0EvgTtwVHm', 'machin', 'truc', '2024-12-29 19:17:24');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `contenu_panier`
--
ALTER TABLE `contenu_panier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_80507DC0F347EFB` (`produit_id`),
  ADD KEY `IDX_80507DC0F77D927C` (`panier_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_24CC0DF2A76ED395` (`user_id`);

--
-- Index pour la table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `contenu_panier`
--
ALTER TABLE `contenu_panier`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `panier`
--
ALTER TABLE `panier`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT pour la table `produit`
--
ALTER TABLE `produit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `contenu_panier`
--
ALTER TABLE `contenu_panier`
  ADD CONSTRAINT `FK_80507DC0F347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`),
  ADD CONSTRAINT `FK_80507DC0F77D927C` FOREIGN KEY (`panier_id`) REFERENCES `panier` (`id`);

--
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `FK_24CC0DF2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

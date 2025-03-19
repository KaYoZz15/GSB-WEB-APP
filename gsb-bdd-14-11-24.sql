-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 14 nov. 2024 à 11:06
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


-- --------------------------------------------------------

--
-- Structure de la table `compte_rendu`
--

CREATE TABLE `compte_rendu` (
  `id` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `date_visite` date NOT NULL,
  `medecin` varchar(100) NOT NULL,
  `piece_jointe` varchar(255) DEFAULT NULL,
  `commentaires` text DEFAULT NULL,
  `echantillons_distribues` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `compte_rendu`
--

INSERT INTO `compte_rendu` (`id`, `id_utilisateur`, `date_visite`, `medecin`, `piece_jointe`, `commentaires`, `echantillons_distribues`) VALUES
(5, 2, '2024-11-06', 'Mr DOCR', NULL, 'Ceci est un CR.', '1,2'),
(6, 4, '2024-11-13', 'Mr Loca', NULL, 'Salut, je suis un visiteur d\'Auvergne-Rhône-Alpes.', '2'),
(7, 7, '2024-10-03', 'Mr Poli', NULL, 'Ceci est un compte rendu de moi meme.', '1'),
(8, 1, '2024-11-01', 'Mr Doc', 'uploads/Design sans titre (43).png', 'Ceci est un cr.', '2'),
(9, 7, '2024-11-14', 'Mr DOC', 'uploads/Design sans titre (43).png', 'test.', '2');

-- --------------------------------------------------------

--
-- Structure de la table `echantillons`
--

CREATE TABLE `echantillons` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `actif` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `echantillons`
--

INSERT INTO `echantillons` (`id`, `nom`, `description`, `actif`) VALUES
(1, 'Échantillons #1', 'Ceci est un test', 1),
(2, 'Échantillons #2', 'Ceci est un test 2', 1);

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `id_expediteur` int(11) NOT NULL,
  `id_recepteur` int(11) NOT NULL,
  `date_envoi` timestamp NOT NULL DEFAULT current_timestamp(),
  `contenu` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `praticiens`
--

CREATE TABLE `praticiens` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `specialite` varchar(255) NOT NULL,
  `region_id` int(11) NOT NULL,
  `actif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `praticiens`
--

INSERT INTO `praticiens` (`id`, `nom`, `specialite`, `region_id`, `actif`) VALUES
(1, 'Mr Doc', 'Docteur', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `regions`
--

CREATE TABLE `regions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `regions`
--

INSERT INTO `regions` (`id`, `name`) VALUES
(1, 'Auvergne-Rhône-Alpes'),
(2, 'Bourgogne-Franche-Comté'),
(3, 'Bretagne'),
(4, 'Centre-Val de Loire'),
(5, 'Corse'),
(6, 'Grand Est'),
(7, 'Hauts-de-France'),
(8, 'Île-de-France'),
(9, 'Normandie'),
(10, 'Nouvelle-Aquitaine'),
(11, 'Occitanie'),
(12, 'Pays de la Loire'),
(13, 'Provence-Alpes-Côte d\'Azur');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('visiteur','delegue','responsable','admin') NOT NULL DEFAULT 'visiteur',
  `region_id` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `pseudo`, `email`, `mot_de_passe`, `nom`, `prenom`, `date_inscription`, `role`, `region_id`, `status`) VALUES
(1, 'ek', 'echallierkevin@gmail.com', '$2y$10$TQgrTzISOue1WUT8RM5zKOdzXmMe/2e8IGiV5r509xHP0lcwch.52', 'ECHALLIER', 'Kevin', '2024-09-10 14:23:09', 'responsable', 2, 1),
(2, 'pm', 'philipponmaxence@gmail.com', '$2y$10$TQgrTzISOue1WUT8RM5zKOdzXmMe/2e8IGiV5r509xHP0lcwch.52', 'PHILIPPON', 'Maxence', '2024-09-10 14:23:09', 'visiteur', 2, 1),
(3, 'admin', 'admin@gmail.com', '$2y$10$TQgrTzISOue1WUT8RM5zKOdzXmMe/2e8IGiV5r509xHP0lcwch.52', 'Admin', '', '2024-09-10 14:23:09', 'admin', 2, 1),
(4, 'da', 'david@gmail.com', '$2y$10$TQgrTzISOue1WUT8RM5zKOdzXmMe/2e8IGiV5r509xHP0lcwch.52', 'Ammomo', 'David', '2024-09-10 14:23:09', 'visiteur', 1, 1),
(7, 'pc', 'p.clopet@gmail.com', '$2y$10$yB4Zu9V4QdnNcmOMlscrdOz1CRuogkb5lFJ0YZyNpUSuXwRaHiuyG', 'CLOPET', 'Perceval', '2024-11-13 11:05:24', 'responsable', 2, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `compte_rendu`
--
ALTER TABLE `compte_rendu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `echantillons`
--
ALTER TABLE `echantillons`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_expediteur` (`id_expediteur`),
  ADD KEY `id_recepteur` (`id_recepteur`);

--
-- Index pour la table `praticiens`
--
ALTER TABLE `praticiens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `region_id` (`region_id`);

--
-- Index pour la table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_region` (`region_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `compte_rendu`
--
ALTER TABLE `compte_rendu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `echantillons`
--
ALTER TABLE `echantillons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `praticiens`
--
ALTER TABLE `praticiens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `compte_rendu`
--
ALTER TABLE `compte_rendu`
  ADD CONSTRAINT `compte_rendu_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`id_expediteur`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`id_recepteur`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `praticiens`
--
ALTER TABLE `praticiens`
  ADD CONSTRAINT `praticiens_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`);

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `fk_region` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

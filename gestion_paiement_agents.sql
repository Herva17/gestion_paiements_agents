-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 21 mai 2026 à 21:54
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_paiement_agents`
--

-- --------------------------------------------------------

--
-- Structure de la table `affectation`
--

CREATE TABLE `affectation` (
  `id` int(11) NOT NULL,
  `lieu_affectation` varchar(100) DEFAULT NULL,
  `date_affectation` date DEFAULT NULL,
  `id_agent` int(11) NOT NULL,
  `id_service` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `affectation`
--

INSERT INTO `affectation` (`id`, `lieu_affectation`, `date_affectation`, `id_agent`, `id_service`) VALUES
(1, 'CS maman mapendo', '2026-05-14', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `agent`
--

CREATE TABLE `agent` (
  `id_agent` int(11) NOT NULL,
  `nom_complet` varchar(100) NOT NULL,
  `adresse` varchar(150) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `profil` varchar(50) DEFAULT NULL,
  `lieu_naissance` varchar(100) DEFAULT NULL,
  `fonction` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `agent`
--

INSERT INTO `agent` (`id_agent`, `nom_complet`, `adresse`, `date_naissance`, `telephone`, `profil`, `lieu_naissance`, `fonction`) VALUES
(1, 'IRAGI Hervé', 'Himbi 2', '0000-00-00', '0977309403', 'Standard', 'Bukavu', 'Enseignant');

-- --------------------------------------------------------

--
-- Structure de la table `paiement`
--

CREATE TABLE `paiement` (
  `id` int(11) NOT NULL,
  `reference` int(11) DEFAULT NULL,
  `montant` float DEFAULT NULL,
  `date_paiement` date DEFAULT NULL,
  `mode_paiement` varchar(50) DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `numeroPrest` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paiement`
--

INSERT INTO `paiement` (`id`, `reference`, `montant`, `date_paiement`, `mode_paiement`, `statut`, `numeroPrest`) VALUES
(1, 903, 200, '2026-05-14', 'Espèces', 'Payé', 1);

-- --------------------------------------------------------

--
-- Structure de la table `prestation`
--

CREATE TABLE `prestation` (
  `numeroPrest` int(11) NOT NULL,
  `libelle` varchar(100) DEFAULT NULL,
  `nbreHeure` int(11) DEFAULT NULL,
  `salaire_horaire` float DEFAULT NULL,
  `montant` float DEFAULT NULL,
  `date_prestation` date DEFAULT NULL,
  `id_affectation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `prestation`
--

INSERT INTO `prestation` (`numeroPrest`, `libelle`, `nbreHeure`, `salaire_horaire`, `montant`, `date_prestation`, `id_affectation`) VALUES
(1, 'mars', 90, 200, 18000, '2026-05-15', 1),
(2, 'mars', 90, 200, 18000, '2026-05-15', 1);

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `service`
--

INSERT INTO `service` (`id`, `designation`, `description`) VALUES
(1, 'Enseignant', 'service d\'enseignement');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `nom_utilisateur` varchar(50) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom_utilisateur`, `mot_de_passe`, `role`, `date_creation`) VALUES
(1, 'SALEH', '$2y$10$n6CJzPKEBo.MRPEs8ByBEegk.KLkSy531o7eIfLHkFPAHGfYpag92', 'administrateur', '2026-05-18 19:50:40'),
(2, 'CHANTAL', '$2y$10$hYLPzpC3BzZFIXbvV9kCLeIR7eeORAu6QuzWeiGkFwI.hjSiIQveW', 'caissier', '2026-05-18 19:51:49');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `affectation`
--
ALTER TABLE `affectation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_agent` (`id_agent`),
  ADD KEY `id_service` (`id_service`);

--
-- Index pour la table `agent`
--
ALTER TABLE `agent`
  ADD PRIMARY KEY (`id_agent`);

--
-- Index pour la table `paiement`
--
ALTER TABLE `paiement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `numeroPrest` (`numeroPrest`);

--
-- Index pour la table `prestation`
--
ALTER TABLE `prestation`
  ADD PRIMARY KEY (`numeroPrest`),
  ADD KEY `id_affectation` (`id_affectation`);

--
-- Index pour la table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `nom_utilisateur` (`nom_utilisateur`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `affectation`
--
ALTER TABLE `affectation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `agent`
--
ALTER TABLE `agent`
  MODIFY `id_agent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `paiement`
--
ALTER TABLE `paiement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `prestation`
--
ALTER TABLE `prestation`
  MODIFY `numeroPrest` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `affectation`
--
ALTER TABLE `affectation`
  ADD CONSTRAINT `affectation_ibfk_1` FOREIGN KEY (`id_agent`) REFERENCES `agent` (`id_agent`),
  ADD CONSTRAINT `affectation_ibfk_2` FOREIGN KEY (`id_service`) REFERENCES `service` (`id`);

--
-- Contraintes pour la table `paiement`
--
ALTER TABLE `paiement`
  ADD CONSTRAINT `paiement_ibfk_1` FOREIGN KEY (`numeroPrest`) REFERENCES `prestation` (`numeroPrest`);

--
-- Contraintes pour la table `prestation`
--
ALTER TABLE `prestation`
  ADD CONSTRAINT `prestation_ibfk_1` FOREIGN KEY (`id_affectation`) REFERENCES `affectation` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

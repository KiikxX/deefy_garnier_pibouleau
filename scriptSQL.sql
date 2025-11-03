-- Adminer 4.8.1 MySQL 5.5.5-10.3.11-MariaDB-1:10.3.11+maria~bionic dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

-- ========================================
-- 1. CRÉER LA TABLE User EN PREMIER
-- ========================================
DROP TABLE IF EXISTS `User`;
CREATE TABLE `User` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `email` varchar(256) NOT NULL,
                        `passwd` varchar(256) NOT NULL,
                        `role` int(11) NOT NULL DEFAULT 1,
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `User` (`id`, `email`, `passwd`, `role`) VALUES
                                                         (1, 'user1@mail.com', '$2y$12$e9DCiDKOGpVs9s.9u2ENEOiq7wGvx7sngyhPvKXo2mUbI3ulGWOdC', 1),
                                                         (2, 'user2@mail.com', '$2y$12$4EuAiwZCaMouBpquSVoiaOnQTQTconCP9rEev6DMiugDmqivxJ3AG', 1),
                                                         (3, 'user3@mail.com', '$2y$12$5dDqgRbmCN35XzhniJPJ1ejM5GIpBMzRizP730IDEHsSNAu24850S', 1),
                                                         (4, 'user4@mail.com', '$2y$12$ltC0A0zZkD87pZ8K0e6TYOJPJeN/GcTSkUbpqq0kBvx6XdpFqzzqq', 1),
                                                         (5, 'admin@mail.com', '$2y$12$JtV1W6MOy/kGILbNwGR2lOqBn8PAO3Z6MupGhXpmkeCXUPQ/wzD8a', 100);

-- ========================================
-- 2. CRÉER LA TABLE playlist
-- ========================================
DROP TABLE IF EXISTS `playlist`;
CREATE TABLE `playlist` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `nom` varchar(100) NOT NULL,
                            `user_id` int(11) NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `user_id` (`user_id`),
                            CONSTRAINT `playlist_ibfk_user` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `playlist` (`id`, `nom`, `user_id`) VALUES
                                                    (1, 'Best of rock', 1),
                                                    (2, 'Musique classique', 1),
                                                    (3, 'Best of country music', 2),
                                                    (4, 'Best of Elvis Presley', 3);

-- ========================================
-- 3. CRÉER LA TABLE track
-- ========================================
DROP TABLE IF EXISTS `track`;
CREATE TABLE `track` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `titre` varchar(100) NOT NULL,
                         `genre` varchar(30) DEFAULT NULL,
                         `duree` int(3) DEFAULT NULL,
                         `filename` varchar(255) DEFAULT NULL,
                         `type` varchar(30) DEFAULT NULL,
                         `artiste_album` varchar(30) DEFAULT NULL,
                         `titre_album` varchar(30) DEFAULT NULL,
                         `annee_album` int(4) DEFAULT NULL,
                         `numero_album` int(11) DEFAULT NULL,
                         `auteur_podcast` varchar(100) DEFAULT NULL,
                         `date_posdcast` date DEFAULT NULL,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `track` (`id`, `titre`, `genre`, `duree`, `filename`, `type`, `artiste_album`, `titre_album`, `annee_album`, `numero_album`, `auteur_podcast`, `date_posdcast`) VALUES
                                                                                                                                                                                (1, 'Wish You Were Here', 'rock', 334, 'audio/wish-you-were-here.mp3', 'album', 'Pink Floyd', 'Wish You Were Here', 1975, 1, NULL, NULL),
                                                                                                                                                                                (2, 'Samba Pati', 'rock', 271, 'audio/samba-pati.mp3', 'album', 'Santana', 'Abraxas', 1970, 3, NULL, NULL),
                                                                                                                                                                                (3, 'Danube Bleu', 'classique', 510, 'audio/danube-bleu.mp3', 'album', 'Johann Strauss', 'Valses', 1867, 1, NULL, NULL),
                                                                                                                                                                                (4, 'Lettre à Elise', 'classique', 210, 'audio/lettre-elise.mp3', 'album', 'Beethoven', 'Bagatelles', 1810, 1, NULL, NULL),
                                                                                                                                                                                (5, 'Annie song', 'country', 202, 'audio/annie-song.mp3', 'album', 'John Denver', 'Back Home Again', 1974, 1, NULL, NULL),
                                                                                                                                                                                (6, 'Tequila sunrise', 'country', 174, 'audio/tequila-sunrise.mp3', 'album', 'Eagles', 'Desperado', 1973, 1, NULL, NULL),
                                                                                                                                                                                (7, 'In the ghetto', 'country', 163, 'audio/in-the-ghetto.mp3', 'album', 'Elvis Presley', 'From Elvis in Memphis', 1969, 1, NULL, NULL),
                                                                                                                                                                                (8, 'La vie des papillons', 'podcast', 420, 'audio/papillons.mp3', 'podcast', NULL, NULL, NULL, NULL, 'Jean Dupont', '2023-01-15'),
                                                                                                                                                                                (9, 'La vie des libellules', 'podcast', 380, 'audio/libellules.mp3', 'podcast', NULL, NULL, NULL, NULL, 'Marie Martin', '2023-02-20');

-- ========================================
-- 4. CRÉER LA TABLE playlist2track
-- ========================================
DROP TABLE IF EXISTS `playlist2track`;
CREATE TABLE `playlist2track` (
                                  `id_pl` int(11) NOT NULL,
                                  `id_track` int(11) NOT NULL,
                                  `no_piste_dans_liste` int(3) NOT NULL,
                                  PRIMARY KEY (`id_pl`,`id_track`),
                                  KEY `id_track` (`id_track`),
                                  CONSTRAINT `playlist2track_ibfk_1` FOREIGN KEY (`id_pl`) REFERENCES `playlist` (`id`) ON DELETE CASCADE,
                                  CONSTRAINT `playlist2track_ibfk_2` FOREIGN KEY (`id_track`) REFERENCES `track` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `playlist2track` (`id_pl`, `id_track`, `no_piste_dans_liste`) VALUES
                                                                              (1, 1, 1),
                                                                              (1, 2, 2),
                                                                              (2, 3, 1),
                                                                              (2, 4, 2),
                                                                              (3, 5, 1),
                                                                              (3, 6, 2),
                                                                              (4, 7, 1),
                                                                              (4, 8, 2);

-- ========================================
-- 5. CRÉER LA TABLE user2playlist
-- ========================================
DROP TABLE IF EXISTS `user2playlist`;
CREATE TABLE `user2playlist` (
                                 `id_user` int(11) NOT NULL,
                                 `id_pl` int(11) NOT NULL,
                                 PRIMARY KEY (`id_user`,`id_pl`),
                                 KEY `id_pl` (`id_pl`),
                                 CONSTRAINT `user2playlist_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `User` (`id`) ON DELETE CASCADE,
                                 CONSTRAINT `user2playlist_ibfk_2` FOREIGN KEY (`id_pl`) REFERENCES `playlist` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user2playlist` (`id_user`, `id_pl`) VALUES
                                                     (1, 1),
                                                     (1, 2),
                                                     (2, 3),
                                                     (3, 4);

-- ========================================
-- Réactiver les vérifications
-- ========================================
SET foreign_key_checks = 1;

-- 2025-11-03 10:39:01
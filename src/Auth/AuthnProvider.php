<?php

namespace IUT\Deefy\Auth;

use PDO;
use Exception;

class AuthnProvider
{
    public static function signin(string $email, string $password): array
    {
        // Lecture du fichier de configuration
        $configFile = __DIR__ . '/../../config/deefy.db.ini';
        if (!file_exists($configFile)) {
            throw new AuthnException("Fichier de configuration introuvable");
        }

        $config = parse_ini_file($configFile);
        if ($config === false) {
            throw new AuthnException("Impossible de lire la configuration");
        }

        try{
            // Connexion à la base de données
            $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']}";
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            // Recherche de l'utilisateur
            $stmt = $pdo->prepare("SELECT * FROM User WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new AuthnException("Identifiants incorrects");
            }

            // Vérification du mot de passe
            if (!password_verify($password, $user['passwd'])) {
                throw new AuthnException("Identifiants incorrects");
            }

            // Retourne les données utilisateur (sans le mot de passe)
            return [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
        } catch (AuthnException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new AuthnException("Erreur d'authentification: " . $e->getMessage());
        }
    }

    public static function register(string $email, string $password): bool
    {
        // Vérification de la longueur du mot de passe
        if (strlen($password) < 10) {
            throw new AuthnException("Le mot de passe doit contenir au moins 10 caractères");
        }

        // Lecture du fichier de configuration
        $configFile = __DIR__ . '/../../config/deefy.db.ini';
        if (!file_exists($configFile)) {
            throw new AuthnException("Fichier de configuration introuvable");
        }

        $config = parse_ini_file($configFile);
        if ($config === false) {
            throw new AuthnException("Impossible de lire la configuration");
        }

        try {
            // Connexion à la base de données
            $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['database']}";
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            // Vérification que l'email n'existe pas déjà
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM User WHERE email = ?");
            $stmt->execute([$email]);
            $result = $stmt->fetch();
            $count = (int)$result['count'];

            if ($count > 0) {
                throw new AuthnException("Un utilisateur avec cet email existe déjà");
            }

            // Hashage du mot de passe avec bcrypt
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

            // Insertion du nouvel utilisateur avec rôle 1
            $stmt = $pdo->prepare("INSERT INTO User (email, passwd, role) VALUES (?, ?, 1)");
            $stmt->execute([$email, $hashedPassword]);

            return true;
        } catch (AuthnException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new AuthnException("Erreur lors de l'inscription: " . $e->getMessage());
        }
    }
}
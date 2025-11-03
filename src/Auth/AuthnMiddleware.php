<?php
namespace IUT\Deefy\Auth;

class AuthnMiddleware
{
    /**
     * Vérifie si un utilisateur est connecté
     */
    public static function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user']);
    }

    /**
     * Redirige vers la page de connexion si non authentifié
     */
    public static function requireAuth(): void
    {
        if (!self::isAuthenticated()) {
            header('Location: index.php?action=signin');
            exit();
        }
    }

    /**
     * Vérifie si l'utilisateur est propriétaire d'une ressource
     */
    public static function isOwner(int $ownerId): bool
    {
        if (!self::isAuthenticated()) {
            return false;
        }
        return $_SESSION['user']['id'] === $ownerId;
    }

    /**
     * Récupère l'utilisateur connecté
     */
    public static function getCurrentUser(): ?array
    {
        if (!self::isAuthenticated()) {
            return null;
        }
        return $_SESSION['user'];
    }
}
<?php

namespace IUT\Deefy\Auth;

use IUT\Deefy\Repository\DeefyRepository;

class Authz
{
    /**
     * Vérifie que l'utilisateur connecté a le rôle attendu
     * @param int $expectedRole Le rôle attendu
     * @return bool
     * @throws AuthnException Si aucun utilisateur n'est connecté
     */
    public static function checkRole(int $expectedRole): bool
    {
        $user = AuthnProvider::getSignedInUser();
        return $user['role'] === $expectedRole;
    }

    /**
     * Vérifie que l'utilisateur est propriétaire de la playlist ou administrateur
     * @param int $playlistId L'ID de la playlist
     * @return bool
     * @throws AuthnException Si aucun utilisateur n'est connecté
     */
    public static function checkPlaylistOwner(int $playlistId): bool
    {
        $user = AuthnProvider::getSignedInUser();

        // Si l'utilisateur est admin (role = 100), il a accès à tout
        if ($user['role'] === 100) {
            return true;
        }

        // Sinon, vérifier qu'il est propriétaire de la playlist
        $repo = DeefyRepository::getInstance();
        $playlistData = $repo->getPlaylistWithTracks($playlistId);

        return $playlistData['user_id'] === $user['id'];
    }
}
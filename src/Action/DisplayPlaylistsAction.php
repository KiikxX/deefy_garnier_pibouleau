<?php
namespace IUT\Deefy\Action;

use IUT\Deefy\Repository\DeefyRepository;
use IUT\Deefy\Auth\AuthnException;
use IUT\Deefy\Auth\AuthnMiddleware;

class DisplayPlaylistsAction extends Action
{
    public function execute(): string
    {
        // Vérifier l'authentification
        if (!AuthnMiddleware::isAuthenticated()) {
            return '<p class="error">Vous devez être connecté.</p>
                <p><a href="index.php?action=signin">Se connecter</a></p>';
        }

        $userId = $_SESSION['user']['id'];
        $repo = DeefyRepository::getInstance();

        try {
            // Récupérer les playlists de l'utilisateur connecté
            $playlists = $repo->getPlaylistsByUserId($userId);

            if (empty($playlists)) {
                return '<h2>Mes Playlists</h2>
                        <p>Vous n\'avez pas encore de playlist.</p>
                        <p><a href="index.php?action=add-playlist">Créer une playlist</a></p>';
            }

            // Générer le HTML pour afficher les playlists
            $html = '<h2>Mes Playlists</h2><ul class="playlist-list">';
            foreach ($playlists as $playlistData) {
                $id = htmlspecialchars($playlistData['id']);
                $nom = htmlspecialchars($playlistData['playlist']->getName());

                // Rendre chaque playlist cliquable
                $html .= '<li>';
                $html .= '<a href="index.php?action=display-playlist&id=' . $id . '">' . $nom . '</a>';
                $html .= '</li>';
            }
            $html .= '</ul>';
            $html .= '<p><a href="index.php?action=add-playlist">Créer une nouvelle playlist</a></p>';

            return $html;

        } catch (\Exception $e) {
            return '<p class="error">Erreur lors du chargement des playlists : '
                . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
}
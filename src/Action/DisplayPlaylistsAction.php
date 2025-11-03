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
        $userRole = $_SESSION['user']['role'];
        $repo = DeefyRepository::getInstance();

        try {
            // Si l'utilisateur est admin (role = 100), récupérer TOUTES les playlists
            if ($userRole === 100) {
                $playlists = $repo->getPlaylists();
                $titre = '<h2>Toutes les Playlists (Admin)</h2>';
            } else {
                // Sinon, récupérer uniquement les playlists de l'utilisateur
                $playlists = $repo->getPlaylistsByUserId($userId);
                $titre = '<h2>Mes Playlists</h2>';
            }

            if (empty($playlists)) {
                return $titre . '
                        <p>Aucune playlist trouvée.</p>
                        <p><a href="index.php?action=add-playlist">Créer une playlist</a></p>';
            }

            // Générer le HTML pour afficher les playlists
            $html = $titre . '<ul class="playlist-list">';
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
<?php
namespace IUT\Deefy\Action;

use IUT\Deefy\Repository\DeefyRepository;
use IUT\Deefy\Auth\AuthnMiddleware;

class DisplayPlaylistAction extends Action
{
    public function execute(): string
    {
        // Vérifier l'authentification
        if (!AuthnMiddleware::isAuthenticated()) {
            return '<p class="error">Vous devez être connecté.</p>
                <p><a href="index.php?action=signin">Se connecter</a></p>';
        }

        // Récupérer l'ID de la playlist depuis l'URL ou la session
        $playlistId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$playlistId && isset($_SESSION['current_playlist_id'])) {
            // Si pas d'ID dans l'URL, utiliser celui en session (playlist courante)
            $playlistId = $_SESSION['current_playlist_id'];
        }

        if (!$playlistId) {
            return '<p class="error">Aucune playlist sélectionnée.</p>
                    <p><a href="index.php?action=my-playlists">Retour à mes playlists</a></p>';
        }

        $repo = DeefyRepository::getInstance();

        try {
            // Récupérer les détails de la playlist
            $playlistData = $repo->getPlaylistWithTracks($playlistId);

            // Vérifier que l'utilisateur est le propriétaire
            if ($playlistData['user_id'] !== $_SESSION['user']['id']) {
                return '<p class="error">Vous n\'êtes pas autorisé à voir cette playlist.</p>';
            }

            // Stocker la playlist comme "playlist courante" en session
            $_SESSION['current_playlist_id'] = $playlistId;

            $nom = htmlspecialchars($playlistData['nom']);
            $tracks = $playlistData['tracks'];

            $html = "<h2>Playlist : $nom</h2>";

            if (empty($tracks)) {
                $html .= '<p>Cette playlist est vide.</p>';
            } else {
                $html .= '<ul class="track-list">';
                foreach ($tracks as $track) {
                    $titre = htmlspecialchars($track['titre']);
                    $duree = gmdate("i:s", $track['duree']);
                    $type = htmlspecialchars($track['type']);

                    $html .= "<li>$titre ($duree) - Type: $type</li>";
                }
                $html .= '</ul>';
            }

            $html .= '<p><a href="index.php?action=add-track">Ajouter une piste à cette playlist</a></p>';
            $html .= '<p><a href="index.php?action=my-playlists">Retour à mes playlists</a></p>';

            return $html;

        } catch (\Exception $e) {
            return '<p class="error">Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
}
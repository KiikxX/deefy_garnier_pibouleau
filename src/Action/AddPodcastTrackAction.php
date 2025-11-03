<?php
namespace IUT\Deefy\Action;

use IUT\Deefy\Repository\DeefyRepository;
use Exception;
use IUT\Deefy\Auth\AuthnMiddleware;

class AddPodcastTrackAction extends Action
{
    protected function executeGet(): string
    {
        // Vérifier l'authentification
        if (!AuthnMiddleware::isAuthenticated()) {
            return '<p class="error">Vous devez être connecté.</p>
                <p><a href="index.php?action=signin">Se connecter</a></p>';
        }

        // Vérifier qu'il y a une playlist courante
        if (!isset($_SESSION['current_playlist_id'])) {
            return '<p class="error">Aucune playlist courante sélectionnée.</p>
                <p><a href="index.php?action=my-playlists">Sélectionner une playlist</a></p>';
        }

        $playlistId = $_SESSION['current_playlist_id'];

        return <<<HTML
    <h2>Ajouter une piste à la playlist courante</h2>
    <form method="post" action="index.php?action=add-track" enctype="multipart/form-data">
        <input type="hidden" name="playlist_id" value="$playlistId">
        
        <div>
            <label for="titre">Titre :</label>
            <input type="text" id="titre" name="titre" required>
        </div>
        
        <div>
            <label for="duree">Durée (en secondes) :</label>
            <input type="number" id="duree" name="duree" min="0" value="0">
        </div>
        
        <div>
            <label for="type">Type :</label>
            <select id="type" name="type" required>
                <option value="album">Album Track</option>
                <option value="podcast">Podcast</option>
            </select>
        </div>
        
        <div id="album-fields">
            <label for="artiste">Artiste :</label>
            <input type="text" id="artiste" name="artiste">
        </div>
        
        <div id="podcast-fields" style="display:none;">
            <label for="auteur">Auteur :</label>
            <input type="text" id="auteur" name="auteur">
        </div>
        
        <div>
            <label for="audio_file">Fichier MP3 :</label>
            <input type="file" id="audio_file" name="audio_file" accept=".mp3,audio/mpeg" required>
            <small>Format accepté : MP3 uniquement (max 10 MB)</small>
        </div>
        
        <button type="submit">Ajouter la piste</button>
    </form>
    
    <script>
        document.getElementById('type').addEventListener('change', function() {
            if (this.value === 'podcast') {
                document.getElementById('album-fields').style.display = 'none';
                document.getElementById('podcast-fields').style.display = 'block';
            } else {
                document.getElementById('album-fields').style.display = 'block';
                document.getElementById('podcast-fields').style.display = 'none';
            }
        });
    </script>
    
    <p><a href="index.php?action=display-playlist">Retour à la playlist</a></p>
    HTML;
    }

    protected function executePost(): string
    {
        // Vérifier l'authentification
        if (!AuthnMiddleware::isAuthenticated()) {
            return '<p class="error">Vous devez être connecté.</p>
                <p><a href="index.php?action=signin">Se connecter</a></p>';
        }

        if (!isset($_SESSION['current_playlist_id'])) {
            return '<p class="error">Aucune playlist courante.</p>';
        }

        $playlistId = $_SESSION['current_playlist_id'];
        $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_SPECIAL_CHARS);
        $duree = filter_input(INPUT_POST, 'duree', FILTER_VALIDATE_INT);
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_SPECIAL_CHARS);

        // Validation des données
        if (!$titre || $duree === false || !in_array($type, ['album', 'podcast'])) {
            return '<p class="error">Données invalides.</p>' . $this->executeGet();
        }

        // Validation du fichier audio
        if (!isset($_FILES['audio_file']) || $_FILES['audio_file']['error'] === UPLOAD_ERR_NO_FILE) {
            return '<p class="error">Vous devez uploader un fichier MP3.</p>' . $this->executeGet();
        }

        try {
            // Valider le fichier audio
            \IUT\Deefy\Audio\AudioFileValidator::validate($_FILES['audio_file']);

            // Sauvegarder le fichier
            $audioFilePath = \IUT\Deefy\Audio\AudioFileValidator::save($_FILES['audio_file']);

            $repo = DeefyRepository::getInstance();

            // Vérifier que l'utilisateur est propriétaire de la playlist
            $playlistData = $repo->getPlaylistWithTracks($playlistId);
            if ($playlistData['user_id'] !== $_SESSION['user']['id']) {
                // Supprimer le fichier uploadé si l'utilisateur n'est pas autorisé
                if (file_exists($audioFilePath)) {
                    unlink($audioFilePath);
                }
                return '<p class="error">Vous n\'êtes pas autorisé à modifier cette playlist.</p>';
            }

            // Créer la piste selon le type
            if ($type === 'album') {
                $artiste = filter_input(INPUT_POST, 'artiste', FILTER_SANITIZE_SPECIAL_CHARS);
                $track = new \IUT\Deefy\Entity\AlbumTrack($titre, $artiste, $audioFilePath, $duree);
            } else {
                $auteur = filter_input(INPUT_POST, 'auteur', FILTER_SANITIZE_SPECIAL_CHARS);
                $track = new \IUT\Deefy\Entity\PodcastTrack($titre, $auteur, $duree);
                $track->filename = $audioFilePath;
            }

            // Sauvegarder la piste avec le chemin du fichier
            $trackId = $repo->sauvegarderPiste($track);

            // Ajouter la piste à la playlist
            $repo->ajouterPisteAPlaylist($playlistId, $trackId);

            return '<p class="success">Piste ajoutée avec succès !</p>
                <p><a href="index.php?action=display-playlist">Voir la playlist</a></p>
                <p><a href="index.php?action=add-track">Ajouter une autre piste</a></p>';

        } catch (\IUT\Deefy\Audio\AudioFileException $e) {
            // Supprimer le fichier si la validation a échoué après upload
            if (isset($audioFilePath) && file_exists($audioFilePath)) {
                unlink($audioFilePath);
            }
            return '<p class="error">Erreur fichier audio : ' . htmlspecialchars($e->getMessage()) . '</p>' . $this->executeGet();
        } catch (\Exception $e) {
            // Supprimer le fichier en cas d'erreur
            if (isset($audioFilePath) && file_exists($audioFilePath)) {
                unlink($audioFilePath);
            }
            return '<p class="error">Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
}
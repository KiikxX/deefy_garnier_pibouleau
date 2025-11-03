<?php
namespace IUT\Deefy\Action;

use IUT\Deefy\Entity\PodcastTrack;
use IUT\Deefy\Render\AudioListRenderer;
use IUT\Deefy\Render\RenderInterface;
use IUT\Deefy\Repository\DeefyRepository; 
use Exception;
use IUT\Deefy\Auth\AuthnMiddleware;

class AddPodcastTrackAction extends Action
{
    public function execute(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_GET['added']) && isset($_SESSION['playlist'])) {
            return $this->renderConfirmation();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $trackTitle = filter_input(INPUT_POST, 'track_title', FILTER_SANITIZE_SPECIAL_CHARS);
            $trackAuthor = filter_input(INPUT_POST, 'track_author', FILTER_SANITIZE_SPECIAL_CHARS);
            if (empty($trackTitle) || empty($trackAuthor)) {
                return "<p>Données manquantes.</p>
                        <p><a href='index.php?action=add-track'>Réessayer</a></p>";
            }
            if (!isset($_SESSION['playlist']) || !isset($_SESSION['playlist_id'])) {
                return "<p>Aucune playlist disponible. <a href='index.php?action=add-playlist'>Créez une playlist d'abord</a>.</p>";
            }
            try {
                $track = new PodcastTrack($trackTitle, $trackAuthor);
                $repo = DeefyRepository::getInstance();
                $trackId = $repo->sauvegarderPiste($track);
                $repo->addPistePlaylist($trackId, $_SESSION['playlist_id']);
                $_SESSION['playlist']->addTrack($track);
                header('Location: index.php?action=add-track&added=1');
                exit;
            } catch (Exception $e) {
                return "<p>Erreur lors de l'ajout : " . $e->getMessage() . "</p>";
            }
        }
        else {
            return $this->renderForm();
        }
    }

    private function renderForm(): string
    {
        return <<<HTML
        <h2>Ajouter une piste à la playlist</h2>
        <form method="POST" action="index.php?action=add-track">
            <label for="track_title">Titre de la piste :</label>
            <input type="text" id="track_title" name="track_title" required><br><br>

            <label for="track_author">Auteur :</label>
            <input type="text" id="track_author" name="track_author" required><br><br>

            <button type="submit">Ajouter la piste</button>
        </form>
        HTML;
    }

    
    private function renderConfirmation(): string
    {
        $renderer = new AudioListRenderer($_SESSION['playlist']);
        $playlistHtml = $renderer->render(RenderInterface::LONG);
        return "
            <h2>Piste ajoutée avec succès !</h2>
            <div class='playlist'>$playlistHtml</div>
            <p><a href='index.php?action=add-track'>Ajouter une autre piste</a></p>
        ";
    }

    protected function executeGet(): string
    {
        // Vérifier qu'il y a une playlist courante
        if (!isset($_SESSION['current_playlist_id'])) {
            return '<p class="error">Aucune playlist courante sélectionnée.</p>
                <p><a href="index.php?action=my-playlists">Sélectionner une playlist</a></p>';
        }

        $playlistId = $_SESSION['current_playlist_id'];

        return <<<HTML
    <h2>Ajouter une piste à la playlist courante</h2>
    <form method="post" action="index.php?action=add-track">
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
        // Vérifier l'authentification et la playlist
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

        if (!$titre || $duree === false || !in_array($type, ['album', 'podcast'])) {
            return '<p class="error">Données invalides.</p>' . $this->executeGet();
        }

        $repo = DeefyRepository::getInstance();

        try {
            // Vérifier que l'utilisateur est propriétaire de la playlist
            $playlistData = $repo->getPlaylistWithTracks($playlistId);
            if ($playlistData['user_id'] !== $_SESSION['user']['id']) {
                return '<p class="error">Vous n\'êtes pas autorisé à modifier cette playlist.</p>';
            }

            // Créer la piste selon le type
            if ($type === 'album') {
                $artiste = filter_input(INPUT_POST, 'artiste', FILTER_SANITIZE_SPECIAL_CHARS);
                $track = new \IUT\Deefy\Entity\AlbumTrack($titre, $artiste, '', $duree);
            } else {
                $auteur = filter_input(INPUT_POST, 'auteur', FILTER_SANITIZE_SPECIAL_CHARS);
                $track = new \IUT\Deefy\Entity\PodcastTrack($titre, $auteur, $duree);
            }

            // Sauvegarder la piste
            $trackId = $repo->sauvegarderPiste($track);

            // Ajouter la piste à la playlist
            $repo->ajouterPisteAPlaylist($playlistId, $trackId);

            return '<p class="success">Piste ajoutée avec succès !</p>
                <p><a href="index.php?action=display-playlist">Voir la playlist</a></p>
                <p><a href="index.php?action=add-track">Ajouter une autre piste</a></p>';

        } catch (\Exception $e) {
            return '<p class="error">Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
}
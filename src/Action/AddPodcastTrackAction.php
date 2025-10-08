<?php
namespace IUT\Deefy\Action;

use IUT\Deefy\Entity\PodcastTrack;
use IUT\Deefy\Render\AudioListRenderer;
use IUT\Deefy\Render\RenderInterface;

class AddPodcastTrackAction extends Action
{
    public function execute(): string
    {
        // Démarrer la session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et filtrer les données du formulaire
            $trackTitle = filter_input(INPUT_POST, 'track_title', FILTER_SANITIZE_SPECIAL_CHARS);
            $trackAuthor = filter_input(INPUT_POST, 'track_author', FILTER_SANITIZE_SPECIAL_CHARS);

            // Vérifier que les champs ne sont pas vides
            if (empty($trackTitle) || empty($trackAuthor)) {
                return "<p>Données manquantes.</p>
                        <p><a href='index.php?action=add-track'>Réessayer</a></p>";
            }

            // Créer la piste
            $track = new PodcastTrack($trackTitle, $trackAuthor);

            // Récupérer la playlist depuis la session
            if (!isset($_SESSION['playlist'])) {
                return "<p>Aucune playlist disponible. <a href='index.php?action=add-playlist'>Créez une playlist d'abord</a>.</p>";
            }

            // Ajouter la piste à la playlist
            $_SESSION['playlist']->addTrack($track);

            // Afficher la playlist avec la nouvelle piste
            $renderer = new AudioListRenderer($_SESSION['playlist']);
            $playlistHtml = $renderer->render(RenderInterface::LONG);

            return "
                <h2>Piste ajoutée avec succès !</h2>
                <div class='playlist'>$playlistHtml</div>
                <p><a href='index.php?action=add-track'>Ajouter une autre piste</a></p>
            ";
        }
        // Si on affiche le formulaire
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
}

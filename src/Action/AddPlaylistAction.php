<?php
namespace IUT\Deefy\Action;

use IUT\Deefy\Entity\Playlist;

class AddPlaylistAction extends Action
{
    public function execute(): string
    {
        // Démarrer la session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et filtrer le nom de la playlist
            $playlistName = filter_input(INPUT_POST, 'playlist_name', FILTER_SANITIZE_SPECIAL_CHARS);

            // Vérifier que le nom n'est pas vide
            if (empty($playlistName)) {
                return "<p>Le nom de la playlist est obligatoire.</p>
                        <p><a href='index.php?action=add-playlist'>Réessayer</a></p>";
            }

            // Créer une nouvelle playlist et la stocker en session
            $_SESSION['playlist'] = new Playlist($playlistName);

            // Afficher un message de confirmation
            return $this->renderConfirmation();
        }
        // Si on affiche le formulaire
        else {
            return $this->renderForm();
        }
    }

    private function renderForm(): string
    {
        return <<<HTML
        <h2>Créer une playlist</h2>
        <form method="POST" action="index.php?action=add-playlist">
            <label for="playlist_name">Nom de la playlist :</label>
            <input type="text" id="playlist_name" name="playlist_name" required>
            <button type="submit">Créer</button>
        </form>
        HTML;
    }

    private function renderConfirmation(): string
    {
        $playlistName = htmlspecialchars($_SESSION['playlist']->getName(), ENT_QUOTES, 'UTF-8');
        return <<<HTML
        <h2>Playlist créée : $playlistName</h2>
        <p><a href="index.php?action=add-track">Ajouter une piste</a></p>
        HTML;
    }
}

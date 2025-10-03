<?php
namespace IUT\Deefy;

class AddPodcastTrackAction extends Action
{
    public function execute(): string
    {
        if (isset($_GET['submit'])) {
            $trackTitle = $_GET['track_title'] ?? '';
            $trackArtist = $_GET['track_artist'] ?? '';
            $safeTrackTitle = htmlspecialchars($trackTitle, ENT_QUOTES, 'UTF-8');
            $safeTrackArtist = htmlspecialchars($trackArtist, ENT_QUOTES, 'UTF-8');
            return "<p>Le podcast '$safeTrackTitle' de '$safeTrackArtist' a été créé avec succès !</p>";
        }
        else {
            return '
                <h2>Ajouter un podcast à la playlist</h2>
                <form method="GET">
                    <input type="hidden" name="action" value="add-track">
                    <label for="track_title">Titre du podcast :</label>
                    <input type="text" id="track_title" name="track_title" required><br><br>
                    <label for="track_artist">Artiste :</label>
                    <input type="text" id="track_artist" name="track_artist"><br><br>
                    <button type="submit" name="submit">Créer le podcast</button>
                </form>
            ';
        }
    }
}
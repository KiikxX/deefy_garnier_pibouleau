<?php
namespace IUT\Deefy\Action;

use IUT\Deefy\Entity\PodcastTrack;
use IUT\Deefy\Render\PodcastTrackRenderer;
use IUT\Deefy\Render\RenderInterface;

class AddPodcastTrackAction extends Action
{
    public function execute(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Si le formulaire est soumis
    if (isset($_GET['track_title'])) {
        $trackTitle = htmlspecialchars($_GET['track_title'], ENT_QUOTES, 'UTF-8');
        $trackAuthor = htmlspecialchars($_GET['track_author'], ENT_QUOTES, 'UTF-8');

        // Créer la piste et l'ajouter à la session
        $track = new PodcastTrack($trackTitle, $trackAuthor);
        $_SESSION['tracks'][] = $track;

        // Rediriger pour éviter les doublons
        header("Location: index.php?action=add-track");
        exit();
    }

    
    $formHtml = "
        <h2>Ajouter une playlist</h2>
        <form method='GET' action='index.php'>
            <input type='hidden' name='action' value='add-track'>
            <label for='track_title'>Titre du podcast :</label>
            <input type='text' id='track_title' name='track_title' required><br><br>
            <label for='track_author'>Auteur :</label>
            <input type='text' id='track_author' name='track_author' required><br><br>
            <button type='submit'>Ajouter</button>
        </form>
    ";

    
    $tracksHtml = "";
    if (isset($_SESSION['tracks']) && !empty($_SESSION['tracks'])) {
        foreach ($_SESSION['tracks'] as $track) {
            $renderer = new PodcastTrackRenderer($track);
            $tracksHtml .= "<div class='track'>{$renderer->render(RenderInterface::LONG)}</div>";
        }
    }

    return $formHtml . $tracksHtml;
}

}

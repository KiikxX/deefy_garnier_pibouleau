<?php
namespace IUT\Deefy\Action;

use IUT\Deefy\Entity\Playlist;
use IUT\Deefy\Render\AudioListRenderer;
use IUT\Deefy\Render\RenderInterface;

class AddPlaylistAction extends Action
{
    public function execute(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    
    if (isset($_GET['playlist_name'])) {
        $playlistName = htmlspecialchars($_GET['playlist_name'], ENT_QUOTES, 'UTF-8');

        
        $playlist = new Playlist($playlistName);
        $_SESSION['playlists'][] = $playlist;

        
        header("Location: index.php?action=add-playlist");
        exit();
    }

    
    $formHtml = "
        <h2>Ajouter une playlist</h2>
        <form method='GET' action='index.php'>
            <input type='hidden' name='action' value='add-playlist'>
            <label for='playlist_name'>Nom de la playlist :</label>
            <input type='text' id='playlist_name' name='playlist_name' required>
            <button type='submit'>Ajouter</button>
        </form>
    ";

    
    $playlistsHtml = "";
    if (isset($_SESSION['playlists']) && !empty($_SESSION['playlists'])) {
        foreach ($_SESSION['playlists'] as $playlist) {
            $renderer = new AudioListRenderer($playlist);
            $playlistsHtml .= "<div class='playlist'>{$renderer->render(RenderInterface::LONG)}</div>";
        }
    }

    return $formHtml . $playlistsHtml;
}

}

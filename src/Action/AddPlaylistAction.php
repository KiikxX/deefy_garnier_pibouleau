<?php
namespace IUT\Deefy\Action;

class AddPlaylistAction extends Action
{
    public function execute(): string
    {
        if ($this->http_method === 'POST') {
            $playlistName = $_POST['playlist_name'] ?? '';
            return "<p>Playlist '$playlistName' ajoutée !</p>";
        } else {
            return '
                <form method="POST">
                    <input type="text" name="playlist_name" placeholder="Nom de la playlist" required>
                    <button type="submit">Ajouter</button>
                </form>
            ';
        }
    }
}

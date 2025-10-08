<?php
namespace IUT\Deefy\Action;

class DefaultAction extends Action
{
    public function execute(): string
    {
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        
        unset($_SESSION['tracks']);
        unset($_SESSION['playlists']);

        return "
            <h1>Bienvenue sur Deefy</h1>
            <p>Utilisez les liens suivants pour naviguer :</p>
            <ul>
                <li><a href='index.php?action=add-track'>Ajouter une piste</a></li>
                <li><a href='index.php?action=add-playlist'>Ajouter une playlist</a></li>
            </ul>
        ";
    }
}

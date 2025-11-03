<?php
namespace IUT\Deefy\Action;

class DefaultAction extends Action
{
    public function execute(): string
    {
        $isAuthenticated = isset($_SESSION['user']);

        // Message de déconnexion réussie
        $logoutMessage = '';
        if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
            $logoutMessage = '<p style="color: green; padding: 10px; border: 1px solid green; border-radius: 5px;">✅ Vous avez été déconnecté avec succès.</p>';
        }

        if ($isAuthenticated) {
            $userEmail = htmlspecialchars($_SESSION['user']['email']);

            $html = <<<HTML
            <h1>Bienvenue sur Deefy</h1>
            <p>Vous êtes connecté en tant que : <strong>$userEmail</strong></p>
            
            <div class="menu">
                <h2>Que voulez-vous faire ?</h2>
                <ul>
                    <li><a href="index.php?action=my-playlists">Voir mes playlists</a></li>
                    <li><a href="index.php?action=add-playlist">Créer une nouvelle playlist</a></li>
            HTML;

            if (isset($_SESSION['current_playlist_id'])) {
                $html .= <<<HTML
                    <li><a href="index.php?action=display-playlist">Voir ma playlist courante</a></li>
                    <li><a href="index.php?action=add-track">Ajouter une piste à ma playlist courante</a></li>
                HTML;
            }

            $html .= <<<HTML
                    <li><a href="index.php?action=logout">Déconnexion</a></li>
                </ul>
            </div>
            HTML;

        } else {
            $html = <<<HTML
            <h1>Bienvenue sur Deefy</h1>
            $logoutMessage
            
            <p>Deefy est votre gestionnaire de playlists musicales personnel.</p>
            
            <div class="menu">
                <h2>Commencez dès maintenant !</h2>
                <ul>
                    <li><a href="index.php?action=signin">Se connecter</a></li>
                    <li><a href="index.php?action=add-user">S'inscrire</a></li>
                </ul>
            </div>
            
            <div class="info">
                <h3>Fonctionnalités :</h3>
                <ul>
                    <li>Créer et gérer vos playlists</li>
                    <li>Ajouter des pistes audio (albums et podcasts)</li>
                    <li>Organiser votre musique</li>
                    <li>Accès sécurisé à vos données</li>
                </ul>
            </div>
            HTML;
        }

        return $html;
    }
}
<?php
namespace IUT\Deefy\Dispatch;

use IUT\Deefy\Action\DefaultAction;
use IUT\Deefy\Action\DisplayPlaylistAction;
use IUT\Deefy\Action\AddPlaylistAction;
use IUT\Deefy\Action\AddPodcastTrackAction;

class Dispatcher
{
    private string $action;

    public function __construct()
    {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run(): void
    {
        $html = match($this->action) {
            'add-playlist' => (new \IUT\Deefy\Action\AddPlaylistAction())->execute(),
            'add-track' => (new \IUT\Deefy\Action\AddPodcastTrackAction())->execute(),
            'signin' => (new \IUT\Deefy\Action\SigninAction())->execute(),
            'add-user' => (new \IUT\Deefy\Action\AddUserAction())->execute(),

            // NOUVELLES ROUTES À AJOUTER
            'my-playlists' => (new \IUT\Deefy\Action\DisplayPlaylistsAction())->execute(),
            'display-playlist' => (new \IUT\Deefy\Action\DisplayPlaylistAction())->execute(),

            default => (new \IUT\Deefy\Action\DefaultAction())->execute(),
        };
        $this->renderPage($html);
    }

    private function renderPage(string $html): void
    {
        // Déterminer si l'utilisateur est connecté
        $isAuthenticated = isset($_SESSION['user']);
        $userEmail = $isAuthenticated ? htmlspecialchars($_SESSION['user']['email']) : '';

        // Créer le menu de navigation
        $nav = '<nav class="main-nav">';
        if ($isAuthenticated) {
            $nav .= '<p>Connecté en tant que : <strong>' . $userEmail . '</strong></p>';
            $nav .= '<ul>';
            $nav .= '<li><a href="index.php">Accueil</a></li>';
            $nav .= '<li><a href="index.php?action=my-playlists">Mes Playlists</a></li>';
            $nav .= '<li><a href="index.php?action=add-playlist">Créer une playlist</a></li>';
            if (isset($_SESSION['current_playlist_id'])) {
                $nav .= '<li><a href="index.php?action=display-playlist">Playlist courante</a></li>';
                $nav .= '<li><a href="index.php?action=add-track">Ajouter une piste</a></li>';
            }
            $nav .= '<li><a href="index.php?action=logout">Déconnexion</a></li>';
            $nav .= '</ul>';
        } else {
            $nav .= '<ul>';
            $nav .= '<li><a href="index.php">Accueil</a></li>';
            $nav .= '<li><a href="index.php?action=signin">Connexion</a></li>';
            $nav .= '<li><a href="index.php?action=add-user">Inscription</a></li>';
            $nav .= '</ul>';
        }
        $nav .= '</nav>';

        echo <<<HTML
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Deefy</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                margin: 0;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                padding: 20px;
            }
            
            .container {
                max-width: 1200px;
                margin: 0 auto;
                background: white;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                overflow: hidden;
            }
            
            header {
                background: #667eea;
                color: white;
                padding: 20px 30px;
            }
            
            header h1 {
                margin: 0;
                font-size: 2em;
            }
            
            .main-nav {
                background: #f8f9fa;
                padding: 15px 30px;
                border-bottom: 2px solid #dee2e6;
            }
            
            .main-nav ul {
                list-style: none;
                display: flex;
                gap: 20px;
                flex-wrap: wrap;
            }
            
            .main-nav a {
                text-decoration: none;
                color: #667eea;
                font-weight: 500;
                transition: color 0.3s;
            }
            
            .main-nav a:hover {
                color: #764ba2;
            }
            
            main {
                padding: 30px;
            }
            
            .track, .playlist { 
                background: #f8f9fa; 
                padding: 15px; 
                border-radius: 8px; 
                margin-bottom: 15px;
                border-left: 4px solid #667eea;
            }
            
            form { 
                background: #f8f9fa;
                padding: 25px;
                border-radius: 8px;
                margin-bottom: 20px; 
            }
            
            form div {
                margin-bottom: 15px;
            }
            
            label {
                display: block;
                margin-bottom: 5px;
                font-weight: 600;
                color: #333;
            }
            
            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="number"],
            select {
                width: 100%;
                padding: 10px;
                border: 1px solid #ced4da;
                border-radius: 5px;
                font-size: 1em;
            }
            
            button {
                background: #667eea;
                color: white;
                padding: 12px 25px;
                border: none;
                border-radius: 5px;
                font-size: 1em;
                cursor: pointer;
                transition: background 0.3s;
            }
            
            button:hover {
                background: #764ba2;
            }
            
            .error {
                background: #f8d7da;
                color: #721c24;
                padding: 15px;
                border-radius: 5px;
                border-left: 4px solid #f5c6cb;
                margin-bottom: 15px;
            }
            
            .success {
                background: #d4edda;
                color: #155724;
                padding: 15px;
                border-radius: 5px;
                border-left: 4px solid #c3e6cb;
                margin-bottom: 15px;
            }
            
            .playlist-list, .track-list {
                list-style: none;
                padding: 0;
            }
            
            .playlist-list li, .track-list li {
                background: #f8f9fa;
                padding: 15px;
                margin-bottom: 10px;
                border-radius: 5px;
                border-left: 4px solid #667eea;
            }
            
            .playlist-list a {
                text-decoration: none;
                color: #667eea;
                font-weight: 600;
                font-size: 1.1em;
            }
            
            .playlist-list a:hover {
                color: #764ba2;
            }
            
            a {
                color: #667eea;
                text-decoration: none;
            }
            
            a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <header>
                <h1>Deefy</h1>
            </header>
            $nav
            <main>
                $html
            </main>
        </div>
    </body>
    </html>
    HTML;
    }
}

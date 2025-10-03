<?php

namespace IUT\Deefy\Dispatch;

use IUT\Deefy\Action\DefaultAction;
use IUT\Deefy\Action\DisplayPlaylistAction;
use IUT\Deefy\Action\AddPlaylistAction;
use IUT\Deefy\Action\AddPodcastTrackAction;

class Dispatcher{
    private string $action;

    public function __construct(){
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run(): void{
        $html = match($this->action){
            'playlist' => (new DisplayPlaylistAction())->execute(),
            'add-playlist' => (new AddPlaylistAction())->execute(),
            'add-track' => (new AddPodcastTrackAction())->execute(),
            default => (new DefaultAction())->execute(),
        };

        $this->renderPage($html);
    }

    private function renderPage(string $html): void
{
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Deefy</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
        </style>
    </head>
    <body>
        $html
    </body>
    </html>
    HTML;
}
}
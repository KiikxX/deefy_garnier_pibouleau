<?php
require_once __DIR__ . '/vendor/autoload.php';

use IUT\Deefy\Action\AddPlaylistAction;
use IUT\Deefy\Action\AddPodcastTrackAction;
use IUT\Deefy\Action\DefaultAction;
use IUT\Deefy\Action\DisplayPlaylistAction;

$action = $_GET['action'] ?? 'default';

switch ($action) {
    case 'playlist':
        $actionObject = new DisplayPlaylistAction();
        break;
    case 'add-playlist':
        $actionObject = new AddPlaylistAction();
        break;
    case 'add-track':
        $actionObject = new AddPodcastTrackAction();
        break;
    default:
        $actionObject = new DefaultAction();
        break;
}

echo $actionObject->execute(); 
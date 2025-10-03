<?php
require_once __DIR__ . '/vendor/autoload.php';

use IUT\Deefy\DefaultAction;
use IUT\Deefy\DisplayPlaylistAction;
use IUT\Deefy\AddPlaylistAction;
use IUT\Deefy\AddPodcastTrackAction;

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
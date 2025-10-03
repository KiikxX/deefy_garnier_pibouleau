<?php

require_once "vendor/autoload.php";

use IUT\Spotify\Entity\AlbumTrack;
use IUT\Spotify\CookieProvider;
use IUT\Spotify\Render\AlbumTrackRenderer;

const COOKIE_ALBUM_TRACK_NAME = 'album_track';

$track = new AlbumTrack('Song Title', 'Artist Name');
$trackSerialized = serialize($track);

$cookieProvider = new CookieProvider();
$cookieProvider->createCookie(COOKIE_ALBUM_TRACK_NAME, $trackSerialized);

$savedTrackSerialized = $cookieProvider->getCookie(COOKIE_ALBUM_TRACK_NAME);
$albumTrackUnserialize = unserialize($savedTrackSerialized);

$albumTrackRender = new AlbumTrackRenderer($albumTrackUnserialize);
echo $albumTrackRender->render(\IUT\Spotify\Render\AudioListRenderer::COMPACT);


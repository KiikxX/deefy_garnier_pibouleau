<?php
namespace IUT\Deefy\Action;

class DisplayPlaylistAction extends Action
{
    public function execute(): string
    {
        return "<h2>Ma Playlist</h2><ul><li>Track 1</li><li>Track 2</li></ul>";
    }
}
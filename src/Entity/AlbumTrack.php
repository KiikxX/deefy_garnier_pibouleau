<?php

namespace IUT\Deefy\Entity;

class AlbumTrack extends AudioTrack {
    protected string $artist;

    public function __construct(protected string $title, string $artist, string $filename = '', int $duration = 0)
    {
        parent::__construct($title, $duration);
        $this->artist = $artist;
    }
}
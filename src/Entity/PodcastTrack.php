<?php

namespace IUT\Deefy\Entity;

class PodcastTrack extends AudioTrack {
    protected string $author;

    public function __construct(protected string $title, string $author, int $duration = 0)
    {
        parent::__construct($title, $duration);
        $this->author = $author;
    }
}
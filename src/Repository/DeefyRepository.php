<?php

namespace IUT\Deefy\Repository;

use PDO;
use Exception;
use IUT\Deefy\Entity\Playlist;
use IUT\Deefy\Entity\AudioTrack;
use IUT\Deefy\Entity\AlbumTrack;
use IUT\Deefy\Entity\PodcastTrack;

class DeefyRepository
{
    private PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf) {
        $this->pdo = new PDO($conf['dsn'], $conf['user'], $conf['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    public static function getInstance(){
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $file) {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new Exception("Error reading configuration file");
        }
        self::$config = [ 
            'dsn'=> $conf['driver'].':host='.$conf['host'].';dbname='.$conf['database'],
            'user'=> $conf['username'],
            'pass'=> $conf['password'] 
        ];
    }

    /**
     * Récupérer la liste des playlists 
     */
    public function getPlaylists(): array {
        try {
            $stmt = $this->pdo->query("SELECT id, nom FROM playlist ORDER BY nom");
            $playlistsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $playlists = [];
            foreach ($playlistsData as $data) {
                $playlist = new Playlist($data['nom']);
                $playlists[] = [
                    'id' => $data['id'],
                    'playlist' => $playlist
                ];
            }
            return $playlists;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des playlists: " . $e->getMessage());
        }
    }

    /**
     * Sauvegarder une playlist vide avec son propriétaire
     */
    public function sauvegarderPlaylistVide(string $name, int $userId): array {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO playlist (nom, user_id) VALUES (?, ?)");
            $stmt->execute([$name, $userId]);

            $playlist = new Playlist($name);
            $playlistId = $this->pdo->lastInsertId();

            return ['id' => $playlistId, 'playlist' => $playlist];
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la création de la playlist: " . $e->getMessage());
        }
    }

    /**
     * Sauvegarder une piste
     */
    public function sauvegarderPiste(AudioTrack $track): int {
        try {
            if ($track instanceof AlbumTrack) {
                $stmt = $this->pdo->prepare("
                INSERT INTO track (titre, duree, type, artiste_album) 
                VALUES (?, ?, ?, ?)
            ");
                $stmt->execute([$track->title, $track->duration, 'album', $track->artist ?? '']);
            } elseif ($track instanceof PodcastTrack) {
                $stmt = $this->pdo->prepare("
                INSERT INTO track (titre, duree, type, auteur_podcast) 
                VALUES (?, ?, ?, ?)
            ");
                $stmt->execute([$track->title, $track->duration, 'podcast', $track->author ?? '']);
            } else {
                $stmt = $this->pdo->prepare("
                INSERT INTO track (titre, duree, type) 
                VALUES (?, ?, ?)
            ");
                $stmt->execute([$track->title, $track->duration, 'audio']);
            }

            return (int) $this->pdo->lastInsertId();

        } catch (Exception $e) {
            throw new Exception("Erreur lors de la sauvegarde de la piste: " . $e->getMessage());
        }
    }

    /**
     * Ajouter une piste existante à une playlist existante
     */
    public function addPistePlaylist(int $trackId, int $playlistId): void {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM playlist WHERE id = ?");
            $stmt->execute([$playlistId]);
            if ($stmt->fetchColumn() == 0) {
                throw new Exception("Playlist avec l'ID $playlistId n'existe pas");
            }
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM track WHERE id = ?");
            $stmt->execute([$trackId]);
            if ($stmt->fetchColumn() == 0) {
                throw new Exception("Track avec l'ID $trackId n'existe pas");
            }
            $stmt = $this->pdo->prepare("
                SELECT COALESCE(MAX(no_piste_dans_liste), 0) + 1 
                FROM playlist2track 
                WHERE id_pl = ?
            ");
            $stmt->execute([$playlistId]);
            $nextPosition = $stmt->fetchColumn();
            $stmt = $this->pdo->prepare("
                INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$playlistId, $trackId, $nextPosition]);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'ajout de la piste à la playlist: " . $e->getMessage());
        }
    }

    /**
     * Récupérer les playlists d'un utilisateur spécifique
     */
    public function getPlaylistsByUserId(int $userId): array {
        try {
            $stmt = $this->pdo->prepare("SELECT id, nom FROM playlist WHERE user_id = ? ORDER BY nom");
            $stmt->execute([$userId]);
            $playlistsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $playlists = [];
            foreach ($playlistsData as $data) {
                $playlist = new Playlist($data['nom']);
                $playlists[] = [
                    'id' => $data['id'],
                    'playlist' => $playlist
                ];
            }
            return $playlists;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des playlists: " . $e->getMessage());
        }
    }

    /**
     * Récupérer une playlist avec toutes ses pistes et vérifier le propriétaire
     */
    public function getPlaylistWithTracks(int $playlistId): array {
        try {
            // Récupérer les infos de la playlist
            $stmt = $this->pdo->prepare("SELECT id, nom, user_id FROM playlist WHERE id = ?");
            $stmt->execute([$playlistId]);
            $playlist = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$playlist) {
                throw new Exception("Playlist non trouvée");
            }

            // Récupérer les tracks de la playlist
            $stmt = $this->pdo->prepare("
            SELECT t.* 
            FROM track t
            INNER JOIN playlist2track pt ON t.id = pt.id_track
            WHERE pt.id_pl = ?
            ORDER BY pt.no_piste_dans_liste
        ");
            $stmt->execute([$playlistId]);
            $tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'id' => $playlist['id'],
                'nom' => $playlist['nom'],
                'user_id' => $playlist['user_id'],
                'tracks' => $tracks
            ];

        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération de la playlist: " . $e->getMessage());
        }
    }
}
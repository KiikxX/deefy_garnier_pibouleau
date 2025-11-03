<?php
namespace IUT\Deefy\Audio;

class AudioFileValidator
{
    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB
    private const ALLOWED_MIME_TYPES = [
        'audio/mpeg',
        'audio/mp3',
        'audio/mpeg3',
        'audio/x-mpeg-3',
        'video/mpeg',
        'video/x-mpeg',
        'application/octet-stream'
    ];
    private const ALLOWED_EXTENSIONS = ['mp3'];

    /**
     * Valide un fichier audio uploadé
     * @throws AudioFileException
     */
    public static function validate(array $file): void
    {
        // Vérifier qu'il n'y a pas d'erreur d'upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new AudioFileException(self::getUploadErrorMessage($file['error']));
        }

        // Vérifier la taille
        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new AudioFileException("Le fichier est trop volumineux (max 10 MB)");
        }

        if ($file['size'] === 0) {
            throw new AudioFileException("Le fichier est vide");
        }

        // ✅ Vérifier que le fichier temporaire existe
        if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            throw new AudioFileException("Le fichier temporaire n'existe pas ou a été supprimé");
        }

        // Vérifier l'extension
        $filename = $file['name'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new AudioFileException("Extension non autorisée. Seul le format MP3 est accepté");
        }

        // Vérifier le MIME type (si disponible)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo && file_exists($file['tmp_name'])) {
            $mimeType = @finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            // Valider seulement si le MIME type est détecté
            if ($mimeType && !in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
                throw new AudioFileException("Type de fichier non valide. Le fichier doit être un MP3 (détecté : $mimeType)");
            }
        }
        // Si fileinfo n'est pas disponible ou échoue, on se fie à l'extension
    }

    /**
     * Génère un nom de fichier sécurisé et unique
     */
    public static function generateSecureFilename(string $originalFilename): string
    {
        $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        $basename = pathinfo($originalFilename, PATHINFO_FILENAME);

        // Nettoyer le nom de fichier
        $basename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $basename);
        $basename = substr($basename, 0, 50); // Limiter la longueur

        // Ajouter un timestamp et hash pour l'unicité
        $uniqueId = uniqid() . '_' . bin2hex(random_bytes(8));

        return $basename . '_' . $uniqueId . '.' . $extension;
    }

    /**
     * Sauvegarde un fichier audio validé
     */
    public static function save(array $file, string $uploadDir = 'uploads/audio/'): string
    {
        // ✅ Vérifier que le fichier temporaire existe toujours
        if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            throw new AudioFileException("Le fichier temporaire n'existe plus. Il a peut-être été déjà déplacé.");
        }

        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new AudioFileException("Impossible de créer le dossier d'upload : $uploadDir");
            }
        }

        // Vérifier que le dossier est accessible en écriture
        if (!is_writable($uploadDir)) {
            throw new AudioFileException("Le dossier d'upload n'est pas accessible en écriture : $uploadDir");
        }

        // Générer un nom de fichier sécurisé
        $secureFilename = self::generateSecureFilename($file['name']);
        $destination = $uploadDir . $secureFilename;

        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $error = error_get_last();
            throw new AudioFileException("Erreur lors de la sauvegarde du fichier : " . ($error['message'] ?? 'Raison inconnue'));
        }

        return $destination;
    }

    private static function getUploadErrorMessage(int $errorCode): string
    {
        return match($errorCode) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => "Le fichier est trop volumineux",
            UPLOAD_ERR_PARTIAL => "Le fichier n'a été que partiellement téléchargé",
            UPLOAD_ERR_NO_FILE => "Aucun fichier n'a été téléchargé",
            UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant",
            UPLOAD_ERR_CANT_WRITE => "Échec de l'écriture du fichier sur le disque",
            UPLOAD_ERR_EXTENSION => "Une extension PHP a arrêté l'upload",
            default => "Erreur inconnue lors de l'upload"
        };
    }
}
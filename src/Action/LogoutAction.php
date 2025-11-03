<?php
namespace IUT\Deefy\Action;

class LogoutAction extends Action
{
    public function execute(): string
    {
        // Démarrer la session si nécessaire
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Détruire toutes les variables de session
        $_SESSION = [];

        // Détruire le cookie de session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }

        // Détruire la session
        session_destroy();

        // Rediriger vers la page d'accueil avec un message
        header('Location: index.php?logout=success');
        exit();
    }
}
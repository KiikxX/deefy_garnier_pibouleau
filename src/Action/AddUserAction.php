<?php
// src/Action/AddUserAction.php
namespace IUT\Deefy\Action;

use IUT\Deefy\Auth\AuthnProvider;
use IUT\Deefy\Auth\AuthnException;

class AddUserAction extends Action
{
    protected function executeGet(): string
    {
        // Récupérer les messages de la session
        $message = '';
        $messageClass = '';

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['register_message'])) {
            $messageType = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'error';
            $messageClass = $messageType === 'success' ? 'success-message' : 'error-message';
            $message = '<div class="' . $messageClass . '">' . $_SESSION['register_message'] . '</div>';

            // Supprimer le message après l'avoir affiché
            unset($_SESSION['register_message']);
            unset($_SESSION['message_type']);
        }

        $form = <<<HTML
        <h2>Inscription</h2>
        $message
        <form method="post" action="index.php?action=add-user">
            <div>
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="passwd">Mot de passe :</label>
                <input type="password" id="passwd" name="passwd" required>
                <small>Le mot de passe doit contenir au moins 10 caractères</small>
            </div>
            <div>
                <label for="passwd_confirm">Confirmer le mot de passe :</label>
                <input type="password" id="passwd_confirm" name="passwd_confirm" required>
            </div>
            <button type="submit">S'inscrire</button>
        </form>
        <p><a href="index.php?action=signin">Déjà inscrit ? Se connecter</a></p>
        HTML;

        return $form;
    }

    protected function executePost(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'passwd', FILTER_UNSAFE_RAW);
        $passwordConfirm = filter_input(INPUT_POST, 'passwd_confirm', FILTER_UNSAFE_RAW);

        if (!$email || !$password || !$passwordConfirm) {
            $_SESSION['register_message'] = "Veuillez remplir tous les champs correctement";
            $_SESSION['message_type'] = "error";
            header('Location: index.php?action=add-user');
            exit();
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['register_message'] = "Les mots de passe ne correspondent pas";
            $_SESSION['message_type'] = "error";
            header('Location: index.php?action=add-user');
            exit();
        }

        try {
            AuthnProvider::register($email, $password);
            $_SESSION['register_message'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            $_SESSION['message_type'] = "success";
            header('Location: index.php?action=signin');
            exit();
        } catch (AuthnException $e) {
            // Capture explicite de l'erreur "utilisateur existe déjà"
            $_SESSION['register_message'] = "Erreur : " . htmlspecialchars($e->getMessage());
            $_SESSION['message_type'] = "error";
            header('Location: index.php?action=add-user');
            exit();
        }
    }
}
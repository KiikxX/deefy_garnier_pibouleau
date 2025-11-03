<?php
namespace IUT\Deefy\Action;

use IUT\Deefy\Auth\AuthnProvider;
use IUT\Deefy\Auth\AuthnException;

class AddUserAction extends Action
{
    protected function executeGet(): string {
        
        $message = '';

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['register_message'])) {
            $messageType = $_SESSION['message_type'] ?? 'error';
            $messageColor = $messageType === 'success' ? 'green' : 'red';

            $message = '<p style="color: ' . $messageColor . '; border: 1px solid ' . $messageColor . '; padding: 10px; border-radius: 5px;">'
                    . htmlspecialchars($_SESSION['register_message'])
                    . '</p>';

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
        return "<h2>Erreur</h2><p>Veuillez remplir tous les champs correctement.</p>";
    }
    if ($password !== $passwordConfirm) {
        return "<h2>Erreur</h2><p>Les mots de passe ne correspondent pas.</p>";
    }

    try {
        AuthnProvider::register($email, $password);
        return "<h2>Succès</h2><p>Inscription réussie ! <a href='index.php?action=signin'>Connectez-vous ici</a>.</p>";
    } catch (AuthnException $e) {
        return "<h2>Erreur</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
}
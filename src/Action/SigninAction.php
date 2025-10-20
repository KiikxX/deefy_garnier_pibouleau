<?php
namespace IUT\Deefy\Action;

use IUT\Deefy\Auth\AuthnProvider;
use IUT\Deefy\Auth\AuthnException;

class SigninAction extends Action
{
    protected function executeGet(): string
    {
        return <<<HTML
        <h2>Connexion</h2>
        <form method="post" action="index.php?action=signin">
            <div>
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="passwd">Mot de passe :</label>
                <input type="password" id="passwd" name="passwd" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
        HTML;
    }

    protected function executePost(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'passwd', FILTER_UNSAFE_RAW);

        if (!$email || !$password) {
            return "<p>Veuillez remplir tous les champs</p>
                    <p><a href='index.php?action=signin'>Retour au formulaire</a></p>";
        }

        try {
            $user = AuthnProvider::signin($email, $password);
            $_SESSION['user'] = $user;
            return "<p>Authentification réussie ! Bienvenue {$user['email']}</p>";
        } catch (AuthnException $e) {
            return "<p>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>
                    <p><a href='index.php?action=signin'>Réessayer</a></p>";
        }
    }
}

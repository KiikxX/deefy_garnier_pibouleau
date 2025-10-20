<?php
namespace IUT\Deefy\Action;

class DefaultAction extends Action
{
    public function execute(): string
    {
        $html = <<<HTML
        <h1>Bienvenue sur Deefy</h1>
        
        <div class="menu">
            <h2>Menu utilisateur</h2>
            <ul>
                <li><a href="index.php?action=signin">Connexion</a></li>
                <li><a href="index.php?action=add-user">Inscription</a></li>
            </ul>
        </div>
        
        <p>Utilisez les liens suivants pour naviguer :</p>
            <ul>
                <li><a href='index.php?action=add-track'>Ajouter une piste</a></li>
                <li><a href='index.php?action=add-playlist'>Ajouter une playlist</a></li>
            </ul>
        HTML;
        return $html;
    }
}

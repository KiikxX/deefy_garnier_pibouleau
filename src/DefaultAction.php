<?php
namespace IUT\Deefy;

class DefaultAction extends Action
{
    public function execute(): string
    {
        
        $createur = "Créer par : Garnier/Pibouleau";
        return "<h1>Bienvenue sur Deefy</h1><p>$createur</p>";
    }
}

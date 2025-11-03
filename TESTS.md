# Documentation de test - Projet Deefy

## Informations de déploiement

- **URL de test** : https://webetu.iutnc.univ-lorraine.fr/~e69077u/deefy/
- **Dépôt GitHub** : https://github.com/KiikxX/deefy_garnier_pibouleau

## Base de données

### Configuration
- **Serveur** : MySQL sur webetu.iutnc.univ-lorraine.fr
- **Base de données** : deefy_db
- **Utilisateur MySQL** : votre_user_mysql
- **Script d'initialisation** : `scriptSQL.sql`

### Installation
```bash
# Se connecter au serveur MySQL
mysql -h localhost -u votre_user -p

# Créer la base de données
CREATE DATABASE deefy;
USE deefy;

# Exécuter les scripts
source scriptSQL.sql;
```

### Utilisateur pour les tests
user1@mail.com, mdp : user1
user2@mail.com, mdp : user2
user3@mail.com, mdp : user3
user4@mail.com, mdp : user4
admin@mail.com, mdp : admin
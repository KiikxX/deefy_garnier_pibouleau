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
CREATE DATABASE deefy_db;
USE deefy_db;

# Exécuter les scripts
source scriptSQL.sql;

### Utilisateurs de test
- user1@mail.com, mdp : $2y$12$e9DCiDKOGpVs9s.9u2ENEOiq7wGvx7sngyhPvKXo2mUbI3ulGWOdC
- user2@mail.com, mdp : $2y$12$4EuAiwZCaMouBpquSVoiaOnQTQTconCP9rEev6DMiugDmqivxJ3AG
- user3@mail.com, mdp : $2y$12$5dDqgRbmCN35XzhniJPJ1ejM5GIpBMzRizP730IDEHsSNAu24850S
- user4@mail.com, mdp : $2y$12$ltC0A0zZkD87pZ8K0e6TYOJPJeN/GcTSkUbpqq0kBvx6XdpFqzzqq
- admin@mail.com (rôle admin = 100), mdp : $2y$12$JtV1W6MOy/kGILbNwGR2lOqBn8PAO3Z6MupGhXpmkeCXUPQ/wzD8a
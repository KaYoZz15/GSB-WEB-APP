# Application Web de Gestion de Comptes Rendus - GSB

<p align="center">
  <img src="https://kevinechallier.fr/gsb/assets/images/GSB-logo.png" alt="GSB Logo">
</p>

<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>MDP</th>
            <th>RÔLE</th>
            <th>RÉGION</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>admin</td>
            <td>A!dM1n#2024</td>
            <td>admin</td>
            <td>Bourgogne-Franche-Comté</td>
        </tr>
        <tr>
            <td>jdupont</td>
            <td>A!z3rTy@9xY7</td>
            <td>visiteur</td>
            <td>Auvergne-Rhône-Alpes</td>
        </tr>
        <tr>
            <td>mleroy</td>
            <td>Qw!ErTy#8Z1@</td>
            <td>delegue</td>
            <td>Auvergne-Rhône-Alpes</td>
        </tr>
        <tr>
            <td>tbernard</td>
            <td>P@$$w0Rd1XyZ!</td>
            <td>responsable</td>
            <td>Île-de-France</td>
        </tr>
        <tr>
            <td>csimon</td>
            <td>S3curE!P@$$678</td>
            <td>visiteur</td>
            <td>Nouvelle-Aquitaine</td>
        </tr>
        <tr>
            <td>efabre</td>
            <td>Xyz@123$%^789</td>
            <td>delegue</td>
            <td>Nouvelle-Aquitaine</td>
        </tr>
        <tr>
            <td>lperrin</td>
            <td>StRoNg!Pwd$456</td>
            <td>visiteur</td>
            <td>Bretagne</td>
        </tr>
    </tbody>
</table>

<br>


## Description

Ce projet vise à développer une application web sécurisée pour la gestion des comptes rendus de visite des visiteurs médicaux. Destinée au laboratoire GSB, cette application permet aux utilisateurs de centraliser les rapports de visite, de gérer la distribution d'échantillons et de fournir des statistiques exploitables par la direction commerciale.

L'application est conçue pour être intuitive, ergonomique et évolutive. Elle prend en charge plusieurs niveaux d'accès pour les utilisateurs, notamment les visiteurs médicaux, les délégués régionaux et les responsables de secteur.

## Objectifs du Projet

- **Centralisation des comptes rendus de visite** : Les visiteurs médicaux pourront enregistrer, consulter et modifier leurs comptes rendus sur une période de trois ans.
- **Statistiques et suivi** : Les responsables pourront obtenir des statistiques détaillées sur les visites effectuées, les produits promus, ainsi que la distribution des échantillons.
- **Gestion des utilisateurs** : Chaque utilisateur a un rôle défini (visiteur, délégué, responsable), avec des permissions spécifiques adaptées à ses besoins professionnels.
- **Sécurité des données** : Les données sont protégées par un système d'authentification sécurisé et le chiffrement des communications.

## Fonctionnalités principales

- **Connexion sécurisée** : Accès à l'application via un nom d'utilisateur et un mot de passe.
- **Saisie des comptes rendus** : Les visiteurs peuvent enregistrer les détails des visites effectuées (date, médecin, produits présentés, échantillons distribués).
- **Consultation historique** : Les utilisateurs peuvent consulter l’historique des comptes rendus sur une période de trois ans.
- **Gestion des échantillons** : Suivi de la distribution des échantillons médicaux.
- **Statistiques et rapports** : Génération de statistiques pour chaque utilisateur en fonction des visites réalisées.
- **Portabilité** : L'application est conçue pour être compatible avec une future version mobile.

## Technologies Utilisées

- **Langage** : PHP
- **Base de données** : MySQL ou PostgreSQL
- **Front-end** : HTML5, CSS3, JavaScript
- **Architecture** : MVC (Modèle-Vue-Contrôleur)
- **Sécurité** : Chiffrement SSL/TLS pour les communications

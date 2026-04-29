# 🏪 L'annonceur — Marketplace Locale

Une plateforme moderne et intuitive pour publier et découvrir des annonces locales. **Simple, rapide et sécurisé.**

![Language](https://img.shields.io/badge/language-PHP%20%7C%20JavaScript%20%7C%20HTML%2FCSS-blue)

---

## 📋 Table des matières

- [À propos](#-à-propos)
- [Fonctionnalités](#-fonctionnalités)
- [Technologies](#-technologies)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Utilisation](#-utilisation)
- [Structure du projet](#-structure-du-projet)
- [API](#-api)
- [Contribuer](#-contribuer)

---

## 📖 À propos

**L'annonceur** est une marketplace locale développée pour connecter acheteurs et vendeurs dans leur région. Inspirée par des plateformes comme LeBonCoin, elle offre une expérience utilisateur moderne avec un thème personnalisable (clair/sombre).

### Objectif

Permettre aux utilisateurs de:

- Publier des annonces facilement
- Rechercher des produits/services locaux
- Gérer leurs annonces et leur panier
- Accéder à une large gamme de catégories

---

## ✨ Fonctionnalités

### 🔐 Authentification

- ✅ Inscription et connexion sécurisées
- ✅ Gestion de sessions utilisateur
- ✅ Protection des données personnelles

### 📢 Gestion des annonces

- ✅ Créer, modifier et supprimer des annonces
- ✅ Télécharger des images pour les annonces
- ✅ 10 catégories disponibles (Immobilier, Véhicules, Électronique, Mode, etc.)
- ✅ Affichage du profil vendeur

### 🛒 Panier & Favoris

- ✅ Ajouter/retirer des annonces du panier
- ✅ Gestion dynamique du panier
- ✅ Interface responsive

### 🔍 Recherche & Filtrage

- ✅ Recherche globale en temps réel
- ✅ Filtrage par catégorie
- ✅ Pagination des résultats

### 🎨 Interface utilisateur

- ✅ Mode clair/sombre
- ✅ Design responsive (mobile, tablette, desktop)
- ✅ Navigation intuitive
- ✅ Chargement dynamique des annonces

---

## 🛠️ Technologies

| Domaine             | Technologies                      |
| ------------------- | --------------------------------- |
| **Frontend**        | HTML5, CSS3, JavaScript (Vanilla) |
| **Backend**         | PHP 7+                            |
| **Base de données** | MySQL                             |
| **Autres**          | Fetch API, LocalStorage           |

---

## 📦 Installation

### Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Un serveur web (Xampp)
- Git

### Étapes d'installation

1. **Cloner le repository**

```bash
git clone https://github.com/<USERNAME>/lannonceur.git
cd lannonceur
```

2. **Configurer la base de données**
   - Importez le schéma SQL:

   ```bash
   mysql -u <votre_user> -p <votre_db> < php/config/schema.sql
   ```

3. **Configurer la connexion BD**
   - Modifiez `php/config/db.php` avec vos paramètres:

   ```php
   $host = 'localhost';
   $db = 'votre_base_de_donnees';
   $user = 'votre_utilisateur';
   $pass = 'votre_mot_de_passe';
   ```

4. **Créer le dossier uploads**

```bash
mkdir uploads
chmod 755 uploads
```

5. **Lancer le serveur local**

```bash
php -S localhost:8000
```

6. **Accéder au site**

```
http://localhost:8000
```

---

## ⚙️ Configuration

### Variables d'environnement (optionnel)

Créez un fichier `.env` à la racine:

```
DB_HOST=localhost
DB_NAME=lannonceur
DB_USER=root
DB_PASS=
```

### Fichier `.gitignore`

Les fichiers sensibles sont ignorés:

- `php/config/db.php` ⚠️
- `node_modules/`, `.vscode/`

---

## 🚀 Utilisation

### Pour les utilisateurs

1. **S'inscrire** - Créez un compte via la page "S'inscrire"
2. **Parcourir les annonces** - Explorez par catégorie ou recherche
3. **Publier une annonce** - Accédez au dashboard pour créer une annonce
4. **Gérer votre panier** - Sauvegardez vos annonces favorites
5. **Modifier/Supprimer** - Gérez vos annonces depuis le dashboard

---

## 📂 Structure du projet

```
lannonceur/
├── index.html                 # Page d'accueil
├── README.md                  # Ce fichier
├── .gitignore                 # Fichiers à ignorer
├── css/
│   └── index.css             # Feuille de styles principale
├── js/
│   └── scripts.js            # Scripts JavaScript globaux
├── images/
│   └── favicon.ico           # Favicon du site
├── includes/
│   ├── header.html           # En-tête réutilisable
│   └── footer.html           # Pied de page réutilisable
├── pages/
│   ├── accueil.html          # Page d'accueil
│   ├── produits.html         # Listing des annonces
│   ├── dashboard.html        # Zone utilisateur
│   ├── login.html            # Connexion
│   ├── signup.html           # Inscription
│   ├── panier.html           # Panier/Favoris
│   └── apropos.html          # À propos
├── php/
│   ├── config/
│   │   ├── db.php            # Configuration BD (à configurer)
│   │   └── schema.sql        # Schéma de la base de données
│   └── api/
│       ├── login.php         # Authentification
│       ├── register.php      # Inscription
│       ├── check_session.php # Vérification de session
│       ├── logout.php        # Déconnexion
│       ├── get_annonces.php  # Récupérer les annonces
│       ├── mes_annonces.php  # Annonces de l'utilisateur
│       ├── creer_annonce.php # Créer une annonce
│       ├── modifier_annonce.php # Modifier une annonce
│       ├── supprimer_annonce.php # Supprimer une annonce
│       ├── get_panier.php    # Récupérer le panier
│       ├── ajouter_panier.php # Ajouter au panier
│       └── supprimer_panier.php # Retirer du panier
└── uploads/                  # Téléchargements d'images

```

---

## 🔌 API

### Endpoints principaux

| Endpoint                           | Méthode | Description                        |
| ---------------------------------- | ------- | ---------------------------------- |
| `/php/api/get_annonces.php?page=X` | GET     | Récupérer les annonces (paginées)  |
| `/php/api/mes_annonces.php`        | GET     | Annonces de l'utilisateur connecté |
| `/php/api/creer_annonce.php`       | POST    | Créer une annonce                  |
| `/php/api/modifier_annonce.php`    | POST    | Modifier une annonce               |
| `/php/api/supprimer_annonce.php`   | POST    | Supprimer une annonce              |
| `/php/api/register.php`            | POST    | Inscription utilisateur            |
| `/php/api/login.php`               | POST    | Connexion utilisateur              |
| `/php/api/logout.php`              | GET     | Déconnexion                        |
| `/php/api/check_session.php`       | GET     | Vérifier la session active         |
| `/php/api/get_panier.php`          | GET     | Récupérer le panier                |
| `/php/api/ajouter_panier.php`      | POST    | Ajouter une annonce au panier      |
| `/php/api/supprimer_panier.php`    | POST    | Retirer une annonce du panier      |

---

## 🎨 Catégories disponibles

- 🏠 **Immobilier**
- 🚗 **Véhicules**
- 📱 **Électronique**
- 👕 **Mode**
- 🪑 **Mobilier**
- 💼 **Emploi**
- 🎓 **Formation**
- 🔧 **Services**
- 🎮 **Loisirs**
- 📚 **Livres**

---

## 🐛 Dépannage

### Problème: "Erreur de connexion à la base de données"

**Solution:** Vérifiez votre configuration dans `php/config/db.php`

### Problème: "Les images ne s'affichent pas"

**Solution:** Assurez-vous que le dossier `uploads/` existe et est accessible en écriture

### Problème: "Session non reconnue"

**Solution:** Vérifiez que les cookies sont activés dans votre navigateur

---

## 🤝 Contribuer

Les contributions sont bienvenues! Pour contribuer:

1. **Fork** le repository
2. Créez une branche (`git checkout -b feature/AmazingFeature`)
3. Faites vos changements et committez (`git commit -m 'Add AmazingFeature'`)
4. Poussez votre branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une **Pull Request**

---

**Merci d'utiliser L'annonceur! 🎉**

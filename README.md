# Portfolio Daniel - Full Stack Developer

Un site portfolio premium et dynamique conçu pour mettre en valeur des compétences en développement Full Stack (Symfony / Flutter). Ce projet se distingue par son design vibrant, ses animations fluides (GSAP) et son architecture moderne basée sur Symfony 7.4.

![Symfony](https://img.shields.io/badge/Symfony-7.4-000000?style=for-the-badge&logo=symfony&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![GSAP](https://img.shields.io/badge/GSAP-3.14-88CE02?style=for-the-badge&logo=greensock&logoColor=white)
![Stimulus](https://img.shields.io/badge/Stimulus-3.2-black?style=for-the-badge&logo=hotwire&logoColor=white)

## 🚀 Fonctionnalités Clés

- **Design Immersif & Moderne** : Utilisation d'une palette de couleurs vibrantes (Midnight Blue, Electric Blue), effets de glassmorphism et typographie soignée.
- **Animations Avancées (GSAP)** :
  - **Scroll Reveal** : Apparition fluide des éléments au défilement.
  - **Hero Slider / Futuristic Scroll** : Effets de parallaxe et transitions 3D sur la page d'accueil.
  - **3D Tilt** : Effet de bascule interactif sur les cartes au survol de la souris.
  - **Tech Rotation** : Badge rotatif affichant les technologies maîtrisées.
- **Portfolio Dynamique** : Filtrage des projets par catégorie (Web, Mobile, Design) avec animations fluides.
- **Compte à Rebours** : Section challenge avec un timer intégré (Début : 19 Jan 2026).
- **Architecture Symfony 7.4** :
  - Utilisation de **AssetMapper** pour la gestion des assets sans build step complexe (No Node.js required for runtime).
  - Contrôleurs légers pour les pages statiques et dynamiques.

## 🛠️ Stack Technique

- **Backend** : PHP 8.2+, Symfony 7.4, Twig.
- **Frontend** :
  - **CSS** : Vanilla CSS avec architecture modulaire (Variables, Components, Utilities).
  - **JS** : Vanilla JS + Stimulus Controllers + GSAP (ScrollTrigger).
- **Hébergement / DevOps** : Docker (configuration `compose.yaml` incluse).

## 📋 Prérequis

Avant de commencer, assurez-vous d'avoir installé :
- [PHP 8.2](https://www.php.net/downloads) ou supérieur.
- [Composer](https://getcomposer.org/).
- [Symfony CLI](https://symfony.com/download) (recommandé pour le serveur local).

## ⚙️ Installation

1. **Cloner le projet**
   ```bash
   git clone <votre-repo-url>
   cd daniel_portfolio
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Installer les dépendances Frontend (AssetMapper)**
   Les dépendances JS sont gérées via `importmap.php`.
   ```bash
   php bin/console importmap:install
   ```

4. **Configuration de l'environnement**
   Copiez le fichier `.env` et configurez vos variables si nécessaire (base de données, mailer, etc.).
   ```bash
   cp .env .env.local
   ```
   *Note : Pour un simple portfolio statique, la base de données n'est pas strictement obligatoire au démarrage sauf si vous utilisez des entités dynamiques.*

## 🚀 Lancer le projet

Utilisez le serveur web de Symfony pour un développement rapide avec support HTTPS et rechargement à chaud des templates :

```bash
symfony serve
```

Accédez ensuite au site via `https://127.0.0.1:8000`.

## 📂 Structure du Projet

- `assets/` : Fichiers sources CSS et JS.
  - `styles/app.css` : Point d'entrée des styles.
  - `app.js` : Logique principale JS et initialisation de GSAP.
- `config/` : Configuration Symfony (routes, packages, etc.).
- `src/Controller/` : Contrôleurs PHP (`MainController.php` gère les pages principales).
- `templates/` : Vues Twig.
  - `main/` : Pages du portfolio (Home, Projects, Services...).
  - `partials/` : Fragments réutilisables (Navbar, Footer).
- `public/` : Point d'entrée web (`index.php`) et assets compilés/copiés.

## 🎨 Personnalisation

### Modifier les animations
Tout le code d'animation GSAP se trouve dans `assets/app.js`. Vous pouvez ajuster les durées, les déclencheurs ScrollTrigger et les effets 3D directement dans ce fichier.

### Ajouter un projet
Pour ajouter un projet statique, éditez `templates/main/projects.html.twig`. Assurez-vous de respecter la structure HTML `.project-item` avec l'attribut `data-category` pour que le filtre JS fonctionne correctement.

---

*Développé avec ❤️ par Daniel - 2026*

# wp_galerie
Optimisation du bloc galerie par default de wp avec Masonry et GLightBox


# Gallery — Masonry + GLightbox

Transforme automatiquement tous les blocs **Galerie natifs WordPress**
en grille Masonry responsive avec lightbox GLightbox.
Aucune classe CSS manuelle requise sur les blocs.

## Libs utilisées

| Lib | Version | Source |
|---|---|---|
| GLightbox | 3 | CDN jsDelivr |
| Masonry | natif WP | WP core |
| imagesLoaded | natif WP | WP core |

## Fichiers

| Fichier | Rôle |
|---|---|
| `enqueue.php` | Enqueue des libs + injection JS inline via WPCode |
| `gallery.css` | Styles Masonry + responsive |
| `gallery.js` | Référence JS (injecté via enqueue.php en prod) |

## Installation

1. Copier `enqueue.php` dans **WPCode** (type : PHP Snippet)
2. Copier `gallery.css` dans **WPCode** (type : CSS Snippet)
3. C'est tout — le JS reconstruit automatiquement le DOM

## Classes utilitaires CSS

Ajouter directement sur le bloc Galerie dans l'éditeur WordPress
via **« Classe(s) CSS supplémentaire(s) »** :

| Classe | Effet |
|---|---|
| `jll-2-colonnes` | Force 2 colonnes |
| `jll-4-colonnes` | Force 4 colonnes |
| *(aucune)* | 6 colonnes par défaut |

## Colonnes responsive (automatique)

| Largeur écran | Colonnes |
|---|---|
| > 1024px | 6 (défaut) |
| ≤ 1024px | 4 |
| ≤ 768px | 3 |
| ≤ 480px | 2 |

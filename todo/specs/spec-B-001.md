# Spec B-001 — Publier l'article « Apprendre à circuler à vélo »

> Source : [resources/Cours_de_velo_UNIGE_inclusif.docx](../../resources/Cours_de_velo_UNIGE_inclusif.docx)
> Arbitrages validés par Max (2026-09-13) : article isolé → page HTML statique · FR seulement · date = date de mise en ligne · bloc sur la home FR + appel au contact · **aucune modification du texte du document** (conclusion UNIGE comprise).

## 1. Spec en une phrase

Une personne qui visite la home FR découvre un bloc « Article » et ouvre une page dédiée qui publie l'article de Stéphane Bachmann sur les cours de vélo pour adultes, avec un appel final vers le formulaire de contact.

## 2. Pourquoi

- Montrer l'expertise de Genevavelo (30 ans de formation) sur son cœur de métier : les cours pour adultes.
- Pousser vers la prise de contact : un bouton vers le formulaire suit l'article.
- Donner au site une première page de contenu référençable, en plus du one-page.

## 3. Comportement attendu

### Page article — `/articles/apprendre-a-circuler-a-velo/`

- **En-tête** : même navigation que la home. Les ancres pointent vers la home (`/#entreprises`, `/#cours`, `/#autre`, `/#accompagnement`, `/#contact`) et le logo vers `/`.
- **Sélecteur de langue** : « English » pointe vers `/en/`, puisque l'article n'existe pas en anglais.
- **Corps de l'article**, dans cet ordre :
  1. Rubrique `Mobilité douce`, affichée comme `.tagline`.
  2. Titre `<h1>` : « Apprendre à circuler à vélo : elles et ils l'ont fait ! »
  3. Ligne de méta : « Par Stéphane Bachmann · <date de mise en ligne> ».
  4. Photo (`<figure>`) avec la légende « Photo : Genevavelo, octobre 2023 » (`<figcaption>`).
  5. Chapeau en gras : « Qui a suivi un cours de vélo à l'âge adulte ? … pour le reste de sa vie. »
  6. Les 6 intertitres en `<h2>`, chacun suivi de ses paragraphes. **Texte repris mot pour mot** du docx.
  7. Conclusion telle quelle : « Un cours de vélo passe à votre portée ? C'est le moment de vous inscrire. » en `<h2>`, puis « Formations gratuites, ouvertes à toutes et tous — renseignements auprès de votre université. »
  8. Signature : « Stéphane Bachmann / Genevavelo ».
  9. **Après l'article** (hors `<article>`, visuellement séparé) : un bouton `.button` « Nous contacter » vers `/#contact`.
- **Pied de page** : identique à la home.
- **Page statique** : aucun chargement de données, donc pas d'état loading, erreur ou vide.

### Bloc sur la home FR — `index.html`

- **Nouvelle section** placée **juste avant** la section Témoignages (`section--review`).
- **Réutilise le motif** `.flexbox-copy-copy` > `.cours--content` + `.div-block` > `img.image-5`, comme les blocs de cours.
- **Contenu du bloc** :
  - `.tagline` « Article » ;
  - `<h5>` « Mobilité douce » ;
  - `<h2>` avec le titre de l'article ;
  - `<p>` avec le chapeau ;
  - lien « Lire l'article → » vers `/articles/apprendre-a-circuler-a-velo/`.
- **Photo** : celle de l'article, en srcset webp.

## 4. Règles métier

- **Texte intouchable** : aucun mot du document n'est ajouté, supprimé, reformulé ou corrigé, conclusion comprise. On conserve la ponctuation, l'écriture inclusive et l'ordre des paragraphes. Seul le balisage HTML (`<h1>`, `<h2>`, `<p>`, `<strong>`, `<figcaption>`) traduit la mise en forme du docx.
- **Ajouts de page autorisés**, hors texte de l'article : ligne de méta (auteur + date), bouton « Nous contacter » après l'article, navigation et pied de page, bloc home (qui reprend titre et chapeau à l'identique).
- **Date** : la date du jour de l'implémentation, affichée en toutes lettres (ex. « 13 septembre 2026 ») et reprise en ISO dans le JSON-LD. Si la mise en ligne est décalée, corriger les deux au même endroit avant le déploiement.
- **Interdit** : afficher la version EN du bloc ou de l'article. Ne pas ajouter de `hreflang` sur la page article.
- **Photo** : sources générées depuis `word/media/image1.jpg` du docx (2640×1980) :
  - webp 500 / 800 / 1080 / 1600 / 2640 px, nommage `images/apprendre-a-circuler-a-velo-p-<N>.webp` et `images/apprendre-a-circuler-a-velo.webp` ;
  - un JPG 1200 px `images/apprendre-a-circuler-a-velo-og.jpg`, réservé à `og:image` (compatibilité des réseaux sociaux) ;
  - conversion avec **ffmpeg**, déjà installé : pas de nouvelle dépendance ;
  - alt : « Trois cyclistes en gilet orange avec leurs vélos devant l'entrée d'un bâtiment de l'Université de Genève ».
- **SEO de la page article** :
  - `<title>` : « Apprendre à circuler à vélo : elles et ils l'ont fait ! – Genevavelo » ;
  - meta description (≤ 160 caractères) : « Peu d'adultes suivent un cours de vélo. Idées reçues, témoignages et conseils de Stéphane Bachmann pour circuler à vélo en sécurité à Genève. » ;
  - canonical `https://www.genevavelo.ch/articles/apprendre-a-circuler-a-velo/` ;
  - `og:type` = `article`, `og:title`, `og:description` et `og:image` (URL absolue du JPG og) ;
  - JSON-LD `Article` : `headline`, `image`, `datePublished`, `author` (Person : Stéphane Bachmann) et `publisher` (Organization : Genevavelo).
- **CSS** : uniquement les variables existantes (`--blue-dark`, `--orange-light`…), aucune couleur hexadécimale.

## 5. Impact docs

- `docs/06-backlog.md` : ligne B-001 (fait avec cette spec).
- `docs/02-functional-spec.md` : section « Page article » (structure, accès depuis la home, FR seulement).
- `docs/05-technical-architecture.md` : remplir au minimum :
  - **Stack** : HTML/CSS/JS vanilla, PHP (`send-mail.php` + PHPMailer), Apache OVH ;
  - **Organisation** : pages `articles/<slug>/index.html`, images `<nom>-p-<N>.webp` générées par ffmpeg ;
  - **Vérifications** : `php -l`, tests navigateur via playwright-cli, pas de build.
- Pas d'entrée D_XXX : le choix statique est facilement réversible (critère 1 non rempli).

## 6. Impact code

| Action | Fichier |
| --- | --- |
| Créer | `articles/apprendre-a-circuler-a-velo/index.html` |
| Créer | `images/apprendre-a-circuler-a-velo.webp`, `-p-500.webp`, `-p-800.webp`, `-p-1080.webp`, `-p-1600.webp`, `-og.jpg` |
| Modifier | `index.html` : nouvelle section avant `section--review` |
| Modifier | `css/style.css` : bloc « Article » (largeur de lecture, taille des `h2` dans l'article, espacements, `figcaption`, méta), avec un breakpoint mobile si nécessaire |
| Ne pas modifier | `js/main.js` : ses gardes gèrent l'absence de slider et de formulaire (à vérifier, pas à changer) |

## 7. Hors-scope

- Version anglaise de l'article et bloc sur `en/index.html`.
- Page liste « Articles », lien dans la navigation, pagination, système de blog ou de build Markdown.
- `sitemap.xml` : à qualifier comme idée séparée si besoin.
- Remplacement des icônes icons8 et nettoyage des images orphelines.
- Commit du fichier source `.docx`.

## 8. Critères d'acceptation

- [ ] `/articles/apprendre-a-circuler-a-velo/` s'affiche sans erreur console ni 404 (CSS, JS, images, polices) à 1280, 768 et 390 px.
- [ ] Le texte de l'article est **strictement identique** au docx : comparaison automatique du texte extrait de `word/document.xml` avec celui de `<article>` (hors ligne de méta), 0 écart.
- [ ] La page a un seul `<h1>`, 7 `<h2>` (6 intertitres + la conclusion), et un `<figure>` avec `<figcaption>`.
- [ ] Les liens de nav de la page article mènent aux bonnes sections de la home ; « Nous contacter » arrive sur `/#contact`.
- [ ] Le menu mobile s'ouvre et se ferme sur la page article.
- [ ] Le bloc home FR est placé juste avant les témoignages, et « Lire l'article → » ouvre la page article.
- [ ] `en/index.html` est inchangé.
- [ ] La photo est servie en webp via srcset ; la plus grande variante chargée à 1280 px fait < 400 Ko.
- [ ] `<title>`, description, canonical, `og:*` et JSON-LD `Article` sont présents ; le JSON-LD est valide (parse JSON OK, `datePublished` au format ISO).
- [ ] Aucune couleur hexadécimale ajoutée dans `css/style.css`.

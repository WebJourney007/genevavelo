# 05 — ARCHITECTURE TECHNIQUE

> **rôle** : la stack et les choix techniques structurants.
> **contient** : technologies, organisation du code, conventions, intégrations externes.
> **vital parce que** : connaître la stack évite d'introduire des outils redondants ou incompatibles.
> **à lire avant de** : ajouter une dépendance, créer un module, changer la structure du code.

---

<!-- À REMPLIR : stack (langages, frameworks, CMS/DB, hébergement), organisation du code,
     conventions structurantes (style, tests, CI). -->

## Stack

- Site statique HTML / CSS / JS vanilla, sans framework ni étape de build (issu d'un export Webflow nettoyé).
- Formulaire de contact : `send-mail.php` + PHPMailer embarqué (`config/PHPMailer-master/`), envoi via `mail()` (relai OVH), configuration dans `.env`.
- Hébergement : Apache sur OVH (`.htaccess` : HTTPS, cache, blocage des fichiers cachés, en-têtes de sécurité).
- En-têtes de sécurité (`.htaccess`) : HSTS, CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy. La CSP liste chaque origine externe (Google Fonts, `img.icons8.com`) : toute nouvelle ressource externe ou script inline doit y être ajoutée, sinon le navigateur la bloque. Le handler `onload` du chargement Google Fonts est autorisé par son hash — le modifier impose de recalculer le hash.

## Organisation du code

- `index.html` (FR) · `en/index.html` (EN, chemins `../`) · `articles/<slug>/index.html` (FR, chemins `../../`).
- `css/style.css` : feuille unique · `js/main.js` : script unique, chaque module s'arrête si son élément est absent de la page.
- `images/` : variantes responsive `<nom>-p-<largeur>.webp` + `<nom>.webp` pleine taille, générées avec **ffmpeg** (`-c:v libwebp -quality 80`) ; image de partage `<nom>-og.jpg` en 1200 px.

## Conventions

- Couleurs et tailles via les variables CSS de `:root`, jamais de hex en dur.
- Vérifications : `php -l send-mail.php` ; tests navigateur avec `playwright-cli` sur `php -S 127.0.0.1:8765`. Pas de typecheck ni de suite de tests.

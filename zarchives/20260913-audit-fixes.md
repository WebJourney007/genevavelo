# Corrections post-audit — sécurité formulaire, parité EN, dette HTML

| Ouverture | 2026-09-13 | 19:33 |
| Clôture | 2026-09-13 | 19:40 |

## Progression

- [x] Phase 1 — Sécurité serveur : règle dotfiles `.htaccess`, `config/.htaccess` Apache 2.4, `send-mail.php` (CORS, erreurs, rate limit, `form_time`, whitelist sujet)
- [x] Phase 2 — Parité EN ↔ FR : preload, width/height, srcset webp, liens contact, aria-labels, icônes sociales inversées
- [x] Phase 3 — Dette HTML FR + EN : restes Webflow, flèches slider mortes, dots en `<button>`, alt des photos, typo, robots.txt
- [x] Phase 4 — Vérification : lint PHP 8.5 OK · endpoint testé en local (405, bot, sujet invalide, email invalide, sujet accentué, 429 au 6e envoi) · navigateur FR + EN (dots clic/clavier, menu mobile, alt, liens, icônes) 0 erreur console/404 (2026-09-13 19:40)

## Notes

- Constat préalable (2026-09-13 19:30) : `/.env`, `/config/`, `/.git/` renvoyaient déjà **403** en prod — la règle dotfiles est une défense en profondeur.
- Flèches du slider supprimées : `div` vides sans style, invisibles à toutes les largeurs depuis le retrait de webflow.js.
- Rate limit : 5 envois / IP / heure, stocké dans `config/form_rate_limit.json` (gitignoré, dossier bloqué en HTTP).

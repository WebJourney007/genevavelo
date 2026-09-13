# Nettoyage CSS / JS — un seul style.css, un seul main.js

| Ouverture | 2026-09-13 | 18:47 |
| Clôture | 2026-09-13 | 19:33 |

## Progression

- [x] Phase 0 — Backup (scratchpad) CSS + HTML
- [x] Phase 1 — Inventaire des classes utilisées (HTML FR/EN + JS inline + webflow.js)
- [x] Phase 2 — Génération de `css/style.css` (normalize + lang-switcher + CSS site + CSS inline), règles mortes retirées
- [x] Phase 3 — HTML : suppression des `<style>` / `style=""`, un seul `<link>`
- [x] Phase 4 — Suppression des anciens fichiers CSS/JS + `styleguide.html` (fait par Max, 2026-09-13 19:10)
- [x] Phase 5 — Vérification (styles calculés avant/après, 3 largeurs, FR + EN) : 0 écart hors logo FR 4em → 6em
- [x] Phase 6 — Logo 6em, fix lang switcher (overflow nav, focus auto, drapeaux emoji), JS regroupé dans `js/main.js` sans jQuery/webflow.js/GSAP — 36/36 tests d'interaction OK (2026-09-13 19:04)
- [x] Phase 7 — Suppression des 3 sections mortes FR + EN (`section-4`, `section-5`, `section--inspirations`) + 15 règles CSS associées — 36/36 tests OK (2026-09-13 19:18)

## Plan phase courante

Toutes les phases sont faites. Session à archiver dans `zarchives/` à la clôture.

## Annexe — textes de `section-4` supprimée (jamais affichés, uniques)

### FR

- **Du premier tour de roue au premier kilomètre en ville** _(Pour débuter ou reprendre.)_
  Depuis 2006, de nombreuses personnes nous font confiance et font de leurs trajets à vélo des moments de plaisir et de sérénité en mettant à profit nos conseils de sécurité.
- **Se (re)mettre en selle avec confiance** _(Libérez vos blocages.)_
  Vous avez des peurs, des blocages liés à la pratique du vélo, vous souffrez d’un traumatisme suite à un accident ou autre ? Ensemble, trouvons des moyens pour faire de vos moments à vélo des moments de plaisir et de liberté.
- **Accompagner les différences, pédaler autrement** _(Le vélo pour tous.)_
  Vous ou un de vos proches (enfant ou autre) avez une particularité qui rend la pratique du vélo peu aisée ? De nombreuses personnes ont été accompagnées avec bienveillance et font maintenant du vélo de manière régulière.

### EN

- **From your first turn of the wheel to your first kilometre in the city** _(For beginners and returners.)_
  Since 2006, many people have trusted us and turned their cycling journeys into moments of pleasure and serenity, making the most of our safety advice.
- **Rebuilding confidence in the saddle** _(Overcome your barriers.)_
  Do you have fears or mental blocks around cycling, or have you suffered trauma following an accident or otherwise? Together, let's find ways to make your time on the bike moments of pleasure and freedom.
- **Embracing differences, riding differently** _(Cycling for all.)_
  Do you or someone close to you (a child or otherwise) have a condition that makes cycling difficult? Many people have been supported with care and compassion and now cycle regularly.

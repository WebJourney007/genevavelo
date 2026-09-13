# 02 — SPEC FONCTIONNELLE

> **rôle** : ce que l'app fait, vue par vue / feature par feature.
> **contient** : comportements attendus, règles métier, parcours utilisateur.
> **vital parce que** : c'est la référence pour coder ou spécifier sans réinventer.
> **à lire avant de** : coder une feature, écrire une spec `/spec-feature`, vérifier un comportement.

---

<!-- À REMPLIR au fil du projet. Une section par vue ou par module fonctionnel.
     Décris le COMPORTEMENT (ce que l'utilisateur voit / peut faire), pas l'implémentation. -->

## Page article

- **URL** : `/articles/<slug>/`, une page par article, en **FR seulement** (pas de hreflang, pas de bloc sur la home EN).
- **Accès** : un bloc « Article » sur la home FR, juste avant les témoignages (rubrique, titre, chapeau, photo, lien « Lire l'article → »). Pas d'entrée dans la navigation.
- **Structure** : même navigation et même pied de page que la home, avec des liens pointant vers les sections de la home (`/#contact`…). Le sélecteur de langue « English » mène à `/en/`.
- **Contenu** : rubrique, titre, auteur et date de mise en ligne, photo avec légende, puis le texte.
- **Texte intouchable** : il est repris mot pour mot du document source, sans correction ni adaptation, et seul le balisage traduit la mise en forme. Une fois l'article terminé, un bouton « Nous contacter » renvoie vers `/#contact`.
- **Article publié** : `/articles/apprendre-a-circuler-a-velo/` (source : `resources/Cours_de_velo_UNIGE_inclusif.docx`).

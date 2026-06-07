# Street Run — Documentation du projet

Document de préparation à l'oral : explique le fonctionnement du projet, les choix techniques et où trouver chaque chose dans le code. L'objectif est de pouvoir répondre à n'importe quelle question sur "pourquoi c'est fait comme ça" et "comment ça marche".

## 1. Le concept

Street Run est un jeu de course infinie ("endless runner") façon Temple Run, jouable directement dans le navigateur (rendu via un canvas HTML5 avec le moteur Phaser), avec une couche Laravel autour qui gère :

- les comptes utilisateurs (inscription, connexion, profil),
- la sauvegarde des scores et un classement (leaderboard),
- une monnaie virtuelle ("pièces") gagnée en jouant,
- un système de coffres (gacha) permettant de débloquer des personnages avec des taux de rareté différents,
- une collection de personnages par utilisateur.

En clair : le jeu lui-même tourne entièrement côté client (JavaScript/Phaser), et Laravel joue le rôle de **backend** : authentification, persistance des données, règles métier (calcul des pièces, tirage des personnages, anti-triche basique), et rendu des pages.

## 2. Stack technique

| Couche | Techno | Où |
|---|---|---|
| Framework backend | Laravel 13 (PHP 8.3+) | `app/`, `routes/`, `database/` |
| Authentification | Laravel Breeze (scaffolding officiel) | `routes/auth.php`, `app/Http/Controllers/Auth/`, vues `auth/*` |
| Base de données | SQLite (fichier `database/database.sqlite`) | migrations dans `database/migrations/` |
| ORM | Eloquent | `app/Models/` |
| Frontend "classique" | Blade + Tailwind CSS + Alpine.js | `resources/views/`, `resources/css/` |
| Moteur de jeu | Phaser 4 (canvas 2D), bundlé via Vite | `resources/js/game.js`, script inline dans `resources/views/game/index.blade.php` |
| Bundler | Vite (`laravel-vite-plugin`) | `vite.config.js` |
| Tests | PHPUnit (style Laravel "Feature tests") | `tests/Feature/` |

Pourquoi SQLite ? C'est la base par défaut d'un projet Laravel fraîchement installé (zéro configuration, un simple fichier). Pour un projet de cette taille c'est largement suffisant, et ça évite d'avoir à installer/configurer un serveur MySQL pour faire tourner le projet en local.

## 3. Comment le projet est lancé

```bash
composer install        # dépendances PHP
npm install             # dépendances JS
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed   # crée les tables + remplit les personnages de base
npm run dev             # lance Vite (hot reload des assets)
php artisan serve       # lance le serveur PHP (http://127.0.0.1:8000)
```

Point important à savoir expliquer : **la base SQLite n'est pas versionnée dans Git** (c'est la pratique standard — un fichier de données ne doit jamais être commité). C'est pourquoi, après un nouveau clone du projet, il faut relancer les migrations : la base démarre vide. Les "seeders" (`database/seeders/CharacterSeeder.php`) servent justement à repeupler les données de référence (les personnages) après une réinstallation.

## 4. Structure de la base de données

Les migrations sont dans `database/migrations/`. Celles qui sont spécifiques au jeu (les autres viennent de Laravel/Breeze) :

- **`add_coins_to_users_table`** : ajoute une colonne `coins` (entier, défaut 0) à la table `users`. C'est le portefeuille du joueur.
- **`create_characters_table`** : catalogue des personnages (`name`, `slug`, `rarity` qui vaut `base|normal|legendary`, `probability`, `color`, `emoji`, `is_base`).
- **`create_user_characters_table`** : table pivot `user_characters` qui relie `users` et `characters` (relation many-to-many : un utilisateur peut posséder plusieurs personnages, un personnage peut être possédé par plusieurs utilisateurs). Contrainte `unique(['user_id', 'character_id'])` pour empêcher les doublons.
- **`create_scores_table`** : historique des parties (`user_id`, `character_id` nullable, `score`, `coins_collected`, `difficulty`, `duration`).
- **`add_score_index_to_scores_table`** : ajoute des index sur `score` et `(user_id, score)`. Utile dès que la table grossit, parce que le leaderboard fait un `ORDER BY score DESC LIMIT 20` — sans index, la base devrait trier toute la table à chaque requête.

### Schéma relationnel simplifié

```
users (1) ──< scores >── (1) characters   [character_id nullable, "set null" si le perso est supprimé]
users (N) ──< user_characters >── (N) characters
```

## 5. Les modèles Eloquent (`app/Models/`)

### `User`
Étend `Authenticatable` (fourni par Laravel pour gérer la connexion). En plus des champs standards (name, email, password), on ajoute `coins`. Relations et méthodes utiles :

- `characters()` : relation `belongsToMany` vers `Character` via la table pivot `user_characters`.
- `scores()` : relation `hasMany` vers `Score`.
- `bestScore()`, `hasCharacter()`, `addCoins()`, `spendCoins()` : petites méthodes "métier" qui évitent de répéter la même logique dans plusieurs contrôleurs. `spendCoins()` retourne `false` si le solde est insuffisant, ce qui permet au contrôleur de réagir proprement sans dupliquer la vérification.

### `Character`
Représente un personnage du jeu. Contient deux méthodes statiques `legendary()` et `normal()` qui filtrent par rareté — pratiques pour le système de coffres.

### `Score`
Une partie jouée. Relations `belongsTo` vers `User` et `Character`.

Les trois modèles utilisent le trait `HasFactory`, ce qui permet de générer des données de test réalistes via les "factories" (`database/factories/`) — utilisées dans les tests automatisés (section 9).

## 6. Le flux applicatif (routes → contrôleurs → vues)

Toutes les routes sont déclarées dans `routes/web.php`. Deux groupes :

**Routes publiques** (visibles sans connexion) :
- `/` → page d'accueil (`GameController::welcome`)
- `/leaderboard` → classement public (`GameController::leaderboard`)

**Routes protégées** par le middleware `auth` (redirige vers `/login` si non connecté) :
- `/game` → page de configuration de partie (`GameController::index`)
- `POST /game/score` → sauvegarde d'un score en fin de partie (`GameController::saveScore`)
- `POST /chest/open` → ouverture d'un coffre (`ChestController::open`)
- `/collection` → collection de personnages (`CharacterController::index`)
- `/profile` → statistiques du joueur (`GameController::profile`)
- `/profile/edit`, `PATCH /profile`, `DELETE /profile` → édition/suppression de compte (fournis par Breeze, `ProfileController`)

Les routes d'authentification (login, register, etc.) sont dans `routes/auth.php`, généré par Laravel Breeze — on ne les a pas réécrites, Breeze fournit déjà tout (contrôleurs + vues + validation).

### `GameController`
- `index()` : charge la page de jeu. Au passage, attache automatiquement à l'utilisateur les personnages "de base" (`is_base = true`) s'il ne les possède pas encore — comme ça un nouveau joueur a toujours au moins un personnage jouable, sans qu'on ait besoin de le faire à l'inscription.
- `saveScore()` : reçoit les résultats d'une partie (score, pièces collectées, difficulté, durée), les enregistre dans `scores`, crédite les pièces au joueur via `$user->addCoins()`, et détermine le type de coffre gagné (`legendary` en mode difficile, `normal` sinon).
- `leaderboard()` : top 20 des scores, triés par score décroissant, avec la relation `user` et `character` chargées en eager loading (`with()`) pour éviter le problème classique du "N+1 requêtes".
- `profile()` : agrège les statistiques du joueur (nombre de parties, meilleur score, total de pièces gagnées, temps de jeu cumulé, meilleur personnage utilisé).

### `ChestController`
Gère le système de coffres (voir détail en section 8 — c'est la partie la plus "métier" du projet).

### `CharacterController`
Affiche la collection : tous les personnages du jeu, avec une indication de ceux que l'utilisateur possède déjà (`ownedIds`).

## 7. Validation : les "Form Requests"

Au lieu de valider les données directement dans le contrôleur avec `$request->validate([...])`, le projet utilise des classes dédiées qui étendent `FormRequest` :

- **`StoreScoreRequest`** (`app/Http/Requests/StoreScoreRequest.php`) : valide le score envoyé en fin de partie — `score` (entier, entre 0 et 1 000 000), `coins_collected`, `difficulty` (doit être `normal` ou `hard`), `duration`, `character_id` (doit exister dans la table `characters`).
- **`OpenChestRequest`** : valide que le `type` de coffre est bien `normal` ou `legendary`.

**Pourquoi c'est mieux qu'une validation inline ?** Ça sépare la responsabilité de validation de la logique métier du contrôleur (principe de responsabilité unique), ça rend la règle testable et réutilisable, et Laravel gère automatiquement le retour d'erreurs 422 avec les messages appropriés si la validation échoue. C'est l'approche "professionnelle" recommandée par la documentation Laravel dès qu'une validation devient un peu conséquente.

Notez aussi les bornes (`max:1000000`, `max:36000` pour la durée, etc.) : elles empêchent un client malveillant d'envoyer un score absurde (`9999999999`) qui polluerait le classement — une forme basique de protection côté serveur, qui rappelle qu'on ne doit **jamais faire confiance aux données envoyées par le navigateur**.

## 8. Le système de coffres (gacha) — la partie la plus intéressante à expliquer

C'est le mécanisme qui permet de débloquer de nouveaux personnages, géré par `ChestController::open()`.

### Les règles

- Un coffre **normal** coûte 50 pièces et donne un personnage de rareté `normal` (tiré au sort selon des probabilités).
- Un coffre **légendaire** est gratuit à ouvrir, mais on ne le gagne qu'en terminant une partie en mode difficile (`hard`). Il donne un personnage de rareté `legendary` (très rare).

### Le tirage pondéré (`rollCharacter`)

Chaque personnage a un champ `probability` (ex: 40, 4, 1, 0.01...). L'algorithme :

1. Récupère tous les personnages du pool concerné (normal ou légendaire).
2. Calcule la somme des probabilités (`$totalProb`).
3. Tire un nombre aléatoire entre 0 et cette somme.
4. Parcourt la liste en cumulant les probabilités, et renvoie le premier personnage dont le cumul dépasse le nombre tiré.

C'est la technique standard pour un tirage pondéré ("weighted random") — typique des systèmes de loot/gacha dans les jeux vidéo. Si la somme des probabilités est nulle (mauvaise config), on retombe sur un tirage uniforme (`$pool->random()`) pour ne jamais planter.

### La transaction et le verrou de ligne

C'est le point le plus subtil et le plus "Laravel avancé" du projet :

```php
$result = DB::transaction(function () use ($type, $userId) {
    $user = User::lockForUpdate()->findOrFail($userId);
    // ... dépense des pièces, tirage, attribution ...
});
```

**Le problème que ça résout** : sans ça, si un joueur clique très vite deux fois sur "ouvrir un coffre" (ou triche en envoyant deux requêtes en parallèle), les deux requêtes pourraient lire le solde "100 pièces" *avant* que l'une d'elles ne l'ait mis à jour, et donc dépenser deux fois 50 pièces alors qu'il n'en avait que pour une seule fois. C'est une **race condition** classique (type "TOCTOU" — *time-of-check to time-of-use*).

`DB::transaction()` garantit que toutes les opérations à l'intérieur réussissent ou échouent ensemble (atomicité). `lockForUpdate()` pose un verrou de ligne sur l'utilisateur pendant la transaction : toute autre requête qui tenterait de modifier ce même utilisateur doit attendre que la transaction se termine. Combinés, ces deux mécanismes rendent l'opération **thread-safe**.

### Le remboursement

Si le tirage ne renvoie aucun personnage (cas où le joueur possède déjà tout, ou pool vide), et que le coffre était payant, les pièces sont automatiquement recréditées (`$user->addCoins(...)`). Ça évite de "voler" le joueur pour rien.

## 9. Le jeu lui-même (Phaser)

### Pourquoi Phaser et pas du "Laravel pur" ?

Un jeu d'action en temps réel (60 images par seconde, contrôles clavier instantanés) ne peut pas se faire en rechargeant des pages côté serveur — il faut du JavaScript qui s'exécute dans le navigateur. Phaser est un moteur de jeu 2D open-source qui gère la boucle de rendu, la gestion du temps (`delta`), les entrées clavier, etc. Laravel sert ici de "support" : il fournit la page, les données du joueur (personnages débloqués), et récupère le résultat final via une API JSON.

### Comment Phaser est intégré

Le script du jeu est écrit directement dans `resources/views/game/index.blade.php` (entre balises `<script>`), ce qui permet d'injecter facilement des données Blade (ex : la liste des personnages du joueur) directement en JavaScript. La librairie Phaser, elle, est gérée proprement comme une dépendance npm (`phaser` dans `package.json`) et bundlée par Vite via un petit point d'entrée dédié, `resources/js/game.js` :

```js
import Phaser from 'phaser';
window.Phaser = Phaser;
```

Ce fichier est déclaré comme entrée Vite dans `vite.config.js` et chargé avec `@vite(['resources/js/game.js'])` dans la vue. Ça évite de dépendre d'un CDN externe (qui pourrait être lent, bloqué, ou simplement indisponible) — toute la chaîne d'assets passe par le pipeline standard Laravel/Vite, ce qui est la bonne pratique attendue dans ce contexte.

### Structure de la scène de jeu (`StreetScene`)

C'est une classe qui étend `Phaser.Scene` (ligne ~445 de `game/index.blade.php`) avec les deux méthodes que tout jeu Phaser doit définir :

- **`create()`** : initialise la scène (sprites, textes, génération des premiers obstacles/pièces).
- **`update(time, delta)`** : appelée à chaque frame. C'est ici que se trouvent : le déplacement des objets vers le joueur, la détection de collision, la mise à jour du score et de la barre de menace ("le monstre").

### Les mécaniques de jeu (et les bugs corrigés)

Le jeu repose sur trois types d'objets qui défilent vers le joueur : des **pièces** à collecter, des obstacles **"jump"** (à éviter en sautant) et des obstacles **"lane"** (à éviter en changeant de voie). En parallèle, un "monstre" poursuit le joueur — une jauge de menace qui, si elle atteint zéro, termine la partie.

Deux bugs gênants ont été corrigés dans la boucle `update()` :

1. **La fenêtre de saut était trop courte.** L'ancien code ne vérifiait la collision que sur une plage très étroite de profondeur (`depth` entre 0.90 et 1.01), et marquait l'objet comme "mort" dès le premier contact — ce qui laissait environ une seule frame (~16 ms à 60 FPS) pour réussir son saut. Le nouveau code élargit la fenêtre de détection à toute la phase d'approche (`depth >= 0.78`) : sauter à n'importe quel moment pendant cette période évite l'obstacle, et un échec n'est constaté que lorsque l'obstacle est réellement passé sur le joueur sans qu'il ait sauté.
2. **La poursuite du monstre était inversée.** Le code mettait à jour `monsterDist` deux fois par frame avec des signes contradictoires (`+0.000042` puis `-0.000004`), et comme le terme positif était dix fois plus grand, la distance augmentait en moyenne avec le temps : le joueur devenait *plus en sécurité* au fil de la partie, ce qui contredit complètement la barre de menace affichée à l'écran (qui suggère une difficulté croissante). Le nouveau code applique une seule diminution progressive (`chaseRate`), et relie la mécanique d'esquive à la pression du monstre : réussir à esquiver un obstacle "jump" repousse légèrement le monstre. Ça donne un vrai sens de progression et de risque/récompense.

### La fin de partie

Quand la partie se termine (le monstre rattrape le joueur), le score est envoyé au serveur via une requête `fetch` en `POST` vers `/game/score` (avec le jeton CSRF dans les en-têtes — obligatoire pour toute requête qui modifie des données côté Laravel). Le serveur répond avec le nouveau solde de pièces et le type de coffre gagné, et le client affiche alors l'interface d'ouverture de coffre (`openChest`/`openChestRequest`/`closeChest`, en bas du fichier).

## 10. Les vues (Blade)

`resources/views/` contient :

- **`welcome.blade.php`** : page d'accueil publique.
- **`game/index.blade.php`** : la page de jeu (configuration + canvas Phaser).
- **`leaderboard/index.blade.php`** : classement public.
- **`collection/index.blade.php`** : galerie des personnages, avec indication "possédé / non possédé".
- **`profile/index.blade.php`** : statistiques de jeu du joueur (parties jouées, meilleur score, etc.).
- **`profile/edit.blade.php`** + ses partials : formulaires d'édition de compte (Breeze).
- **`auth/*`** : pages de connexion/inscription/mot de passe (Breeze).
- **`layouts/app.blade.php`** et **`layouts/guest.blade.php`** : layouts partagés (Breeze).
- **`components/*`** : petits composants Blade réutilisables fournis par Breeze (boutons, champs de formulaire, dropdowns...).

## 11. Tests automatisés (`tests/Feature/`)

Le projet contient une suite de **38 tests** (97 assertions) qui passent intégralement (`php artisan test`). Ils utilisent le trait `RefreshDatabase` (la base est recréée à chaque test, donc aucun risque de pollution entre les tests) et des **factories** pour générer des données réalistes (`database/factories/CharacterFactory.php`, `ScoreFactory.php`).

- **`GameTest`** (7 tests) : un invité ne peut pas accéder au jeu, la page se charge pour un utilisateur connecté, les personnages de base sont attachés automatiquement à la première visite, un score est bien sauvegardé et crédite des pièces, une partie en mode difficile donne un coffre légendaire, la validation rejette les données invalides, le classement est trié par score décroissant.
- **`ChestTest`** (6 tests) : un invité ne peut pas ouvrir de coffre, ouvrir un coffre normal coûte des pièces et donne un personnage, un solde insuffisant échoue *sans débiter* le joueur, un coffre légendaire est gratuit, le type de coffre est validé, un coffre sans personnage disponible rembourse le joueur.
- Les tests **`Auth/*`** et **`ProfileTest`** : générés par Breeze, vérifient les flux d'inscription/connexion/vérification d'email/édition et suppression de compte.

**Pourquoi ces tests sont importants à mentionner à l'oral** : ils ne se contentent pas de vérifier que "ça marche", ils vérifient des règles métier précises (par exemple : *"si le joueur n'a pas assez de pièces, son solde ne doit pas bouger"*). Ce sont exactement le genre de scénarios qu'un développeur professionnel doit anticiper et figer sous forme de tests, pour être sûr qu'une modification future ne casse pas une règle existante (non-régression).

## 12. Choix d'architecture à pouvoir justifier

- **Form Requests plutôt que validation inline** : sépare les responsabilités, rend les règles réutilisables et testables.
- **Transactions + verrous de ligne sur les opérations sensibles** (dépense de pièces) : protège contre les conditions de concurrence, un sujet souvent négligé par les débutants mais essentiel dès qu'une application gère de l'argent (même virtuel).
- **Eager loading (`with()`) sur les requêtes du leaderboard et du profil** : évite le problème classique du "N+1 requêtes" (charger 20 scores puis faire 20 requêtes supplémentaires pour leurs utilisateurs).
- **Index de base de données sur les colonnes utilisées dans les `ORDER BY`** : anticipe la montée en charge.
- **Bundling local des assets via Vite plutôt qu'un CDN** : le projet reste autonome et fonctionne hors-ligne / sans dépendre d'un service tiers.
- **Tests Feature couvrant les règles métier critiques** (pas seulement "la page s'affiche", mais "l'argent ne disparaît pas par erreur").

## 13. Questions/réponses pour l'oral — le "pourquoi" de chaque choix

L'examinateur ne demande presque jamais "qu'est-ce que fait ce code" (ça, il peut le lire). Il demande **"pourquoi tu as fait comme ça et pas autrement"**. Voici les questions les plus probables, formulées comme si on te les posait, avec la réponse à donner. Apprends le raisonnement, pas la phrase par cœur — comme ça tu peux répondre même si la question est tournée différemment.

> **Q : Pourquoi avoir utilisé une transaction (`DB::transaction`) avec un verrou (`lockForUpdate`) pour ouvrir un coffre, et pas ailleurs ?**
> R : Parce que c'est la seule opération du projet qui *lit* un solde, *décide* en fonction de cette lecture, puis *écrit* un nouveau solde — en plusieurs étapes. Entre la lecture et l'écriture, une autre requête peut s'intercaler (par exemple si le joueur double-clique très vite, ou triche en envoyant deux requêtes en parallèle). Sans protection, les deux requêtes liraient "j'ai 100 pièces", dépenseraient chacune 50, et le solde final serait 50 au lieu de 0 — le joueur aurait ouvert deux coffres pour le prix d'un. `lockForUpdate()` verrouille la ligne de l'utilisateur en base le temps de la transaction : la deuxième requête doit attendre que la première ait fini avant de pouvoir lire à son tour. Je n'ai pas eu besoin de ça pour `saveScore` ou `profile`, par exemple, parce que ce sont de simples insertions ou lectures qui ne dépendent pas d'un état "lu puis modifié".

> **Q : Pourquoi des Form Requests plutôt que `$request->validate([...])` directement dans le contrôleur ?**
> R : Deux raisons concrètes : (1) ça garde le contrôleur centré sur la logique métier — il reçoit déjà des données propres et n'a pas à se soucier de la validation ; (2) la classe de validation devient un objet à part entière, donc testable isolément et réutilisable si jamais une autre route a besoin des mêmes règles. Et niveau "boîte noire" : si la validation échoue, Laravel intercepte automatiquement l'exception et renvoie une réponse 422 avec les erreurs au bon format — je n'ai rien eu à écrire pour ça.

> **Q : Pourquoi limiter le score à 1 000 000 et la durée à 36 000 secondes dans `StoreScoreRequest` ?**
> R : Parce que ces valeurs viennent du client (le navigateur), et qu'on ne doit jamais faire confiance aux données envoyées par un client — un utilisateur peut très bien appeler l'API directement avec curl ou Postman et envoyer un score de 999999999999. Ces bornes ne rendent pas la triche impossible (un vrai anti-triche demanderait de recalculer le score côté serveur à partir d'événements de jeu), mais elles empêchent au minimum qu'une valeur absurde vienne polluer le classement public.

> **Q : Pourquoi `with('user', 'character')` dans la requête du leaderboard ?**
> R : Sans ça, Eloquent chargerait les 20 scores avec une requête, puis ferait une requête supplémentaire à chaque fois qu'on accède à `$score->user` ou `$score->character` dans la vue — soit potentiellement 41 requêtes au lieu d'une seule. C'est le problème classique du "N+1" : invisible sur une petite base de test, mais qui peut faire exploser les temps de réponse en production. `with()` indique à Eloquent de précharger ces relations en une poignée de requêtes groupées.

> **Q : Pourquoi avoir ajouté un index sur `scores.score` ?**
> R : Le leaderboard exécute `ORDER BY score DESC LIMIT 20` à chaque visite de la page. Sans index, la base doit lire et trier toutes les lignes de la table à chaque requête (un "full table scan") — ça passe inaperçu avec 50 lignes, mais devient lent avec 500 000. Un index sur `score` permet à la base de garder les données déjà triées et de récupérer directement le top 20 sans tout parcourir.

> **Q : Pourquoi avoir bundlé Phaser via npm/Vite plutôt que de garder le `<script src="cdn...">` ?**
> R : Un CDN externe ajoute une dépendance à un service tiers : si le CDN est lent, hors-ligne, ou bloqué par un pare-feu, le jeu ne se charge plus, sans qu'on y puisse rien. En le déclarant comme dépendance npm et en le faisant passer par le pipeline Vite (comme tout le reste des assets du projet), tout est versionné, reproductible, et fonctionne même sans connexion à un service externe. C'est aussi tout simplement la manière "standard Laravel" de gérer ses assets front.

> **Q : Pourquoi `character_id` est `nullable` avec `onDelete('set null')` dans `scores`, alors que `user_id` est en cascade ?**
> R : Ce sont deux relations avec un sens différent. Si un utilisateur est supprimé, ses scores n'ont plus de raison d'exister : `cascade` les supprime avec lui. Si en revanche un personnage est supprimé du catalogue (par exemple si on rééquilibre le jeu), ce serait dommage de perdre l'historique des parties jouées avec ce personnage — `set null` garde le score mais retire juste la référence au personnage supprimé. Le score reste consultable, juste sans personnage associé.

> **Q : Pourquoi une contrainte `unique(['user_id', 'character_id'])` sur la table pivot ?**
> R : Pour garantir au niveau base de données qu'un joueur ne peut pas posséder deux fois le même personnage. J'aurais pu vérifier ça uniquement en PHP avant l'`attach()`, mais une contrainte en base est une garantie beaucoup plus solide : même si un bug ou une requête concurrente contournait la vérification PHP, la base refuserait l'insertion en doublon. C'est le principe de ne jamais reposer une règle d'intégrité uniquement sur le code applicatif quand la base peut la garantir elle-même.

> **Q : Pourquoi le tirage des personnages utilise une somme cumulée plutôt qu'un simple `random()` ?**
> R : Parce que les personnages n'ont pas tous la même probabilité d'apparaître (certains sont à 40%, d'autres à 0.01%). Un `random()` simple donnerait une chance égale à chacun, ce qui casserait complètement l'idée de rareté. La technique de la somme cumulée ("weighted random") est l'algorithme standard pour respecter des poids différents : on construit des tranches proportionnelles aux probabilités sur une ligne numérique, on tire un nombre au hasard dessus, et la tranche dans laquelle il tombe désigne le gagnant. Plus une tranche est large (= probabilité élevée), plus elle a de chances d'être touchée.

> **Q : Pourquoi les personnages "de base" sont attachés dans `GameController::index()` plutôt qu'à l'inscription (dans le contrôleur d'auth de Breeze) ?**
> R : Parce que ça évite de toucher au code généré par Breeze (donc moins de risque de casser le flux d'authentification standard, et plus facile à mettre à jour plus tard), et que ça garantit la règle même pour des comptes créés autrement (seed, tinker, etc.) : à chaque visite de la page de jeu, on vérifie et on complète si besoin. C'est une logique "idempotente" — l'exécuter plusieurs fois ne change rien si c'est déjà fait (`hasCharacter()` vérifie avant d'attacher).

> **Q : Pourquoi utiliser `RefreshDatabase` et des factories dans les tests plutôt que de réutiliser la vraie base ?**
> R : Pour que chaque test parte d'un état propre et connu, sans dépendre de ce qu'un test précédent a laissé en base (ce qui rendrait les résultats imprévisibles et les bugs très durs à reproduire). `RefreshDatabase` recrée le schéma avant chaque test, et les factories génèrent des données réalistes à la volée (avec `fake()`), donc les tests sont rapides, isolés et reproductibles — exactement ce qu'on attend de tests automatisés professionnels.

## 14. Trois choses à présenter comme "ce que j'ai appris / ce qui pourrait intéresser le prof"

L'énoncé demandait d'apprendre au moins une chose au prof sur Laravel. Voici trois sujets du projet qui sortent du programme "standard" d'un Bachelor 3 et qui valent la peine d'être mis en avant à l'oral — présente-les comme une découverte que tu as faite en construisant le projet :

### a) Les race conditions et le verrouillage pessimiste (`lockForUpdate`)

C'est probablement le point le plus "avancé" du projet. La plupart des cours abordent les transactions (`DB::transaction`) pour le côté "tout ou rien" (atomicité), mais beaucoup moins le **verrouillage de lignes** pour gérer la concurrence. Le scénario concret à raconter : *"Si deux requêtes arrivent en même temps pour dépenser les pièces du même joueur, comment être sûr qu'on ne dépense pas deux fois le même argent ?"* Tu peux expliquer la différence entre :
- le **verrouillage pessimiste** (`lockForUpdate()`/`SELECT ... FOR UPDATE`) : on bloque l'accès concurrent dès la lecture, quitte à faire attendre l'autre requête — l'approche utilisée ici, adaptée parce que les conflits sont rares mais coûteux (de l'argent virtuel) ;
- le **verrouillage optimiste** (vérifier une version/un timestamp au moment d'écrire, et rejeter si quelqu'un d'autre a modifié entre-temps) : une alternative plus performante quand les conflits sont très rares.

C'est un sujet que beaucoup de développeurs débutants découvrent seulement en arrivant en entreprise — l'avoir anticipé dans un projet d'école est un vrai plus à mettre en avant.

### b) Le problème du N+1 et l'eager loading

Une bonne illustration concrète : *"Sans `with('user', 'character')`, afficher 20 scores sur le leaderboard déclenche 1 requête pour récupérer les scores, puis jusqu'à 40 requêtes supplémentaires (une par utilisateur, une par personnage) — soit 41 requêtes au lieu d'une poignée."* C'est un piège extrêmement courant avec les ORM (pas seulement Eloquent — Doctrine, Hibernate, etc. ont exactement le même problème), souvent invisible en développement (peu de données) et catastrophique en production. Le fait de l'avoir identifié et corrigé avec `with()` montre une compréhension de ce qui se passe *réellement* derrière l'ORM, pas juste de la façade.

### c) Form Requests : la validation comme objet à part entière

Beaucoup de projets étudiants valident "en ligne" dans le contrôleur (`$request->validate([...])`). Présenter les Form Requests (`StoreScoreRequest`, `OpenChestRequest`) comme une étape au-delà : la validation devient une classe autonome, injectée automatiquement par le conteneur de service de Laravel via le type-hint dans la signature de la méthode (`saveScore(StoreScoreRequest $request)`). Tu peux expliquer que Laravel **résout cette dépendance automatiquement** — c'est un exemple concret d'**injection de dépendances**, un concept qui revient dans énormément de frameworks (Symfony, Spring, Angular...) et que la plupart des étudiants utilisent sans avoir conscience de ce qui se passe en coulisses.

*(Astuce pour l'oral : si le prof demande "comment Laravel sait quelle classe instancier pour `$request`", c'est exactement le moment de placer ce sujet — ça montre que tu ne te contentes pas de copier un pattern, tu sais pourquoi il fonctionne.)*

## 15. Pistes d'amélioration possibles (si on te demande "et après ?")

- Ajouter un système d'amis / défis entre joueurs.
- Ajouter une pagination sur le leaderboard (actuellement limité au top 20).
- Ajouter un système de niveaux ou de saisons pour renouveler l'intérêt du classement.
- Écrire des tests unitaires sur `rollCharacter()` pour vérifier statistiquement la distribution des tirages sur un grand nombre d'essais.
- Passer le moteur de jeu en TypeScript pour un typage plus strict du code Phaser.

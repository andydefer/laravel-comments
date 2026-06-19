# Laravel Comments

> Système de commentaires polymorphiques pour applications Laravel

Un package Laravel complet pour gérer des commentaires polymorphiques avec le pattern Repository, des DTOs, des Value Objects, une modération intégrée et un système de réponses.

---

## 📋 Table des matières

- [Fonctionnalités](#fonctionnalités)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
  - [Ajouter un commentaire](#ajouter-un-commentaire)
  - [Ajouter une réponse](#ajouter-une-réponse)
  - [Récupérer des commentaires](#récupérer-des-commentaires)
  - [Mettre à jour un commentaire](#mettre-à-jour-un-commentaire)
  - [Supprimer un commentaire](#supprimer-un-commentaire)
  - [Modération des commentaires](#modération-des-commentaires)
  - [Compter les commentaires](#compter-les-commentaires)
- [Référence de l'API](#référence-de-lapi)
- [Statuts des commentaires](#statuts-des-commentaires)
- [Value Objects](#value-objects)
- [Structure de la base de données](#structure-de-la-base-de-données)
- [Tests](#tests)
- [Journal des modifications](#journal-des-modifications)
- [Contribuer](#contribuer)
- [Licence](#licence)

---

## ✨ Fonctionnalités

- ✅ **Double polymorphisme** - Commentez n'importe quel modèle avec n'importe quel auteur
- ✅ **Système de réponses** - Commentaires imbriqués avec support des threads
- ✅ **Modération intégrée** - Statuts : Publié, Masqué, Signalé
- ✅ **Pattern Repository** - Séparation propre de la logique d'accès aux données
- ✅ **Support des DTOs** - Objets de transfert de données typés
- ✅ **Value Objects** - DateTime, Métadonnées
- ✅ **Support des métadonnées** - Stockez des données supplémentaires au format JSON
- ✅ **Suppression douce** - Suppression sécurisée avec possibilité de restauration
- ✅ **Filtrage avancé** - Filtrez par auteur, objet, parent, statut
- ✅ **Tests complets** - Couverture complète des tests d'intégration

---

## 🚀 Prérequis

- PHP 8.2 ou supérieur
- Laravel 12.0, 13.0, 14.0 ou 15.0

---

## 📦 Installation

Installez le package via Composer :

```bash
composer require andydefer/laravel-comments
```

### Publier les migrations

```bash
php artisan vendor:publish --tag=Comments-migrations
```

### Exécuter les migrations

```bash
php artisan migrate
```

---

## ⚙️ Configuration

Le package est automatiquement découvert par Laravel. Aucune configuration supplémentaire n'est requise.

Si vous devez personnaliser le Service Provider, ajoutez-le manuellement dans `config/app.php` :

```php
'providers' => [
    // ...
    AndyDefer\LaravelComments\CommentsServiceProvider::class,
],
```

---

## 📖 Utilisation

### Ajouter un commentaire

```php
use AndyDefer\LaravelComments\Services\CommentService;

class PostController extends Controller
{
    public function store(CommentService $commentService)
    {
        $user = auth()->user();
        $post = Post::find(1);

        // Ajouter un commentaire
        $comment = $commentService->add(
            commenter: $user,      // Qui commente
            commentable: $post,    // Quoi est commenté
            content: 'Super article !'
        );

        return $comment;
    }
}
```

### Ajouter une réponse

```php
// Ajouter une réponse à un commentaire existant
$reply = $commentService->add(
    commenter: $user,
    commentable: $post,
    content: 'Merci pour ce retour !',
    parentId: $comment->id  // ID du commentaire parent
);
```

### Récupérer des commentaires

#### Tous les commentaires d'un modèle

```php
// Récupère uniquement les commentaires publiés par défaut
$comments = $commentService->get($post);

// Récupère tous les commentaires (incluant masqués et signalés)
$allComments = $commentService->get($post, false);
```

#### Réponses d'un commentaire

```php
$replies = $commentService->getReplies($commentId);
```

#### Commentaires d'un auteur

```php
$userComments = $commentService->getByCommenter($user);
```

#### Trouver un commentaire par ID

```php
$comment = $commentService->find($commentId);
```

### Mettre à jour un commentaire

```php
$updated = $commentService->update($commentId, 'Nouveau contenu modifié');
```

### Supprimer un commentaire

```php
$commentService->delete($commentId);
```

### Modération des commentaires

#### Masquer un commentaire

```php
$hidden = $commentService->hide($commentId);
// Le statut passe à 'hidden'
```

#### Publier un commentaire

```php
$published = $commentService->publish($commentId);
// Le statut passe à 'published'
```

#### Signaler un commentaire

```php
$flagged = $commentService->flag($commentId);
// Le statut passe à 'flagged'
```

### Compter les commentaires

```php
// Compter les commentaires publiés d'un modèle
$count = $commentService->count($post);

// Compter tous les commentaires (incluant masqués et signalés)
$total = $commentService->count($post, false);

// Compter les commentaires signalés
$flaggedCount = $commentService->countFlagged();

// Compter les commentaires masqués
$hiddenCount = $commentService->countHidden();

// Compter les commentaires publiés
$publishedCount = $commentService->countPublished();
```

---

## 📚 Référence de l'API

### CommentService

| Méthode | Description | Retourne |
|---------|-------------|----------|
| `add(Model $commenter, Model $commentable, string $content, ?int $parentId = null)` | Créer un commentaire | `Model` |
| `update(int $commentId, string $content)` | Mettre à jour le contenu | `Model` |
| `delete(int $commentId)` | Supprimer un commentaire | `void` |
| `hide(int $commentId)` | Masquer un commentaire | `Model` |
| `publish(int $commentId)` | Publier un commentaire | `Model` |
| `flag(int $commentId)` | Signaler un commentaire | `Model` |
| `get(Model $commentable, bool $onlyPublished = true)` | Récupérer les commentaires | `Collection` |
| `getReplies(int $parentId, bool $onlyPublished = true)` | Récupérer les réponses | `Collection` |
| `getByCommenter(Model $commenter)` | Récupérer par auteur | `Collection` |
| `find(int $commentId)` | Trouver par ID | `?Model` |
| `count(Model $commentable, bool $onlyPublished = true)` | Compter les commentaires | `int` |
| `countFlagged()` | Compter les signalés | `int` |
| `countHidden()` | Compter les masqués | `int` |
| `countPublished()` | Compter les publiés | `int` |

---

## 🏷️ Statuts des commentaires

| Statut | Valeur | Description | Méthode associée |
|--------|--------|-------------|------------------|
| `PUBLISHED` | `'published'` | Commentaire visible | `publish()` |
| `HIDDEN` | `'hidden'` | Commentaire masqué | `hide()` |
| `FLAGGED` | `'flagged'` | Commentaire signalé | `flag()` |

### Méthodes du statut

```php
use AndyDefer\LaravelComments\Enums\CommentStatus;

$status = CommentStatus::PUBLISHED;

$status->getLabel();    // 'Publié'
$status->isPublished(); // true
$status->isHidden();    // false
$status->isFlagged();   // false
```

---

## 🎯 Value Objects

Le package supporte les Value Objects suivants :

| Value Object | Description | Exemple |
|--------------|-------------|---------|
| `DateTimeVO` | Date/heure | `DateTimeVO::from('2024-01-01 12:00:00')` |
| `StrictDataObject` | Métadonnées typées | `StrictDataObject::from(['key' => 'value'])` |

### Accesseurs dans le modèle Comment

```php
$comment = Comment::find(1);

// Accès sous forme de Value Objects
$createdAt = $comment->getCreatedAt();    // DateTimeVO
$updatedAt = $comment->getUpdatedAt();    // DateTimeVO
$metadata = $comment->getMetadata();      // StrictDataObject

// Relations
$commenter = $comment->commenter;    // Auteur (User, Admin, etc.)
$commentable = $comment->commentable; // Objet commenté (Post, Article, etc.)
$parent = $comment->parent;           // Commentaire parent
$replies = $comment->replies;         // Réponses (HasMany)
```

---

## 📝 Structure de la base de données

```sql
CREATE TABLE comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    commenter_type VARCHAR(255) NOT NULL,   -- Type de l'auteur
    commenter_id BIGINT UNSIGNED NOT NULL,  -- ID de l'auteur
    commentable_type VARCHAR(255) NOT NULL, -- Type de l'objet commenté
    commentable_id BIGINT UNSIGNED NOT NULL,-- ID de l'objet commenté
    content TEXT NOT NULL,                  -- Contenu du commentaire
    parent_id BIGINT UNSIGNED NULL,         -- Commentaire parent (réponses)
    status VARCHAR(20) DEFAULT 'published', -- Statut du commentaire
    metadata JSON NULL,                     -- Métadonnées
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_commenter (commenter_type, commenter_id),
    INDEX idx_commentable (commentable_type, commentable_id),
    INDEX idx_parent (parent_id),
    INDEX idx_status (status)
);
```

---

## 🔍 Exemple complet

```php
use AndyDefer\LaravelComments\Services\CommentService;
use AndyDefer\LaravelComments\Enums\CommentStatus;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService
    ) {}

    public function store(Request $request, Post $post)
    {
        $comment = $this->commentService->add(
            commenter: $request->user(),
            commentable: $post,
            content: $request->input('content')
        );

        return response()->json($comment, 201);
    }

    public function index(Post $post)
    {
        $comments = $this->commentService->get($post);

        return response()->json($comments);
    }

    public function reply(Request $request, Post $post, Comment $parent)
    {
        $reply = $this->commentService->add(
            commenter: $request->user(),
            commentable: $post,
            content: $request->input('content'),
            parentId: $parent->id
        );

        return response()->json($reply, 201);
    }

    public function moderate(Comment $comment, string $action)
    {
        $result = match($action) {
            'hide' => $this->commentService->hide($comment->id),
            'flag' => $this->commentService->flag($comment->id),
            'publish' => $this->commentService->publish($comment->id),
            'delete' => $this->commentService->delete($comment->id),
            default => throw new \InvalidArgumentException('Invalid action')
        };

        return response()->json(['success' => true, 'comment' => $result]);
    }

    public function stats(Post $post)
    {
        return response()->json([
            'total' => $this->commentService->count($post, false),
            'published' => $this->commentService->count($post, true),
            'flagged' => $this->commentService->countFlagged(),
            'hidden' => $this->commentService->countHidden(),
        ]);
    }
}
```

---

## 🧪 Tests

### Exécuter les tests

```bash
composer test
```

### Exécuter uniquement les tests unitaires

```bash
composer test-unit
```

### Exécuter uniquement les tests d'intégration

```bash
composer test-integration
```

### Configuration des tests

Le package utilise `orchestra/testbench` pour les tests d'intégration avec une base de données SQLite en mémoire.

---

## 🔧 Développement

### Style de code

```bash
./vendor/bin/pint
```

### Analyse statique

```bash
./vendor/bin/phpstan analyse
./vendor/bin/psalm
```

---

## 📄 Journal des modifications

Veuillez consulter le [CHANGELOG](CHANGELOG.md) pour plus d'informations sur les modifications récentes.

---

## 🤝 Contribuer

Veuillez consulter [CONTRIBUTING](CONTRIBUTING.md) pour plus de détails.

### Flux de développement

1. Forkez le dépôt
2. Créez une branche de fonctionnalité (`git checkout -b feature/amazing-feature`)
3. Apportez vos modifications
4. Exécutez les tests (`composer test`)
5. Committez vos modifications (`git commit -m 'Ajouter une fonctionnalité géniale'`)
6. Poussez vers la branche (`git push origin feature/amazing-feature`)
7. Ouvrez une Pull Request

---

## 📦 Dépendances

- [`andydefer/php-vo`](https://github.com/andydefer/php-vo) - Value Objects
- [`andydefer/laravel-repository`](https://github.com/andydefer/laravel-repository) - Implémentation du pattern Repository
- [`andydefer/domain-structures`](https://github.com/andydefer/domain-structures) - Structures de domaine (AbstractRecord, AbstractData)

---

## 👨‍💻 Auteur

**Andy Kani**
- GitHub: [@andydefer](https://github.com/andydefer)
- Email: andykanidimbu@gmail.com

---


## ⭐ Support

Si vous trouvez ce package utile, n'hésitez pas à lui donner une ⭐ sur GitHub !

---

## 🙏 Remerciements

- Framework Laravel
- Tous les contributeurs et utilisateurs de ce package

---

**Construit avec ❤️ pour la communauté Laravel**
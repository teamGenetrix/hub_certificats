# Guide d'Authentification - Hub Certificats

## 📋 Vue d'ensemble

Le système d'authentification protège l'accès à toutes les fonctionnalités d'administration du Hub Certificats.

## 🔐 Configuration initiale

### 1. Créer l'utilisateur administrateur

Après avoir configuré votre base de données, exécutez les seeders pour créer l'utilisateur admin par défaut :

```bash
php artisan db:seed --class=AdminUserSeeder
```

Ou pour réinitialiser complètement la base de données :

```bash
php artisan migrate:fresh --seed
```

### 2. Identifiants par défaut

**⚠️ IMPORTANT : Ces identifiants doivent être changés en production !**

- **Email** : `admin@genetrix.com`
- **Mot de passe** : `admin123`

## 🚀 Utilisation

### Connexion

1. Accédez à l'application via votre navigateur
2. Vous serez automatiquement redirigé vers la page de connexion (`/login`)
3. Entrez vos identifiants
4. Cochez "Se souvenir de moi" si vous souhaitez rester connecté

### Déconnexion

Cliquez sur l'icône utilisateur en haut à droite, puis sur "Déconnexion"

## 🔒 Sécurité

### Routes protégées

Toutes les routes sous le préfixe `/admin` sont protégées par le middleware `auth`. Les utilisateurs non authentifiés sont automatiquement redirigés vers la page de connexion.

### Changer le mot de passe

Pour changer le mot de passe d'un utilisateur, utilisez Laravel Tinker :

```bash
php artisan tinker
```

Puis exécutez :

```php
$user = App\Models\User::where('email', 'admin@genetrix.com')->first();
$user->password = Hash::make('nouveau_mot_de_passe');
$user->save();
```

### Créer un nouvel utilisateur

Via Tinker :

```bash
php artisan tinker
```

```php
App\Models\User::create([
    'name' => 'Nom Utilisateur',
    'email' => 'email@example.com',
    'password' => Hash::make('mot_de_passe_securise')
]);
```

## 📝 Fonctionnalités

- ✅ Authentification par email/mot de passe
- ✅ Option "Se souvenir de moi"
- ✅ Protection automatique de toutes les routes admin
- ✅ Menu utilisateur avec informations et déconnexion
- ✅ Redirection automatique après connexion
- ✅ Messages d'erreur en français
- ✅ Interface responsive et moderne

## 🛠️ Structure technique

### Fichiers créés/modifiés

- `app/Http/Controllers/Auth/LoginController.php` - Contrôleur d'authentification
- `resources/views/auth/login.blade.php` - Page de connexion
- `resources/views/partials/header.blade.php` - Menu utilisateur avec déconnexion
- `database/seeders/AdminUserSeeder.php` - Seeder pour l'utilisateur admin
- `routes/web.php` - Routes d'authentification et protection middleware

### Middleware appliqué

Le middleware `auth` est appliqué à toutes les routes du groupe `/admin`, ce qui garantit que seuls les utilisateurs authentifiés peuvent y accéder.

## 🔄 Maintenance

### Réinitialiser le mot de passe admin

Si vous avez oublié le mot de passe admin :

```bash
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'admin@genetrix.com')->first();
$user->password = Hash::make('admin123');
$user->save();
```

### Vérifier les utilisateurs existants

```bash
php artisan tinker
```

```php
App\Models\User::all();
```

## ⚠️ Recommandations de sécurité

1. **Changez immédiatement le mot de passe par défaut** en production
2. Utilisez des mots de passe forts (minimum 12 caractères, avec majuscules, minuscules, chiffres et symboles)
3. Ne partagez jamais vos identifiants
4. Déconnectez-vous toujours après utilisation sur un ordinateur partagé
5. Envisagez d'ajouter l'authentification à deux facteurs pour une sécurité renforcée

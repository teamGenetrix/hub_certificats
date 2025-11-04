# Hub Certificats - Installation et Configuration

## Vue d'ensemble

Application Laravel pour la gestion centralisée des certificats et attestations de Genetrix Academy avec génération automatique de numéros de référence.

## Prérequis

- PHP 8.2 ou supérieur
- Composer
- MySQL 8.0 ou supérieur
- Node.js et NPM (pour les assets front-end)

## Installation

### 1. Cloner le projet

```bash
git clone <repository-url>
cd hub-certificats
```

### 2. Installer les dépendances

```bash
# Dépendances PHP
composer install

# Dépendances JavaScript
npm install
```

### 3. Configuration de l'environnement

```bash
# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate
```

### 4. Configuration de la base de données

Éditer le fichier `.env` avec vos paramètres de base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hub_certificats
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

### 5. Exécuter les migrations

```bash
# Créer les tables
php artisan migrate

# Peupler la base de données avec les piliers et formations
php artisan db:seed
```

### 6. Compiler les assets

```bash
# Développement
npm run dev

# Production
npm run build
```

### 7. Lancer le serveur

```bash
php artisan serve
```

L'application sera accessible à : `http://localhost:8000`

## Structure de la Base de Données

### Tables Principales

1. **pillars** - Les 8 piliers de l'excellence opérationnelle
2. **trainings** - Catalogue des formations par pilier
3. **sessions** - Sessions de formation avec dates et type de livraison
4. **participants** - Informations des participants
5. **enrollments** - Inscriptions des participants aux sessions
6. **references** - Numéros de référence générés (certificats/attestations)
7. **legacy_aliases** - Redirection des anciennes références vers les nouvelles

## Utilisation

### Accès Admin

Par défaut, un utilisateur admin est créé :
- **Email** : admin@genetrix.com
- **Mot de passe** : Définir via `php artisan tinker` ou modifier le seeder

### Workflow de Génération de Références

1. **Accéder au tableau de bord** : `/admin/references`
2. **Créer une nouvelle génération** : `/admin/references/create`
3. **Suivre le workflow en 4 étapes** :
   - Sélectionner le pilier
   - Choisir le type de formation
   - Sélectionner la formation et la session
   - Choisir les participants et générer

### Vérification de Certificat

Les participants peuvent vérifier l'authenticité de leur certificat :
- URL : `/admin/references/verify`
- Saisir le numéro de référence complet

### Export des Données

Exporter toutes les références en CSV :
- URL : `/admin/references/export`

## Format des Numéros de Référence

```
(codepays)_(doc_kind)_(codeformation)_numbermois_annee_(rangpilier)_(rangglobal)
```

**Exemple** : `BJ_CERT_AMCONT-007-F_112024_0001_00042`

- **BJ** : Bénin (code pays)
- **CERT** : Certificat (ou ATT pour Attestation)
- **AMCONT-007-F** : Code de la formation
- **112024** : Novembre 2024
- **0001** : 1er formé du pilier ce mois
- **00042** : 42ème formé global Genetrix ce mois

## Gestion des Formations Personnalisées

Pour ajouter une formation custom :

```php
Training::create([
    'uuid' => Str::uuid(),
    'code' => 'CUSTOM-CLIENT-001',
    'title' => 'Formation Spéciale Client XYZ',
    'has_exam' => false,
    'is_custom' => true,
    'custom_code' => 'CLIENT-XYZ-2024',
    'pillar_id' => $pillar_id,
]);
```

## Gestion des Alias (Anciennes Références)

Pour rediriger une ancienne référence :

```php
use App\Models\LegacyAlias;
use Illuminate\Support\Str;

LegacyAlias::create([
    'uuid' => Str::uuid(),
    'old_reference' => 'ANCIEN-FORMAT-123',
    'reference_id' => $nouvelle_reference_id,
]);
```

## API Endpoints

### Endpoints AJAX (Admin)

```
GET /admin/references/trainings?pillar_id={id}
GET /admin/references/sessions?training_id={id}&delivery_type={type}
GET /admin/references/enrollments?session_id={id}
```

### Génération

```
POST /admin/references/generate
Body: {
    "enrollment_ids": [1, 2, 3],
    "country_code": "BJ"
}
```

## Commandes Artisan Utiles

```bash
# Réinitialiser la base de données
php artisan migrate:fresh --seed

# Créer un nouveau pilier
php artisan tinker
>>> Pillar::create(['uuid' => Str::uuid(), 'name' => 'Nouveau Pilier', 'code' => 'NEWPIL'])

# Vérifier les références générées
php artisan tinker
>>> Reference::count()
>>> Reference::where('doc_kind', 'CERT')->count()
```

## Maintenance

### Backup des Références

Exporter régulièrement les références via l'interface web ou :

```bash
php artisan tinker
>>> $service = app(\App\Services\ReferenceGeneratorService::class);
>>> $data = $service->exportToArray();
>>> // Sauvegarder $data
```

### Logs

Les logs sont stockés dans : `storage/logs/laravel.log`

## Sécurité

1. **Ne jamais committer le fichier `.env`**
2. **Utiliser des mots de passe forts** pour la base de données
3. **Activer HTTPS** en production
4. **Configurer les permissions** appropriées sur `storage/` et `bootstrap/cache/`

```bash
chmod -R 775 storage bootstrap/cache
```

## Support

Pour toute question ou problème :
- Consulter la documentation : `REFERENCE_SYSTEM_GUIDE.md`
- Vérifier les logs : `storage/logs/laravel.log`
- Contacter l'équipe technique Genetrix Academy

## Développement

### Tests

```bash
# Exécuter les tests
php artisan test
```

### Code Style

```bash
# Formater le code
./vendor/bin/pint
```

## Déploiement en Production

1. Configurer les variables d'environnement
2. Optimiser l'application :

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

3. Configurer le serveur web (Nginx/Apache)
4. Configurer le supervisor pour les queues (si utilisées)
5. Activer HTTPS avec Let's Encrypt

---

**Version** : 1.0  
**Date** : Novembre 2024  
**Équipe** : Genetrix Academy

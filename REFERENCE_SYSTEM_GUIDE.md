# Guide du Système de Génération de Références

## Vue d'ensemble

Ce système permet de générer automatiquement des numéros de référence uniques pour les certificats et attestations délivrés par Genetrix Academy.

## Format des Numéros de Référence

Les numéros de référence suivent ce format standardisé :

```
(codepays)_(doc_kind)_(codeformation)_numbermois_annee_(rangpilier)_(rangglobal)
```

### Exemple
```
BJ_CERT_AMCONT-007-F_112024_0001_00042
```

### Décomposition
- **BJ** : Code pays (2 lettres)
- **CERT** : Type de document (CERT = Certificat, ATT = Attestation)
- **AMCONT-007-F** : Code de la formation
- **112024** : Mois et année (novembre 2024)
- **0001** : Rang du formé dans le pilier (4 chiffres)
- **00042** : Rang global tous formés Genetrix (5 chiffres)

## Architecture du Système

### 1. Service Principal : `ReferenceGeneratorService`

Le service principal situé dans `app/Services/ReferenceGeneratorService.php` gère :

- **Génération de références** pour un participant, une session ou plusieurs inscriptions
- **Compteurs automatiques** par pilier et global
- **Vérification** de l'authenticité des références
- **Export** des données en CSV/Excel
- **Statistiques** sur les références générées

#### Méthodes principales

```php
// Générer pour une inscription
$reference = $service->generateForEnrollment($enrollment, 'BJ');

// Générer pour toute une session
$references = $service->generateForSession($session, 'BJ');

// Générer pour plusieurs inscriptions
$references = $service->generateForEnrollments([1, 2, 3], 'BJ');

// Vérifier une référence
$reference = $service->verifyReference('BJ_CERT_AMCONT-007-F_112024_0001_00042');

// Obtenir les statistiques
$stats = $service->getStatistics();

// Exporter en CSV
$data = $service->exportToArray([1, 2, 3]);
```

### 2. Contrôleur : `ReferenceController`

Le contrôleur situé dans `app/Http/Controllers/ReferenceController.php` gère :

- Interface de génération avec workflow en 4 étapes
- Endpoints AJAX pour chargement dynamique
- Vérification publique des références
- Export des données

### 3. Modèles de Données

#### Reference
```php
- uuid : Identifiant unique
- doc_kind : Type (CERT/ATT)
- reference : Numéro de référence complet
- increment_no : Numéro d'incrément global
- enrollment_id : Lien vers l'inscription
- meta : Métadonnées JSON (pilier, formation, participant, etc.)
```

#### LegacyAlias
```php
- uuid : Identifiant unique
- old_reference : Ancienne référence
- reference_id : Lien vers la nouvelle référence
```

Ce modèle permet de rediriger les anciennes références vers les nouvelles.

## Workflow de Génération

### Interface Admin (4 étapes)

1. **Sélection du Pilier**
   - Choisir parmi les 8 piliers de l'excellence opérationnelle

2. **Type de Formation**
   - Inter-entreprise
   - Intra-entreprise
   - En ligne
   - Blending (Hybride)

3. **Sélection Formation et Session**
   - Choisir la formation spécifique du pilier
   - Sélectionner la session concernée

4. **Sélection des Participants**
   - Cocher les participants pour lesquels générer les références
   - Saisir le code pays (par défaut : BJ)
   - Générer les références

### Fonctionnalités Clés

- **Détection automatique** : Le système détecte si une référence existe déjà
- **Génération par lot** : Possibilité de générer pour plusieurs participants simultanément
- **Compteurs intelligents** : Incrémentation automatique par pilier et globale
- **Type de document** : Certificat si examen, Attestation sinon

## Routes Disponibles

### Interface Admin

```
GET  /admin/references              → Tableau de bord
GET  /admin/references/create       → Interface de génération
POST /admin/references/generate     → Générer des références
GET  /admin/references/verify       → Formulaire de vérification
POST /admin/references/verify       → Vérifier une référence
GET  /admin/references/export       → Exporter en CSV
```

### Endpoints AJAX

```
GET /admin/references/trainings     → Formations par pilier
GET /admin/references/sessions      → Sessions par formation
GET /admin/references/enrollments   → Inscriptions par session
```

## Gestion des Alias (Anciennes Références)

Pour les certificats délivrés avant la mise en place du nouveau système :

1. Créer une nouvelle référence dans le système
2. Créer un alias dans la table `legacy_aliases`
3. L'ancienne référence redirigera automatiquement vers la nouvelle

### Exemple

```php
// Créer la nouvelle référence
$newReference = Reference::create([...]);

// Créer l'alias
LegacyAlias::create([
    'uuid' => Str::uuid(),
    'old_reference' => 'ANCIEN-FORMAT-123',
    'reference_id' => $newReference->id,
]);
```

## Vérification Publique

Les participants peuvent vérifier l'authenticité de leur certificat via l'interface de vérification.

### Informations Affichées

- Validité du certificat
- Informations du participant
- Détails de la formation
- Date de session
- Pilier d'excellence

## Export des Données

L'export CSV inclut :

- Numéro de référence
- Type de document
- Nom du participant
- Email et téléphone
- Entreprise
- Formation et pilier
- Dates de session
- Date de génération

## Statistiques

Le tableau de bord affiche :

- **Total des références** générées
- **Répartition** Certificats vs Attestations
- **Statistiques par pilier**
- **Activité par mois**

## Sécurité et Bonnes Pratiques

1. **Unicité garantie** : Les numéros sont uniques grâce aux compteurs
2. **Transactions** : Génération en transaction pour éviter les doublons
3. **Validation** : Vérification des données avant génération
4. **Traçabilité** : Métadonnées complètes stockées en JSON
5. **Audit** : Timestamps de création et modification

## Personnalisation

### Ajouter un Nouveau Pays

Modifier simplement le code pays lors de la génération :

```php
$service->generateForEnrollment($enrollment, 'TG'); // Togo
$service->generateForEnrollment($enrollment, 'CI'); // Côte d'Ivoire
```

### Formations Personnalisées

Les formations custom utilisent le champ `custom_code` au lieu de `code` :

```php
Training::create([
    'code' => 'CUSTOM-001',
    'custom_code' => 'CLIENT-SPECIAL-2024',
    'is_custom' => true,
    ...
]);
```

## Maintenance

### Réinitialiser les Compteurs (Nouvelle Année)

Les compteurs sont automatiquement réinitialisés chaque mois. Aucune action manuelle requise.

### Backup des Références

Exporter régulièrement les références via l'interface d'export pour archivage.

## Support

Pour toute question ou problème :
- Vérifier les logs Laravel : `storage/logs/laravel.log`
- Consulter la documentation du code source
- Contacter l'équipe technique Genetrix Academy

---

**Version** : 1.0  
**Date** : Novembre 2024  
**Auteur** : Genetrix Academy - Hub Certificats

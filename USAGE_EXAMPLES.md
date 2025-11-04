# Exemples d'Utilisation du Service de Génération de Références

## 1. Génération Simple pour un Participant

```php
use App\Services\ReferenceGeneratorService;
use App\Models\Enrollment;

$service = app(ReferenceGeneratorService::class);
$enrollment = Enrollment::find(1);

// Générer une référence pour le Bénin
$reference = $service->generateForEnrollment($enrollment, 'CI');

echo $reference->reference;
// Output: CI_CERT_AMCONT-007-F_112024_0001_00042
```

## 2. Génération pour une Session Complète

```php
use App\Services\ReferenceGeneratorService;
use App\Models\Session;

$service = app(ReferenceGeneratorService::class);
$session = Session::find(1);

// Générer pour tous les participants de la session
$references = $service->generateForSession($session, 'TG');

foreach ($references as $reference) {
    echo "{$reference->enrollment->participant->full_name}: {$reference->reference}\n";
}
```

## 3. Génération pour Plusieurs Participants Sélectionnés

```php
use App\Services\ReferenceGeneratorService;

$service = app(ReferenceGeneratorService::class);

// IDs des inscriptions sélectionnées
$enrollmentIds = [1, 2, 3, 4, 5];

// Générer pour la Côte d'Ivoire
$references = $service->generateForEnrollments($enrollmentIds, 'CI');

echo "Généré " . count($references) . " références";
```

## 4. Vérification d'une Référence

```php
use App\Services\ReferenceGeneratorService;

$service = app(ReferenceGeneratorService::class);

$referenceNumber = 'CI_CERT_AMCONT-007-F_112024_0001_00042';
$reference = $service->verifyReference($referenceNumber);

if ($reference) {
    echo "Référence valide!\n";
    echo "Participant: {$reference->enrollment->participant->full_name}\n";
    echo "Formation: {$reference->enrollment->session->training->title}\n";
} else {
    echo "Référence invalide ou introuvable";
}
```

## 5. Obtenir les Statistiques

```php
use App\Services\ReferenceGeneratorService;

$service = app(ReferenceGeneratorService::class);
$stats = $service->getStatistics();

echo "Total références: {$stats['total_references']}\n";
echo "Certificats: {$stats['total_certificates']}\n";
echo "Attestations: {$stats['total_attestations']}\n";

echo "\nPar pilier:\n";
foreach ($stats['by_pillar'] as $stat) {
    echo "- {$stat->name}: {$stat->count}\n";
}
```

## 6. Export des Données

```php
use App\Services\ReferenceGeneratorService;

$service = app(ReferenceGeneratorService::class);

// Exporter toutes les références
$allData = $service->exportToArray();

// Exporter des références spécifiques
$specificData = $service->exportToArray([1, 2, 3]);

// Sauvegarder en CSV
$filename = 'references_' . date('Y-m-d') . '.csv';
$file = fopen($filename, 'w');

// Ajouter BOM UTF-8
fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

foreach ($allData as $row) {
    fputcsv($file, $row, ';');
}

fclose($file);
```

## 7. Créer un Alias pour Ancienne Référence

```php
use App\Models\LegacyAlias;
use App\Models\Reference;
use Illuminate\Support\Str;

// Trouver la nouvelle référence
$newReference = Reference::where('reference', 'CI_CERT_AMCONT-007-F_112024_0001_00042')->first();

// Créer l'alias
LegacyAlias::create([
    'uuid' => Str::uuid(),
    'old_reference' => 'LSS-YB-2020-001',
    'reference_id' => $newReference->id,
]);

// Maintenant, la vérification de l'ancienne référence redirigera vers la nouvelle
```

## 8. Créer une Formation Personnalisée

```php
use App\Models\Training;
use App\Models\Pillar;
use Illuminate\Support\Str;

$pillar = Pillar::where('code', 'AMCONT')->first();

$customTraining = Training::create([
    'uuid' => Str::uuid(),
    'code' => 'CUSTOM-ACME-001',
    'title' => 'Formation Excellence Opérationnelle ACME Corp',
    'has_exam' => true,
    'is_custom' => true,
    'custom_code' => 'ACME-EXO-2024',
    'description' => 'Formation sur mesure pour ACME Corporation',
    'pillar_id' => $pillar->id,
]);

// Les références générées utiliseront 'ACME-EXO-2024' au lieu de 'CUSTOM-ACME-001'
```

## 9. Créer une Session de Formation

```php
use App\Models\Session;
use App\Models\Training;
use Illuminate\Support\Str;

$training = Training::where('code', 'AMCONT-007-F')->first();

$session = Session::create([
    'uuid' => Str::uuid(),
    'training_id' => $training->id,
    'delivery_type' => 'inter-entreprise',
    'start_date' => '2024-11-15',
    'end_date' => '2024-11-17',
    'duration' => 3,
    'location' => 'Abidjan, Côte d\'Ivoire',
]);
```

## 10. Inscrire des Participants à une Session

```php
use App\Models\Enrollment;
use App\Models\Participant;
use App\Models\Session;
use Illuminate\Support\Str;

$session = Session::find(1);

// Créer un participant
$participant = Participant::create([
    'uuid' => Str::uuid(),
    'first_name' => 'Jean',
    'last_name' => 'Dupont',
    'email' => 'jean.dupont@example.com',
    'phone' => '+229 12 34 56 78',
    'company' => 'ACME Corporation',
    'job_title' => 'Responsable Qualité',
    'country' => 'Bénin',
    'city' => 'Cotonou',
]);

// Inscrire le participant
$enrollment = Enrollment::create([
    'uuid' => Str::uuid(),
    'participant_id' => $participant->id,
    'session_id' => $session->id,
]);

// Générer la référence
$service = app(ReferenceGeneratorService::class);
$reference = $service->generateForEnrollment($enrollment, 'CI');
```

## 11. Workflow Complet - De A à Z

```php
use App\Models\{Pillar, Training, Session, Participant, Enrollment};
use App\Services\ReferenceGeneratorService;
use Illuminate\Support\Str;

// 1. Récupérer le pilier
$pillar = Pillar::where('code', 'AMCONT')->first();

// 2. Récupérer la formation
$training = Training::where('code', 'AMCONT-007-F')->first();

// 3. Créer une session
$session = Session::create([
    'uuid' => Str::uuid(),
    'training_id' => $training->id,
    'delivery_type' => 'inter-entreprise',
    'start_date' => now()->addDays(7),
    'end_date' => now()->addDays(9),
    'duration' => 3,
    'location' => 'Lomé, Togo',
]);

// 4. Créer des participants
$participants = [
    ['first_name' => 'Marie', 'last_name' => 'Kouassi', 'email' => 'marie.k@example.com'],
    ['first_name' => 'Paul', 'last_name' => 'Mensah', 'email' => 'paul.m@example.com'],
    ['first_name' => 'Sophie', 'last_name' => 'Diallo', 'email' => 'sophie.d@example.com'],
];

$enrollmentIds = [];

foreach ($participants as $data) {
    $participant = Participant::create([
        'uuid' => Str::uuid(),
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'email' => $data['email'],
        'phone' => '+228 90 00 00 00',
        'company' => 'Entreprise XYZ',
        'country' => 'Togo',
        'city' => 'Lomé',
    ]);
    
    $enrollment = Enrollment::create([
        'uuid' => Str::uuid(),
        'participant_id' => $participant->id,
        'session_id' => $session->id,
    ]);
    
    $enrollmentIds[] = $enrollment->id;
}

// 5. Générer toutes les références
$service = app(ReferenceGeneratorService::class);
$references = $service->generateForEnrollments($enrollmentIds, 'TG');

// 6. Afficher les résultats
foreach ($references as $reference) {
    echo "{$reference->enrollment->participant->full_name}: {$reference->reference}\n";
}

// 7. Exporter en CSV
$exportData = $service->exportToArray(collect($references)->pluck('id')->toArray());
// Sauvegarder $exportData...
```

## 12. Requêtes Utiles

```php
// Compter les références par type
use App\Models\Reference;

$certCount = Reference::where('doc_kind', 'CERT')->count();
$attCount = Reference::where('doc_kind', 'ATT')->count();

// Trouver toutes les références d'un participant
$participant = Participant::find(1);
$references = Reference::whereHas('enrollment', function($query) use ($participant) {
    $query->where('participant_id', $participant->id);
})->get();

// Trouver les références générées ce mois
$thisMonth = Reference::whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->get();

// Trouver les références d'un pilier spécifique
$pillar = Pillar::where('code', 'AMCONT')->first();
$references = Reference::whereHas('enrollment.session.training', function($query) use ($pillar) {
    $query->where('pillar_id', $pillar->id);
})->get();
```

## 13. Utilisation via Artisan Tinker

```bash
php artisan tinker
```

```php
// Dans tinker
$service = app(\App\Services\ReferenceGeneratorService::class);

// Générer pour une inscription
$enrollment = \App\Models\Enrollment::first();
$ref = $service->generateForEnrollment($enrollment, 'BJ');
echo $ref->reference;

// Vérifier une référence
$check = $service->verifyReference('BJ_CERT_AMCONT-007-F_112024_0001_00042');
echo $check ? 'Valide' : 'Invalide';

// Statistiques
$stats = $service->getStatistics();
print_r($stats);
```

---

Ces exemples couvrent les cas d'usage les plus courants du système de génération de références.

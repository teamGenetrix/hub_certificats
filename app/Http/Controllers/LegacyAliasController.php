<?php

namespace App\Http\Controllers;

use App\Models\LegacyAlias;
use App\Models\Reference;
use App\Services\ReferenceGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LegacyAliasController extends Controller
{
    /**
     * Display a listing of the legacy aliases.
     */
    public function index(Request $request)
    {
        $query = LegacyAlias::with(['reference.pillar', 'reference.enrollment.participant']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('old_reference', 'like', "%{$search}%")
                    ->orWhereHas('reference', function ($q) use ($search) {
                        $q->where('reference', 'like', "%{$search}%");
                    });
            });
        }

        // Pillar filter
        if ($request->filled('pillar_id')) {
            $query->whereHas('reference', function ($q) use ($request) {
                $q->where('pillar_id', $request->pillar_id);
            });
        }

        $aliases = $query->latest()->paginate(20);

        // Get pillars for filter
        $pillars = \App\Models\Pillar::orderBy('name')->get();

        return view('admin.legacy-aliases.index', compact('aliases', 'pillars'));
    }

    /**
     * Show the form for creating a new legacy alias.
     */
    public function create()
    {
        // Get all references with their details
        $references = Reference::with(['pillar', 'enrollment.participant'])
            ->latest()
            ->get();

        return view('admin.legacy-aliases.create', compact('references'));
    }

    /**
     * Store a newly created legacy alias in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'old_reference' => 'required|string|max:255|unique:legacy_aliases,old_reference',
            'reference_id' => 'required|exists:references,id',
        ], [
            'old_reference.required' => 'L\'ancienne référence est obligatoire.',
            'old_reference.unique' => 'Cette ancienne référence existe déjà.',
            'reference_id.required' => 'La nouvelle référence est obligatoire.',
            'reference_id.exists' => 'La référence sélectionnée n\'existe pas.',
        ]);

        try {
            $alias = LegacyAlias::create($validated);

            Log::info('Legacy alias created', [
                'alias_id' => $alias->id,
                'old_reference' => $alias->old_reference,
                'reference_id' => $alias->reference_id,
            ]);

            return redirect()
                ->route('admin.legacy-aliases.index')
                ->with('success', 'Alias créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Error creating legacy alias', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'alias.');
        }
    }

    /**
     * Show the form for editing the specified legacy alias.
     */
    public function edit(LegacyAlias $legacyAlias)
    {
        // Get all references with their details
        $references = Reference::with(['pillar', 'enrollment.participant'])
            ->latest()
            ->get();

        return view('admin.legacy-aliases.edit', compact('legacyAlias', 'references'));
    }

    /**
     * Update the specified legacy alias in storage.
     */
    public function update(Request $request, LegacyAlias $legacyAlias)
    {
        $validated = $request->validate([
            'old_reference' => 'required|string|max:255|unique:legacy_aliases,old_reference,' . $legacyAlias->id,
            'reference_id' => 'required|exists:references,id',
        ], [
            'old_reference.required' => 'L\'ancienne référence est obligatoire.',
            'old_reference.unique' => 'Cette ancienne référence existe déjà.',
            'reference_id.required' => 'La nouvelle référence est obligatoire.',
            'reference_id.exists' => 'La référence sélectionnée n\'existe pas.',
        ]);

        try {
            $legacyAlias->update($validated);

            Log::info('Legacy alias updated', [
                'alias_id' => $legacyAlias->id,
                'old_reference' => $legacyAlias->old_reference,
                'reference_id' => $legacyAlias->reference_id,
            ]);

            return redirect()
                ->route('admin.legacy-aliases.index')
                ->with('success', 'Alias modifié avec succès.');
        } catch (\Exception $e) {
            Log::error('Error updating legacy alias', [
                'error' => $e->getMessage(),
                'alias_id' => $legacyAlias->id,
                'data' => $validated,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la modification de l\'alias.');
        }
    }

    /**
     * Remove the specified legacy alias from storage.
     */
    public function destroy(LegacyAlias $legacyAlias)
    {
        try {
            $oldReference = $legacyAlias->old_reference;
            $legacyAlias->delete();

            Log::info('Legacy alias deleted', [
                'alias_id' => $legacyAlias->id,
                'old_reference' => $oldReference,
            ]);

            return redirect()
                ->route('admin.legacy-aliases.index')
                ->with('success', 'Alias supprimé avec succès.');
        } catch (\Exception $e) {
            Log::error('Error deleting legacy alias', [
                'error' => $e->getMessage(),
                'alias_id' => $legacyAlias->id,
            ]);

            return back()
                ->with('error', 'Erreur lors de la suppression de l\'alias.');
        }
    }

    /**
     * Search references via AJAX for autocomplete.
     */
    public function searchReferences(Request $request)
    {
        $search = $request->get('q', '');

        $references = Reference::with(['pillar', 'enrollment.participant'])
            ->where('reference', 'like', "%{$search}%")
            ->limit(20)
            ->get()
            ->map(function ($ref) {
                return [
                    'id' => $ref->id,
                    'reference' => $ref->reference,
                    'pillar' => $ref->pillar->name ?? 'N/A',
                    'participant' => $ref->enrollment->participant->full_name ?? 'N/A',
                    'doc_kind' => $ref->doc_kind,
                ];
            });

        return response()->json($references);
    }

    /**
     * Show the import form.
     */
    public function showImportForm()
    {
        $sessions = \App\Models\Session::with('training.pillar')
            ->whereHas('training', function($query) {
                $query->whereNotNull('pillar_id');
            })
            ->latest()
            ->get();

        return view('admin.legacy-aliases.import', compact('sessions'));
    }

    /**
     * Download CSV template for import.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="legacy_aliases_template.csv"',
        ];

        $columns = ['old_reference', 'participant_email'];
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, $columns);
            
            // Example rows
            fputcsv($file, ['OLD-CERT-2023-001', 'john.doe@example.com']);
            fputcsv($file, ['OLD-ATT-2023-002', 'jane.smith@example.com']);
            fputcsv($file, ['LEGACY-REF-123', 'bob@example.com']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process the CSV import.
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:training_sessions,id',
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ], [
            'session_id.required' => 'Veuillez sélectionner une session.',
            'session_id.exists' => 'La session sélectionnée est invalide.',
            'csv_file.required' => 'Veuillez sélectionner un fichier CSV.',
            'csv_file.mimes' => 'Le fichier doit être au format CSV.',
            'csv_file.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
        ]);

        try {
            // Get session (pillar is determined by session's training)
            $session = \App\Models\Session::with('training.pillar')->findOrFail($request->session_id);

            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            
            $csv = array_map(function($line) {
                return str_getcsv($line, ',', '"');
            }, file($path));

            // Remove BOM if present
            if (isset($csv[0][0])) {
                $csv[0][0] = preg_replace('/^\x{FEFF}/u', '', $csv[0][0]);
            }

            $header = array_shift($csv);
            
            // Validate header
            $requiredColumns = ['old_reference', 'participant_email'];
            $missingColumns = array_diff($requiredColumns, $header);
            
            if (!empty($missingColumns)) {
                return back()->with('error', 'Colonnes manquantes dans le CSV : ' . implode(', ', $missingColumns));
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($csv as $index => $row) {
                $lineNumber = $index + 2; // +2 because we removed header and arrays are 0-indexed
                
                if (count($row) !== count($header)) {
                    $errors[] = "Ligne {$lineNumber} : Nombre de colonnes incorrect";
                    $skipped++;
                    continue;
                }

                $data = array_combine($header, $row);
                
                // Skip empty rows
                if (empty(trim($data['old_reference']))) {
                    continue;
                }

                try {
                    // Check if alias already exists
                    if (LegacyAlias::where('old_reference', $data['old_reference'])->exists()) {
                        $errors[] = "Ligne {$lineNumber} : L'alias '{$data['old_reference']}' existe déjà";
                        $skipped++;
                        continue;
                    }

                    // Find enrollment and generate reference
                    $reference = $this->findEnrollmentAndGenerateReference(
                        $session,
                        $data['participant_email']
                    );

                    // Create alias
                    LegacyAlias::create([
                        'old_reference' => $data['old_reference'],
                        'reference_id' => $reference->id,
                    ]);

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Ligne {$lineNumber} : " . $e->getMessage();
                    $skipped++;
                    Log::error('Error importing legacy alias', [
                        'line' => $lineNumber,
                        'data' => $data,
                        'session_id' => $session->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('Legacy aliases import completed', [
                'imported' => $imported,
                'skipped' => $skipped,
                'errors_count' => count($errors),
            ]);

            // Build appropriate message based on results
            if ($imported > 0 && $skipped === 0) {
                $message = "Import terminé avec succès ! {$imported} alias créé(s).";
                $messageType = 'success';
            } elseif ($imported > 0 && $skipped > 0) {
                $message = "Import terminé avec avertissements. {$imported} alias créé(s), {$skipped} ligne(s) ignorée(s).";
                $messageType = 'warning';
            } elseif ($imported === 0 && $skipped > 0) {
                $message = "Aucun alias créé. {$skipped} ligne(s) ignorée(s). Consultez les détails ci-dessous.";
                $messageType = 'error';
            } else {
                $message = "Aucune donnée à importer.";
                $messageType = 'warning';
            }
            
            return redirect()
                ->route('admin.legacy-aliases.index')
                ->with([
                    $messageType => $message,
                    'imported_count' => $imported,
                    'skipped_count' => $skipped,
                    'errors_detail' => !empty($errors) ? array_slice($errors, 0, 10) : null,
                ]);

        } catch (\Exception $e) {
            Log::error('Error processing CSV import', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Erreur lors du traitement du fichier : ' . $e->getMessage());
        }
    }

    /**
     * Find enrollment in session and generate new reference using the service.
     */
    private function findEnrollmentAndGenerateReference($session, $participantEmail)
    {
        // Find participant
        $participant = \App\Models\Participant::where('email', $participantEmail)->first();
        if (!$participant) {
            throw new \Exception("Participant '{$participantEmail}' introuvable");
        }

        // Find enrollment in the selected session (use training_session_id)
        $enrollment = \App\Models\Enrollment::where('training_session_id', $session->id)
            ->where('participant_id', $participant->id)
            ->first();
            
        if (!$enrollment) {
            throw new \Exception("Participant {$participantEmail} non inscrit à cette session");
        }

        // Check if reference already exists for this enrollment
        if ($enrollment->reference()->exists()) {
            throw new \Exception("Une référence existe déjà pour ce participant");
        }

        // Use the ReferenceGeneratorService to generate the reference
        $referenceService = app(ReferenceGeneratorService::class);
        $reference = $referenceService->generateForEnrollment($enrollment);

        Log::info('New reference generated via legacy alias import', [
            'reference' => $reference->reference,
            'enrollment_id' => $enrollment->id,
            'participant_email' => $participantEmail,
        ]);

        return $reference;
    }
}

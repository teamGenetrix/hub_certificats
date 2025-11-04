<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ParticipantController extends Controller
{
    /**
     * Display a listing of participants
     */
    public function index()
    {
        $participants = Participant::withCount('enrollments')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20);

        return view('admin.participants.index', compact('participants'));
    }

    /**
     * Show the form for creating a new participant
     */
    public function create()
    {
        return view('admin.participants.create');
    }

    /**
     * Store a newly created participant
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:participants,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
        ]);

        $participant = Participant::create($validated);

        return redirect()
            ->route('admin.participants.show', $participant)
            ->with('success', 'Participant créé avec succès !');
    }

    /**
     * Display the specified participant
     */
    public function show(Participant $participant)
    {
        $participant->load(['enrollments.session.training.pillar', 'enrollments.reference']);

        return view('admin.participants.show', compact('participant'));
    }

    /**
     * Show the form for editing the specified participant
     */
    public function edit(Participant $participant)
    {
        return view('admin.participants.edit', compact('participant'));
    }

    /**
     * Update the specified participant
     */
    public function update(Request $request, Participant $participant)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:participants,email,' . $participant->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
        ]);

        $participant->update($validated);

        return redirect()
            ->route('admin.participants.show', $participant)
            ->with('success', 'Participant mis à jour avec succès !');
    }

    /**
     * Remove the specified participant
     */
    public function destroy(Participant $participant)
    {
        // Check if participant has enrollments with references
        $hasReferences = $participant->enrollments()
            ->whereHas('reference')
            ->exists();

        if ($hasReferences) {
            return back()->with('error', 'Impossible de supprimer ce participant car des références ont été générées.');
        }

        $participant->delete();

        return redirect()
            ->route('admin.participants.index')
            ->with('success', 'Participant supprimé avec succès !');
    }

    /**
     * Download Excel template for participants import
     */

    public function downloadTemplate()
    {
        $filename = 'template_participants_' . now()->format('Y-m-d') . '.csv';

        return Response::streamDownload(function () {
            $out = fopen('php://output', 'w');

            // BOM UTF-8 pour Excel
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Délimiteur ; (convention Excel FR)
            $delim = ';';

            // En-têtes ALIGNÉES AVEC L’IMPORT (machine names)
            fputcsv($out, [
                'prenom',
                'nom',
                'email',
                'telephone',
                'entreprise',
                'poste',
                'pays',
            ], $delim);

            // Astuce pour Excel : forcer l'affichage du téléphone tel quel
            // en l'enveloppant avec ="...".
            $phone1 = '=" +225 01 02 03 04 05"';
            $phone2 = '=" +225 06 07 08 09 10"';

            // Lignes d’exemple
            fputcsv($out, [
                'Ange',
                'Koffi',
                'ange.koffi@example.com',
                $phone1,
                'Entreprise ABC',
                'Directeur',
                'Côte d’Ivoire',
            ], $delim);

            fputcsv($out, [
                'Marie',
                'Senede',
                'marie.senede@example.com',
                $phone2,
                'Société XYZ',
                'Manager',
                'Côte d’Ivoire',
            ], $delim);

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            // Empêcher certains proxies de bufferiser
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }



    /**
     * Import participants from Excel file
     */
    public function import(Request $request)
    {
        Log::info('Import started');

        // 1) Validation : CSV only
        $request->validate([
            'excel_file' => [
                'required',
                'file',
                'max:5120', // 5MB
                'mimes:csv,txt',
                'mimetypes:text/csv,text/plain,application/csv,text/comma-separated-values'
            ],
        ]);

        if (!$request->hasFile('excel_file')) {
            Log::warning('No file uploaded');
            return back()->with('error', 'Aucun fichier reçu.');
        }

        $file = $request->file('excel_file');

        Log::info('File uploaded', [
            'original_name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize()
        ]);

        try {
            $handle = fopen($file->getRealPath(), 'r');
            if ($handle === false) {
                throw new \RuntimeException('Impossible d’ouvrir le fichier CSV.');
            }

            // 2) Skip BOM
            $bom = fread($handle, 3);
            if ($bom !== chr(0xEF) . chr(0xBB) . chr(0xBF)) {
                rewind($handle);
            }

            // 3) Détection délimiteur: ; , ou \t
            $probe = fgets($handle);
            rewind($handle);
            if ($bom === chr(0xEF) . chr(0xBB) . chr(0xBF)) {
                fread($handle, 3); // resaut BOM après rewind
            }
            $counts = [
                ';'  => substr_count($probe, ';'),
                ','  => substr_count($probe, ','),
                "\t" => substr_count($probe, "\t"),
            ];
            arsort($counts);
            $delimiter = array_key_first($counts) ?? ',';

            // 4) Header + mapping colonnes
            $header = fgetcsv($handle, 0, $delimiter);
            if (!$header) {
                fclose($handle);
                return back()->with('error', 'Fichier vide ou en-tête manquant.');
            }
            // Normaliser header (lowercase, trim)
            $norm = fn($s) => strtolower(trim((string) $s));
            $header = array_map($norm, $header);

            // Colonnes attendues (au minimum)
            $required = ['prenom', 'nom', 'email'];
            foreach ($required as $col) {
                if (!in_array($col, $header, true)) {
                    fclose($handle);
                    return back()->with('error', "Colonne requise manquante: {$col}");
                }
            }

            // Index des colonnes utiles
            $idx = fn($col) => array_search($col, $header, true);
            $iFirst = $idx('prenom');
            $iLast  = $idx('nom');
            $iEmail = $idx('email');
            $iPhone = $idx('telephone')    !== false ? $idx('telephone')    : null;
            $iComp  = $idx('entreprise')  !== false ? $idx('entreprise')  : null;
            $iJob   = $idx('poste') !== false ? $idx('poste') : null;
            $iCtry  = $idx('pays')  !== false ? $idx('pays')  : null;

            // 5) Lecture lignes
            $participants = [];
            $errors = [];
            $duplicates = 0;
            $line = 0;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $line++;

                // Skip lignes vides
                if (empty(array_filter($row, fn($v) => trim((string)$v) !== ''))) {
                    continue;
                }

                $first = trim($row[$iFirst] ?? '');
                $last  = trim($row[$iLast]  ?? '');
                $email = trim($row[$iEmail] ?? '');

                if ($first === '' || $last === '' || $email === '') {
                    $errors[] = "Ligne CSV #{$line}: Prénom, Nom, Email obligatoires.";
                    continue;
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Ligne CSV #{$line}: Email invalide ({$email}).";
                    continue;
                }

                $participants[] = [
                    'first_name' => $first,
                    'last_name'  => $last,
                    'email'      => $email,
                    'phone'      => $iPhone ? trim($row[$iPhone] ?? '') : null,
                    'company'    => $iComp  ? trim($row[$iComp]  ?? '') : null,
                    'job_title'  => $iJob   ? trim($row[$iJob]   ?? '') : null,
                    'country'    => $iCtry  ? trim($row[$iCtry]  ?? '') : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($participants) >= 500) {
                    break;
                }
            }
            fclose($handle);

            if (empty($participants) && empty($errors)) {
                return back()->with('warning', 'Aucun participant valide trouvé.');
            }

            // 6) Insert rapide & sûr (ignore les doublons grâce à l’index UNIQUE)
            //    Retourne le nombre de lignes insérées; les doublons seront ignorés.
            $inserted = 0;
            if (!empty($participants)) {
                $inserted = \DB::table('participants')->insertOrIgnore($participants);
                // A défaut de retour précis des ignorés, on peut estimer:
                // $duplicates = count($participants) - $inserted; (approximation)
                $duplicates = max(count($participants) - $inserted, 0);
            }

            // 7) Message final
            $msg = "{$inserted} participant(s) importé(s).";
            if ($duplicates > 0) $msg .= " {$duplicates} doublon(s) ignoré(s).";
            if (count($errors) > 0) $msg .= " " . count($errors) . " erreur(s).";

            return redirect()
                ->route('admin.participants.index')
                ->with($inserted > 0 ? 'success' : 'warning', $msg)
                ->with('import_errors', $errors);
        } catch (\Throwable $e) {
            Log::error('Import exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Erreur lors de l’import: ' . $e->getMessage());
        }
    }
}

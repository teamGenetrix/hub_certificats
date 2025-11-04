<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Pillar;
use App\Models\Session;
use App\Models\Training;
use App\Services\ReferenceGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReferenceController extends Controller
{
    protected ReferenceGeneratorService $referenceService;
    
    public function __construct(ReferenceGeneratorService $referenceService)
    {
        $this->referenceService = $referenceService;
    }
    
    /**
     * Display the reference generation interface
     */
    public function index()
    {
        $statistics = $this->referenceService->getStatistics();
        
        return view('admin.references.index', compact('statistics'));
    }
    
    /**
     * Show the form for generating references
     */
    public function create(Request $request)
    {
        $pillars = Pillar::with(['trainings' => function ($query) {
            $query->orderBy('title');
        }])->orderBy('name')->get();
        
        // Pre-fill data if session_id is provided
        $prefilledSession = null;
        if ($request->has('session_id')) {
            $prefilledSession = Session::with(['training.pillar', 'enrollments.participant'])
                ->findOrFail($request->session_id);
        }
        
        return view('admin.references.create', compact('pillars', 'prefilledSession'));
    }
    
    /**
     * Get trainings for a specific pillar (AJAX)
     */
    public function getTrainingsByPillar(Request $request)
    {
        $request->validate([
            'pillar_id' => 'required|exists:pillars,id',
        ]);
        
        $trainings = Training::where('pillar_id', $request->pillar_id)
            ->orderBy('title')
            ->get(['id', 'code', 'title', 'has_exam', 'is_custom', 'custom_code']);
        
        return response()->json($trainings);
    }
    
    /**
     * Get sessions for a specific training (AJAX)
     */
    public function getSessionsByTraining(Request $request)
    {
        $request->validate([
            'training_id' => 'required|exists:trainings,id',
            'delivery_type' => 'nullable|in:inter-entreprise,intra-entreprise,en-ligne,blending',
        ]);
        
        $query = Session::where('training_id', $request->training_id)
            ->with(['enrollments.participant']);
        
        if ($request->filled('delivery_type')) {
            $query->where('delivery_type', $request->delivery_type);
        }
        
        $sessions = $query->orderBy('start_date', 'desc')->get();
        
        return response()->json($sessions);
    }
    
    /**
     * Get enrollments for a specific session (AJAX)
     */
    public function getEnrollmentsBySession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:training_sessions,id',
        ]);
        
        $enrollments = Enrollment::where('training_session_id', $request->session_id)
            ->with(['participant', 'reference'])
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id' => $enrollment->id,
                    'participant' => [
                        'id' => $enrollment->participant->id,
                        'full_name' => $enrollment->participant->full_name,
                        'email' => $enrollment->participant->email,
                        'company' => $enrollment->participant->company,
                    ],
                    'has_reference' => $enrollment->reference()->exists(),
                    'reference' => $enrollment->reference ? $enrollment->reference->reference : null,
                ];
            });
        
        return response()->json($enrollments);
    }
    
    /**
     * Generate references for selected enrollments
     */
    public function generate(Request $request)
    {
        $request->validate([
            'enrollment_ids' => 'required|array|min:1',
            'enrollment_ids.*' => 'exists:enrollments,id',
            'country_code' => 'required|string|size:2',
        ]);
        
        try {
            $references = $this->referenceService->generateForEnrollments(
                $request->enrollment_ids,
                strtoupper($request->country_code)
            );
            
            return response()->json([
                'success' => true,
                'message' => count($references) . ' référence(s) générée(s) avec succès.',
                'references' => collect($references)->map(function ($ref) {
                    return [
                        'id' => $ref->id,
                        'reference' => $ref->reference,
                        'participant' => $ref->enrollment->participant->full_name,
                    ];
                })->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Generate references for an entire session
     */
    public function generateForSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:training_sessions,id',
            'country_code' => 'required|string|size:2',
        ]);
        
        try {
            $session = Session::findOrFail($request->session_id);
            
            $references = $this->referenceService->generateForSession(
                $session,
                strtoupper($request->country_code)
            );
            
            return response()->json([
                'success' => true,
                'message' => count($references) . ' référence(s) générée(s) avec succès pour la session.',
                'references' => collect($references)->map(function ($ref) {
                    return [
                        'id' => $ref->id,
                        'reference' => $ref->reference,
                        'participant' => $ref->enrollment->participant->full_name,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Show export page with filters
     */
    public function showExportForm()
    {
        $pillars = Pillar::orderBy('name')->get();
        
        return view('admin.references.export', compact('pillars'));
    }
    
    /**
     * List references with filters (AJAX)
     */
    public function list(Request $request)
    {
        $query = \App\Models\Reference::with(['enrollment.participant', 'pillar'])
            ->orderBy('created_at', 'desc');
        
        // Filter by pillar
        if ($request->filled('pillar_id')) {
            $query->where('pillar_id', $request->pillar_id);
        }
        
        // Filter by doc_kind
        if ($request->filled('doc_kind')) {
            $query->where('doc_kind', $request->doc_kind);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('enrollment.participant', function($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $references = $query->get()->map(function($ref) {
            $participant = $ref->enrollment->participant ?? null;
            return [
                'id' => $ref->id,
                'reference' => $ref->reference,
                'doc_kind' => $ref->doc_kind,
                'participant_name' => $participant?->full_name,
                'participant_email' => $participant?->email,
                'training_title' => $ref->meta['training_title'] ?? 'N/A',
                'pillar_name' => $ref->pillar?->name ?? 'N/A',
                'created_at' => $ref->created_at->toISOString(),
            ];
        });
        
        return response()->json($references);
    }
    
    /**
     * Export references to Excel
     */
    public function export(Request $request)
    {
        $request->validate([
            'reference_ids' => 'nullable|array',
            'reference_ids.*' => 'exists:references,id',
        ]);
        
        $data = $this->referenceService->exportToArray($request->reference_ids ?? []);
        
        // Create CSV content
        $filename = 'references_' . now()->format('Y-m-d_His') . '.csv';
        
        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            foreach ($data as $row) {
                fputcsv($file, $row, ';');
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Verify a reference number
     */
    public function verify(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);
        
        $searchedReference = strtoupper(trim($request->reference));
        $isLegacyAlias = false;
        $oldReference = null;
        
        // First, try to find by new reference
        $reference = $this->referenceService->verifyReference($searchedReference);
        
        // If not found, check if it's a legacy alias
        if (!$reference) {
            $legacyAlias = \App\Models\LegacyAlias::where('old_reference', $searchedReference)
                ->with('reference.enrollment.participant', 'reference.enrollment.session.training.pillar')
                ->first();
            
            if ($legacyAlias) {
                $reference = $legacyAlias->reference;
                $isLegacyAlias = true;
                $oldReference = $legacyAlias->old_reference;
            }
        }
        
        if (!$reference) {
            return view('admin.references.verify', [
                'found' => false,
                'reference_number' => $searchedReference,
            ]);
        }
        
        return view('admin.references.verify', [
            'found' => true,
            'reference' => $reference,
            'reference_number' => $searchedReference,
            'is_legacy_alias' => $isLegacyAlias,
            'old_reference' => $oldReference,
        ]);
    }
    
    /**
     * Show verification form
     */
    public function showVerifyForm()
    {
        return view('admin.references.verify', ['found' => null]);
    }
}

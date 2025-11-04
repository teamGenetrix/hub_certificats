<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Participant;
use App\Models\Pillar;
use App\Models\Session;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SessionController extends Controller
{
    /**
     * Display a listing of sessions
     */
    public function index()
    {
        $sessions = Session::with(['training.pillar', 'enrollments'])
            ->orderBy('start_date', 'desc')
            ->paginate(15);
        
        return view('admin.sessions.index', compact('sessions'));
    }
    
    /**
     * Show the form for creating a new session
     */
    public function create()
    {
        $pillars = Pillar::with('trainings')
            ->orderBy('name')
            ->get();
        
        $trainings = Training::with('pillar')
            ->orderBy('title')
            ->get()
            ->groupBy('pillar_id');
        
        return view('admin.sessions.create', compact('pillars', 'trainings'));
    }
    
    /**
     * Store a newly created session
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pillar_id' => 'required|exists:pillars,id',
            'training_id' => 'required_without:custom_training_title|nullable|exists:trainings,id',
            'custom_training_title' => 'required_without:training_id|nullable|string|max:255',
            'custom_training_code' => 'nullable|string|max:50',
            'delivery_type' => 'required|in:inter-entreprise,intra-entreprise,en-ligne,blending',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
        ]);
        
        // Si une formation personnalisée est demandée
        if ($request->filled('custom_training_title')) {
            $training = Training::create([
                'title' => $validated['custom_training_title'],
                'code' => $validated['custom_training_code'] ?? 'CUSTOM-' . strtoupper(Str::random(6)),
                'custom_code' => $validated['custom_training_code'] ?? null,
                'pillar_id' => $validated['pillar_id'],
                'is_custom' => true,
                'has_exam' => $request->boolean('custom_training_has_exam', false),
            ]);
            
            $validated['training_id'] = $training->id;
        }
        
        // Créer la session
        $session = Session::create([
            'training_id' => $validated['training_id'],
            'delivery_type' => $validated['delivery_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'duration' => $validated['duration'],
            'location' => $validated['location'],
        ]);
        
        return redirect()
            ->route('admin.sessions.show', $session)
            ->with('success', 'Session créée avec succès !');
    }
    
    /**
     * Display the specified session
     */
    public function show(Session $session)
    {
        $session->load(['training.pillar', 'enrollments.participant']);
        
        return view('admin.sessions.show', compact('session'));
    }
    
    /**
     * Show the form for editing the specified session
     */
    public function edit(Session $session)
    {
        $pillars = Pillar::with('trainings')
            ->orderBy('name')
            ->get();
        
        $trainings = Training::with('pillar')
            ->orderBy('title')
            ->get()
            ->groupBy('pillar_id');
        
        $session->load('training.pillar');
        
        return view('admin.sessions.edit', compact('session', 'pillars', 'trainings'));
    }
    
    /**
     * Update the specified session
     */
    public function update(Request $request, Session $session)
    {
        $validated = $request->validate([
            'pillar_id' => 'required|exists:pillars,id',
            'training_id' => 'required|exists:trainings,id',
            'delivery_type' => 'required|in:inter-entreprise,intra-entreprise,en-ligne,blending',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
        ]);
        
        // Mettre à jour uniquement les champs de la session (pas le pillar_id qui est sur la formation)
        $session->update([
            'training_id' => $validated['training_id'],
            'delivery_type' => $validated['delivery_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'duration' => $validated['duration'],
            'location' => $validated['location'],
        ]);
        
        return redirect()
            ->route('admin.sessions.show', $session)
            ->with('success', 'Session mise à jour avec succès !');
    }
    
    /**
     * Remove the specified session
     */
    public function destroy(Session $session)
    {
        $session->delete();
        
        return redirect()
            ->route('admin.sessions.index')
            ->with('success', 'Session supprimée avec succès !');
    }

    /**
     * Get all sessions for dropdown (AJAX)
     */
    public function getAll()
    {
        $sessions = Session::with(['training', 'enrollments'])
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'start_date' => $session->start_date->format('Y-m-d'),
                    'end_date' => $session->end_date->format('Y-m-d'),
                    'training' => [
                        'title' => $session->training->title,
                    ],
                    'enrollments' => [
                        'count' => $session->enrollments->count(),
                    ],
                ];
            });
        
        return response()->json($sessions);
    }
    
    /**
     * Show form to add participants to session
     */
    public function addParticipants(Session $session)
    {
        $session->load(['training', 'enrollments.participant']);
        $participants = Participant::orderBy('last_name')->get();
        
        return view('admin.sessions.add-participants', compact('session', 'participants'));
    }
    
    /**
     * Store participants enrollment
     */
    public function storeParticipants(Request $request, Session $session)
    {
        $validated = $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => 'exists:participants,id',
        ]);
        
        $enrolled = 0;
        foreach ($validated['participant_ids'] as $participantId) {
            // Check if already enrolled
            $exists = Enrollment::where('training_session_id', $session->id)
                ->where('participant_id', $participantId)
                ->exists();
            
            if (!$exists) {
                Enrollment::create([
                    'training_session_id' => $session->id,
                    'participant_id' => $participantId,
                ]);
                $enrolled++;
            }
        }
        
        return redirect()
            ->route('admin.sessions.show', $session)
            ->with('success', "$enrolled participant(s) inscrit(s) avec succès !");
    }
    
    /**
     * Remove a participant from session
     */
    public function removeParticipant(Session $session, Enrollment $enrollment)
    {
        if ($enrollment->training_session_id !== $session->id) {
            abort(404);
        }
        
        // Check if reference exists
        if ($enrollment->reference()->exists()) {
            return back()->with('error', 'Impossible de retirer ce participant car une référence a déjà été générée.');
        }
        
        $enrollment->delete();
        
        return back()->with('success', 'Participant retiré de la session.');
    }
}

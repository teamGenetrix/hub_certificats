@extends('layouts.app')

@section('title', 'Ajouter des Participants')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title">Ajouter des Participants</h4>
                    <p class="text-muted mb-0">{{ $session->training->title }}</p>
                </div>
                <a href="{{ route('admin.sessions.show', $session) }}" class="btn btn-secondary">
                    <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon>
                    Retour
                </a>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('admin.sessions.store-participants', $session) }}" method="POST">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Sélectionner les Participants</h4>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                        <label class="form-check-label" for="select-all">
                            Tout sélectionner
                        </label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="search" class="form-control" placeholder="Rechercher un participant...">
                    </div>

                    <div id="participants-list" style="max-height: 500px; overflow-y: auto;">
                        @forelse($participants as $participant)
                        @php
                            $alreadyEnrolled = $session->enrollments->where('participant_id', $participant->id)->count() > 0;
                        @endphp
                        <div class="form-check p-3 border-bottom participant-item" data-search="{{ strtolower($participant->full_name . ' ' . $participant->email . ' ' . $participant->company) }}">
                            <input class="form-check-input participant-checkbox" 
                                   type="checkbox" 
                                   name="participant_ids[]" 
                                   value="{{ $participant->id }}" 
                                   id="participant{{ $participant->id }}"
                                   {{ $alreadyEnrolled ? 'disabled' : '' }}>
                            <label class="form-check-label w-100" for="participant{{ $participant->id }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-medium">{{ $participant->full_name }}</div>
                                        <small class="text-muted">
                                            {{ $participant->email }}
                                            @if($participant->company)
                                                • {{ $participant->company }}
                                            @endif
                                        </small>
                                        @if($alreadyEnrolled)
                                            <div class="badge bg-success-subtle text-success mt-1">Déjà inscrit</div>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        </div>
                        @empty
                        <div class="text-center text-muted py-5">
                            <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="fs-1 mb-3"></iconify-icon>
                            <p class="mb-0">Aucun participant disponible</p>
                        </div>
                        @endforelse
                    </div>

                    @error('participant_ids')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Session Info -->
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Informations de la Session</h5>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Formation</label>
                        <div class="fw-medium">{{ $session->training->title }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Dates</label>
                        <div class="fw-medium">
                            {{ $session->start_date->format('d/m/Y') }} - {{ $session->end_date->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Participants actuels</label>
                        <div>
                            <span class="badge bg-primary fs-6">{{ $session->enrollments->count() }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="form-label text-muted small mb-1">Participants sélectionnés</label>
                        <div>
                            <span class="badge bg-success fs-6" id="selected-count">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                            <iconify-icon icon="solar:user-plus-bold-duotone" class="me-1"></iconify-icon>
                            Ajouter les Participants
                        </button>
                        <a href="{{ route('admin.sessions.show', $session) }}" class="btn btn-outline-secondary">
                            Annuler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const participantCheckboxes = document.querySelectorAll('.participant-checkbox:not([disabled])');
    const selectedCount = document.getElementById('selected-count');
    const submitBtn = document.getElementById('submit-btn');
    const searchInput = document.getElementById('search');
    const participantItems = document.querySelectorAll('.participant-item');
    
    // Update count and button state
    function updateSelection() {
        const checkedCount = document.querySelectorAll('.participant-checkbox:checked:not([disabled])').length;
        selectedCount.textContent = checkedCount;
        submitBtn.disabled = checkedCount === 0;
    }
    
    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        participantCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelection();
    });
    
    // Individual checkbox change
    participantCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelection);
    });
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        participantItems.forEach(item => {
            const searchData = item.getAttribute('data-search');
            if (searchData.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Initial update
    updateSelection();
});
</script>
@endsection

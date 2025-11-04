@extends('layouts.app')

@section('title', 'Créer un Alias Legacy')

@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="page-title-box d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Créer un Alias Legacy</h4>
            <a href="{{ route('admin.legacy-aliases.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Retour
            </a>
        </div>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.legacy-aliases.store') }}" method="POST">
                            @csrf

                            <!-- Old Reference -->
                            <div class="mb-4">
                                <label for="old_reference" class="form-label">
                                    Ancienne Référence <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('old_reference') is-invalid @enderror" 
                                       id="old_reference" 
                                       name="old_reference" 
                                       value="{{ old('old_reference') }}"
                                       placeholder="Ex: REF-2023-001, OLD-CERT-123..."
                                       required>
                                <div class="form-text">
                                    Saisissez l'ancienne référence qui ne respecte pas la nomenclature actuelle.
                                </div>
                                @error('old_reference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- New Reference Selection -->
                            <div class="mb-4">
                                <label for="reference_search" class="form-label">
                                    Nouvelle Référence <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           id="reference_search" 
                                           placeholder="Rechercher une référence..."
                                           autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" id="clear-search">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>
                                <input type="hidden" 
                                       name="reference_id" 
                                       id="reference_id" 
                                       value="{{ old('reference_id') }}">
                                <div id="search-results" class="list-group mt-2" style="display: none;"></div>
                                <div class="form-text">
                                    Recherchez et sélectionnez la nouvelle référence à lier.
                                </div>
                                @error('reference_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Selected Reference Display -->
                            <div id="selected-reference" class="mb-4" style="display: none;">
                                <label class="form-label">Référence sélectionnée</label>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Référence:</strong> <span id="sel-reference"></span></p>
                                                <p class="mb-1"><strong>Type:</strong> <span id="sel-doc-kind"></span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Pilier:</strong> <span id="sel-pillar"></span></p>
                                                <p class="mb-0"><strong>Participant:</strong> <span id="sel-participant"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.legacy-aliases.index') }}" class="btn btn-secondary">
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Créer l'alias
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Help Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bx bx-help-circle text-info me-2"></i>Aide
                        </h5>
                        <p class="card-text">
                            <strong>Qu'est-ce qu'un alias legacy ?</strong><br>
                            Un alias permet de créer un lien entre une ancienne référence et une nouvelle référence 
                            conforme à la nomenclature actuelle.
                        </p>
                        <hr>
                        <p class="card-text">
                            <strong>Quand l'utiliser ?</strong><br>
                            Utilisez les alias pour maintenir la traçabilité des documents existants qui utilisent 
                            l'ancienne nomenclature.
                        </p>
                        <hr>
                        <p class="card-text mb-0">
                            <strong>Exemple :</strong><br>
                            <code>OLD-CERT-2023-001</code> → <code>CER-2024-11-001-DIG-001</code>
                        </p>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bx bx-bar-chart text-success me-2"></i>Statistiques
                        </h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total des alias:</span>
                            <strong>{{ \App\Models\LegacyAlias::count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Références disponibles:</span>
                            <strong>{{ \App\Models\Reference::count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let searchTimeout;
        const searchInput = document.getElementById('reference_search');
        const searchResults = document.getElementById('search-results');
        const referenceIdInput = document.getElementById('reference_id');
        const selectedReferenceDiv = document.getElementById('selected-reference');
        const clearButton = document.getElementById('clear-search');

        // Search references
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('admin.legacy-aliases.search-references') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data);
                    })
                    .catch(error => {
                        console.error('Error searching references:', error);
                    });
            }, 300);
        });

        // Display search results
        function displaySearchResults(references) {
            if (references.length === 0) {
                searchResults.innerHTML = '<div class="list-group-item text-muted">Aucune référence trouvée</div>';
                searchResults.style.display = 'block';
                return;
            }

            let html = '';
            references.forEach(ref => {
                const docKindBadge = ref.doc_kind === 'CER' 
                    ? '<span class="badge bg-info">Certificat</span>' 
                    : '<span class="badge bg-warning">Attestation</span>';
                
                html += `
                    <a href="#" class="list-group-item list-group-item-action" data-ref='${JSON.stringify(ref)}'>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${ref.reference}</strong>
                                <br>
                                <small class="text-muted">${ref.pillar} • ${ref.participant}</small>
                            </div>
                            ${docKindBadge}
                        </div>
                    </a>
                `;
            });

            searchResults.innerHTML = html;
            searchResults.style.display = 'block';

            // Add click handlers
            searchResults.querySelectorAll('.list-group-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const refData = JSON.parse(this.dataset.ref);
                    selectReference(refData);
                });
            });
        }

        // Select a reference
        function selectReference(ref) {
            referenceIdInput.value = ref.id;
            searchInput.value = ref.reference;
            searchResults.style.display = 'none';

            // Display selected reference details
            document.getElementById('sel-reference').textContent = ref.reference;
            document.getElementById('sel-doc-kind').textContent = ref.doc_kind === 'CER' ? 'Certificat' : 'Attestation';
            document.getElementById('sel-pillar').textContent = ref.pillar;
            document.getElementById('sel-participant').textContent = ref.participant;
            selectedReferenceDiv.style.display = 'block';
        }

        // Clear search
        clearButton.addEventListener('click', function() {
            searchInput.value = '';
            referenceIdInput.value = '';
            searchResults.style.display = 'none';
            selectedReferenceDiv.style.display = 'none';
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    </script>
    @endpush
@endsection

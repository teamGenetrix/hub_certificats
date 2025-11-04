@extends('layouts.app')

@section('title', 'Exporter les Références')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex justify-content-between align-items-center">
            <div>
                <h4 class="page-title">Exporter les Références</h4>
                <p class="text-muted">Filtrez et exportez les références au format CSV</p>
            </div>
            <a href="{{ route('admin.references.index') }}" class="btn btn-secondary">
                <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon>
                Retour
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Filtres -->
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <iconify-icon icon="solar:filter-bold-duotone" class="me-1"></iconify-icon>
                    Filtres
                </h5>
            </div>
            <div class="card-body">
                <form id="filter-form">
                    <!-- Pilier -->
                    <div class="mb-3">
                        <label for="pillar_id" class="form-label">Pilier</label>
                        <select id="pillar_id" name="pillar_id" class="form-select">
                            <option value="">Tous les piliers</option>
                            @foreach($pillars as $pillar)
                                <option value="{{ $pillar->id }}">{{ $pillar->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type de document -->
                    <div class="mb-3">
                        <label for="doc_kind" class="form-label">Type de document</label>
                        <select id="doc_kind" name="doc_kind" class="form-select">
                            <option value="">Tous les types</option>
                            <option value="CER">Certificats</option>
                            <option value="ATT">Attestations</option>
                        </select>
                    </div>

                    <!-- Période -->
                    <div class="mb-3">
                        <label for="date_from" class="form-label">Date de début</label>
                        <input type="date" id="date_from" name="date_from" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="date_to" class="form-label">Date de fin</label>
                        <input type="date" id="date_to" name="date_to" class="form-control">
                    </div>

                    <!-- Recherche -->
                    <div class="mb-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" id="search" name="search" class="form-control" 
                               placeholder="Nom, email, référence...">
                    </div>

                    <!-- Boutons -->
                    <div class="d-grid gap-2">
                        <button type="button" id="apply-filters" class="btn btn-primary">
                            <iconify-icon icon="solar:magnifer-bold-duotone" class="me-1"></iconify-icon>
                            Appliquer
                        </button>
                        <button type="button" id="reset-filters" class="btn btn-secondary">
                            <iconify-icon icon="solar:refresh-bold-duotone" class="me-1"></iconify-icon>
                            Réinitialiser
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Statistiques</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Total affiché</small>
                    <div class="fw-bold" id="stats-total">0</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Sélectionnés</small>
                    <div class="fw-bold text-primary" id="stats-selected">0</div>
                </div>
                <hr>
                <div class="mb-2">
                    <small class="text-muted">Certificats</small>
                    <div class="fw-bold text-success" id="stats-certificates">0</div>
                </div>
                <div>
                    <small class="text-muted">Attestations</small>
                    <div class="fw-bold text-info" id="stats-attestations">0</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des références -->
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <iconify-icon icon="solar:document-text-bold-duotone" class="me-1"></iconify-icon>
                        Références
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" id="select-all" class="btn btn-sm btn-outline-primary">
                            <iconify-icon icon="solar:check-square-bold-duotone" class="me-1"></iconify-icon>
                            Tout sélectionner
                        </button>
                        <button type="button" id="export-selected" class="btn btn-sm btn-success" disabled>
                            <iconify-icon icon="solar:download-bold-duotone" class="me-1"></iconify-icon>
                            Exporter la sélection
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Loading -->
                <div id="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="text-muted mt-2">Chargement des références...</p>
                </div>

                <!-- Table -->
                <div id="references-table" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="select-all-checkbox" class="form-check-input">
                                    </th>
                                    <th>Référence</th>
                                    <th>Type</th>
                                    <th>Participant</th>
                                    <th>Formation</th>
                                    <th>Pilier</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="references-tbody">
                                <!-- Filled by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Empty state -->
                <div id="empty-state" class="text-center py-5" style="display: none;">
                    <iconify-icon icon="solar:document-bold-duotone" class="fs-1 text-muted"></iconify-icon>
                    <p class="text-muted mt-2">Aucune référence trouvée</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let allReferences = [];
    let filteredReferences = [];
    let selectedIds = new Set();

    // Load references
    function loadReferences() {
        document.getElementById('loading').style.display = 'block';
        document.getElementById('references-table').style.display = 'none';
        document.getElementById('empty-state').style.display = 'none';

        const formData = new FormData(document.getElementById('filter-form'));
        const params = new URLSearchParams(formData);

        fetch(`/admin/references/list?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                allReferences = data;
                filteredReferences = data;
                renderReferences();
                updateStats();
                document.getElementById('loading').style.display = 'none';
            })
            .catch(error => {
                console.error('Error loading references:', error);
                document.getElementById('loading').style.display = 'none';
                document.getElementById('empty-state').style.display = 'block';
            });
    }

    // Render references table
    function renderReferences() {
        const tbody = document.getElementById('references-tbody');
        tbody.innerHTML = '';

        if (filteredReferences.length === 0) {
            document.getElementById('references-table').style.display = 'none';
            document.getElementById('empty-state').style.display = 'block';
            return;
        }

        document.getElementById('references-table').style.display = 'block';
        document.getElementById('empty-state').style.display = 'none';

        filteredReferences.forEach(ref => {
            const tr = document.createElement('tr');
            const isSelected = selectedIds.has(ref.id);
            
            tr.innerHTML = `
                <td>
                    <input type="checkbox" class="form-check-input reference-checkbox" 
                           value="${ref.id}" ${isSelected ? 'checked' : ''}>
                </td>
                <td>
                    <code class="text-primary">${ref.reference}</code>
                </td>
                <td>
                    <span class="badge ${ref.doc_kind === 'CER' ? 'bg-success' : 'bg-info'}">
                        ${ref.doc_kind === 'CER' ? 'Certificat' : 'Attestation'}
                    </span>
                </td>
                <td>
                    <div class="fw-medium">${ref.participant_name || 'N/A'}</div>
                    <small class="text-muted">${ref.participant_email || ''}</small>
                </td>
                <td>
                    <small>${ref.training_title || 'N/A'}</small>
                </td>
                <td>
                    <small class="text-muted">${ref.pillar_name || 'N/A'}</small>
                </td>
                <td>
                    <small>${new Date(ref.created_at).toLocaleDateString('fr-FR')}</small>
                </td>
            `;
            
            tbody.appendChild(tr);
        });

        // Add event listeners to checkboxes
        document.querySelectorAll('.reference-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                if (this.checked) {
                    selectedIds.add(parseInt(this.value));
                } else {
                    selectedIds.delete(parseInt(this.value));
                }
                updateStats();
                updateExportButton();
            });
        });
    }

    // Update statistics
    function updateStats() {
        const total = filteredReferences.length;
        const selected = selectedIds.size;
        const certificates = filteredReferences.filter(r => r.doc_kind === 'CER').length;
        const attestations = filteredReferences.filter(r => r.doc_kind === 'ATT').length;

        document.getElementById('stats-total').textContent = total;
        document.getElementById('stats-selected').textContent = selected;
        document.getElementById('stats-certificates').textContent = certificates;
        document.getElementById('stats-attestations').textContent = attestations;
    }

    // Update export button state
    function updateExportButton() {
        const exportBtn = document.getElementById('export-selected');
        exportBtn.disabled = selectedIds.size === 0;
    }

    // Apply filters
    document.getElementById('apply-filters').addEventListener('click', function() {
        loadReferences();
    });

    // Reset filters
    document.getElementById('reset-filters').addEventListener('click', function() {
        document.getElementById('filter-form').reset();
        selectedIds.clear();
        loadReferences();
    });

    // Select all
    document.getElementById('select-all').addEventListener('click', function() {
        const allChecked = Array.from(document.querySelectorAll('.reference-checkbox')).every(cb => cb.checked);
        
        document.querySelectorAll('.reference-checkbox').forEach(cb => {
            cb.checked = !allChecked;
            const id = parseInt(cb.value);
            if (!allChecked) {
                selectedIds.add(id);
            } else {
                selectedIds.delete(id);
            }
        });
        
        updateStats();
        updateExportButton();
    });

    // Select all checkbox in header
    document.getElementById('select-all-checkbox').addEventListener('change', function() {
        document.querySelectorAll('.reference-checkbox').forEach(cb => {
            cb.checked = this.checked;
            const id = parseInt(cb.value);
            if (this.checked) {
                selectedIds.add(id);
            } else {
                selectedIds.delete(id);
            }
        });
        updateStats();
        updateExportButton();
    });

    // Export selected
    document.getElementById('export-selected').addEventListener('click', function() {
        if (selectedIds.size === 0) {
            alert('Veuillez sélectionner au moins une référence');
            return;
        }

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'GET';
        form.action = '{{ route("admin.references.download") }}';

        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'reference_ids[]';
            input.value = id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    });

    // Initial load
    loadReferences();
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'Créer une Session')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title">Créer une Session de Formation</h4>
                    <p class="text-muted">Planifiez une nouvelle session de formation</p>
                </div>
                <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary">
                    <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.sessions.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Informations de la Session</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Pilier -->
                            <div class="col-12 mb-3">
                                <label for="pillar_id" class="form-label">Pilier <span class="text-danger">*</span></label>
                                <select id="pillar_id" name="pillar_id"
                                    class="form-select @error('pillar_id') is-invalid @enderror" required>
                                    <option value="">-- Sélectionnez un pilier --</option>
                                    @foreach ($pillars as $pillar)
                                        <option value="{{ $pillar->id }}"
                                            {{ old('pillar_id') == $pillar->id ? 'selected' : '' }}>
                                            {{ $pillar->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pillar_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Formation existante -->
                            <div class="col-12 mb-3">
                                <label for="training_id" class="form-label">Formation</label>
                                <select id="training_id" name="training_id"
                                    class="form-select @error('training_id') is-invalid @enderror" disabled>
                                    <option value="">-- Sélectionnez d'abord un pilier --</option>
                                </select>
                                @error('training_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Ou créez une formation personnalisée ci-dessous</small>
                            </div>

                            <!-- Divider -->
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center">
                                    <hr class="flex-grow-1">
                                    <span class="px-3 text-muted"><b>OU</b></span>
                                    <hr class="flex-grow-1">
                                </div>
                            </div>

                            <!-- Formation personnalisée -->
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="create_custom_training">
                                    <label class="form-check-label" for="create_custom_training">
                                        Créer une formation personnalisée
                                    </label>
                                </div>
                            </div>

                            <!-- Champs formation personnalisée (cachés par défaut) -->
                            <div id="custom_training_fields" style="display: none;">
                                <div class="col-12 mb-3">
                                    <label for="custom_training_title" class="form-label">Titre de la formation
                                        personnalisée</label>
                                    <input type="text" id="custom_training_title" name="custom_training_title"
                                        class="form-control @error('custom_training_title') is-invalid @enderror"
                                        value="{{ old('custom_training_title') }}"
                                        placeholder="Ex: Formation spéciale sur mesure">
                                    @error('custom_training_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="custom_training_code" class="form-label">Code personnalisé
                                        (optionnel)</label>
                                    <input type="text" id="custom_training_code" name="custom_training_code"
                                        class="form-control @error('custom_training_code') is-invalid @enderror"
                                        value="{{ old('custom_training_code') }}" placeholder="Ex: CUSTOM-001">
                                    @error('custom_training_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" id="custom_training_has_exam" name="custom_training_has_exam"
                                            class="form-check-input" value="1"
                                            {{ old('custom_training_has_exam') ? 'checked' : '' }}>
                                        <label for="custom_training_has_exam" class="form-check-label">
                                            Cette formation comporte un examen (génère un <strong>Certificat</strong> au
                                            lieu d'une <strong>Attestation</strong>)
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        <iconify-icon icon="solar:info-circle-bold-duotone" class="me-1"></iconify-icon>
                                        Si coché, les références générées seront de type <strong>(Certificat)</strong>,
                                        sinon <strong>(Attestation)</strong>
                                    </small>
                                </div>
                            </div>

                            <!-- Type de livraison -->
                            <div class="col-md-6 mb-3">
                                <label for="delivery_type" class="form-label">Type de Livraison <span
                                        class="text-danger">*</span></label>
                                <select id="delivery_type" name="delivery_type"
                                    class="form-select @error('delivery_type') is-invalid @enderror" required>
                                    <option value="">-- Sélectionnez un type --</option>
                                    <option value="inter-entreprise"
                                        {{ old('delivery_type') == 'inter-entreprise' ? 'selected' : '' }}>Inter-entreprise
                                    </option>
                                    <option value="intra-entreprise"
                                        {{ old('delivery_type') == 'intra-entreprise' ? 'selected' : '' }}>Intra-entreprise
                                    </option>
                                    <option value="en-ligne" {{ old('delivery_type') == 'en-ligne' ? 'selected' : '' }}>En
                                        ligne</option>
                                    <option value="blending" {{ old('delivery_type') == 'blending' ? 'selected' : '' }}>
                                        Blending (Hybride)</option>
                                </select>
                                @error('delivery_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Durée -->
                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Durée (jours) <span
                                        class="text-danger">*</span></label>
                                <input type="number" id="duration" name="duration"
                                    class="form-control @error('duration') is-invalid @enderror"
                                    value="{{ old('duration', 3) }}" min="1" required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date de début -->
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Date de Début <span
                                        class="text-danger">*</span></label>
                                <input type="date" id="start_date" name="start_date"
                                    class="form-control @error('start_date') is-invalid @enderror"
                                    value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date de fin -->
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Date de Fin <span
                                        class="text-danger">*</span></label>
                                <input type="date" id="end_date" name="end_date"
                                    class="form-control @error('end_date') is-invalid @enderror"
                                    value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Lieu -->
                            <div class="col-12 mb-3">
                                <label for="location" class="form-label">Lieu</label>
                                <input type="text" id="location" name="location"
                                    class="form-control @error('location') is-invalid @enderror"
                                    value="{{ old('location') }}" placeholder="Ex: Abidjan, Cote d'Ivoire">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Laissez vide pour les formations en ligne</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Info Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="avatar-lg mx-auto mb-3">
                                <div class="avatar-title bg-primary-subtle rounded-circle">
                                    <iconify-icon icon="solar:calendar-add-bold-duotone"
                                        class="fs-1 text-primary"></iconify-icon>
                                </div>
                            </div>
                            <h5>Nouvelle Session</h5>
                            <p class="text-muted mb-0">Remplissez les informations de la session</p>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <iconify-icon icon="solar:info-circle-bold-duotone" class="me-1"></iconify-icon>
                                Informations
                            </h6>
                            <ul class="mb-0 ps-3">
                                <li class="small">Sélectionnez d'abord le pilier, puis la formation</li>
                                <li class="small">Vous pouvez créer une formation personnalisée si nécessaire</li>
                                <li class="small">Vous pourrez ajouter des participants après la création</li>
                                <li class="small">La durée est calculée en jours ouvrables</li>
                                <li class="small">Les champs marqués <span class="text-danger">*</span> sont obligatoires
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <iconify-icon icon="solar:check-circle-bold-duotone" class="me-1"></iconify-icon>
                                Créer la Session
                            </button>
                            <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline-secondary">
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
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const durationInput = document.getElementById('duration');
            const pillarSelect = document.getElementById('pillar_id');
            const trainingSelect = document.getElementById('training_id');
            const customTrainingCheckbox = document.getElementById('create_custom_training');
            const customTrainingFields = document.getElementById('custom_training_fields');
            const customTitleInput = document.getElementById('custom_training_title');
            const customCodeInput = document.getElementById('custom_training_code');

            // Données des formations par pilier
            const trainingsByPillar = @json($trainings);

            // Auto-calculate end date based on start date and duration
            function calculateEndDate() {
                if (startDateInput.value && durationInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const duration = parseInt(durationInput.value);
                    const endDate = new Date(startDate);
                    endDate.setDate(endDate.getDate() + duration - 1);

                    endDateInput.value = endDate.toISOString().split('T')[0];
                }
            }

            startDateInput.addEventListener('change', calculateEndDate);
            durationInput.addEventListener('change', calculateEndDate);

            // Set minimum end date
            startDateInput.addEventListener('change', function() {
                endDateInput.min = this.value;
            });

            // Filtrer les formations par pilier
            pillarSelect.addEventListener('change', function() {
                const pillarId = this.value;
                trainingSelect.innerHTML = '<option value="">-- Sélectionnez une formation --</option>';

                if (pillarId && trainingsByPillar[pillarId]) {
                    trainingSelect.disabled = false;
                    trainingsByPillar[pillarId].forEach(function(training) {
                        const option = document.createElement('option');
                        option.value = training.id;
                        option.textContent = training.title;
                        trainingSelect.appendChild(option);
                    });
                } else {
                    trainingSelect.disabled = true;
                    trainingSelect.innerHTML =
                        '<option value="">-- Sélectionnez d\'abord un pilier --</option>';
                }

                // Réinitialiser la sélection si une formation était sélectionnée
                trainingSelect.value = '';
            });

            // Gérer l'affichage des champs de formation personnalisée
            customTrainingCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    customTrainingFields.style.display = 'block';
                    trainingSelect.disabled = true;
                    trainingSelect.required = false;
                    customTitleInput.required = true;

                    // Désélectionner la formation existante
                    trainingSelect.value = '';
                } else {
                    customTrainingFields.style.display = 'none';
                    customTitleInput.required = false;
                    customTitleInput.value = '';
                    customCodeInput.value = '';

                    // Réactiver le select de formation si un pilier est sélectionné
                    if (pillarSelect.value) {
                        trainingSelect.disabled = false;
                        trainingSelect.required = true;
                    }
                }
            });

            // Validation du formulaire
            document.querySelector('form').addEventListener('submit', function(e) {
                const isCustom = customTrainingCheckbox.checked;
                const hasTraining = trainingSelect.value !== '';
                const hasCustomTitle = customTitleInput.value.trim() !== '';

                if (!isCustom && !hasTraining) {
                    e.preventDefault();
                    alert('Veuillez sélectionner une formation ou créer une formation personnalisée.');
                    return false;
                }

                if (isCustom && !hasCustomTitle) {
                    e.preventDefault();
                    alert('Veuillez saisir un titre pour la formation personnalisée.');
                    customTitleInput.focus();
                    return false;
                }
            });

            // Restaurer l'état si old() contient des valeurs
            @if (old('pillar_id'))
                pillarSelect.dispatchEvent(new Event('change'));
            @endif

            @if (old('custom_training_title'))
                customTrainingCheckbox.checked = true;
                customTrainingCheckbox.dispatchEvent(new Event('change'));
            @endif
        });
    </script>
@endsection

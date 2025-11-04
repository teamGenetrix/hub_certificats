@extends('layouts.app')

@section('title', 'Générer des Références')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title">Générer des Références</h4>
                    <p class="text-muted">Sélectionnez les participants pour générer leurs numéros de référence</p>
                </div>
                <a href="{{ route('admin.references.index') }}" class="btn btn-secondary">
                    <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Step Indicator -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center position-relative">
                            <div class="d-flex align-items-center flex-fill">
                                <div id="step1-indicator" class="step-circle active">1</div>
                                <div class="step-line flex-fill" id="line1"></div>
                                <div id="step2-indicator" class="step-circle">2</div>
                                <div class="step-line flex-fill" id="line2"></div>
                                <div id="step3-indicator" class="step-circle">3</div>
                                <div class="step-line flex-fill" id="line3"></div>
                                <div id="step4-indicator" class="step-circle">4</div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-primary fw-medium">Pilier</small>
                            <small class="text-muted">Type</small>
                            <small class="text-muted">Formation</small>
                            <small class="text-muted">Participants</small>
                        </div>
                    </div>

                    <!-- Summary Card -->
                    <div id="summary-card" class="alert alert-info d-none mb-4">
                        <h6 class="alert-heading mb-3">
                            <iconify-icon icon="solar:clipboard-list-bold-duotone" class="me-1"></iconify-icon>
                            Récapitulatif de votre sélection
                        </h6>
                        <div class="row g-2 small">
                            <div class="col-md-3" id="summary-pillar" style="display: none;">
                                <strong>Pilier:</strong><br>
                                <span id="summary-pillar-text" class="text-muted"></span>
                            </div>
                            <div class="col-md-3" id="summary-type" style="display: none;">
                                <strong>Type:</strong><br>
                                <span id="summary-type-text" class="text-muted"></span>
                            </div>
                            <div class="col-md-3" id="summary-training" style="display: none;">
                                <strong>Formation:</strong><br>
                                <span id="summary-training-text" class="text-muted"></span>
                            </div>
                            <div class="col-md-3" id="summary-session" style="display: none;">
                                <strong>Session:</strong><br>
                                <span id="summary-session-text" class="text-muted"></span>
                            </div>
                        </div>
                    </div>

                    <form id="reference-form">
                        @csrf

                        <!-- Step 1: Select Pillar -->
                        <div id="step1" class="step-content">
                            <h5 class="mb-3">Étape 1: Sélectionner le Pilier</h5>
                            <div class="mb-3">
                                <label for="pillar_id" class="form-label">Pilier de Formation</label>
                                <select id="pillar_id" name="pillar_id" class="form-select" required>
                                    <option value="">-- Sélectionnez un pilier --</option>
                                    @foreach ($pillars as $pillar)
                                        <option value="{{ $pillar->id }}">{{ $pillar->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" id="next-step1" class="btn btn-primary" disabled>
                                Suivant <iconify-icon icon="solar:arrow-right-bold-duotone" class="ms-1"></iconify-icon>
                            </button>
                        </div>

                        <!-- Step 2: Select Delivery Type -->
                        <div id="step2" class="step-content d-none">
                            <h5 class="mb-3">Étape 2: Type de Formation</h5>
                            <div class="mb-3">
                                <label for="delivery_type" class="form-label">Type de Livraison</label>
                                <select id="delivery_type" name="delivery_type" class="form-select" required>
                                    <option value="">-- Sélectionnez un type --</option>
                                    <option value="inter-entreprise">Inter-entreprise</option>
                                    <option value="intra-entreprise">Intra-entreprise</option>
                                    <option value="en-ligne">En ligne</option>
                                    <option value="blending">Blending (Hybride)</option>
                                </select>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" id="prev-step2" class="btn btn-secondary">
                                    <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon>
                                    Précédent
                                </button>
                                <button type="button" id="next-step2" class="btn btn-primary" disabled>
                                    Suivant <iconify-icon icon="solar:arrow-right-bold-duotone"
                                        class="ms-1"></iconify-icon>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Select Training and Session -->
                        <div id="step3" class="step-content d-none">
                            <h5 class="mb-3">Étape 3: Sélectionner la Formation et la Session</h5>
                            <div class="mb-3">
                                <label for="training_id" class="form-label">Formation</label>
                                <select id="training_id" name="training_id" class="form-select" required>
                                    <option value="">-- Sélectionnez une formation --</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="session_id" class="form-label">Session</label>
                                <select id="session_id" name="session_id" class="form-select" required>
                                    <option value="">-- Sélectionnez une session --</option>
                                </select>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" id="prev-step3" class="btn btn-secondary">
                                    <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon>
                                    Précédent
                                </button>
                                <button type="button" id="next-step3" class="btn btn-primary" disabled>
                                    Suivant <iconify-icon icon="solar:arrow-right-bold-duotone"
                                        class="ms-1"></iconify-icon>
                                </button>
                            </div>
                        </div>

                        <!-- Step 4: Select Participants and Generate -->
                        <div id="step4" class="step-content d-none">
                            <h5 class="mb-3">Étape 4: Sélectionner les Participants</h5>

                            <!-- Info message when prefilled -->
                            @if (request()->has('session_id'))
                                <div class="alert alert-success mb-4">
                                    <iconify-icon icon="solar:check-circle-bold-duotone" class="me-2"></iconify-icon>
                                    Les informations de la session ont été pré-remplies automatiquement. Vous pouvez
                                    modifier les sélections en utilisant les boutons "Précédent" si nécessaire.
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="country_code" class="form-label">Code Pays</label>
                                <input type="text" id="country_code" name="country_code" value="CI"
                                    maxlength="2" class="form-control w-auto text-uppercase" style="max-width: 100px;"
                                    required>
                                <small class="text-muted">Code pays à 2 lettres (ex: BJ, TG, CI)</small>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0">Participants</label>
                                    <button type="button" id="select-all" class="btn btn-sm btn-outline-primary">
                                        Tout sélectionner
                                    </button>
                                </div>
                                <div id="participants-list" class="border rounded p-3"
                                    style="max-height: 400px; overflow-y: auto;">
                                    <div class="text-center text-muted py-4">
                                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                        Chargement des participants...
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" id="prev-step4" class="btn btn-secondary">
                                    <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon>
                                    Précédent
                                </button>
                                <button type="submit" id="generate-btn" class="btn btn-success" disabled>
                                    <iconify-icon icon="solar:check-circle-bold-duotone" class="me-1"></iconify-icon>
                                    Générer les Références
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Modal -->
    <div class="modal fade" id="results-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Références Générées</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="results-content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <a href="{{ route('admin.references.index') }}" class="btn btn-primary">Retour au Tableau de Bord</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e9ecef;
            color: #6c757d;
            font-weight: 600;
            z-index: 1;
        }

        .step-circle.active {
            background-color: var(--bs-primary);
            color: white;
        }

        .step-line {
            height: 2px;
            background-color: #e9ecef;
            margin: 0 -1px;
        }

        .step-line.active {
            background-color: var(--bs-primary);
        }
    </style>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentStep = 1;
            const pillarsData = @json($pillars);
            const prefilledSession = @json($prefilledSession);

            // Summary data
            const summaryData = {
                pillar: null,
                type: null,
                training: null,
                session: null
            };

            // Update summary display
            function updateSummary() {
                const summaryCard = document.getElementById('summary-card');
                let hasData = false;

                if (summaryData.pillar) {
                    document.getElementById('summary-pillar').style.display = 'block';
                    document.getElementById('summary-pillar-text').textContent = summaryData.pillar;
                    hasData = true;
                }

                if (summaryData.type) {
                    document.getElementById('summary-type').style.display = 'block';
                    document.getElementById('summary-type-text').textContent = summaryData.type;
                    hasData = true;
                }

                if (summaryData.training) {
                    document.getElementById('summary-training').style.display = 'block';
                    document.getElementById('summary-training-text').textContent = summaryData.training;
                    hasData = true;
                }

                if (summaryData.session) {
                    document.getElementById('summary-session').style.display = 'block';
                    document.getElementById('summary-session-text').textContent = summaryData.session;
                    hasData = true;
                }

                if (hasData) {
                    summaryCard.classList.remove('d-none');
                } else {
                    summaryCard.classList.add('d-none');
                }
            }

            // Step navigation
            function showStep(step) {
                document.querySelectorAll('.step-content').forEach(el => el.classList.add('d-none'));
                document.getElementById('step' + step).classList.remove('d-none');

                // Update indicators
                for (let i = 1; i <= 4; i++) {
                    const indicator = document.getElementById('step' + i + '-indicator');
                    if (i <= step) {
                        indicator.classList.add('active');
                    } else {
                        indicator.classList.remove('active');
                    }

                    if (i < 4) {
                        const line = document.getElementById('line' + i);
                        if (i < step) {
                            line.classList.add('active');
                        } else {
                            line.classList.remove('active');
                        }
                    }
                }

                currentStep = step;
                updateSummary();
            }

            // Pre-fill form if session data is provided
            if (prefilledSession) {
                console.log('Pre-filling form with session data:', prefilledSession);

                // Set pillar
                document.getElementById('pillar_id').value = prefilledSession.training.pillar.id;
                summaryData.pillar = prefilledSession.training.pillar.name;

                // Set delivery type
                document.getElementById('delivery_type').value = prefilledSession.delivery_type;
                const types = {
                    'inter-entreprise': 'Inter-entreprise',
                    'intra-entreprise': 'Intra-entreprise',
                    'en-ligne': 'En ligne',
                    'blending': 'Blending (Hybride)'
                };
                summaryData.type = types[prefilledSession.delivery_type] || prefilledSession.delivery_type;

                // Set training
                const trainingSelect = document.getElementById('training_id');
                const trainingOption = document.createElement('option');
                trainingOption.value = prefilledSession.training.id;
                trainingOption.textContent = prefilledSession.training.title;
                trainingOption.selected = true;
                trainingSelect.appendChild(trainingOption);
                summaryData.training = prefilledSession.training.title;

                // Set session
                const sessionSelect = document.getElementById('session_id');
                const sessionOption = document.createElement('option');
                sessionOption.value = prefilledSession.id;

                // Format dates from ISO to readable format
                const formatDate = (isoDate) => {
                    const date = new Date(isoDate);
                    return date.toLocaleDateString('fr-FR', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit'
                    });
                };

                const startDate = formatDate(prefilledSession.start_date);
                const endDate = formatDate(prefilledSession.end_date);

                sessionOption.textContent = `${prefilledSession.training.title} - ${startDate} au ${endDate}`;
                sessionOption.selected = true;
                sessionSelect.appendChild(sessionOption);
                summaryData.session = sessionOption.textContent;

                // Load participants and go directly to step 4
                loadParticipants();
                showStep(4);
            }

            // Step 1: Pillar selection
            document.getElementById('pillar_id').addEventListener('change', function() {
                const nextBtn = document.getElementById('next-step1');
                nextBtn.disabled = !this.value;

                // Update summary
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    summaryData.pillar = selectedOption.text;
                } else {
                    summaryData.pillar = null;
                }
                updateSummary();
            });

            document.getElementById('next-step1').addEventListener('click', function() {
                showStep(2);
            });

            // Step 2: Delivery type
            document.getElementById('delivery_type').addEventListener('change', function() {
                const nextBtn = document.getElementById('next-step2');
                nextBtn.disabled = !this.value;

                // Update summary
                if (this.value) {
                    const types = {
                        'inter-entreprise': 'Inter-entreprise',
                        'intra-entreprise': 'Intra-entreprise',
                        'en-ligne': 'En ligne',
                        'blending': 'Blending (Hybride)'
                    };
                    summaryData.type = types[this.value] || this.value;
                    loadTrainings();
                } else {
                    summaryData.type = null;
                }
                updateSummary();
            });

            document.getElementById('prev-step2').addEventListener('click', () => showStep(1));
            document.getElementById('next-step2').addEventListener('click', () => showStep(3));

            // Step 3: Training and Session
            document.getElementById('training_id').addEventListener('change', function() {
                // Update summary
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    summaryData.training = selectedOption.text;
                    loadSessions();
                } else {
                    summaryData.training = null;
                }
                updateSummary();
            });

            document.getElementById('session_id').addEventListener('change', function() {
                const nextBtn = document.getElementById('next-step3');
                nextBtn.disabled = !this.value;

                // Update summary
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    summaryData.session = selectedOption.text;
                } else {
                    summaryData.session = null;
                }
                updateSummary();
            });

            document.getElementById('prev-step3').addEventListener('click', () => showStep(2));
            document.getElementById('next-step3').addEventListener('click', function() {
                showStep(4);
                loadParticipants();
            });

            // Step 4: Participants
            document.getElementById('prev-step4').addEventListener('click', () => showStep(3));

            document.getElementById('select-all').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.participant-checkbox');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => cb.checked = !allChecked);
                updateGenerateButton();
            });

            // Load trainings based on pillar
            function loadTrainings() {
                const pillarId = document.getElementById('pillar_id').value;
                const trainingSelect = document.getElementById('training_id');

                trainingSelect.innerHTML = '<option value="">-- Sélectionnez une formation --</option>';

                fetch(`/admin/references/trainings?pillar_id=${pillarId}`)
                    .then(response => response.json())
                    .then(trainings => {
                        trainings.forEach(training => {
                            const option = document.createElement('option');
                            option.value = training.id;
                            option.textContent = training.title;
                            trainingSelect.appendChild(option);
                        });
                    });
            }

            // Load sessions based on training and delivery type
            function loadSessions() {
                const trainingId = document.getElementById('training_id').value;
                const deliveryType = document.getElementById('delivery_type').value;
                const sessionSelect = document.getElementById('session_id');

                sessionSelect.innerHTML = '<option value="">-- Sélectionnez une session --</option>';

                fetch(`/admin/sessions/all`)
                    .then(response => response.json())
                    .then(sessions => {
                        sessions.forEach(session => {
                            const option = document.createElement('option');
                            option.value = session.id;
                            option.textContent =
                                `${session.training.title} - ${session.start_date} au ${session.end_date} (${session.enrollments.count} participants)`;
                            sessionSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des sessions:', error);
                    });
            }

            // Load participants for selected session
            function loadParticipants() {
                const sessionId = document.getElementById('session_id').value;
                const participantsList = document.getElementById('participants-list');

                participantsList.innerHTML =
                    '<div class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm me-2"></div>Chargement...</div>';

                fetch(`/admin/references/enrollments?session_id=${sessionId}`)
                    .then(response => response.json())
                    .then(enrollments => {
                        if (enrollments.length === 0) {
                            participantsList.innerHTML =
                                '<div class="text-center text-muted py-4">Aucun participant trouvé</div>';
                            return;
                        }

                        participantsList.innerHTML = '';
                        enrollments.forEach(enrollment => {
                            const div = document.createElement('div');
                            div.className = 'form-check p-3 border-bottom';

                            const disabled = enrollment.has_reference ? 'disabled' : '';
                            const checked = enrollment.has_reference ? '' : 'checked';

                            div.innerHTML = `
                        <input class="form-check-input participant-checkbox" type="checkbox" 
                               value="${enrollment.id}" id="enroll${enrollment.id}" ${disabled} ${checked}>
                        <label class="form-check-label w-100" for="enroll${enrollment.id}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-medium">${enrollment.participant.full_name}</div>
                                    <small class="text-muted">${enrollment.participant.email} - ${enrollment.participant.company || 'N/A'}</small>
                                    ${enrollment.has_reference ? '<div class="badge bg-success-subtle text-success mt-1">✓ Référence: ' + enrollment.reference + '</div>' : ''}
                                </div>
                            </div>
                        </label>
                    `;

                            participantsList.appendChild(div);
                        });

                        // Add event listeners to checkboxes
                        document.querySelectorAll('.participant-checkbox').forEach(cb => {
                            cb.addEventListener('change', updateGenerateButton);
                        });

                        updateGenerateButton();
                    });
            }

            function updateGenerateButton() {
                const checkedBoxes = document.querySelectorAll('.participant-checkbox:checked:not([disabled])');
                document.getElementById('generate-btn').disabled = checkedBoxes.length === 0;
            }

            // Form submission
            document.getElementById('reference-form').addEventListener('submit', function(e) {
                e.preventDefault();

                const checkedBoxes = document.querySelectorAll(
                    '.participant-checkbox:checked:not([disabled])');
                const enrollmentIds = Array.from(checkedBoxes).map(cb => cb.value);
                const countryCode = document.getElementById('country_code').value;

                const generateBtn = document.getElementById('generate-btn');
                const originalText = generateBtn.innerHTML;
                generateBtn.disabled = true;
                generateBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Génération en cours...';

                fetch('/admin/references/generate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({
                            enrollment_ids: enrollmentIds,
                            country_code: countryCode
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showResults(data.references);
                        } else {
                            alert('Erreur: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Erreur lors de la génération: ' + error.message);
                    })
                    .finally(() => {
                        generateBtn.disabled = false;
                        generateBtn.innerHTML = originalText;
                    });
            });

            function showResults(references) {
                const resultsContent = document.getElementById('results-content');

                let html =
                    '<div class="alert alert-success"><iconify-icon icon="solar:check-circle-bold-duotone" class="me-2"></iconify-icon>';
                html += references.length + ' référence(s) générée(s) avec succès!</div>';

                html += '<div class="table-responsive">';
                html += '<table class="table table-hover mb-0">';
                html += '<thead class="table-light"><tr>';
                html += '<th>Participant</th>';
                html += '<th>Référence</th>';
                html += '</tr></thead><tbody>';

                references.forEach(ref => {
                    html += '<tr>';
                    html += '<td>' + ref.participant + '</td>';
                    html += '<td><code class="text-primary">' + ref.reference + '</code></td>';
                    html += '</tr>';
                });

                html += '</tbody></table></div>';

                resultsContent.innerHTML = html;

                const modal = new bootstrap.Modal(document.getElementById('results-modal'));
                modal.show();
            }
        });
    </script>
@endsection

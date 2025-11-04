@extends('layouts.app')

@section('title', 'Créer un Participant')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title">Ajouter des Participants</h4>
                    <p class="text-muted">Créez un participant ou importez plusieurs participants via Excel</p>
                </div>
                <a href="{{ route('admin.participants.index') }}" class="btn btn-secondary">
                    <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ session('error') || $errors->has('excel_file') ? '' : 'active' }}"
                        id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab">
                        <iconify-icon icon="solar:user-plus-bold-duotone" class="me-1"></iconify-icon>
                        Création Manuelle
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ session('error') || $errors->has('excel_file') ? 'active' : '' }}"
                        id="import-tab" data-bs-toggle="tab" data-bs-target="#import" type="button" role="tab">
                        <iconify-icon icon="solar:file-download-bold-duotone" class="me-1"></iconify-icon>
                        Import Excel
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content">
        <!-- Manual Creation Tab -->
        <div class="tab-pane fade {{ session('error') || $errors->has('excel_file') ? '' : 'show active' }}" id="manual"
            role="tabpanel">

            <form action="{{ route('admin.participants.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Informations du Participant</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Prénom -->
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">Prénom <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="first_name" name="first_name"
                                            class="form-control @error('first_name') is-invalid @enderror"
                                            value="{{ old('first_name') }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Nom -->
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Nom <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="last_name" name="last_name"
                                            class="form-control @error('last_name') is-invalid @enderror"
                                            value="{{ old('last_name') }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" id="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Téléphone -->
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Téléphone</label>
                                        <input type="tel" id="phone" name="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone') }}" placeholder="+225 XX XX XX XX XX">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Entreprise -->
                                    <div class="col-md-6 mb-3">
                                        <label for="company" class="form-label">Entreprise</label>
                                        <input type="text" id="company" name="company"
                                            class="form-control @error('company') is-invalid @enderror"
                                            value="{{ old('company') }}">
                                        @error('company')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Poste -->
                                    <div class="col-md-6 mb-3">
                                        <label for="job_title" class="form-label">Poste</label>
                                        <input type="text" id="job_title" name="job_title"
                                            class="form-control @error('job_title') is-invalid @enderror"
                                            value="{{ old('job_title') }}">
                                        @error('job_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Pays -->
                                    <div class="col-12 mb-3">
                                        <label for="country" class="form-label">Pays</label>
                                        <input type="text" id="country" name="country"
                                            class="form-control @error('country') is-invalid @enderror"
                                            value="{{ old('country') }}" placeholder="Ex: Côte d'Ivoire">
                                        @error('country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                            <iconify-icon icon="solar:user-plus-bold-duotone"
                                                class="fs-1 text-primary"></iconify-icon>
                                        </div>
                                    </div>
                                    <h5>Nouveau Participant</h5>
                                    <p class="text-muted mb-0">Remplissez les informations du participant</p>
                                </div>

                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <iconify-icon icon="solar:info-circle-bold-duotone" class="me-1"></iconify-icon>
                                        Informations
                                    </h6>
                                    <ul class="mb-0 ps-3">
                                        <li class="small">L'email doit être unique</li>
                                        <li class="small">Les champs marqués <span class="text-danger">*</span> sont
                                            obligatoires</li>
                                        <li class="small">Vous pourrez inscrire le participant à des sessions après la
                                            création</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <iconify-icon icon="solar:check-circle-bold-duotone"
                                            class="me-1"></iconify-icon>
                                        Créer le Participant
                                    </button>
                                    <a href="{{ route('admin.participants.index') }}" class="btn btn-outline-secondary">
                                        Annuler
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <!-- End Manual Creation Tab -->

        <!-- Import Excel Tab -->
        <div class="tab-pane fade {{ session('error') || $errors->has('excel_file') ? 'show active' : '' }}"
            id="import" role="tabpanel">
            <form action="{{ route('admin.participants.import') }}" method="POST" enctype="multipart/form-data"
                id="import-form">
                @csrf

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Importer des Participants</h4>
                            </div>
                            <div class="card-body">
                                <!-- Validation Errors -->
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show mb-4">
                                        <h6 class="alert-heading">
                                            <iconify-icon icon="solar:danger-circle-bold-duotone"
                                                class="me-1"></iconify-icon>
                                            Erreur de validation
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        <ul class="mb-0 small">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show mb-4">
                                        <iconify-icon icon="solar:danger-circle-bold-duotone"
                                            class="me-2"></iconify-icon>
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <!-- Download Template -->
                                <div class="alert alert-primary d-flex align-items-center mb-4">
                                    <iconify-icon icon="solar:document-add-bold-duotone" class="fs-2 me-3"></iconify-icon>
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading mb-1">Téléchargez le template Excel</h6>
                                        <p class="mb-2 small">Utilisez notre template pour vous assurer que vos données
                                            sont au bon format.</p>
                                        <a href="{{ route('admin.participants.template') }}"
                                            class="btn btn-sm btn-primary">
                                            <iconify-icon icon="solar:download-bold-duotone"
                                                class="me-1"></iconify-icon>
                                            Télécharger le Template
                                        </a>
                                    </div>
                                </div>

                                <!-- File Upload -->
                                <div class="mb-3">
                                    <label for="excel_file" class="form-label">Fichier Excel <span
                                            class="text-danger">*</span></label>
                                    <input type="file" id="excel_file" name="excel_file"
                                        class="form-control @error('excel_file') is-invalid @enderror"
                                        accept=".xlsx,.xls,.csv" required>
                                    @error('excel_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Formats acceptés: .xlsx, .xls, .csv (Max: 5MB)</small>
                                </div>

                                <!-- Preview Area -->
                                <div id="preview-area" style="display: none;">
                                    <hr class="my-4">
                                    <h6 class="mb-3">Aperçu des données</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered" id="preview-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Prénom</th>
                                                    <th>Nom</th>
                                                    <th>Email</th>
                                                    <th>Téléphone</th>
                                                    <th>Entreprise</th>
                                                    <th>Poste</th>
                                                    <th>Pays</th>
                                                </tr>
                                            </thead>
                                            <tbody id="preview-body">
                                            </tbody>
                                        </table>
                                    </div>
                                    <p class="text-muted small"><strong id="preview-count">0</strong> participant(s)
                                        seront importé(s)</p>
                                </div>

                                <!-- Instructions -->
                                <div class="alert alert-info mt-4">
                                    <h6 class="alert-heading">
                                        <iconify-icon icon="solar:info-circle-bold-duotone" class="me-1"></iconify-icon>
                                        Instructions
                                    </h6>
                                    <ul class="mb-0 ps-3 small">
                                        <li>Téléchargez le template Excel et remplissez-le avec vos données</li>
                                        <li>Les colonnes <strong>Prénom</strong>, <strong>Nom</strong> et
                                            <strong>Email</strong> sont obligatoires</li>
                                        <li>Les emails doivent être uniques</li>
                                        <li>Les participants en doublon (même email) seront ignorés</li>
                                        <li>Maximum 500 participants par import</li>
                                    </ul>
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
                                        <div class="avatar-title bg-success-subtle rounded-circle">
                                            <iconify-icon icon="solar:file-download-bold-duotone"
                                                class="fs-1 text-success"></iconify-icon>
                                        </div>
                                    </div>
                                    <h5>Import Excel</h5>
                                    <p class="text-muted mb-0">Importez plusieurs participants en une seule fois</p>
                                </div>

                                <div class="alert alert-success">
                                    <h6 class="alert-heading">
                                        <iconify-icon icon="solar:check-circle-bold-duotone"
                                            class="me-1"></iconify-icon>
                                        Avantages
                                    </h6>
                                    <ul class="mb-0 ps-3 small">
                                        <li>Gain de temps considérable</li>
                                        <li>Import en masse facilité</li>
                                        <li>Validation automatique des données</li>
                                        <li>Rapport détaillé après import</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success" id="import-btn" disabled>
                                        <iconify-icon icon="solar:upload-bold-duotone" class="me-1"></iconify-icon>
                                        Importer les Participants
                                    </button>
                                    <a href="{{ route('admin.participants.index') }}" class="btn btn-outline-secondary">
                                        Annuler
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- End Import Excel Tab -->
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('excel_file');
            const importBtn = document.getElementById('import-btn');
            const importForm = document.getElementById('import-form');
            const previewArea = document.getElementById('preview-area');

            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const fileName = file.name;
                    const fileSize = (file.size / 1024).toFixed(2); // KB

                    console.log('Fichier sélectionné:', fileName, '(' + fileSize + ' KB)');
                    importBtn.disabled = false;
                } else {
                    importBtn.disabled = true;
                }
            });

            // Show loading state on form submit
            importForm.addEventListener('submit', function(e) {
                importBtn.disabled = true;
                importBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Import en cours...';
                console.log('Formulaire soumis, import en cours...');
            });
        });
    </script>
@endsection

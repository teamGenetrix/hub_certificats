@extends('layouts.app')

@section('title', 'Importer des Alias Legacy')

@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="page-title-box d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Importer des Alias Legacy</h4>
            <a href="{{ route('admin.legacy-aliases.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Retour
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>
                <strong>{{ session('success') }}</strong>
                @if (session('imported_count'))
                    <br><small>{{ session('imported_count') }} alias importés avec succès.</small>
                @endif
                @if (session('skipped_count'))
                    <br><small>{{ session('skipped_count') }} lignes ignorées (doublons ou erreurs).</small>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('errors_detail'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Erreurs détectées :</strong>
                <ul class="mb-0 mt-2">
                    @foreach (session('errors_detail') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <!-- Upload Card -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-upload me-2"></i>Télécharger le fichier CSV
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.legacy-aliases.process-import') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Session Selection -->
                            <div class="mb-4">
                                <label for="session_id" class="form-label">
                                    Session <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('session_id') is-invalid @enderror" 
                                        id="session_id" 
                                        name="session_id" 
                                        required>
                                    <option value="">Sélectionnez une session...</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" {{ old('session_id') == $session->id ? 'selected' : '' }}>
                                            {{ $session->training->title }} - {{ $session->start_date->format('d/m/Y') }} 
                                            ({{ $session->training->pillar->name }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">
                                    Sélectionnez la session pour laquelle vous souhaitez générer les références.
                                </div>
                                @error('session_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="csv_file" class="form-label">
                                    Fichier CSV <span class="text-danger">*</span>
                                </label>
                                <input type="file" 
                                       class="form-control @error('csv_file') is-invalid @enderror" 
                                       id="csv_file" 
                                       name="csv_file" 
                                       accept=".csv"
                                       required>
                                <div class="form-text">
                                    Le fichier doit être au format CSV avec les colonnes : 
                                    <code>old_reference</code> et <code>participant_email</code>
                                </div>
                                @error('csv_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                <strong>Comment ça marche ?</strong>
                                <ol class="mb-0 mt-2 ps-3">
                                    <li>Sélectionnez la <strong>session</strong> concernée</li>
                                    <li>Préparez un CSV avec : ancienne référence + email du participant</li>
                                    <li>Le système trouve l'inscription (enrollment) du participant dans la session</li>
                                    <li><strong>Génère une nouvelle référence</strong> conforme à la nomenclature actuelle</li>
                                    <li>Crée automatiquement le lien entre ancienne et nouvelle référence</li>
                                    <li>Les doublons et erreurs sont ignorés avec un rapport détaillé</li>
                                </ol>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.legacy-aliases.download-template') }}" class="btn btn-outline-primary">
                                    <i class="bx bx-download me-1"></i> Télécharger le modèle CSV
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-upload me-1"></i> Importer les alias
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview Section (if data exists) -->
                @if (session('preview_data'))
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-list-ul me-2"></i>Aperçu des données
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Ancienne Réf.</th>
                                            <th>Pilier</th>
                                            <th>Participant</th>
                                            <th>Formation</th>
                                            <th>Date Session</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (session('preview_data') as $row)
                                            <tr>
                                                <td><code>{{ $row['old_reference'] ?? 'N/A' }}</code></td>
                                                <td>{{ $row['pillar_code'] ?? 'N/A' }}</td>
                                                <td>{{ $row['participant_email'] ?? 'N/A' }}</td>
                                                <td>{{ $row['training_name'] ?? 'N/A' }}</td>
                                                <td>{{ $row['session_date'] ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <!-- Instructions Card -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bx bx-book-open text-primary me-2"></i>Instructions
                        </h5>
                        <p class="card-text">
                            <strong>Format du fichier CSV :</strong>
                        </p>
                        <ul class="small">
                            <li><strong>old_reference</strong> : L'ancienne référence à lier</li>
                            <li><strong>participant_email</strong> : Email du participant inscrit à la session</li>
                        </ul>
                        <hr>
                        <p class="card-text">
                            <strong>Exemple de ligne :</strong><br>
                            <code class="small">OLD-2023-001,mail@mail.com</code>
                        </p>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bx bx-bar-chart text-success me-2"></i>Statistiques
                        </h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Alias existants:</span>
                            <strong>{{ \App\Models\LegacyAlias::count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Références disponibles:</span>
                            <strong>{{ \App\Models\Reference::count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Participants:</span>
                            <strong>{{ \App\Models\Participant::count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Preview file name on selection
        document.getElementById('csv_file').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                console.log('Fichier sélectionné:', fileName);
            }
        });
    </script>
    @endpush
@endsection

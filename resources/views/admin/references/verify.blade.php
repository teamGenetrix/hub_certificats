@extends('layouts.app')

@section('title', 'Vérifier une Référence')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title">Vérifier une Référence</h4>
                    <p class="text-muted">Vérifiez l'authenticité d'un certificat ou d'une attestation</p>
                </div>
                <a href="{{ route('admin.references.index') }}" class="btn btn-secondary">
                    <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.references.verify') }}" method="POST">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-10">
                                <label for="reference" class="form-label">Numéro de Référence</label>
                                <input type="text" id="reference" name="reference" value="{{ $reference_number ?? '' }}"
                                    placeholder="Ex: CI_CERT_AMCONT-007-F_112024_0001_00042 ou OLD-CERT-2023-001"
                                    class="form-control text-uppercase" style="font-family: monospace;" required>
                                <div class="form-text">
                                    <i class="bx bx-info-circle me-1"></i>
                                    Vous pouvez rechercher une nouvelle référence ou une ancienne référence liée.
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <iconify-icon icon="solar:magnifer-bold-duotone" class="me-1"></iconify-icon>
                                    Vérifier
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results -->
    @if (isset($found))
        @if ($found)
            <!-- Valid Reference -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex align-items-center">
                                <iconify-icon icon="solar:check-circle-bold-duotone" class="fs-1 me-3"></iconify-icon>
                                <div>
                                    <h4 class="text-white mb-1">Référence Valide</h4>
                                    <p class="mb-0 opacity-75">Ce certificat/attestation est authentique</p>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            @if (isset($is_legacy_alias) && $is_legacy_alias)
                                <!-- Legacy Alias Notice -->
                                <div class="alert alert-info mb-4">
                                    <div class="d-flex align-items-start">
                                        <iconify-icon icon="solar:info-circle-bold-duotone" class="fs-3 me-3"></iconify-icon>
                                        <div>
                                            <h6 class="mb-2">Ancienne Référence Reconnue</h6>
                                            <p class="mb-2">
                                                Vous avez recherché une ancienne référence : <code class="text-info">{{ $old_reference }}</code>
                                            </p>
                                            <p class="mb-0">
                                                Cette référence a été liée à la nouvelle référence conforme : 
                                                <code class="text-primary">{{ $reference->reference }}</code>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Reference Number -->
                            <div class="mb-4 pb-4 border-bottom">
                                <label class="form-label text-muted mb-2">Numéro de Référence</label>
                                <h3 class="text-primary mb-0" style="font-family: monospace;">{{ $reference->reference }}
                                </h3>
                                @if (isset($is_legacy_alias) && $is_legacy_alias)
                                    <div class="mt-2">
                                        <span class="badge bg-info-subtle text-info">
                                            <iconify-icon icon="solar:link-bold-duotone" class="me-1"></iconify-icon>
                                            Liée à l'ancienne référence : {{ $old_reference }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Document Type -->
                            <div class="mb-4 pb-4 border-bottom">
                                <label class="form-label text-muted mb-2">Type de Document</label>
                                <div>
                                    <span
                                        class="badge {{ $reference->doc_kind === 'CERT' ? 'bg-success' : 'bg-warning' }} fs-6 px-3 py-2">
                                        {{ $reference->doc_kind === 'CERT' ? 'Certificat' : 'Attestation' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Participant Information -->
                            <div class="mb-4 pb-4 border-bottom">
                                <h5 class="mb-3">Informations du Participant</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small">Nom Complet</label>
                                        <p class="mb-0 fw-medium">{{ $reference->enrollment->participant->full_name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small">Email</label>
                                        <p class="mb-0 fw-medium">{{ $reference->enrollment->participant->email }}</p>
                                    </div>
                                    @if ($reference->enrollment->participant->phone)
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small">Téléphone</label>
                                            <p class="mb-0 fw-medium">{{ $reference->enrollment->participant->phone }}</p>
                                        </div>
                                    @endif
                                    @if ($reference->enrollment->participant->company)
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small">Entreprise</label>
                                            <p class="mb-0 fw-medium">{{ $reference->enrollment->participant->company }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Training Information -->
                            <div class="mb-4 pb-4 border-bottom">
                                <h5 class="mb-3">Informations de la Formation</h5>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label text-muted small">Formation</label>
                                        <p class="mb-0 fw-medium">{{ $reference->enrollment->session->training->title }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small">Pilier</label>
                                        <p class="mb-0 fw-medium">
                                            {{ $reference->enrollment->session->training->pillar->name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small">Code Formation</label>
                                        <p class="mb-0" style="font-family: monospace;">
                                            {{ $reference->enrollment->session->training->code }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Session Information -->
                            <div class="mb-4 pb-4 border-bottom">
                                <h5 class="mb-3">Informations de la Session</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small">Date de Début</label>
                                        <p class="mb-0 fw-medium">
                                            {{ $reference->enrollment->session->start_date->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small">Date de Fin</label>
                                        <p class="mb-0 fw-medium">
                                            {{ $reference->enrollment->session->end_date->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small">Type de Livraison</label>
                                        <p class="mb-0 fw-medium text-capitalize">
                                            {{ $reference->enrollment->session->delivery_type }}</p>
                                    </div>
                                    @if ($reference->enrollment->session->location)
                                        <div class="col-md-6">
                                            <label class="form-label text-muted small">Lieu</label>
                                            <p class="mb-0 fw-medium">{{ $reference->enrollment->session->location }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Generation Date -->
                            <div>
                                <label class="form-label text-muted small">Date de Génération</label>
                                <p class="mb-0 fw-medium">{{ $reference->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Invalid Reference -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <div class="d-flex align-items-center">
                                <iconify-icon icon="solar:close-circle-bold-duotone" class="fs-1 me-3"></iconify-icon>
                                <div>
                                    <h4 class="text-white mb-1">Référence Invalide</h4>
                                    <p class="mb-0 opacity-75">Cette référence n'existe pas dans notre base de données</p>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="alert alert-danger">
                                <p class="mb-2">
                                    <strong>Numéro recherché:</strong> <code
                                        class="text-danger">{{ $reference_number }}</code>
                                </p>
                                <p class="mb-0">
                                    Ce numéro de référence n'a pas été trouvé. Veuillez vérifier que vous avez saisi le bon
                                    numéro ou contactez Genetrix Academy pour plus d'informations.
                                </p>
                            </div>

                            <div class="mt-4">
                                <h6 class="mb-3">Raisons possibles:</h6>
                                <ul class="text-muted mb-0">
                                    <li>Le numéro de référence contient une erreur de frappe</li>
                                    <li>Le certificat/attestation n'a pas encore été enregistré dans le système</li>
                                    <li>Il s'agit d'un faux document</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endsection

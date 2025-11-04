@extends('layouts.app')

@section('title', 'Détails du Participant')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title">Détails du Participant</h4>
                    <p class="text-muted mb-0">{{ $participant->full_name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.participants.edit', $participant) }}" class="btn btn-primary">
                        <iconify-icon icon="solar:pen-bold-duotone" class="me-1"></iconify-icon>
                        Modifier
                    </a>
                    <a href="{{ route('admin.participants.index') }}" class="btn btn-secondary">
                        <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon>
                        Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <iconify-icon icon="solar:check-circle-bold-duotone" class="me-2"></iconify-icon>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Participant Info -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-primary-subtle rounded-circle">
                                <iconify-icon icon="solar:user-bold-duotone" class="fs-1 text-primary"></iconify-icon>
                            </div>
                        </div>
                        <h5>{{ $participant->full_name }}</h5>
                        @if ($participant->job_title)
                            <p class="text-muted mb-0">{{ $participant->job_title }}</p>
                        @endif
                    </div>

                    <div class="border-top pt-3">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Email</label>
                            <div class="fw-medium">{{ $participant->email }}</div>
                        </div>

                        @if ($participant->phone)
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Téléphone</label>
                                <div class="fw-medium">{{ $participant->phone }}</div>
                            </div>
                        @endif

                        @if ($participant->company)
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Entreprise</label>
                                <div class="fw-medium">{{ $participant->company }}</div>
                            </div>
                        @endif

                        @if ($participant->country)
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Pays</label>
                                <div class="fw-medium">{{ $participant->country }}</div>
                            </div>
                        @endif

                        <div>
                            <label class="form-label text-muted small mb-1">Inscriptions</label>
                            <div>
                                <span class="badge bg-primary fs-6">{{ $participant->enrollments->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollments & References -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Historique des Formations</h4>
                </div>
                <div class="card-body">
                    @if ($participant->enrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Formation</th>
                                        <th>Session</th>
                                        <th>Type</th>
                                        <th>Référence</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($participant->enrollments as $enrollment)
                                        <tr>
                                            <td>
                                                <div class="fw-medium">{{ $enrollment->session->training->title }}</div>
                                                <small
                                                    class="text-muted">{{ $enrollment->session->training->pillar->name }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $enrollment->session->start_date->format('d/m/Y') }}</div>
                                                <small
                                                    class="text-muted">{{ $enrollment->session->end_date->format('d/m/Y') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info text-capitalize">
                                                    {{ $enrollment->session->delivery_type }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($enrollment->reference)
                                                    <code
                                                        class="text-success">{{ $enrollment->reference->reference }}</code>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">En attente</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <iconify-icon icon="solar:document-text-bold-duotone" class="fs-1 mb-3"></iconify-icon>
                            <p class="mb-0">Aucune formation suivie pour le moment</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded">
                                        <iconify-icon icon="solar:document-text-bold-duotone" class="fs-4"></iconify-icon>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Formations</p>
                                    <h4 class="mb-0">{{ $participant->enrollments->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded">
                                        <iconify-icon icon="solar:check-circle-bold-duotone" class="fs-4"></iconify-icon>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Références</p>
                                    <h4 class="mb-0">{{ $participant->enrollments->whereNotNull('reference')->count() }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded">
                                        <iconify-icon icon="solar:clock-circle-bold-duotone" class="fs-4"></iconify-icon>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">En attente</p>
                                    <h4 class="mb-0">{{ $participant->enrollments->whereNull('reference')->count() }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

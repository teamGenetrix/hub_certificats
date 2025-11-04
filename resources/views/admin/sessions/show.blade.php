@extends('layouts.app')

@section('title', 'Détails de la Session')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title">Détails de la Session</h4>
                    <p class="text-muted mb-0">{{ $session->training->title }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.sessions.edit', $session) }}" class="btn btn-primary">
                        <iconify-icon icon="solar:pen-bold-duotone" class="me-1"></iconify-icon>
                        Modifier
                    </a>
                    <a href="{{ route('admin.sessions.index') }}" class="btn btn-secondary">
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

    @if (session('error'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <iconify-icon icon="solar:danger-circle-bold-duotone" class="me-2"></iconify-icon>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Session Info -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-primary-subtle rounded-circle">
                                <iconify-icon icon="solar:calendar-bold-duotone" class="fs-1 text-primary"></iconify-icon>
                            </div>
                        </div>
                        <h5>{{ $session->training->title }}</h5>
                        <p class="text-muted mb-0">{{ $session->training->pillar->name }}</p>
                    </div>

                    <div class="border-top pt-3">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Type de Formation</label>
                            <div>
                                <span class="badge bg-info-subtle text-info text-capitalize">
                                    {{ $session->delivery_type }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Date de Début</label>
                            <div class="fw-medium">{{ $session->start_date->format('d/m/Y') }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Date de Fin</label>
                            <div class="fw-medium">{{ $session->end_date->format('d/m/Y') }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Durée</label>
                            <div class="fw-medium">{{ $session->duration }} jour(s)</div>
                        </div>

                        @if ($session->location)
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Lieu</label>
                                <div class="fw-medium">{{ $session->location }}</div>
                            </div>
                        @endif

                        <div>
                            <label class="form-label text-muted small mb-1">Participants Inscrits</label>
                            <div>
                                <span class="badge bg-primary fs-6">{{ $session->enrollments->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Participants List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Participants Inscrits</h4>
                    <a href="{{ route('admin.sessions.add-participants', $session) }}" class="btn btn-sm btn-primary">
                        <iconify-icon icon="solar:user-plus-bold-duotone" class="me-1"></iconify-icon>
                        Ajouter des Participants
                    </a>
                </div>
                <div class="card-body">
                    @if ($session->enrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Participant</th>
                                        <th>Email</th>
                                        <th>Entreprise</th>
                                        <th>Référence</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($session->enrollments as $enrollment)
                                        <tr>
                                            <td class="fw-medium">{{ $enrollment->participant->full_name }}</td>
                                            <td>{{ $enrollment->participant->email }}</td>
                                            <td>{{ $enrollment->participant->company ?? '-' }}</td>
                                            <td>
                                                @if ($enrollment->reference)
                                                    <code
                                                        class="text-success">{{ $enrollment->reference->reference }}</code>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">En attente</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if (!$enrollment->reference)
                                                    <button type="button" class="btn btn-sm btn-light text-danger"
                                                        onclick="confirmRemove({{ $enrollment->id }})" title="Retirer">
                                                        <iconify-icon
                                                            icon="solar:trash-bin-trash-bold-duotone"></iconify-icon>
                                                    </button>

                                                    <form id="remove-form-{{ $enrollment->id }}"
                                                        action="{{ route('admin.sessions.remove-participant', [$session, $enrollment]) }}"
                                                        method="POST" class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @else
                                                    <span class="text-muted small">Référence générée</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="fs-1 mb-3"></iconify-icon>
                            <p class="mb-3">Aucun participant inscrit pour le moment</p>
                            <a href="{{ route('admin.sessions.add-participants', $session) }}" class="btn btn-primary">
                                <iconify-icon icon="solar:user-plus-bold-duotone" class="me-1"></iconify-icon>
                                Ajouter des Participants
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Actions Rapides</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('admin.references.create') }}?session_id={{ $session->id }}"
                                class="btn btn-success w-100">
                                <iconify-icon icon="solar:document-add-bold-duotone" class="me-2"></iconify-icon>
                                Générer les Références
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.sessions.add-participants', $session) }}"
                                class="btn btn-primary w-100">
                                <iconify-icon icon="solar:user-plus-bold-duotone" class="me-2"></iconify-icon>
                                Ajouter des Participants
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function confirmRemove(enrollmentId) {
            if (confirm('Êtes-vous sûr de vouloir retirer ce participant de la session ?')) {
                document.getElementById('remove-form-' + enrollmentId).submit();
            }
        }
    </script>
@endsection

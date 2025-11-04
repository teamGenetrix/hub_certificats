@extends('layouts.app')

@section('title', 'Participants')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title">Participants</h4>
                    <p class="text-muted">Gérez les participants aux formations</p>
                </div>
                <a href="{{ route('admin.participants.create') }}" class="btn btn-primary">
                    <iconify-icon icon="solar:user-plus-bold-duotone" class="me-1"></iconify-icon>
                    Nouveau Participant
                </a>
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

    @if (session('warning'))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <iconify-icon icon="solar:danger-triangle-bold-duotone" class="me-2"></iconify-icon>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    @if (session('import_errors') && count(session('import_errors')) > 0)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading">
                        <iconify-icon icon="solar:danger-triangle-bold-duotone" class="me-1"></iconify-icon>
                        Erreurs d'import ({{ count(session('import_errors')) }})
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <ul class="mb-0 small">
                        @foreach (array_slice(session('import_errors'), 0, 10) as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        @if (count(session('import_errors')) > 10)
                            <li><em>... et {{ count(session('import_errors')) - 10 }} autre(s) erreur(s)</em></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if (session('import_duplicates') && count(session('import_duplicates')) > 0)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading">
                        <iconify-icon icon="solar:info-circle-bold-duotone" class="me-1"></iconify-icon>
                        Doublons ignorés ({{ count(session('import_duplicates')) }})
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <ul class="mb-0 small">
                        @foreach (array_slice(session('import_duplicates'), 0, 10) as $duplicate)
                            <li>{{ $duplicate }}</li>
                        @endforeach
                        @if (count(session('import_duplicates')) > 10)
                            <li><em>... et {{ count(session('import_duplicates')) - 10 }} autre(s) doublon(s)</em></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Liste des Participants</h4>
                    <div class="text-muted">
                        Total: <strong>{{ $participants->total() }}</strong> participants
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom Complet</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Entreprise</th>
                                    <th>Pays</th>
                                    <th>Inscriptions</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($participants as $participant)
                                    <tr>
                                        <td>
                                            <div class="fw-medium">{{ $participant->full_name }}</div>
                                            @if ($participant->job_title)
                                                <small class="text-muted">{{ $participant->job_title }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $participant->email }}</td>
                                        <td>{{ $participant->phone ?? '-' }}</td>
                                        <td>{{ $participant->company ?? '-' }}</td>
                                        <td>{{ $participant->country ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary">
                                                {{ $participant->enrollments_count }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.participants.show', $participant) }}"
                                                    class="btn btn-light" title="Voir">
                                                    <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon>
                                                </a>
                                                <a href="{{ route('admin.participants.edit', $participant) }}"
                                                    class="btn btn-light" title="Modifier">
                                                    <iconify-icon icon="solar:pen-bold-duotone"></iconify-icon>
                                                </a>
                                                <button type="button" class="btn btn-light text-danger"
                                                    onclick="confirmDelete({{ $participant->id }})" title="Supprimer">
                                                    <iconify-icon icon="solar:trash-bin-trash-bold-duotone"></iconify-icon>
                                                </button>
                                            </div>

                                            <form id="delete-form-{{ $participant->id }}"
                                                action="{{ route('admin.participants.destroy', $participant) }}"
                                                method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <iconify-icon icon="solar:users-group-rounded-bold-duotone"
                                                class="fs-1 mb-2"></iconify-icon>
                                            <p class="mb-0">Aucun participant trouvé</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($participants->hasPages())
                    <div class="card-footer">
                        {{ $participants->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function confirmDelete(participantId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce participant ? Cette action est irréversible.')) {
                document.getElementById('delete-form-' + participantId).submit();
            }
        }
    </script>
@endsection

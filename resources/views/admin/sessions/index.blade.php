@extends('layouts.app')

@section('title', 'Sessions de Formation')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title">Sessions de Formation</h4>
                    <p class="text-muted">Gérez les sessions de formation et les inscriptions</p>
                </div>
                <a href="{{ route('admin.sessions.create') }}" class="btn btn-primary">
                    <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>
                    Nouvelle Session
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Liste des Sessions</h4>
                    <div class="text-muted">
                        Total: <strong>{{ $sessions->total() }}</strong> sessions
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Formation</th>
                                    <th>Type</th>
                                    <th>Dates</th>
                                    <th>Durée</th>
                                    <th>Lieu</th>
                                    <th>Participants</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessions as $session)
                                    <tr>
                                        <td>
                                            <div class="fw-medium">{{ Str::of($session->training->title)->limit(50) }}</div>
                                            {{-- truncate le nom de la formation --}}
                                            <small
                                                class="text-muted">{{ Str::of($session->training->pillar->name)->limit(35) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info text-capitalize">
                                                {{ $session->delivery_type }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>{{ $session->start_date->format('d/m/Y') }}</div>
                                            <small class="text-muted">au {{ $session->end_date->format('d/m/Y') }}</small>
                                        </td>
                                        <td>{{ $session->duration }} jour(s)</td>
                                        <td>{{ $session->location ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary">
                                                {{ $session->enrollments->count() }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.sessions.show', $session) }}"
                                                    class="btn btn-light" title="Voir">
                                                    <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon>
                                                </a>
                                                <a href="{{ route('admin.sessions.edit', $session) }}"
                                                    class="btn btn-light" title="Modifier">
                                                    <iconify-icon icon="solar:pen-bold-duotone"></iconify-icon>
                                                </a>
                                                <button type="button" class="btn btn-light text-danger"
                                                    onclick="confirmDelete({{ $session->id }})" title="Supprimer">
                                                    <iconify-icon icon="solar:trash-bin-trash-bold-duotone"></iconify-icon>
                                                </button>
                                            </div>

                                            <form id="delete-form-{{ $session->id }}"
                                                action="{{ route('admin.sessions.destroy', $session) }}" method="POST"
                                                class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <iconify-icon icon="solar:inbox-line-bold-duotone"
                                                class="fs-1 mb-2"></iconify-icon>
                                            <p class="mb-0">Aucune session trouvée</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($sessions->hasPages())
                    <div class="card-footer">
                        {{ $sessions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function confirmDelete(sessionId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette session ? Cette action est irréversible.')) {
                document.getElementById('delete-form-' + sessionId).submit();
            }
        }
    </script>
@endsection

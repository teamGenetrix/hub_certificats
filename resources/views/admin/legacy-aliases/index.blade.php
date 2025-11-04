@extends('layouts.app')

@section('title', 'Gestion des Alias Legacy')

@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="page-title-box d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Gestion des Alias Legacy</h4>
            <a href="{{ route('admin.legacy-aliases.import') }}" class="btn btn-success">
                <i class="bx bx-upload me-1"></i> Générer & Lier (Import CSV)
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>
                {{ session('success') }}
                @if (session('imported_count') !== null)
                    <div class="mt-2">
                        <strong>Résumé :</strong> {{ session('imported_count') }} alias créé(s)
                        @if (session('skipped_count') > 0)
                            , {{ session('skipped_count') }} ligne(s) ignorée(s)
                        @endif
                    </div>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-2"></i>
                {{ session('warning') }}
                @if (session('imported_count') !== null)
                    <div class="mt-2">
                        <strong>Résumé :</strong> {{ session('imported_count') }} alias créé(s), {{ session('skipped_count') }} ligne(s) ignorée(s)
                    </div>
                @endif
                @if (session('errors_detail'))
                    <div class="mt-2">
                        <strong>Détails des erreurs :</strong>
                        <ul class="mb-0 mt-1">
                            @foreach (session('errors_detail') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-x-circle me-2"></i>
                {{ session('error') }}
                @if (session('skipped_count') !== null)
                    <div class="mt-2">
                        <strong>Résumé :</strong> {{ session('skipped_count') }} ligne(s) ignorée(s)
                    </div>
                @endif
                @if (session('errors_detail'))
                    <div class="mt-2">
                        <strong>Détails des erreurs :</strong>
                        <ul class="mb-0 mt-1">
                            @foreach (session('errors_detail') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Info Card -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bx bx-info-circle text-info fs-3 me-3"></i>
                    <div>
                        <h6 class="mb-1">À propos des Alias Legacy</h6>
                        <p class="mb-0 text-muted">
                            Les alias permettent de <strong>générer de nouvelles références</strong> conformes à la nomenclature actuelle 
                            et de les lier aux anciennes références existantes. Le système crée automatiquement les nouvelles références 
                            en fonction du pilier, de la formation et du participant. Cela garantit la traçabilité et la continuité des documents.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.legacy-aliases.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <label for="search" class="form-label">Rechercher</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Ancienne ou nouvelle référence...">
                    </div>
                    <div class="col-md-4">
                        <label for="pillar_id" class="form-label">Pilier</label>
                        <select class="form-select" id="pillar_id" name="pillar_id">
                            <option value="">Tous les piliers</option>
                            @foreach ($pillars as $pillar)
                                <option value="{{ $pillar->id }}" {{ request('pillar_id') == $pillar->id ? 'selected' : '' }}>
                                    {{ $pillar->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('admin.legacy-aliases.index') }}" class="btn btn-secondary">
                            <i class="bx bx-reset me-1"></i> Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Aliases Table -->
        <div class="card">
            <div class="card-body">
                @if ($aliases->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Ancienne Référence</th>
                                    <th>Nouvelle Référence</th>
                                    <th>Type</th>
                                    <th>Pilier</th>
                                    <th>Participant</th>
                                    <th>Date de création</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($aliases as $alias)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $alias->old_reference }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $alias->reference->reference }}</span>
                                        </td>
                                        <td>
                                            @if ($alias->reference->doc_kind === 'CER')
                                                <span class="badge bg-info">Certificat</span>
                                            @else
                                                <span class="badge bg-warning">Attestation</span>
                                            @endif
                                        </td>
                                        <td>{{ $alias->reference->pillar->name ?? 'N/A' }}</td>
                                        <td>{{ $alias->reference->enrollment->participant->full_name ?? 'N/A' }}</td>
                                        <td>{{ $alias->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.legacy-aliases.edit', $alias) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="Modifier">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-danger" 
                                                        onclick="confirmDelete({{ $alias->id }})"
                                                        title="Supprimer">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                            <form id="delete-form-{{ $alias->id }}" 
                                                  action="{{ route('admin.legacy-aliases.destroy', $alias) }}" 
                                                  method="POST" 
                                                  class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Affichage de {{ $aliases->firstItem() }} à {{ $aliases->lastItem() }} sur {{ $aliases->total() }} alias
                        </div>
                        <div>
                            {{ $aliases->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bx bx-folder-open text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">Aucun alias trouvé</h5>
                        <p class="text-muted">
                            @if (request()->hasAny(['search', 'pillar_id']))
                                Aucun résultat ne correspond à vos critères de recherche.
                            @else
                                Importez vos anciennes références pour générer automatiquement les nouvelles et créer les liens.
                            @endif
                        </p>
                        <a href="{{ route('admin.legacy-aliases.import') }}" class="btn btn-success mt-2">
                            <i class="bx bx-upload me-1"></i> Importer des alias
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmDelete(aliasId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet alias ? Cette action est irréversible.')) {
                document.getElementById('delete-form-' + aliasId).submit();
            }
        }
    </script>
    @endpush
@endsection

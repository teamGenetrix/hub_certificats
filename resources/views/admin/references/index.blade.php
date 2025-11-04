@extends('layouts.app')

@section('title', 'Gestion des Références')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Gestion des Références</h4>
            <p class="text-muted">Générez et gérez les numéros de référence pour les certificats et attestations</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded bg-primary-subtle">
                            <iconify-icon icon="solar:document-text-bold-duotone" class="fs-2 text-primary"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1">Total Références</p>
                        <h3 class="mb-0">{{ $statistics['total_references'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded bg-success-subtle">
                            <iconify-icon icon="solar:verified-check-bold-duotone" class="fs-2 text-success"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1">Certificats</p>
                        <h3 class="mb-0 text-success">{{ $statistics['total_certificates'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded bg-warning-subtle">
                            <iconify-icon icon="solar:document-bold-duotone" class="fs-2 text-warning"></iconify-icon>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1">Attestations</p>
                        <h3 class="mb-0 text-warning">{{ $statistics['total_attestations'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Actions Rapides</h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ route('admin.references.create') }}" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:add-circle-bold-duotone" class="me-2 fs-5"></iconify-icon>
                            Générer des Références
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('admin.references.verify.form') }}" class="btn btn-success w-100 d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:check-circle-bold-duotone" class="me-2 fs-5"></iconify-icon>
                            Vérifier une Référence
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('admin.references.export') }}" class="btn btn-secondary w-100 d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:download-bold-duotone" class="me-2 fs-5"></iconify-icon>
                            Exporter en Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(count($statistics['by_pillar']) > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Références par Pilier</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Pilier</th>
                                <th>Nombre de Références</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statistics['by_pillar'] as $stat)
                            <tr>
                                <td class="fw-medium">{{ $stat->name }}</td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $stat->count }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if(count($statistics['by_month']) > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Activité Récente (par Mois)</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mois/Année</th>
                                <th>Nombre de Références</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statistics['by_month'] as $stat)
                            <tr>
                                <td class="fw-medium">{{ $stat->month_year }}</td>
                                <td><span class="badge bg-info-subtle text-info">{{ $stat->count }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

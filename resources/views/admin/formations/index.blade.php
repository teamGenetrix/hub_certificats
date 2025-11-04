@extends('layouts.app')

@section('content')
    <!-- Start here.... -->
    <div class="row">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="card-title mb-2 d-flex align-items-center gap-2">Total des formations</h4>
                            <p class="text-muted fw-medium fs-22 mb-0">2310</p>
                        </div>
                        <div>
                            <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                <iconify-icon icon="solar:bill-list-bold-duotone"
                                    class="fs-32 text-primary avatar-title"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="card-title mb-2 d-flex align-items-center gap-2">Formations en attente</h4>
                            <p class="text-muted fw-medium fs-22 mb-0">1000</p>
                        </div>
                        <div>
                            <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                <iconify-icon icon="solar:bill-cross-bold-duotone"
                                    class="fs-32 text-primary avatar-title"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="card-title mb-2 d-flex align-items-center gap-2">Formations validées</h4>
                            <p class="text-muted fw-medium fs-22 mb-0">1310</p>
                        </div>
                        <div>
                            <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                <iconify-icon icon="solar:bill-check-bold-duotone"
                                    class="fs-32 text-primary avatar-title"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="card-title mb-2 d-flex align-items-center gap-2">Formations archivées</h4>
                            <p class="text-muted fw-medium fs-22 mb-0">1243</p>
                        </div>
                        <div>
                            <div class="avatar-md bg-primary bg-opacity-10 rounded">
                                <iconify-icon icon="solar:bill-bold-duotone"
                                    class="fs-32 text-primary avatar-title"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-1">
                        <h4 class="card-title flex-grow-1">Liste des formations</h4>

                        <a href="{{ route('admin.formations.create') }}" class="btn btn-sm btn-primary">
                            Ajouter une formation
                        </a>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-sm btn-outline-light rounded"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            This Month
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="#!" class="dropdown-item">Download</a>
                            <!-- item-->
                            <a href="#!" class="dropdown-item">Export</a>
                            <!-- item-->
                            <a href="#!" class="dropdown-item">Import</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="customCheck1">
                                            <label class="form-check-label" for="customCheck1"></label>
                                        </div>
                                    </th>
                                    <th>Titre</th>
                                    <th>Date début</th>
                                    <th>Durée (h)</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="customCheck7">
                                            <label class="form-check-label" for="customCheck7">&nbsp;</label>
                                        </div>
                                    </td>
                                    <td>Formation 1</td>
                                    <td>01/01/2023</td>
                                    <td>10</td>
                                    <td>En attente</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="#!" class="btn btn-light btn-sm"><iconify-icon
                                                    icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon></a>
                                            <a href="#!" class="btn btn-soft-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#staticBackdrop"><iconify-icon icon="solar:pen-2-broken"
                                                    class="align-middle fs-18"></iconify-icon></a>
                                            <a href="#!" class="btn btn-soft-danger btn-sm"><iconify-icon
                                                    icon="solar:trash-bin-minimalistic-2-broken"
                                                    class="align-middle fs-18"></iconify-icon></a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- end table-responsive -->
                </div>
                <div class="card-footer border-top">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-end mb-0">
                            <li class="page-item"><a class="page-link" href="javascript:void(0);">Previous</a></li>
                            <li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li>
                            <li class="page-item"><a class="page-link" href="javascript:void(0);">2</a></li>
                            <li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
                            <li class="page-item"><a class="page-link" href="javascript:void(0);">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
@endsection

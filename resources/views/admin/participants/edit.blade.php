@extends('layouts.app')

@section('title', 'Modifier un Participant')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title">Modifier un Participant</h4>
                    <p class="text-muted">Modifiez les informations du participant</p>
                </div>
                <a href="{{ route('admin.participants.show', $participant) }}" class="btn btn-secondary">
                    <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.participants.update', $participant) }}" method="POST">
        @csrf
        @method('PUT')

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
                                <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" id="first_name" name="first_name"
                                    class="form-control @error('first_name') is-invalid @enderror"
                                    value="{{ old('first_name', $participant->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nom -->
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" id="last_name" name="last_name"
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    value="{{ old('last_name', $participant->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $participant->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Téléphone -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" id="phone" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $participant->phone) }}" placeholder="+225 XX XX XX XX XX">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Entreprise -->
                            <div class="col-md-6 mb-3">
                                <label for="company" class="form-label">Entreprise</label>
                                <input type="text" id="company" name="company"
                                    class="form-control @error('company') is-invalid @enderror"
                                    value="{{ old('company', $participant->company) }}">
                                @error('company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Poste -->
                            <div class="col-md-6 mb-3">
                                <label for="job_title" class="form-label">Poste</label>
                                <input type="text" id="job_title" name="job_title"
                                    class="form-control @error('job_title') is-invalid @enderror"
                                    value="{{ old('job_title', $participant->job_title) }}">
                                @error('job_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Pays -->
                            <div class="col-12 mb-3">
                                <label for="country" class="form-label">Pays</label>
                                <input type="text" id="country" name="country"
                                    class="form-control @error('country') is-invalid @enderror"
                                    value="{{ old('country', $participant->country) }}" placeholder="Ex: Côte d'Ivoire">
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
                                    <iconify-icon icon="solar:user-bold-duotone" class="fs-1 text-primary"></iconify-icon>
                                </div>
                            </div>
                            <h5>{{ $participant->full_name }}</h5>
                            <p class="text-muted mb-0">Modifier les informations</p>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <iconify-icon icon="solar:info-circle-bold-duotone" class="me-1"></iconify-icon>
                                Informations
                            </h6>
                            <ul class="mb-0 ps-3">
                                <li class="small">L'email doit être unique</li>
                                <li class="small">Les champs marqués <span class="text-danger">*</span> sont obligatoires
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <iconify-icon icon="solar:check-circle-bold-duotone" class="me-1"></iconify-icon>
                                Enregistrer les Modifications
                            </button>
                            <a href="{{ route('admin.participants.show', $participant) }}"
                                class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

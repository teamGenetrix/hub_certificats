@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-xl-3 col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="bg-light text-center rounded bg-light">
                    <iconify-icon icon="mdi:file-document-multiple-outline" class="avatar-xxl"></iconify-icon>
                </div>
                <div class="mt-3">
                    <h4>References d'identifiants de formation</h4>
                    <div class="row">
                        <div class="col-lg-4 col-4">
                            <p class="mb-1 mt-2">Created By :</p>
                            <h5 class="mb-0">Seller</h5>
                        </div>
                        <div class="col-lg-4 col-4">
                            <p class="mb-1 mt-2">Stock :</p>
                            <h5 class="mb-0">46233</h5>
                        </div>
                        <div class="col-lg-4 col-4">
                            <p class="mb-1 mt-2">ID :</p>
                            <h5 class="mb-0">FS16276</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer border-top">
                <div class="row g-2">
                    <div class="col-lg-6">
                        <a href="#!" class="btn btn-outline-secondary w-100">Voir tout</a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-9 col-lg-8 ">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Informations Générales </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form>
                            <div class="mb-3">
                                <label for="title" class="form-label">Titre de formation</label>
                                <input type="text" id="title" class="form-control"
                                    placeholder="Entrer le titre">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea type="text" id="description" class="form-control"
                                    placeholder="Entrer la description"></textarea>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-6">
                        <form>
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-control" id="type" data-choices data-choices-groups
                                    data-placeholder="Select Type">
                                    <option value="">Select Type</option>
                                    <option value="Seller">Seller</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-6">
                        <form>
                            <div class="mb-3">
                                <label for="product-stock" class="form-label">Stock</label>
                                <input type="number" id="product-stock" class="form-control" placeholder="Quantity">
                            </div>

                        </form>
                    </div>
                    <div class="col-lg-6">
                        <form>
                            <div class="mb-3">
                                <label for="product-id" class="form-label">Tag ID</label>
                                <input type="number" id="product-id" class="form-control" placeholder="#******">
                            </div>

                        </form>
                    </div>
                    <div class="col-lg-12">
                        <div class="mb-0">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control bg-light-subtle" id="description" rows="7" placeholder="Type description"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Meta Options</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form>
                            <div class="mb-3">
                                <label for="meta-title" class="form-label">Meta Title</label>
                                <input type="text" id="meta-title" class="form-control"
                                    placeholder="Enter Title">
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-6">
                        <form>
                            <div class="mb-3">
                                <label for="meta-tag" class="form-label">Meta Tag Keyword</label>
                                <input type="text" id="meta-tag" class="form-control" placeholder="Enter word">
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-12">
                        <div class="mb-0">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control bg-light-subtle" id="description" rows="4" placeholder="Type description"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-3 bg-light mb-3 rounded">
            <div class="row justify-content-end g-2">
                <div class="col-lg-2">
                    <a href="#!" class="btn btn-outline-secondary w-100">Save Change</a>
                </div>
                <div class="col-lg-2">
                    <a href="#!" class="btn btn-primary w-100">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

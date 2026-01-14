@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-group mb-2">
                <a href="{{url('master-items/form/new')}}" class="btn btn-secondary">+ Master Items Baru</a>
            </div>
            <div class="card">
                <div class="card-header">Daftar Master Items</div>

                <div class="card-body">
                    @include('master_items.index.filter')
                    @include('master_items.index.table')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Master Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateForm">
                    <input type="hidden" id="update-item-id" name="id">
                    <div class="form-group mb-3">
                        <label>Kode Barang</label>
                        <input type="text" class="form-control" id="update-kode" name="kode" readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label>Nama</label>
                        <input type="text" class="form-control" id="update-nama" name="nama" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Harga Beli</label>
                        <input type="number" class="form-control" id="update-harga-beli" name="harga_beli" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Laba (dalam persen)</label>
                        <input type="number" class="form-control" id="update-laba" name="laba" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Supplier</label>
                        <select class="form-control" id="update-supplier" name="supplier" required>
                            <option value="">--Pilih--</option>
                            <option value="Tokopaedi">Tokopaedi</option>
                            <option value="Bukulapuk">Bukulapuk</option>
                            <option value="TokoBagas">TokoBagas</option>
                            <option value="E Commurz">E Commurz</option>
                            <option value="Blublu">Blublu</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Jenis</label>
                        <select class="form-control" id="update-jenis" name="jenis" required>
                            <option value="">--Pilih--</option>
                            <option value="Obat">Obat</option>
                            <option value="Alkes">Alkes</option>
                            <option value="Matkes">Matkes</option>
                            <option value="Umum">Umum</option>
                            <option value="ATK">ATK</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Kategoris</label>
                        <div class="kategori-selector-container">
                            <!-- Search/Select Input -->
                            <div class="input-group mb-2">
                                <input type="text" 
                                       class="form-control" 
                                       id="update-kategori-search" 
                                       placeholder="Cari atau pilih kategori..."
                                       autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="update-kategori-dropdown-toggle">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
                            
                            <!-- Dropdown Menu -->
                            <div class="kategori-dropdown" id="update-kategori-dropdown" style="display: none;">
                                <!-- Options will be populated by JavaScript -->
                            </div>
                            
                            <!-- Selected Kategoris Display -->
                            <div class="selected-kategoris mt-2" id="update-selected-kategoris">
                                <!-- Selected kategoris will appear here -->
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label>Picture</label>
                        <input type="file" class="form-control" id="update-picture" name="picture" accept="image/*">
                        <div id="update-picture-preview" class="mt-2" style="display: none;">
                            <small class="text-muted">Current picture:</small><br>
                            <img id="update-picture-img" src="" alt="Current picture" style="max-width: 200px; max-height: 200px; margin-top: 10px;" class="img-thumbnail">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-update-submit">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus item ini?</p>
                <p><strong>Kode:</strong> <span id="delete-kode"></span></p>
                <p><strong>Nama:</strong> <span id="delete-nama"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btn-delete-confirm">Hapus</button>
            </div>
        </div>
    </div>
</div>

<style>
    .kategori-selector-container {
        position: relative;
    }
    
    .kategori-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        max-height: 250px;
        overflow-y: auto;
        z-index: 1050;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        margin-top: 2px;
    }
    
    .kategori-option {
        padding: 0.75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
    }
    
    .kategori-option:hover {
        background-color: #f8f9fa;
    }
    
    .kategori-option:last-child {
        border-bottom: none;
    }
    
    .kategori-option.selected {
        background-color: #e7f3ff;
        color: #0d6efd;
    }
    
    .selected-kategoris {
        min-height: 40px;
        padding: 0.5rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        background-color: #f8f9fa;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .kategori-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .kategori-badge .btn-close {
        font-size: 0.75rem;
        margin-left: 0.5rem;
        opacity: 0.8;
    }
    
    .kategori-badge .btn-close:hover {
        opacity: 1;
    }
    
    #update-kategori-search:focus,
    #kategori-search:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>

@endsection
@section('js')
@include('master_items.index.js')
@endsection
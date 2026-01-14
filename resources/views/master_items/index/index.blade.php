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

@endsection
@section('js')
@include('master_items.index.js')
@endsection
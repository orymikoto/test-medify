@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="form-group mb-2">
                <a href="{{url('kategoris')}}" class="btn btn-secondary">Kembali ke Daftar Kategori</a>
            </div>
            <div class="card mb-3">
                <div class="card-header">Kategori</div>

                <div class="card-body">
                    <table>
                        <tr>
                            <th>Kode</th>
                            <td>:</td>
                            <td>{{$data->kode}}</td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>:</td>
                            <td>{{$data->nama}}</td>
                        </tr>
                    </table>
                    <div class="mt-3">
                        <a class="btn btn-info" href="{{url('kategoris/form/edit')}}/{{$data->id}}">Edit</a>
                        <a class="btn btn-danger" href="{{url('kategoris/delete')}}/{{$data->id}}" onclick="return confirm('Are you sure you want to delete this kategori?');">Delete</a>
                        <a class="btn btn-success" href="{{url('kategoris/pdf')}}/{{$data->kode}}" target="_blank">
                            <i class="bi bi-file-pdf"></i> Download PDF
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Daftar Items</div>
                <div class="card-body">
                    <table id="itemsTable" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Picture</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Supplier</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($masterItems && $masterItems->count() > 0)
                                @foreach($masterItems as $item)
                                <tr>
                                    <td>
                                        @if($item->picture)
                                            <img src="{{ asset('storage/' . $item->picture) }}" alt="Picture" style="max-width: 50px; max-height: 50px; object-fit: cover;" class="img-thumbnail">
                                        @else
                                            <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->kode }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->jenis }}</td>
                                    <td>{{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                                    <td>{{ number_format($item->harga_beli + $item->harga_beli * $item->laba / 100, 0, ',', '.') }}</td>
                                    <td>{{ $item->supplier }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{url('master-items/view/')}}/{{$item->kode}}" class="btn btn-sm btn-primary" title="View"><i class="bi bi-eye"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Tidak ada items yang memiliki kategori ini</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#itemsTable').DataTable({
            searching: true,
            order: [[1, 'asc']], // Sort by Kode ascending
            pageLength: 10,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ items per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ items",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 items",
                infoFiltered: "(difilter dari _MAX_ total items)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    });
</script>
@endsection


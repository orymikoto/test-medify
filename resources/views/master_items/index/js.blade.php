<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    var start_date = '';
    var end_date = '';
    var data_per_fetch = 500;
    var data_fetched = 0;
    var currentDeleteId = null;

    $(document).ready(function() {
        $('#table').DataTable({
            searching: false,
            order: [[0, 'desc']],
        });
        getData()
    });

    $('.btn-get-data').click(function() {
        getData()
    })

    function getData(){
        
        $('#loading-filter').show();
        var dataTableObj = $('#table').DataTable();
        var filter_kode = $('#filter-kode').val()
        var filter_nama = $('#filter-nama').val()
        var filter_harga_min = $('#filter-harga-min').val()
        var filter_harga_max = $('#filter-harga-max').val()
        dataTableObj.clear().draw();

        $.ajax({
            url: '{{url("master-items/search")}}',
            dataType: 'json',
            tryCount: 0,
            retryLimit: 3,
            data: 'kode=' + filter_kode + '&nama=' + filter_nama + '&hargamin=' + filter_harga_min + '&hargamax=' + filter_harga_max,
            success: function(results) {
                var data = results.data

                $.each(data, function(index, item) {
                    array_temp = [];
                    var harga_jual = item.harga_beli + item.harga_beli * item.laba / 100;
                    harga_jual = Math.round(harga_jual)
                    var kode = item.kode;
                    var id = item.id;

                    // Create picture thumbnail
                    var pictureHtml = '<span class="text-muted">No image</span>';
                    if (item.picture) {
                        var pictureUrl = '{{ asset("storage") }}/' + item.picture;
                        pictureHtml = '<img src="' + pictureUrl + '" alt="Picture" style="max-width: 50px; max-height: 50px; object-fit: cover;" class="img-thumbnail">';
                    }

                    // Create action buttons with icons
                    var html = `<div class="btn-group" role="group">`;
                    html += `<a href="{{url('master-items/view/')}}/` + kode + `" class="btn btn-sm btn-primary" title="View"><i class="bi bi-eye"></i></a>`;
                    html += `<button class="btn btn-sm btn-warning btn-update" data-id="` + id + `" data-kode="` + item.kode + `" data-nama="` + item.nama + `" data-harga-beli="` + item.harga_beli + `" data-laba="` + item.laba + `" data-supplier="` + item.supplier + `" data-jenis="` + item.jenis + `" data-picture="` + (item.picture || '') + `" title="Update"><i class="bi bi-pencil"></i></button>`;
                    html += `<button class="btn btn-sm btn-danger btn-delete" data-id="` + id + `" data-kode="` + item.kode + `" data-nama="` + item.nama + `" title="Delete"><i class="bi bi-trash"></i></button>`;
                    html += `</div>`;

                    // Push data in the correct column order: Picture, Kode, Nama, Jenis, Harga Beli, Harga Jual, Supplier, Actions
                    array_temp.push(pictureHtml);      // Picture
                    array_temp.push(item.kode);        // Kode
                    array_temp.push(item.nama);        // Nama
                    array_temp.push(item.jenis);       // Jenis
                    array_temp.push(item.harga_beli);  // Harga Beli
                    array_temp.push(harga_jual);       // Harga Jual (calculated)
                    array_temp.push(item.supplier);    // Supplier
                    array_temp.push(html);             // Actions

                    dataTableObj.row.add(array_temp).draw(true);
                });
                $('#loading-filter').hide();
            },
            error: function(xhr, textStatus, errorThrown) {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    $.ajax(this);
                    return;
                }
                alert('Terjadi kesalahan server, tidak dapat mengambil data')
                $('#loading-filter').hide();

                return;
            }
        })
    }

    // Handle Update Button Click
    $(document).on('click', '.btn-update', function() {
        var id = $(this).data('id');
        var kode = $(this).data('kode');
        var nama = $(this).data('nama');
        var hargaBeli = $(this).data('harga-beli');
        var laba = $(this).data('laba');
        var supplier = $(this).data('supplier');
        var jenis = $(this).data('jenis');
        var picture = $(this).data('picture');

        $('#update-item-id').val(id);
        $('#update-kode').val(kode);
        $('#update-nama').val(nama);
        $('#update-harga-beli').val(hargaBeli);
        $('#update-laba').val(laba);
        $('#update-supplier').val(supplier);
        $('#update-jenis').val(jenis);
        $('#update-picture').val(''); // Reset file input

        // Show/hide picture preview
        if (picture) {
            var pictureUrl = '{{ asset("storage") }}/' + picture;
            $('#update-picture-img').attr('src', pictureUrl);
            $('#update-picture-preview').show();
        } else {
            $('#update-picture-preview').hide();
        }

        var updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
        updateModal.show();
    });

    // Handle Update Form Submission
    $('#btn-update-submit').click(function() {
        var id = $('#update-item-id').val();
        var formData = new FormData();
        
        formData.append('nama', $('#update-nama').val());
        formData.append('harga_beli', $('#update-harga-beli').val());
        formData.append('laba', $('#update-laba').val());
        formData.append('supplier', $('#update-supplier').val());
        formData.append('jenis', $('#update-jenis').val());
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PUT');

        // Add picture if file is selected
        var pictureFile = $('#update-picture')[0].files[0];
        if (pictureFile) {
            formData.append('picture', pictureFile);
        }

        if (!formData.get('nama') || !formData.get('harga_beli') || !formData.get('laba') || !formData.get('supplier') || !formData.get('jenis')) {
            alert('Mohon lengkapi semua field');
            return;
        }

        $.ajax({
            url: '/api/master-items/' + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    var updateModal = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
                    updateModal.hide();
                    alert('Item berhasil diupdate');
                    getData(); // Refresh table
                } else {
                    alert('Gagal mengupdate item: ' + response.message);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Gagal mengupdate item';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            }
        });
    });

    // Handle Delete Button Click
    $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        var kode = $(this).data('kode');
        var nama = $(this).data('nama');

        currentDeleteId = id;
        $('#delete-kode').text(kode);
        $('#delete-nama').text(nama);

        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    });

    // Handle Delete Confirmation
    $('#btn-delete-confirm').click(function() {
        if (!currentDeleteId) return;

        $.ajax({
            url: '/api/master-items/' + currentDeleteId,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    var deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    deleteModal.hide();
                    alert('Item berhasil dihapus');
                    currentDeleteId = null;
                    getData(); // Refresh table
                } else {
                    alert('Gagal menghapus item: ' + response.message);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Gagal menghapus item';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            }
        });
    });
</script>
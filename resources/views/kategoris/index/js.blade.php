<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    var currentDeleteId = null;

    $(document).ready(function() {
        $('#table').DataTable({
            searching: false,
            order: [[1, 'asc']], // Sort by Nama (column index 1) ascending
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
        dataTableObj.clear().draw();

        $.ajax({
            url: '{{url("kategoris/search")}}',
            dataType: 'json',
            tryCount: 0,
            retryLimit: 3,
            data: 'kode=' + filter_kode + '&nama=' + filter_nama,
            success: function(results) {
                var data = results.data

                $.each(data, function(index, item) {
                    array_temp = [];
                    var kode = item.kode;
                    var id = item.id;

                    // Create action buttons with icons
                    var html = `<div class="btn-group" role="group">`;
                    html += `<a href="{{url('kategoris/view/')}}/` + kode + `" class="btn btn-sm btn-primary" title="View"><i class="bi bi-eye"></i></a>`;
                    html += `<button class="btn btn-sm btn-warning btn-update" data-id="` + id + `" data-kode="` + item.kode + `" data-nama="` + item.nama + `" title="Update"><i class="bi bi-pencil"></i></button>`;
                    html += `<button class="btn btn-sm btn-danger btn-delete" data-id="` + id + `" data-kode="` + item.kode + `" data-nama="` + item.nama + `" title="Delete"><i class="bi bi-trash"></i></button>`;
                    html += `</div>`;

                    // Push data in the correct column order: Kode, Nama, Actions
                    array_temp.push(item.kode);        // Kode
                    array_temp.push(item.nama);        // Nama
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

    // Function to generate kode from nama
    function generateKodeFromNama(nama) {
        if (!nama) return '';
        
        var words = nama.trim().split(' ').filter(function(word) { return word.trim().length > 0; });
        var kodeParts = [];
        
        words.forEach(function(word) {
            word = word.toUpperCase().trim();
            if (word.length >= 3) {
                kodeParts.push(word.substring(0, 3));
            } else {
                // Use the word as-is if it's 2 letters or less (no padding)
                kodeParts.push(word);
            }
        });
        
        return kodeParts.join('-');
    }

    // Handle Update Button Click
    $(document).on('click', '.btn-update', function() {
        var id = $(this).data('id');
        var kode = $(this).data('kode');
        var nama = $(this).data('nama');

        $('#update-item-id').val(id);
        $('#update-kode').val(kode);
        $('#update-nama').val(nama);
        $('#update-kode').data('auto-generated', 'false');

        var updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
        updateModal.show();
    });

    // Auto-generate kode when nama changes in update modal
    $('#update-nama').on('input', function() {
        var nama = $(this).val().trim();
        var kodeInput = $('#update-kode');
        
        // Only auto-generate if kode was auto-generated or empty
        if (nama && (kodeInput.data('auto-generated') === true || !kodeInput.val())) {
            var generatedKode = generateKodeFromNama(nama);
            kodeInput.val(generatedKode);
            kodeInput.data('auto-generated', true);
        }
    });

    // Reset auto-generated flag when user manually edits kode
    $('#update-kode').on('input', function() {
        $(this).data('auto-generated', false);
    });

    // Handle Update Form Submission
    $('#btn-update-submit').click(function() {
        var id = $('#update-item-id').val();
        var formData = {
            nama: $('#update-nama').val(),
            kode: $('#update-kode').val(),
            _token: '{{ csrf_token() }}',
            _method: 'PUT'
        };

        if (!formData.nama || !formData.kode) {
            alert('Mohon lengkapi semua field');
            return;
        }

        $.ajax({
            url: '/api/kategoris/' + id,
            type: 'PUT',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    var updateModal = bootstrap.Modal.getInstance(document.getElementById('updateModal'));
                    updateModal.hide();
                    alert('Kategori berhasil diupdate');
                    getData(); // Refresh table
                } else {
                    alert('Gagal mengupdate kategori: ' + response.message);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Gagal mengupdate kategori';
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
            url: '/api/kategoris/' + currentDeleteId,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    var deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    deleteModal.hide();
                    alert('Kategori berhasil dihapus');
                    currentDeleteId = null;
                    getData(); // Refresh table
                } else {
                    alert('Gagal menghapus kategori: ' + response.message);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Gagal menghapus kategori';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            }
        });
    });
</script>


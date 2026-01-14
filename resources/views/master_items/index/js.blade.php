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
    var allKategoris = [];

    $(document).ready(function() {
        $('#table').DataTable({
            searching: false,
            order: [[0, 'desc']],
        });
        loadKategorisForFilter();
        loadKategoris();
        getData()
    });

    // Load all kategoris for the filter dropdown
    function loadKategorisForFilter() {
        $.ajax({
            url: '/api/kategoris',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    var select = $('#filter-kategori');
                    response.data.forEach(function(kategori) {
                        select.append('<option value="' + kategori.id + '">' + kategori.kode + ' - ' + kategori.nama + '</option>');
                    });
                }
            },
            error: function() {
                console.error('Failed to load kategoris for filter');
            }
        });
    }

    // Load all kategoris for the update modal
    function loadKategoris() {
        $.ajax({
            url: '/api/kategoris',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status == 200) {
                    allKategoris = response.data;
                    populateKategorisSelect();
                }
            },
            error: function() {
                console.error('Failed to load kategoris');
            }
        });
    }

    // Populate kategoris dropdown in update modal
    function populateKategorisSelect() {
        var dropdown = $('#update-kategori-dropdown');
        dropdown.empty();
        allKategoris.forEach(function(kategori) {
            var option = $('<div class="kategori-option" data-id="' + kategori.id + '" data-kode="' + kategori.kode + '" data-nama="' + kategori.nama + '">' +
                '<strong>' + kategori.kode + '</strong> - ' + kategori.nama + '</div>');
            dropdown.append(option);
        });
        initUpdateKategoriSelector();
    }

    // Initialize kategori selector for update modal
    var updateSelectedKategoris = []; // Store selected kategoris outside function scope
    
    function initUpdateKategoriSelector() {
        const searchInput = $('#update-kategori-search');
        const dropdown = $('#update-kategori-dropdown');
        const dropdownToggle = $('#update-kategori-dropdown-toggle');
        const selectedContainer = $('#update-selected-kategoris');
        const options = dropdown.find('.kategori-option');
        
        // Toggle dropdown
        function toggleDropdown() {
            if (dropdown.is(':hidden')) {
                dropdown.show();
                filterUpdateOptions();
            } else {
                dropdown.hide();
            }
        }
        
        dropdownToggle.off('click').on('click', function(e) {
            e.preventDefault();
            toggleDropdown();
        });
        
        searchInput.off('click').on('click', function() {
            if (dropdown.is(':hidden')) {
                toggleDropdown();
            }
        });
        
        // Filter options based on search
        function filterUpdateOptions() {
            const searchTerm = searchInput.val().toLowerCase();
            options.each(function() {
                const option = $(this);
                const text = option.text().toLowerCase();
                const isSelected = updateSelectedKategoris.includes(parseInt(option.data('id')));
                
                if (text.includes(searchTerm) && !isSelected) {
                    option.show();
                } else {
                    option.hide();
                }
            });
        }
        
        searchInput.off('input').on('input', filterUpdateOptions);
        
        // Add kategori
        function addKategori(id, kode, nama) {
            id = parseInt(id);
            if (updateSelectedKategoris.includes(id)) {
                return;
            }
            
            updateSelectedKategoris.push(id);
            
            // Create badge
            const badge = $('<span class="badge bg-primary kategori-badge" data-id="' + id + '">' +
                kode + ' - ' + nama +
                '<button type="button" class="btn-close btn-close-white ms-2" aria-label="Remove"></button>' +
                '</span>');
            
            // Add remove functionality - use event delegation for better reliability
            badge.find('.btn-close').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                removeKategori(id);
            });
            
            selectedContainer.append(badge);
            
            // Clear search and hide dropdown
            searchInput.val('');
            dropdown.hide();
        }
        
        // Remove kategori
        function removeKategori(id) {
            id = parseInt(id);
            // Remove from array
            updateSelectedKategoris = updateSelectedKategoris.filter(function(katId) {
                return katId !== id;
            });
            
            // Remove badge from DOM
            selectedContainer.find('.kategori-badge[data-id="' + id + '"]').remove();
        }
        
        // Use event delegation for existing badges
        selectedContainer.off('click', '.btn-close').on('click', '.btn-close', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const badge = $(this).closest('.kategori-badge');
            const id = parseInt(badge.data('id'));
            removeKategori(id);
        });
        
        // Add click handler to options
        options.off('click').on('click', function() {
            const option = $(this);
            const id = parseInt(option.data('id'));
            const kode = option.data('kode');
            const nama = option.data('nama');
            addKategori(id, kode, nama);
        });
        
        // Close dropdown when clicking outside
        $(document).off('click.updateKategori').on('click.updateKategori', function(e) {
            if (!$(e.target).closest('.kategori-selector-container').length) {
                dropdown.hide();
            }
        });
        
        // Store selected kategoris getter
        window.getUpdateSelectedKategoris = function() {
            return updateSelectedKategoris.slice(); // Return a copy
        };
        
        // Clear selected kategoris
        window.clearUpdateSelectedKategoris = function() {
            updateSelectedKategoris = [];
            selectedContainer.empty();
        };
        
        // Set selected kategoris
        window.setUpdateSelectedKategoris = function(kategoris) {
            clearUpdateSelectedKategoris();
            if (Array.isArray(kategoris) && kategoris.length > 0) {
                kategoris.forEach(function(kategoriId) {
                    const kategori = allKategoris.find(function(k) { return k.id == kategoriId; });
                    if (kategori) {
                        addKategori(kategori.id, kategori.kode, kategori.nama);
                    }
                });
            }
        };
    }

    $('.btn-get-data').click(function() {
        getData()
    })

    function getData(){
        
        $('#loading-filter').show();
        var dataTableObj = $('#table').DataTable();
        var filter_kode = $('#filter-kode').val()
        var filter_nama = $('#filter-nama').val()
        var filter_kategori = $('#filter-kategori').val()
        var filter_harga_min = $('#filter-harga-min').val()
        var filter_harga_max = $('#filter-harga-max').val()
        dataTableObj.clear().draw();

        $.ajax({
            url: '{{url("master-items/search")}}',
            dataType: 'json',
            tryCount: 0,
            retryLimit: 3,
            data: 'kode=' + filter_kode + '&nama=' + filter_nama + '&kategori=' + filter_kategori + '&hargamin=' + filter_harga_min + '&hargamax=' + filter_harga_max,
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

                    // Create kategoris display
                    var kategorisHtml = '<span class="text-muted">-</span>';
                    if (item.kategoris_detail && item.kategoris_detail.length > 0) {
                        kategorisHtml = '';
                        item.kategoris_detail.forEach(function(kat, idx) {
                            if (idx > 0) kategorisHtml += ', ';
                            kategorisHtml += '<span class="badge bg-secondary">' + kat.kode + ' - ' + kat.nama + '</span>';
                        });
                    }

                    // Create action buttons with icons
                    var kategorisJson = item.kategoris ? JSON.stringify(item.kategoris) : '[]';
                    var html = `<div class="btn-group" role="group">`;
                    html += `<a href="{{url('master-items/view/')}}/` + kode + `" class="btn btn-sm btn-primary" title="View"><i class="bi bi-eye"></i></a>`;
                    html += `<button class="btn btn-sm btn-warning btn-update" data-id="` + id + `" data-kode="` + item.kode + `" data-nama="` + item.nama + `" data-harga-beli="` + item.harga_beli + `" data-laba="` + item.laba + `" data-supplier="` + item.supplier + `" data-jenis="` + item.jenis + `" data-picture="` + (item.picture || '') + `" data-kategoris='` + kategorisJson + `' title="Update"><i class="bi bi-pencil"></i></button>`;
                    html += `<button class="btn btn-sm btn-danger btn-delete" data-id="` + id + `" data-kode="` + item.kode + `" data-nama="` + item.nama + `" title="Delete"><i class="bi bi-trash"></i></button>`;
                    html += `</div>`;

                    // Push data in the correct column order: Picture, Kode, Nama, Jenis, Kategoris, Harga Beli, Harga Jual, Supplier, Actions
                    array_temp.push(pictureHtml);      // Picture
                    array_temp.push(item.kode);        // Kode
                    array_temp.push(item.nama);        // Nama
                    array_temp.push(item.jenis);       // Jenis
                    array_temp.push(kategorisHtml);    // Kategoris
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
        var kategoris = $(this).data('kategoris') || [];

        $('#update-item-id').val(id);
        $('#update-kode').val(kode);
        $('#update-nama').val(nama);
        $('#update-harga-beli').val(hargaBeli);
        $('#update-laba').val(laba);
        $('#update-supplier').val(supplier);
        $('#update-jenis').val(jenis);
        $('#update-picture').val(''); // Reset file input

        // Set selected kategoris
        if (typeof setUpdateSelectedKategoris === 'function') {
            setUpdateSelectedKategoris(kategoris);
        }

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

        // Add selected kategoris
        var selectedKategoris = [];
        if (typeof getUpdateSelectedKategoris === 'function') {
            selectedKategoris = getUpdateSelectedKategoris();
        }
        // Always send kategoris array (even if empty) so controller knows to sync relationships
        if (selectedKategoris && selectedKategoris.length > 0) {
            selectedKategoris.forEach(function(kategoriId) {
                if (kategoriId) { // Only add non-empty values
                    formData.append('kategoris[]', kategoriId);
                }
            });
        }
        // Note: If empty, controller will still delete all old relationships

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

    // Download Excel with current filters
    $('#btn-download-excel').click(function() {
        var filter_kode = $('#filter-kode').val();
        var filter_nama = $('#filter-nama').val();
        var filter_kategori = $('#filter-kategori').val();
        var filter_harga_min = $('#filter-harga-min').val();
        var filter_harga_max = $('#filter-harga-max').val();
        
        // Build query string
        var params = [];
        if (filter_kode) params.push('kode=' + encodeURIComponent(filter_kode));
        if (filter_nama) params.push('nama=' + encodeURIComponent(filter_nama));
        if (filter_kategori) params.push('kategori=' + encodeURIComponent(filter_kategori));
        if (filter_harga_min) params.push('hargamin=' + encodeURIComponent(filter_harga_min));
        if (filter_harga_max) params.push('hargamax=' + encodeURIComponent(filter_harga_max));
        
        var queryString = params.length > 0 ? '?' + params.join('&') : '';
        var url = '{{url("master-items/download-excel")}}' + queryString;
        
        // Open download in new window
        window.location.href = url;
    });
</script>
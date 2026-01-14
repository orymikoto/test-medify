<form method="POST" enctype="multipart/form-data">
    @csrf
    @if($method == 'edit')
    <div class="form-group">
        <label>Kode Barang</label>
        <input type="text" class="form-control" name="kode_barang" required readonly value="{{$item->kode ?? ''}}">
    </div>
    @endif

    <div class="form-group">
        <label>Nama</label>
        <input type="text" class="form-control" name="nama" required  value="{{$item->nama ?? ''}}">
    </div>

    <div class="form-group">
        <label>Harga Beli</label>
        <input type="number" class="form-control" name="harga_beli" required  value="{{$item->harga_beli ?? ''}}">
    </div>

    <div class="form-group">
        <label>Laba (dalam persen)</label>
        <input type="number" class="form-control" name="laba" required  value="{{$item->laba ?? ''}}">
    </div>

    @php $selected = $item->supplier ?? ''; @endphp
    <div class="form-group">
        <label>Supplier</label>
        <select class="form-control" required name="supplier">
            <option @if($selected == '') selected @endif value="">--Pilih--</option>
            <option @if($selected == 'Tokopaedi') selected @endif>Tokopaedi</option>
            <option @if($selected == 'Bukulapuk') selected @endif>Bukulapuk</option>
            <option @if($selected == 'TokoBagas') selected @endif>TokoBagas</option>
            <option @if($selected == 'E Commurz') selected @endif>E Commurz</option>
            <option @if($selected == 'Blublu') selected @endif>Blublu</option>
        </select>
    </div>

    @php $selected = $item->jenis ?? ''; @endphp
    <div class="form-group">
        <label>Jenis</label>
        <select class="form-control" required name="jenis">
            <option @if($selected == '') selected @endif value="">--Pilih--</option>
            <option @if($selected == 'Obat') selected @endif>Obat</option>
            <option @if($selected == 'Alkes') selected @endif>Alkes</option>
            <option @if($selected == 'Matkes') selected @endif>Matkes</option>
            <option @if($selected == 'Umum') selected @endif>Umum</option>
            <option @if($selected == 'ATK') selected @endif>ATK</option>
        </select>
    </div>

    <div class="form-group">
        <label>Kategoris</label>
        <div class="kategori-selector-container">
            <!-- Search/Select Input -->
            <div class="input-group mb-2">
                <input type="text" 
                       class="form-control" 
                       id="kategori-search" 
                       placeholder="Cari atau pilih kategori..."
                       autocomplete="off">
                <button class="btn btn-outline-secondary" type="button" id="kategori-dropdown-toggle">
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
            
            <!-- Dropdown Menu -->
            <div class="kategori-dropdown" id="kategori-dropdown" style="display: none;">
                @foreach($kategoris as $kategori)
                    <div class="kategori-option" 
                         data-id="{{ $kategori->id }}" 
                         data-kode="{{ $kategori->kode }}" 
                         data-nama="{{ $kategori->nama }}">
                        <strong>{{ $kategori->kode }}</strong> - {{ $kategori->nama }}
                    </div>
                @endforeach
            </div>
            
            <!-- Selected Kategoris Display -->
            <div class="selected-kategoris mt-2" id="selected-kategoris">
                @if(isset($selectedKategoris) && !empty($selectedKategoris))
                    @foreach($kategoris as $kategori)
                        @if(in_array($kategori->id, $selectedKategoris))
                            <span class="badge bg-primary kategori-badge" data-id="{{ $kategori->id }}">
                                {{ $kategori->kode }} - {{ $kategori->nama }}
                                <button type="button" class="btn-close btn-close-white ms-2" aria-label="Remove"></button>
                            </span>
                        @endif
                    @endforeach
                @endif
            </div>
            
            <!-- Hidden inputs for form submission -->
            <div id="kategori-hidden-inputs">
                @if(isset($selectedKategoris) && !empty($selectedKategoris))
                    @foreach($selectedKategoris as $kategoriId)
                        <input type="hidden" name="kategoris[]" value="{{ $kategoriId }}">
                    @endforeach
                @endif
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
            z-index: 1000;
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
        
        #kategori-search:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('kategori-search');
            const dropdown = document.getElementById('kategori-dropdown');
            const dropdownToggle = document.getElementById('kategori-dropdown-toggle');
            const selectedContainer = document.getElementById('selected-kategoris');
            const hiddenInputsContainer = document.getElementById('kategori-hidden-inputs');
            const options = dropdown.querySelectorAll('.kategori-option');
            let selectedKategoris = [];
            
            // Initialize selected kategoris from existing hidden inputs
            hiddenInputsContainer.querySelectorAll('input[type="hidden"]').forEach(function(input) {
                selectedKategoris.push(parseInt(input.value));
            });
            
            // Attach event listeners to existing badges (from server)
            selectedContainer.querySelectorAll('.kategori-badge .btn-close').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const badge = btn.closest('.kategori-badge');
                    const id = parseInt(badge.dataset.id);
                    removeKategori(id);
                });
            });
            
            // Toggle dropdown
            function toggleDropdown() {
                if (dropdown.style.display === 'none') {
                    dropdown.style.display = 'block';
                    filterOptions();
                } else {
                    dropdown.style.display = 'none';
                }
            }
            
            dropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                toggleDropdown();
            });
            
            searchInput.addEventListener('click', function() {
                if (dropdown.style.display === 'none') {
                    toggleDropdown();
                }
            });
            
            // Filter options based on search
            function filterOptions() {
                const searchTerm = searchInput.value.toLowerCase();
                options.forEach(function(option) {
                    const text = option.textContent.toLowerCase();
                    const isSelected = selectedKategoris.includes(parseInt(option.dataset.id));
                    
                    if (text.includes(searchTerm) && !isSelected) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                });
            }
            
            searchInput.addEventListener('input', filterOptions);
            
            // Add kategori
            function addKategori(id, kode, nama) {
                if (selectedKategoris.includes(id)) {
                    return;
                }
                
                selectedKategoris.push(id);
                
                // Create badge
                const badge = document.createElement('span');
                badge.className = 'badge bg-primary kategori-badge';
                badge.dataset.id = id;
                badge.innerHTML = kode + ' - ' + nama + 
                    '<button type="button" class="btn-close btn-close-white ms-2" aria-label="Remove"></button>';
                
                // Add remove functionality
                badge.querySelector('.btn-close').addEventListener('click', function() {
                    removeKategori(id);
                });
                
                selectedContainer.appendChild(badge);
                
                // Add hidden input
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'kategoris[]';
                hiddenInput.value = id;
                hiddenInputsContainer.appendChild(hiddenInput);
                
                // Clear search and hide dropdown
                searchInput.value = '';
                dropdown.style.display = 'none';
            }
            
            // Remove kategori
            function removeKategori(id) {
                selectedKategoris = selectedKategoris.filter(function(katId) {
                    return katId !== id;
                });
                
                // Remove badge
                const badge = selectedContainer.querySelector('.kategori-badge[data-id="' + id + '"]');
                if (badge) {
                    badge.remove();
                }
                
                // Remove hidden input
                const hiddenInput = hiddenInputsContainer.querySelector('input[value="' + id + '"]');
                if (hiddenInput) {
                    hiddenInput.remove();
                }
            }
            
            // Add click handler to options
            options.forEach(function(option) {
                option.addEventListener('click', function() {
                    const id = parseInt(option.dataset.id);
                    const kode = option.dataset.kode;
                    const nama = option.dataset.nama;
                    addKategori(id, kode, nama);
                });
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.kategori-selector-container')) {
                    dropdown.style.display = 'none';
                }
            });
        });
    </script>

    <div class="form-group">
        <label>Picture</label>
        <input type="file" class="form-control" name="picture" accept="image/*">
        @if(isset($item) && is_object($item) && $item->picture)
            <div class="mt-2">
                <small class="text-muted">Current picture:</small><br>
                <img src="{{ asset('storage/' . $item->picture) }}" alt="Current picture" style="max-width: 200px; max-height: 200px; margin-top: 10px;" class="img-thumbnail">
            </div>
        @endif
    </div>

    <button class="btn btn-primary mt-3">Submit</button>

</form>
<div id="filter-container" class="mb-3">
    <h5 class="mb-3">Filter</h5>
    <div class="row g-3">
        <div class="col-md-2">
            <div class="form-group">
                <label class="form-label">Kode</label>
                <input type="text" class="form-control" id="filter-kode" placeholder="Kode barang">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="form-label">Nama</label>
                <input type="text" class="form-control" id="filter-nama" placeholder="Nama barang">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select class="form-control" id="filter-kategori">
                    <option value="">-- Semua Kategori --</option>
                    <!-- Options will be populated by JavaScript -->
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="form-label">Harga Min</label>
                <input type="number" class="form-control" id="filter-harga-min" placeholder="Min">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="form-label">Harga Max</label>
                <input type="number" class="form-control" id="filter-harga-max" placeholder="Max">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button class="btn btn-primary btn-get-data w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-2">
        <span id="loading-filter" style="display: none;" class="text-muted">
            <i class="bi bi-arrow-repeat spin"></i> Loading...
        </span>
    </div>
</div>
# Master Items Data Flow Explanation

## 📋 Overview
This application uses **AJAX (Asynchronous JavaScript)** to load data dynamically. Unlike traditional Laravel Blade loops, the data is fetched via JavaScript and rendered using DataTables.

---

## 🔄 Complete Data Flow

### **Step 1: Initial Page Load**

**Route:** `GET /master-items` (from `routes/web.php`)
```php
Route::get('/master-items', [MasterItemsController::class, 'index']);
```

**Controller Method:** `MasterItemsController@index` (line 10-13)
```php
public function index()
{
    return view('master_items.index.index');
}
```

**What happens:**
- Laravel renders the Blade template `master_items.index.index`
- The page loads with an **EMPTY table** (see `table.blade.php` - the `<tbody>` is empty)
- The JavaScript file is included at the bottom

---

### **Step 2: JavaScript Initialization**

**File:** `resources/views/master_items/index/js.blade.php`

**When page loads:**
```javascript
$(document).ready(function() {
    $('#table').DataTable({  // Initialize DataTables plugin
        searching: false,
        order: [[0, 'desc']],
    });
    getData()  // ⭐ THIS IS WHERE DATA FETCHING STARTS
});
```

**What happens:**
1. jQuery waits for DOM to be ready
2. DataTables plugin initializes on the empty table
3. `getData()` function is called immediately

---

### **Step 3: Fetching Data via AJAX**

**The `getData()` function (lines 25-83):**

```javascript
function getData(){
    // 1. Get filter values from the form
    var filter_kode = $('#filter-kode').val()
    var filter_nama = $('#filter-nama').val()
    var filter_harga_min = $('#filter-harga-min').val()
    var filter_harga_max = $('#filter-harga-max').val()
    
    // 2. Clear existing table data
    dataTableObj.clear().draw();
    
    // 3. Make AJAX request to backend
    $.ajax({
        url: '{{url("master-items/search")}}',
        data: 'kode=' + filter_kode + '&nama=' + filter_nama + '...',
        success: function(results) {
            // Process the data here
        }
    })
}
```

**AJAX Request Details:**
- **URL:** `/master-items/search` (web route, not API route)
- **Method:** GET (default)
- **Data sent:** Filter parameters as query string
- **Response:** JSON data

---

### **Step 4: Backend Processing**

**Route:** `GET /master-items/search` (from `routes/web.php`)
```php
Route::get('/master-items/search', [MasterItemsController::class, 'search']);
```

**Controller Method:** `MasterItemsController@search` (lines 15-35)

```php
public function search(Request $request)
{
    // 1. Get filter parameters from request
    $kode = $request->kode;
    $nama = $request->nama;
    $hargamin = $request->hargamin;
    $hargamax = $request->hargamax;

    // 2. Start building query
    $data_search = MasterItem::query();

    // 3. Apply filters conditionally
    if (!empty($kode)) 
        $data_search = $data_search->where('kode', $kode);
    
    if (!empty($nama)) 
        $data_search = $data_search->where('nama', 'LIKE', '%' . $nama . '%');
    
    if (!empty($hargamin)) 
        $data_search = $data_search->where('harga_beli', '>=', $hargamin)
                                   ->where('harga_beli', '<=', $hargamax);

    // 4. Select specific columns and get results
    $data_search = $data_search->select('id', 'kode', 'nama', 'jenis', 'harga_beli', 'laba', 'supplier')
                                ->orderBy('id')
                                ->get();

    // 5. Return JSON response
    return json_encode([
        'status' => 200,
        'data' => $data_search
    ]);
}
```

**How Filters Work:**
- **Kode Filter:** Exact match (`where('kode', $kode)`)
- **Nama Filter:** Partial match using LIKE (`where('nama', 'LIKE', '%' . $nama . '%')`)
- **Price Range:** Between min and max (`where('harga_beli', '>=', $hargamin)->where('harga_beli', '<=', $hargamax)`)
- **Empty filters:** Ignored (not applied to query)

**Database Query Example:**
```sql
SELECT id, kode, nama, jenis, harga_beli, laba, supplier 
FROM master_items 
WHERE nama LIKE '%obat%' 
  AND harga_beli >= 1000 
  AND harga_beli <= 5000
ORDER BY id
```

---

### **Step 5: Frontend Data Rendering (THE LOOP)**

**This is where the "loop" happens!** (lines 44-68 in `js.blade.php`)

```javascript
success: function(results) {
    var data = results.data  // Array of items from backend
    
    // ⭐ THIS IS THE LOOP - $.each() iterates through each item
    $.each(data, function(index, item) {
        // For each item, build a row array
        array_temp = [];
        
        // Calculate harga_jual
        var harga_jual = item.harga_beli + item.harga_beli * item.laba / 100;
        harga_jual = Math.round(harga_jual)
        
        // Build action buttons HTML
        var html = `<div class="btn-group">`;
        html += `<a href="..." class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a>`;
        html += `<button class="btn btn-sm btn-warning btn-update" ...>Update</button>`;
        html += `<button class="btn btn-sm btn-danger btn-delete" ...>Delete</button>`;
        html += `</div>`;

        // Build row data in correct column order
        array_temp.push(item.kode);        // Column 1: Kode
        array_temp.push(item.nama);        // Column 2: Nama
        array_temp.push(item.jenis);       // Column 3: Jenis
        array_temp.push(item.harga_beli);  // Column 4: Harga Beli
        array_temp.push(harga_jual);       // Column 5: Harga Jual (calculated)
        array_temp.push(item.supplier);    // Column 6: Supplier
        array_temp.push(html);             // Column 7: Actions (buttons)

        // Add row to DataTable
        dataTableObj.row.add(array_temp).draw(true);
    });
}
```

**Key Points:**
- **No Blade loop:** The loop is in JavaScript using `$.each()`
- **Dynamic rendering:** Each item creates one table row
- **DataTables handles display:** The `row.add()` method adds rows to the table
- **Order matters:** Data is pushed in the exact order of table columns

---

## 🔄 Real-Time Update Flow

### **How Update Works in Real-Time:**

**Step 1: User clicks Update button**
```javascript
$(document).on('click', '.btn-update', function() {
    // Get data from button's data attributes
    var id = $(this).data('id');
    var kode = $(this).data('kode');
    // ... get other data
    
    // Fill modal form with current data
    $('#update-nama').val(nama);
    $('#update-harga-beli').val(hargaBeli);
    // ...
    
    // Show modal
    var updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
    updateModal.show();
});
```

**Step 2: User submits form**
```javascript
$('#btn-update-submit').click(function() {
    // Collect form data
    var formData = {
        nama: $('#update-nama').val(),
        harga_beli: $('#update-harga-beli').val(),
        // ...
    };
    
    // Send AJAX PUT request
    $.ajax({
        url: '/api/master-items/' + id,
        type: 'PUT',
        data: formData,
        success: function(response) {
            // Close modal
            updateModal.hide();
            
            // ⭐ REFRESH THE TABLE - THIS IS THE "REAL-TIME" PART
            getData();  // Calls the same function that loads data initially
        }
    });
});
```

**Step 3: Backend processes update**
```php
// Route: PUT /api/master-items/{id}
public function update(Request $request, $id)
{
    $data_item = MasterItem::findOrFail($id);
    
    // Update fields
    $data_item->nama = $request->nama;
    $data_item->harga_beli = $request->harga_beli;
    // ...
    
    $data_item->save();
    
    // Return JSON response
    return response()->json([
        'status' => 200,
        'message' => 'Item berhasil diupdate'
    ]);
}
```

**Step 4: Table refreshes automatically**
- `getData()` is called again
- New AJAX request fetches updated data
- Table is cleared and repopulated with fresh data
- **No page reload needed!** This is why it's "real-time"

---

## 🗑️ Delete Flow (Similar to Update)

```javascript
// 1. User clicks Delete button
$('.btn-delete').click() → Shows confirmation modal

// 2. User confirms
$('#btn-delete-confirm').click() → Sends DELETE AJAX request

// 3. Backend deletes
DELETE /api/master-items/{id} → MasterItem::find($id)->delete()

// 4. Table refreshes
getData() → Fetches fresh data, deleted item is gone
```

---

## 📊 Visual Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│ 1. USER VISITS PAGE                                          │
│    GET /master-items                                         │
│    ↓                                                          │
│    Controller returns view (empty table)                     │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. JAVASCRIPT INITIALIZES                                    │
│    $(document).ready() → getData() called                    │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. AJAX REQUEST SENT                                         │
│    GET /master-items/search?kode=...&nama=...               │
│    ↓                                                          │
│    JavaScript waits for response                             │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. BACKEND PROCESSES                                         │
│    Controller receives request                               │
│    ↓                                                          │
│    Builds database query with filters                        │
│    ↓                                                          │
│    Executes: SELECT * FROM master_items WHERE ...            │
│    ↓                                                          │
│    Returns JSON: {status: 200, data: [...]}                 │
└─────────────────────────────────────────────────────────────┘
                          ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. FRONTEND RECEIVES DATA                                    │
│    AJAX success callback triggered                           │
│    ↓                                                          │
│    $.each(data, function(item) {                             │
│        // LOOP: For each item...                             │
│        - Calculate harga_jual                                │
│        - Build action buttons HTML                           │
│        - Create row array                                    │
│        - Add row to DataTable                               │
│    })                                                         │
│    ↓                                                          │
│    Table displays all rows                                   │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔑 Key Concepts

### **Why No Blade Loop?**
- Traditional Laravel uses Blade `@foreach` loops
- This app uses **AJAX + JavaScript** for dynamic loading
- Benefits:
  - No page reload needed
  - Can filter/search without refreshing
  - Better user experience

### **Where is the Loop?**
- **NOT in Blade template** (table.blade.php has empty `<tbody>`)
- **IN JavaScript** - `$.each(data, function(index, item) { ... })` (line 44)
- This loop runs **after** data is fetched from server

### **How Filters Work?**
1. User enters filter values in form inputs
2. JavaScript reads values: `$('#filter-kode').val()`
3. Values sent as query parameters in AJAX request
4. Backend builds dynamic SQL query based on filters
5. Only matching records returned
6. Frontend displays filtered results

### **Real-Time Update Explained:**
- "Real-time" means **no page reload**
- After update/delete, `getData()` is called again
- Fresh data fetched from database
- Table automatically updates
- User sees changes immediately

---

## 📝 Summary

1. **Page loads** → Empty table rendered
2. **JavaScript runs** → Calls `getData()`
3. **AJAX request** → Fetches data from `/master-items/search`
4. **Backend filters** → Database query with conditions
5. **JSON response** → Array of items returned
6. **JavaScript loop** → `$.each()` creates table rows
7. **DataTables displays** → Rows appear in table
8. **Update/Delete** → AJAX request → `getData()` called again → Table refreshes

The "loop" you're looking for is in **JavaScript**, not Blade! 🎯




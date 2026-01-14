<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kategori - {{ $kategori->kode }}</title>
    <style>
        @page {
            margin: 20mm;
            margin-bottom: 30mm;
            footer: html_pdfFooter;
        }
        
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }
        
        .kategori-info {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 12px;
            border: 1px solid #dee2e6;
        }
        
        .kategori-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .kategori-info td {
            padding: 6px 8px;
        }
        
        .kategori-info td:first-child {
            font-weight: bold;
            width: 140px;
        }
        
        .items-section {
            margin-bottom: 30px;
        }
        
        .items-section h2 {
            font-size: 14px;
            margin-bottom: 12px;
            font-weight: bold;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        
        .items-table th {
            background-color: #343a40;
            color: white;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #212529;
            font-weight: bold;
        }
        
        .items-table td {
            padding: 6px;
            border: 1px solid #dee2e6;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .no-items {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }
        
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Kategori</h1>
    </div>
    
    <div class="kategori-info">
        <table>
            <tr>
                <td>Kode Kategori</td>
                <td>: {{ $kategori->kode }}</td>
            </tr>
            <tr>
                <td>Nama Kategori</td>
                <td>: {{ $kategori->nama }}</td>
            </tr>
        </table>
    </div>
    
    <div class="items-section">
        <h2>Daftar Items</h2>
        
        @if($masterItems && $masterItems->count() > 0)
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Jenis</th>
                    <th class="text-right" style="width: 100px;">Harga Beli</th>
                    <th class="text-right" style="width: 100px;">Harga Jual</th>
                    <th>Supplier</th>
                </tr>
            </thead>
            <tbody>
                @foreach($masterItems as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->jenis }}</td>
                    <td class="text-right">{{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->harga_beli + $item->harga_beli * $item->laba / 100, 0, ',', '.') }}</td>
                    <td>{{ $item->supplier }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-items">
            Tidak ada items yang memiliki kategori ini
        </div>
        @endif
    </div>
    
    <htmlpagefooter name="pdfFooter">
        <div style="text-align: center; font-size: 9px; color: #666; padding-top: 5px; border-top: 1px solid #ccc; width: 100%;">
            Dicetak pada: {{ $tanggalCetak }}
        </div>
    </htmlpagefooter>
</body>
</html>


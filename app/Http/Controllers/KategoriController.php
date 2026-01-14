<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KategoriController extends Controller
{
    /**
     * Generate kode from nama
     * Single word: first 3 letters (or all if less than 3)
     * Multiple words: first 3 letters of each word (or all if less than 3) separated by dash
     */
    private function generateKode($nama)
    {
        $words = explode(' ', trim($nama));
        $kodeParts = [];
        
        foreach ($words as $word) {
            $word = strtoupper(trim($word));
            if (strlen($word) >= 3) {
                $kodeParts[] = substr($word, 0, 3);
            } else {
                // Use the word as-is if it's 2 letters or less (no padding)
                $kodeParts[] = $word;
            }
        }
        
        return implode('-', $kodeParts);
    }

    public function index()
    {
        return view('kategoris.index.index');
    }

    public function getAll()
    {
        $kategoris = Kategori::orderBy('nama')->get(['id', 'kode', 'nama']);
        return response()->json([
            'status' => 200,
            'data' => $kategoris
        ]);
    }

    public function search(Request $request)
    {
        $kode = $request->kode;
        $nama = $request->nama;

        $data_search = Kategori::query();

        if (!empty($kode)) $data_search = $data_search->where('kode', $kode);
        if (!empty($nama)) $data_search = $data_search->where('nama', 'LIKE', '%' . $nama . '%');

        $data_search = $data_search->select('id', 'kode', 'nama')->orderBy('nama')->get();

        return json_encode([
            'status' => 200,
            'data' => $data_search
        ]);
    }

    public function formView($method, $id = 0)
    {
        if ($method == 'new') {
            $item = [];
        } else {
            $item = Kategori::find($id);
        }
        $data['item'] = $item;
        $data['method'] = $method;
        return view('kategoris.form.index', $data);
    }

    public function singleView($kode)
    {
        $kategori = Kategori::where('kode', $kode)->with('masterItems')->first();
        $data['data'] = $kategori;
        $data['masterItems'] = $kategori ? $kategori->masterItems : collect();
        return view('kategoris.single.index', $data);
    }

    public function formSubmit(Request $request, $method, $id = 0)
    {
        if ($method == 'new') {
            $data_item = new Kategori;
            // Generate kode from nama if not provided
            if ($request->filled('kode')) {
                $kode = $request->kode;
            } else {
                $kode = $this->generateKode($request->nama);
            }
        } else {
            $data_item = Kategori::find($id);
            // Use provided kode or keep existing
            $kode = $request->filled('kode') ? $request->kode : $data_item->kode;
        }

        $data_item->nama = $request->nama;
        $data_item->kode = $kode;
        $data_item->save();

        return redirect('kategoris');
    }

    public function delete($id)
    {
        Kategori::find($id)->delete();
        return redirect('kategoris');
    }

    public function update(Request $request, $id)
    {
        try {
            $data_item = Kategori::findOrFail($id);
            
            $data_item->nama = $request->nama;
            // Update kode if provided
            if ($request->filled('kode')) {
                $data_item->kode = $request->kode;
            }
            $data_item->save();

            return response()->json([
                'status' => 200,
                'message' => 'Kategori berhasil diupdate',
                'data' => $data_item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Gagal mengupdate kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteApi($id)
    {
        try {
            $item = Kategori::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Kategori berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Gagal menghapus kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadPdf($kode)
    {
        $kategori = Kategori::where('kode', $kode)->with('masterItems')->first();
        
        if (!$kategori) {
            abort(404, 'Kategori tidak ditemukan');
        }

        $data = [
            'kategori' => $kategori,
            'masterItems' => $kategori->masterItems,
            'tanggalCetak' => now()->format('d/m/Y H:i:s')
        ];

        $pdf = Pdf::loadView('kategoris.pdf.index', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('Kategori_' . $kategori->kode . '_' . date('YmdHis') . '.pdf');
    }
}

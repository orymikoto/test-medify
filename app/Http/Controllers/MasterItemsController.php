<?php

namespace App\Http\Controllers;

use App\Models\MasterItem;
use App\Models\Kategori;
use App\Models\KategoriItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MasterItemsController extends Controller
{
    public function index()
    {
        return view('master_items.index.index');
    }

    public function search(Request $request)
    {
        $kode = $request->kode;
        $nama = $request->nama;
        $kategori = $request->kategori;
        $hargamin = $request->hargamin;
        $hargamax = $request->hargamax;

        $data_search = MasterItem::query();

        if (!empty($kode)) $data_search = $data_search->where('kode', $kode);
        if (!empty($nama)) $data_search = $data_search->where('nama', 'LIKE', '%' . $nama . '%');
        if (!empty($hargamin)) $data_search = $data_search->where('harga_beli', '>=', $hargamin);
        if (!empty($hargamax)) $data_search = $data_search->where('harga_beli', '<=', $hargamax);
        
        // Filter by kategori
        if (!empty($kategori)) {
            $data_search = $data_search->whereHas('kategoris', function($query) use ($kategori) {
                $query->where('kategoris.id', $kategori);
            });
        }

        $data_search = $data_search->select('id', 'kode', 'nama', 'jenis', 'harga_beli', 'laba', 'supplier', 'picture')->orderBy('id')->get();

        // Load kategoris for each item with full details
        foreach ($data_search as $item) {
            $kategoris = $item->kategoris()->get(['kategoris.id', 'kategoris.kode', 'kategoris.nama']);
            $item->kategoris = $kategoris->pluck('id')->toArray();
            $item->kategoris_detail = $kategoris->map(function($k) {
                return [
                    'id' => $k->id,
                    'kode' => $k->kode,
                    'nama' => $k->nama
                ];
            })->toArray();
        }

        return json_encode([
            'status' => 200,
            'data' => $data_search
        ]);
    }

    public function formView($method, $id = 0)
    {
        if ($method == 'new') {
            $item = [];
            $selectedKategoris = [];
        } else {
            $item = MasterItem::find($id);
            $selectedKategoris = $item->kategoris()->pluck('kategoris.id')->toArray();
        }
        $data['item'] = $item;
        $data['method'] = $method;
        $data['kategoris'] = Kategori::orderBy('nama')->get();
        $data['selectedKategoris'] = $selectedKategoris;
        return view('master_items.form.index', $data);
    }

    public function singleView($kode)
    {
        $data['data'] = MasterItem::where('kode', $kode)->with('kategoris')->first();
        return view('master_items.single.index', $data);
    }

    public function formSubmit(Request $request, $method, $id = 0)
    {
        if ($method == 'new') {
            $data_item = new MasterItem;
            $kode = MasterItem::count('id');
            $kode = $kode + 1;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);
            sleep(3);
        } else {
            $data_item = MasterItem::find($id);
            $kode = $data_item->kode;
        }

        $data_item->nama = $request->nama;
        $data_item->harga_beli = $request->harga_beli;
        $data_item->laba = $request->laba;
        $data_item->kode = $kode;
        $data_item->supplier = $request->supplier;
        $data_item->jenis = $request->jenis;

        // Handle picture upload
        if ($request->hasFile('picture')) {
            // Delete old picture if exists
            if ($data_item->picture && Storage::disk('public')->exists($data_item->picture)) {
                Storage::disk('public')->delete($data_item->picture);
            }

            $picture = $request->file('picture');
            $pictureName = 'master_items/' . time() . '_' . $picture->getClientOriginalName();
            $picture->storeAs('public', $pictureName);
            $data_item->picture = $pictureName;
        }

        $data_item->save();

        // Always delete existing relationships first (force delete to permanently remove)
        KategoriItem::where('master_item_id', $data_item->id)->forceDelete();
        
        // Handle kategoris relationships - create new ones if provided
        if ($request->has('kategoris') && is_array($request->kategoris) && count($request->kategoris) > 0) {
            // Create new relationships
            foreach ($request->kategoris as $kategoriId) {
                if (!empty($kategoriId)) {
                    KategoriItem::create([
                        'master_item_id' => $data_item->id,
                        'kategori_id' => $kategoriId
                    ]);
                }
            }
        }

        return redirect('master-items');
    }

    public function delete($id)
    {
        MasterItem::find($id)->delete();
        return redirect('master-items');
    }

    public function updateRandomData()
    {
        $data = MasterItem::get();
        foreach($data as $item)
        {
            $kode = $item->id;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);

            $item->harga_beli = rand(100,1000000);
            $item->laba = rand(10,99);
            $item->kode = $kode;
            $item->supplier = $this->getRandomSupplier();
            $item->jenis = $this->getRandomJenis();
            $item->save();
        }
    }

    private function getRandomSupplier()
    {
        $array = ['Tokopaedi','Bukulapuk','TokoBagas','E Commurz','Blublu'];
        $random = rand(0,4);
        return $array[$random];
    }

    private function getRandomJenis()
    {
        $array = ['Obat','Alkes','Matkes','Umum','ATK'];
        $random = rand(0,4);
        return $array[$random];
    }

    public function update(Request $request, $id)
    {
        try {
            $data_item = MasterItem::findOrFail($id);
            
            $data_item->nama = $request->nama;
            $data_item->harga_beli = $request->harga_beli;
            $data_item->laba = $request->laba;
            $data_item->supplier = $request->supplier;
            $data_item->jenis = $request->jenis;

            // Handle picture upload
            if ($request->hasFile('picture')) {
                // Delete old picture if exists
                if ($data_item->picture && Storage::disk('public')->exists($data_item->picture)) {
                    Storage::disk('public')->delete($data_item->picture);
                }

                $picture = $request->file('picture');
                $pictureName = 'master_items/' . time() . '_' . $picture->getClientOriginalName();
                $picture->storeAs('public', $pictureName);
                $data_item->picture = $pictureName;
            }

            $data_item->save();

            // Always delete existing relationships first (force delete to permanently remove)
            KategoriItem::where('master_item_id', $data_item->id)->forceDelete();
            
            // Handle kategoris relationships - create new ones if provided
            if ($request->has('kategoris') && is_array($request->kategoris) && count($request->kategoris) > 0) {
                // Create new relationships
                foreach ($request->kategoris as $kategoriId) {
                    if (!empty($kategoriId)) {
                        KategoriItem::create([
                            'master_item_id' => $data_item->id,
                            'kategori_id' => $kategoriId
                        ]);
                    }
                }
            }

            // Reload kategoris for response
            $data_item->load('kategoris');

            return response()->json([
                'status' => 200,
                'message' => 'Item berhasil diupdate',
                'data' => $data_item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Gagal mengupdate item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteApi($id)
    {
        try {
            $item = MasterItem::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Item berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Gagal menghapus item: ' . $e->getMessage()
            ], 500);
        }
    }
}

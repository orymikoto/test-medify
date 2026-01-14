<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKategoriItemRequest;
use App\Http\Requests\UpdateKategoriItemRequest;
use App\Models\KategoriItem;

class KategoriItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreKategoriItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreKategoriItemRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\KategoriItem  $kategoriItem
     * @return \Illuminate\Http\Response
     */
    public function show(KategoriItem $kategoriItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\KategoriItem  $kategoriItem
     * @return \Illuminate\Http\Response
     */
    public function edit(KategoriItem $kategoriItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateKategoriItemRequest  $request
     * @param  \App\Models\KategoriItem  $kategoriItem
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateKategoriItemRequest $request, KategoriItem $kategoriItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\KategoriItem  $kategoriItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(KategoriItem $kategoriItem)
    {
        //
    }
}

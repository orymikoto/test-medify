<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'nama',
        'kode'
    ];

    public function kategoriItems()
    {
        return $this->hasMany(KategoriItem::class)->whereNull('deleted_at');
    }

    /**
     * Many-to-many relationship with MasterItem through KategoriItem
     * Only includes non-deleted kategori_items
     */
    public function masterItems()
    {
        return $this->belongsToMany(MasterItem::class, 'kategori_items', 'kategori_id', 'master_item_id')
            ->wherePivotNull('deleted_at')
            ->withTimestamps()
            ->withPivot('id', 'deleted_at');
    }
}

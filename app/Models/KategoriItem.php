<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'kategori_id',
        'master_item_id'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
    public function masterItem()
    {
        return $this->belongsTo(MasterItem::class);
    }
}

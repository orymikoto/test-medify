<?php

use App\Models\Kategori;
use App\Models\MasterItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kategori_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Kategori::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(MasterItem::class)->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kategori_items');
    }
};

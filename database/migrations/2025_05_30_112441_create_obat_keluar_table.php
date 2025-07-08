<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('obat_keluar', function (Blueprint $table) {
            $table->id('id_obatkeluar');
            $table->string('nama', 50);
            $table->string('jenis_kelamin', 10);
            $table->string('keluhan', 100);
            $table->decimal('suhu_tubuh', 4, 1);
            $table->integer('denyut_nadi');
            $table->string('tekanan_darah', 10);
            $table->string('diagnosa', 100);
            $table->string('keterangan', 150);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat_keluar');
    }
};

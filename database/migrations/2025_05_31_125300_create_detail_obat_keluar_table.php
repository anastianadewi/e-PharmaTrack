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
        Schema::create('detail_obat_keluar', function (Blueprint $table) {
            $table->id('id_detailobatkeluar');
            $table->unsignedBigInteger('id_obatkeluar');
            $table->unsignedBigInteger('id_detailobat');
            $table->integer('jumlah');
            $table->timestamps();

            $table->foreign('id_obatkeluar')->references('id_obatkeluar')->on('obat_keluar')->onDelete('cascade');
            $table->foreign('id_detailobat')->references('id_detailobat')->on('detail_obat')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_obat_keluar');
    }
};
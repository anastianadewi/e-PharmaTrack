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
        Schema::create('obat', function (Blueprint $table) {
            $table->id('id_obat');
            $table->unsignedBigInteger('id_jenisobat');
            $table->string('nama', 50);
            $table->string('golongan', 50);
            $table->string('keterangan', 150);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_jenisobat')->references('id_jenisobat')->on('jenis_obat')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('obat_masuk', function (Blueprint $table) {
            $table->id('id_obatmasuk');
            $table->unsignedBigInteger('id_obat');
            $table->integer('jumlah');
            $table->date('expired');
            $table->timestamps();

            $table->foreign('id_obat')->references('id_obat')->on('obat')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat_masuk');
    }
};

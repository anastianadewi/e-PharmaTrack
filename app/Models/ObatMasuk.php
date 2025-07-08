<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObatMasuk extends Model
{
    protected $table = 'obat_masuk';

    protected $primaryKey = 'id_obatmasuk'; // Primary key

    protected $fillable = ['id_obat', 'jumlah', 'expired'];

    public $timestamps = true;
    //relasi
    public function stokobat()
    {
        return $this->belongsTo(StokObat::class, 'id_obat')->withTrashed();
    }
}

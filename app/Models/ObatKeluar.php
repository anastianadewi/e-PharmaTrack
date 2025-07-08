<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObatKeluar extends Model
{
    protected $table = 'obat_keluar';
    protected $primaryKey = 'id_obatkeluar';
    public $timestamps = true;

    protected $fillable = [
        'nama',
        'jenis_kelamin',
        'keluhan',
        'suhu_tubuh',
        'denyut_nadi',
        'tekanan_darah',
        'diagnosa',
        'keterangan'
    ];

    public function detailObat()
    {
        return $this->hasMany(DetailObatKeluar::class, 'id_obatkeluar');
    }
}

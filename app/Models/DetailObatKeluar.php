<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailObatKeluar extends Model
{
    protected $table = 'detail_obat_keluar';
    protected $primaryKey = 'id_detailobatkeluar';
    public $timestamps = true;

    protected $fillable = [
        'id_obatkeluar',
        'id_detailobat',
        'jumlah'
    ];

    public function obatKeluar()
    {
        return $this->belongsTo(ObatKeluar::class, 'id_obatkeluar');
    }

    public function detailObat()
    {
        return $this->belongsTo(DetailObat::class, 'id_detailobat')->withTrashed();
    }
}

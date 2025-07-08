<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StokObat extends Model
{
    use SoftDeletes;
    
    protected $table = 'obat'; // pastikan sesuai nama tabel

    protected $primaryKey = 'id_obat'; // Primary key

    protected $fillable = ['id_jenisobat', 'nama', 'golongan', 'keterangan'];

    public $timestamps = true;
    //relasi
    public function detail()
    {
        return $this->hasMany(DetailObat::class, 'id_obat');
    }
    
    public function jenisObat()
    {
        return $this->belongsTo(JenisObat::class, 'id_jenisobat')->withTrashed();
    }

    public function detailObatKeluar()
    {
        return $this->hasMany(DetailObatKeluar::class, 'id_obat');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisObat extends Model
{
    use SoftDeletes;
    
    protected $table = 'jenis_obat'; // Nama tabel
    protected $primaryKey = 'id_jenisobat'; // Primary key
    protected $fillable = ['nama'];
    public $timestamps = true;

    //relasi
    public function stokobat()
    {
        return $this->hasMany(StokObat::class, 'id_jenisobat');
    }
}

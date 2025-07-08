<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailObat extends Model
{
    use SoftDeletes;

    protected $table = 'detail_obat';

    protected $primaryKey = 'id_detailobat'; // Primary key

    protected $fillable = ['id_obat', 'jumlah', 'expired'];

    public $timestamps = true;

    protected static function booted()
    {
        static::updated(function ($detail) {
            if ($detail->jumlah <= 0) {
                $detail->delete();
            }
        });
    }

    //relasi
    public function stokobat()
    {
        return $this->belongsTo(StokObat::class, 'id_obat')->withTrashed();
    }

    public function detailobatkeluar()
    {
        return $this->hasMany(DetailObatKeluar::class, 'id_detailobatkeluar');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id_user')->withTrashed();
    }

}

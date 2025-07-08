<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;
    
    protected $table = 'user'; // Nama tabel

    protected $primaryKey = 'id_user'; // Primary key

    protected $fillable = ['nama', 'username', 'password', 'role'];

    public $timestamps = true;

    // Jika mau Laravel mengenali nama (untuk Auth), override getAuthIdentifierName
    public function getAuthIdentifierName()
    {
        return 'id_user';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kebun extends Model
{
    use HasFactory;

    protected $table = 'kebun'; // Nama tabel di database

    protected $fillable = ['nama_kebun']; // Kolom yang bisa diisi
}
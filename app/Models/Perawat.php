<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Poli;

class Perawat extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_perawat';

    protected $fillable = [
        'nama',
        'phone',
        'email',
        'id_poli',
    ];

    public function poli()
    {
        return $this->belongsTo(Poli::class, 'id_poli');
    }
}

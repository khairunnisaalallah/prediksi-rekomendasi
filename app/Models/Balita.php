<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balita extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nik',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'tanggal_posyandu',
        'berat',
        'tinggi',
        'nama_ayah',
        'nama_ibu',
        'status_gizi',
        'rekomendasi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_posyandu' => 'date',
        'berat' => 'float',
        'tinggi' => 'float',
    ];

    public function getUsiaBulanAttribute(): ?int
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->diffInMonths(now()) : null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'mision',
        'vision',
        'mapa_url',
        'departamento',
        'provincia',
        'distrito',
        'calle',
        'descripcion',
        'favicon_url',
        'delivery',
        'telefono'
    ];

    public function image_m()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
    public function getCoordenadas()
    {
        if (preg_match('/@([-0-9.]+),([-0-9.]+)/', $this->mapa_url, $matches)) {
            return ['lat' => $matches[1], 'lng' => $matches[2]];
        }
        return null;
    }
}

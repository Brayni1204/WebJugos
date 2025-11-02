<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtitulo extends Model
{
    use HasFactory;

    protected $fillable = ['id_pagina', 'titulo_subtitulo', 'resumen', 'status', 'imagen'];

    public function pagina()
    {
        return $this->belongsTo(Pagina::class, 'id_pagina');
    }

    public function Paginas()
    {
        return $this->belongsTo(Pagina::class, 'id_pagina');
    }

    public function Parrafo()
    {
        return $this->hasMany(Parrafo::class, 'id_subtitulo', 'id');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}

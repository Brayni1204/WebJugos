<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagina extends Model
{
    use HasFactory;

    protected $fillable = ['titulo_paginas', 'slug', 'resumen', 'status'];

    public function Subtitulo()
    {
        return $this->hasMany(Subtitulo::class, 'id_pagina', 'id');
    }
    
    public function subtitulos()
    {
        return $this->hasMany(Subtitulo::class, 'id_pagina')->where('status', 2)->take(3);
    }



    public function image_pagina()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}

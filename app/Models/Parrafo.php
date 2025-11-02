<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parrafo extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_subtitulo',
        'contenido',
        'status',
    ];

    public function Subtitulo()
    {
        return $this->belongsTo(Subtitulo::class, 'id_subtitulo', 'id');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}

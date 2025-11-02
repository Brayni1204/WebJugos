<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class Producto extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_categoria',
        'nombre_producto',
        'descripcion',
        'stock',
        'status',
    ];
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }
    public function precios()
    {
        return $this->hasOne(Precio::class, 'id_producto');
    }
    public function Componentes()
    {
        return $this->hasMany(Componente::class, 'id_producto', 'id');
    }
    public function HistorialPrecio()
    {
        return $this->hasMany(HistorialPrecio::class);
    }
    public function DetalleVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto');
    }
    public function image()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}

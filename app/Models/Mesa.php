<?php

namespace App\Models;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Mesa extends Model
{
    use HasFactory;

    protected $table = 'mesas';

    protected $fillable = ['codigo_qr', 'numero_mesa', 'estado', 'uuid', 'status'];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function generarQr()
    {
        $url = route('views.reservar', ['mesa' => $this->uuid]); // ✅ Usa UUID en la URL
        $qrCode = new QrCode($url);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        return $result->getDataUri();
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($mesa) {
            $mesa->uuid = Str::uuid(); // Genera un UUID único
        });
    }
}

<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\NuevoPedido;

Broadcast::channel('pedidos', function ($user) {
    return true; // Permitir a todos los usuarios escuchar
});

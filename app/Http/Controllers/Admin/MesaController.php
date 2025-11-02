<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class MesaController extends Controller
{
    public function index()
    {
        $mesas = Mesa::orderBy('created_at', 'desc')->get();
        return view('admin.mesas.index', compact('mesas'));
    }

    public function create()
    {
        $ultimaMesa = Mesa::latest()->first();
        $numeroMesa = $ultimaMesa ? $ultimaMesa->numero_mesa + 1 : 1;
        return view('admin.mesas.create', compact('numeroMesa'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'numero_mesa' => 'required|integer|unique:mesas,numero_mesa',
        ]);
        $mesa = Mesa::create([
            'numero_mesa' => $request->numero_mesa,
            'estado' => 'disponible',
            'uuid' => Str::uuid(),
        ]);
        $token = Crypt::encryptString($mesa->uuid);
        $url = route('views.reservar', ['token' => $token]);
        $qrCode = new QrCode($url);
        $writer = new PngWriter();
        $qrImage = base64_encode($writer->write($qrCode)->getString());
        $mesa->update(['codigo_qr' => $qrImage]);
        return redirect()->route('admin.mesas.index')->with('success', 'Mesa creada exitosamente');
    }

    public function show(Mesa $mesa)
    {
        return view('admin.mesas.show', compact('mesa'));
    }
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}

    public function toggleStatus(Mesa $mesa)
    {
        $mesa->status = $mesa->status == '1' ? '0' : '1';
        $mesa->save();

        return redirect()->route('admin.mesas.index')->with('success', 'Estado de la mesa actualizado correctamente.');
    }
}

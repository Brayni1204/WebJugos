<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\DetalleVenta;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $ventas = Venta::latest()->paginate(10);
        return view('admin.ventas.index', compact('ventas'));
    }


    public function create()
    {
        return view('admin.ventas.create', compact('clientes', 'productos', 'categorias'));
    }

    public function store(Request $request) {}

    public function edit(Venta $venta)
    {
        /* $clientes = Cliente::all();
        $productos = Producto::where('status', 1)->get();
        $categorias = Categoria::where('status', 2)->get();

        // Cargar detalles de la venta con los productos
        $venta->load(['detalles.producto', 'cliente']);

        return view('admin.ventas.edit', compact('venta', 'productos', 'categorias', 'clientes'));
    */
    }


    public function update(Request $request, Venta $venta)
    {
        /* $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Actualizar cliente y total de la venta
            $venta->update([
                'id_cliente' => $request->cliente_id,
                'total' => $request->total,
            ]);

            // Eliminar detalles de venta anteriores
            $venta->detalles()->delete();

            // Insertar los nuevos detalles
            foreach ($request->productos as $producto) {
                DetalleVenta::create([
                    'id_venta' => $venta->id,
                    'id_producto' => $producto['id'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio'],
                    'subtotal' => $producto['cantidad'] * $producto['precio'],
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Venta actualizada correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al actualizar la venta'], 500);
        } */
    }


    public function show(Venta $venta)
    {
        /* // Cargar las relaciones con detalles de venta y productos
        $venta->load(['cliente', 'detalles.producto']);

        return view('admin.ventas.show', compact('venta')); */
    }


    public function generarComprobante(Venta $venta)
    {
        /*        // Cargar la venta con sus detalles
        $venta->load(['cliente', 'detalles.producto']);

        // Generar el PDF con la vista 'admin.ventas.comprobante'
        $pdf = Pdf::loadView('admin.ventas.comprobante', compact('venta'));

        // Retornar el PDF como descarga
        return $pdf->stream('Comprobante_Venta_' . $venta->id . '.pdf'); */
    }
}

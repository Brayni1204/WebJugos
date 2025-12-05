<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel; // Added for Excel exports
use Barryvdh\DomPDF\Facade\Pdf; // Added for PDF exports

class CajaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::all();
        return view('admin.reportes.index', compact('categorias'));
    }

    private function obtenerEstadisticas($ventas)
    {
        // Obtener el producto más vendido
        $productoMasVendido = 'N/A';
        if ($ventas->isNotEmpty()) {
            $productoMasVendido = DB::table('productos')
                ->select('productos.nombre_producto')
                ->join('detalle_ventas', 'productos.id', '=', 'detalle_ventas.producto_id')
                ->whereIn('detalle_ventas.venta_id', $ventas->pluck('id'))
                ->groupBy('productos.id', 'productos.nombre_producto')
                ->orderByRaw('SUM(detalle_ventas.cantidad) DESC')
                ->limit(1)
                ->pluck('productos.nombre_producto')
                ->first() ?? 'N/A';
        }

        // Obtener clientes frecuentes
        $clientesFrecuentes = collect();
        if ($ventas->isNotEmpty()) {
            $clientesFrecuentes = DB::table('clientes')
                ->select('clientes.nombre', DB::raw('COUNT(*) as total_compras'))
                ->join('ventas', 'clientes.id', '=', 'ventas.cliente_id')
                ->whereIn('ventas.id', $ventas->pluck('id'))
                ->groupBy('clientes.id', 'clientes.nombre')
                ->orderBy('total_compras', 'DESC')
                ->limit(5)
                ->get();
        }


        return [
            'producto_mas_vendido' => $productoMasVendido,
            'clientes_frecuentes' => $clientesFrecuentes
        ];
    }


    // 🔹 Reporte diario
    public function reporteDiario(Request $request)
    {
        try {
            $fecha = $request->input('fecha');

            // Verifica que la fecha esté presente
            if (!$fecha) {
                return response()->json(['error' => 'Fecha requerida'], 400);
            }

            $ventasDelDia = Venta::whereDate('created_at', $fecha)->get();
            $totalVentas = $ventasDelDia->sum('total_pago');
            $cantidadPedidos = $ventasDelDia->count();

            $estadisticas = $this->obtenerEstadisticas($ventasDelDia);


            return response()->json([
                'fecha' => $fecha,
                'total_ventas' => $totalVentas,
                'cantidad_pedidos' => $cantidadPedidos,
                'producto_mas_vendido' => $estadisticas['producto_mas_vendido'],
                'clientes_frecuentes' => $estadisticas['clientes_frecuentes']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al generar el reporte diario', 'message' => $e->getMessage()], 500);
        }
    }


    // 🔹 Reporte semanal
    public function reporteSemanal(Request $request)
    {
        try {
            if (!$request->has('inicio_semana')) {
                return response()->json(['error' => 'Fecha de inicio de semana requerida'], 400);
            }

            $inicioSemana = Carbon::parse($request->input('inicio_semana'))->startOfWeek();
            $finSemana = $inicioSemana->copy()->endOfWeek();

            $ventas = Venta::whereBetween('created_at', [$inicioSemana, $finSemana])->with('detalles')->get();
            $totalVentas = $ventas->sum('total_pago');

            $estadisticas = $this->obtenerEstadisticas($ventas);

            return response()->json([
                'rango' => $inicioSemana->toDateString() . ' -> ' . $finSemana->toDateString(),
                'total_ventas' => $totalVentas,
                'producto_mas_vendido' => $estadisticas['producto_mas_vendido'],
                'clientes_frecuentes' => $estadisticas['clientes_frecuentes']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al generar el reporte semanal', 'message' => $e->getMessage()], 500);
        }
    }


    // 🔹 Reporte mensual
    public function reporteMensual(Request $request)
    {
        try {
            $mes = $request->input('mes');
            $anio = $request->input('anio', Carbon::now()->year); // Año actual por defecto

            if (!$mes) {
                return response()->json(['error' => 'Mes requerido'], 400);
            }

            $ventas = Venta::whereYear('created_at', $anio)
                ->whereMonth('created_at', $mes)
                ->with('detalles')
                ->get();
            $totalVentas = $ventas->sum('total_pago');

            $estadisticas = $this->obtenerEstadisticas($ventas);

            return response()->json([
                'mes' => $mes,
                'anio' => $anio,
                'total_ventas' => $totalVentas,
                'producto_mas_vendido' => $estadisticas['producto_mas_vendido'],
                'clientes_frecuentes' => $estadisticas['clientes_frecuentes']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al generar el reporte mensual', 'message' => $e->getMessage()], 500);
        }
    }

    // 🔹 Reporte por rango de fechas
    public function reportePorRango(Request $request)
    {
        try {
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');

            if (!$fechaInicio || !$fechaFin) {
                return response()->json(['error' => 'Fechas de inicio y fin requeridas'], 400);
            }

            $fechaInicio = Carbon::parse($fechaInicio);
            $fechaFin = Carbon::parse($fechaFin)->endOfDay();

            $ventas = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin])->with('detalles')->get();
            $totalVentas = $ventas->sum('total_pago');

            $estadisticas = $this->obtenerEstadisticas($ventas);

            return response()->json([
                'rango' => $fechaInicio->toDateString() . ' -> ' . $fechaFin->toDateString(),
                'total_ventas' => $totalVentas,
                'producto_mas_vendido' => $estadisticas['producto_mas_vendido'],
                'clientes_frecuentes' => $estadisticas['clientes_frecuentes']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al generar el reporte por rango de fechas', 'message' => $e->getMessage()], 500);
        }
    }

    // 🔹 Nuevo: Ventas por día (detallado)
    public function ventasPorDia(Request $request)
    {
        try {
            $fecha = $request->input('fecha', Carbon::now()->toDateString());

            $ventas = Pedido::whereDate('created_at', $fecha)
                ->where('estado', 'Completado')
                ->selectRaw('COALESCE(SUM(total_pago), 0) as total_ventas, COUNT(*) as cantidad_pedidos')
                ->first();

            return response()->json([
                'fecha' => $fecha,
                'total_ventas' => floatval($ventas->total_ventas),
                'cantidad_pedidos' => intval($ventas->cantidad_pedidos),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en ventasPorDia: ' . $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Nuevo: Frecuencia de Clientes
    public function frecuenciaClientes()
    {
        try {
            $clientes = DB::table('ventas')
                ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
                ->select('clientes.nombre', 'clientes.apellidos', 'clientes.email', DB::raw('COUNT(ventas.id) as compras_realizadas'))
                ->groupBy('clientes.id', 'clientes.nombre', 'clientes.apellidos', 'clientes.email')
                ->orderByDesc('compras_realizadas')
                ->get();

            return response()->json(['clientes' => $clientes]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en frecuenciaClientes: ' . $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Nuevo: Productos Más Vendidos
    public function productosMasVendidos(Request $request)
    {
        try {
            $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
            $fechaFin = $request->input('fecha_fin', Carbon::now()->toDateString());

            $productos = DB::table('detalle_ventas')
                ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
                ->select('productos.nombre_producto', DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'))
                ->whereBetween('detalle_ventas.created_at', [$fechaInicio, $fechaFin])
                ->groupBy('productos.nombre_producto')
                ->orderByDesc('total_vendido')
                ->get();

            return response()->json(['productos' => $productos]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en productosMasVendidos: ' . $e->getMessage()
            ], 500);
        }
    }

    // 🔹 Nuevo: Reporte de Ventas General
    public function reporteVentas(Request $request)
    {
        try {
            $tipo = $request->input('tipo', 'diario'); // 'diario', 'semanal', 'mensual'
            $fecha = Carbon::now();

            switch ($tipo) {
                case 'semanal':
                    $inicio = $fecha->startOfWeek()->toDateString();
                    $fin = $fecha->endOfWeek()->toDateString();
                    break;
                case 'mensual':
                    $inicio = $fecha->startOfMonth()->toDateString();
                    $fin = $fecha->endOfMonth()->toDateString();
                    break;
                default:
                    $inicio = $fecha->toDateString();
                    $fin = $inicio;
                    break;
            }

            $ventas = Venta::whereBetween('created_at', [$inicio, $fin])
                ->selectRaw('SUM(total_pago) as total_ventas, COUNT(*) as cantidad_pedidos')
                ->first();

            return response()->json([
                'tipo' => $tipo,
                'inicio' => $inicio,
                'fin' => $fin,
                'total_ventas' => floatval($ventas->total_ventas),
                'cantidad_pedidos' => intval($ventas->cantidad_pedidos),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en reporteVentas: ' . $e->getMessage()
            ], 500);
        }
    }


    private function filtrarVentas(Request $request)
    {
        $query = Venta::query();

        // Filtro de fecha
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $fechaInicio = Carbon::parse($request->input('fecha_inicio'))->startOfDay();
            $fechaFin = Carbon::parse($request->input('fecha_fin'))->endOfDay();
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        // Filtro de categoría
        if ($request->filled('categoria_id')) {
            $query->whereHas('detalles', function ($q) use ($request) {
                $q->whereHas('producto', function ($q2) use ($request) {
                    $q2->where('id_categoria', $request->input('categoria_id'));
                });
            });
        }
        
        return $query;
    }

    // 🔹 Nuevo: Reporte General para el Dashboard
    public function reporteGeneral(Request $request)
    {
        try {
            // Validar y parsear fechas
            $request->validate([
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'categoria_id' => 'nullable|integer|exists:categorias,id'
            ]);

            // Obtener la consulta base de ventas filtradas
            $ventasQuery = $this->filtrarVentas($request);

            // Clonar la consulta para obtener los resultados
            $ventas = $ventasQuery->clone()->get();
            $ventaIds = $ventas->pluck('id');

            // Calcular estadísticas de resumen
            $totalVentas = $ventas->sum('total_pago');
            $cantidadPedidos = $ventas->count();

            // Obtener productos más vendidos (sin límite)
            $productosMasVendidos = DB::table('detalle_ventas')
                ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
                ->whereIn('detalle_ventas.venta_id', $ventaIds)
                ->select('productos.nombre_producto', DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'))
                ->groupBy('productos.nombre_producto')
                ->orderByDesc('total_vendido')
                ->get();
            
            $productoEstrella = $productosMasVendidos->first()->nombre_producto ?? 'N/A';

            // Obtener clientes frecuentes (sin límite)
            $clientesFrecuentes = DB::table('ventas')
                ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
                ->whereIn('ventas.id', $ventaIds)
                ->select('clientes.nombre', 'clientes.apellidos', 'clientes.email', DB::raw('COUNT(ventas.id) as compras_realizadas'), DB::raw('SUM(ventas.total_pago) as total_gastado'))
                ->groupBy('clientes.id', 'clientes.nombre', 'clientes.apellidos', 'clientes.email')
                ->orderByDesc('compras_realizadas')
                ->get();

            // Obtener ventas por método de pago
            $pedidoIds = $ventas->whereNotNull('pedido_id')->pluck('pedido_id');
            $ventasPorMetodo = Pedido::whereIn('id', $pedidoIds)
                ->whereNotNull('metodo_pago')
                ->where('metodo_pago', '!=', '')
                ->select('metodo_pago', DB::raw('SUM(total_pago) as total'), DB::raw('COUNT(*) as count'))
                ->groupBy('metodo_pago')
                ->get()
                ->map(function ($item) {
                    $item->metodo_pago = ucfirst($item->metodo_pago); // Capitalizar
                    return $item;
                });

            // Devolver JSON estructurado
            return response()->json([
                'summary' => [
                    'total_ventas' => $totalVentas,
                    'cantidad_pedidos' => $cantidadPedidos,
                    'producto_estrella' => $productoEstrella,
                ],
                'productos_mas_vendidos' => $productosMasVendidos,
                'clientes_frecuentes' => $clientesFrecuentes,
                'ventas_por_metodo_pago' => $ventasPorMetodo,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Datos de entrada inválidos.', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al generar el reporte general', 'message' => $e->getMessage()], 500);
        }
    }


    // 🔹 Exportar Productos Más Vendidos
    public function exportProductosMasVendidos(Request $request, $format)
    {
        $fechaInicio = Carbon::parse($request->input('fecha_inicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->input('fecha_fin'))->endOfDay();

        $ventas = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin])->get();
        $ventaIds = $ventas->pluck('id');

        $productos = DB::table('detalle_ventas')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->whereIn('detalle_ventas.venta_id', $ventaIds)
            ->select('productos.nombre_producto', DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'))
            ->groupBy('productos.nombre_producto')
            ->orderByDesc('total_vendido')
            ->get();

        $data = [
            'productos' => $productos,
            'fechaInicio' => $fechaInicio->toDateString(),
            'fechaFin' => $fechaFin->toDateString(),
        ];

        if ($format == 'xlsx') {
            // Implement Excel export using Maatwebsite\Excel
            // For simplicity, I'll return a basic array for now.
            // A full implementation would use a dedicated Export class.
            $exportData = $productos->map(function($item) {
                return (array) $item;
            })->toArray();
            array_unshift($exportData, ['Producto', 'Total Vendido']); // Add header
            return Excel::download(new \App\Exports\GenericExport($exportData), 'productos_mas_vendidos_' . $fechaInicio->toDateString() . '_' . $fechaFin->toDateString() . '.xlsx');
        } elseif ($format == 'pdf') {
            $pdf = PDF::loadView('admin.reportes.exports.productos_pdf', $data);
            return $pdf->download('productos_mas_vendidos_' . $fechaInicio->toDateString() . '_' . $fechaFin->toDateString() . '.pdf');
        }

        return back()->with('error', 'Formato de exportación no soportado.');
    }

    // 🔹 Exportar Clientes Frecuentes
    public function exportClientesFrecuentes(Request $request, $format)
    {
        $fechaInicio = Carbon::parse($request->input('fecha_inicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->input('fecha_fin'))->endOfDay();

        $ventas = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin])->get();
        $ventaIds = $ventas->pluck('id');

        $clientes = DB::table('ventas')
            ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->whereIn('ventas.id', $ventaIds)
            ->select('clientes.nombre', DB::raw('COUNT(ventas.id) as compras_realizadas'), DB::raw('SUM(ventas.total_pago) as total_gastado'))
            ->groupBy('clientes.id', 'clientes.nombre', 'clientes.apellidos', 'clientes.email')
            ->orderByDesc('compras_realizadas')
            ->get();

        $data = [
            'clientes' => $clientes,
            'fechaInicio' => $fechaInicio->toDateString(),
            'fechaFin' => $fechaFin->toDateString(),
        ];

        if ($format == 'xlsx') {
            $exportData = $clientes->map(function($item) {
                return (array) $item;
            })->toArray();
            array_unshift($exportData, ['Nombre', 'Compras Realizadas', 'Total Gastado']);
            return Excel::download(new \App\Exports\GenericExport($exportData), 'clientes_frecuentes_' . $fechaInicio->toDateString() . '_' . $fechaFin->toDateString() . '.xlsx');
        } elseif ($format == 'pdf') {
            $pdf = PDF::loadView('admin.reportes.exports.clientes_pdf', $data);
            return $pdf->download('clientes_frecuentes_' . $fechaInicio->toDateString() . '_' . $fechaFin->toDateString() . '.pdf');
        }

        return back()->with('error', 'Formato de exportación no soportado.');
    }

    // 🔹 Exportar Ventas por Método de Pago
    public function exportVentasPorMetodo(Request $request, $format)
    {
        $fechaInicio = Carbon::parse($request->input('fecha_inicio'))->startOfDay();
        $fechaFin = Carbon::parse($request->input('fecha_fin'))->endOfDay();

        $ventas = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin])->get();
        $pedidoIds = $ventas->whereNotNull('pedido_id')->pluck('pedido_id');

        $ventasPorMetodo = Pedido::whereIn('id', $pedidoIds)
            ->whereNotNull('metodo_pago')
            ->where('metodo_pago', '!=', '')
            ->select('metodo_pago', DB::raw('SUM(total_pago) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('metodo_pago')
            ->get()
            ->map(function ($item) {
                $item->metodo_pago = ucfirst($item->metodo_pago);
                return $item;
            });

        $data = [
            'metodos' => $ventasPorMetodo,
            'fechaInicio' => $fechaInicio->toDateString(),
            'fechaFin' => $fechaFin->toDateString(),
        ];

        if ($format == 'xlsx') {
            $exportData = $ventasPorMetodo->map(function($item) {
                return (array) $item;
            })->toArray();
            array_unshift($exportData, ['Método de Pago', 'Total', 'Cantidad']);
            return Excel::download(new \App\Exports\GenericExport($exportData), 'ventas_por_metodo_' . $fechaInicio->toDateString() . '_' . $fechaFin->toDateString() . '.xlsx');
        } elseif ($format == 'pdf') {
            $pdf = PDF::loadView('admin.reportes.exports.metodos_pago_pdf', $data);
            return $pdf->download('ventas_por_metodo_' . $fechaInicio->toDateString() . '_' . $fechaFin->toDateString() . '.pdf');
        }

        return back()->with('error', 'Formato de exportación no soportado.');
    }


    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}

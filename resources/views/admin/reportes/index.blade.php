@extends('adminlte::page')

@section('title', 'Dashboard de Reportes')

@section('content_header')
@stop

@section('css')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    {{-- Daterange Picker CSS --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        /* Custom Dashboard Styles */
        .custom-box {
            border-radius: .5rem;
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
            transition: all .3s ease-in-out;
            color: #fff;
        }

        .custom-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, .2);
        }

        .custom-box .inner h3 {
            font-weight: 600;
            font-size: 2rem;
        }

        .custom-box .inner p {
            font-size: 0.9rem;
        }

        .custom-box .icon {
            transition: all .3s ease-in-out;
        }

        .custom-box:hover .icon {
            transform: scale(1.1);
        }

        .custom-box-1 {
            background-color: #4f46e5;
        }

        /* Indigo-600 */
        .custom-box-2 {
            background-color: #0d9488;
        }

        /* Teal-600 */
        .custom-box-3 {
            background-color: #d97706;
        }

        /* Amber-600 */

        .card-tabs .nav-link.active {
            background-color: #eef2ff;
            /* Indigo-100 */
            border-color: #c7d2fe;
            /* Indigo-200 */
            color: #3730a3;
            /* Indigo-800 */
            font-weight: 600;
        }

        .small-box .icon-text {
            font-size: 60px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.15);
        }

        .small-box h3 {
            font-size: 2.2rem;
        }
    </style>
@stop

@section('content')
    {{-- Fila de Filtros --}}
    <div class="card card-primary mt-1">
        <div class="card-header ">
            <h3 class="card-title">Reportes</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="daterange" class="form-label"><strong>Seleccionar Rango de Fechas:</strong></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                    </div>
                    <input type="text" id="daterange" class="form-control" />
                </div>
            </div>
        </div>
    </div>

    {{-- Fila de Resumen (Small Boxes) --}}
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box custom-box custom-box-1">
                <div class="inner">
                    <h3 id="totalVentas">S/ 0.00</h3>
                    <p>Total de Ventas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cash-register"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box custom-box custom-box-2">
                <div class="inner">
                    <h3 id="cantidadPedidos">0</h3>
                    <p>Cantidad de Pedidos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="small-box custom-box custom-box-3">
                <div class="inner">
                    <h3 id="productoEstrella"
                        style="font-size: 1.8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">N/A</h3>
                    <p>Producto Estrella</p>
                </div>
                <div class="icon">
                    <i class="fas fa-trophy"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Fila de Contenido Principal --}}
    <div class="row">
        {{-- Columna Izquierda (Tablas) --}}
        <div class="col-lg-8">
            <div class="card card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-productos-link" data-toggle="pill" href="#tab-productos"
                                role="tab" aria-controls="tab-productos" aria-selected="true">Productos Más
                                Vendidos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-clientes-link" data-toggle="pill" href="#tab-clientes"
                                role="tab" aria-controls="tab-clientes" aria-selected="false">Clientes Frecuentes</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        {{-- Tab de Productos --}}
                        <div class="tab-pane fade show active" id="tab-productos" role="tabpanel"
                            aria-labelledby="tab-productos-link">
                            <div class="d-flex justify-content-end mb-3">
                                <a id="exportProductosExcel" href="#" class="btn btn-success btn-sm mr-2"><i class="fas fa-file-excel"></i> Excel</a>
                                <a id="exportProductosPdf" href="#" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</a>
                            </div>
                            <table id="tablaProductos" class="table table-bordered table-striped dt-responsive nowrap"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Total Vendido</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        {{-- Tab de Clientes --}}
                        <div class="tab-pane fade" id="tab-clientes" role="tabpanel" aria-labelledby="tab-clientes-link">
                            <div class="d-flex justify-content-end mb-3">
                                <a id="exportClientesExcel" href="#" class="btn btn-success btn-sm mr-2"><i class="fas fa-file-excel"></i> Excel</a>
                                <a id="exportClientesPdf" href="#" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</a>
                            </div>
                            <table id="tablaClientes" class="table table-bordered table-striped dt-responsive nowrap"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Compras Realizadas</th>
                                        <th>Total Gastado</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna Derecha (Gráfico) --}}
        <div class="col-lg-4">
            <div class="card card-outline">
                <div class="card-header">
                    <h3 class="card-title">Método de Pago</h3>
                    <div class="card-tools">
                        <a id="exportMetodosExcel" href="#" class="btn btn-success btn-sm mr-2"><i class="fas fa-file-excel"></i></a>
                        <a id="exportMetodosPdf" href="#" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="metodosPagoChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- jQuery, Moment.js, Daterange Picker --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- DataTables --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            let metodosPagoChart;

            // Inicializar DataTables (vacías por ahora)
            const tablaProductos = $('#tablaProductos').DataTable({
                responsive: true,
                columns: [{
                        data: 'nombre_producto'
                    },
                    {
                        data: 'total_vendido'
                    }
                ],
                order: [
                    [1, 'desc']
                ]
            });

            const tablaClientes = $('#tablaClientes').DataTable({
                responsive: true,
                columns: [{
                        data: 'nombre',
                        render: function(data, type, row) {
                            return `${row.nombre || ''}`;
                        }
                    },
                    {
                        data: 'compras_realizadas'
                    },
                    {
                        data: 'total_gastado',
                        render: function(data) {
                            return `S/ ${parseFloat(data || 0).toFixed(2)}`;
                        }
                    }
                ],
                order: [
                    [2, 'desc']
                ]
            });

            // Inicializar Daterangepicker
            const daterangeInput = $('#daterange');
            daterangeInput.daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                ranges: {
                    'Hoy': [moment(), moment()],
                    'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Últimos 7 Días': [moment().subtract(6, 'days'), moment()],
                    'Últimos 30 Días': [moment().subtract(29, 'days'), moment()],
                    'Este Mes': [moment().startOf('month'), moment().endOf('month')],
                    'Mes Pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                },
                locale: {
                    format: 'YYYY-MM-DD',
                    cancelLabel: 'Limpiar',
                    applyLabel: 'Aplicar',
                    fromLabel: 'Desde',
                    toLabel: 'Hasta',
                    customRangeLabel: 'Rango Personalizado',
                }
            });

            // Función para actualizar el dashboard
            function updateDashboard(startDate, endDate) {
                const url = `{{ route('admin.reportes.general') }}?fecha_inicio=${startDate}&fecha_fin=${endDate}`;

                // Mostrar overlay de carga
                $('body').append('<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>');

                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Error en la respuesta del servidor');
                        return response.json();
                    })
                    .then(data => {
                        // 1. Actualizar Summary Boxes
                        $('#totalVentas').text(`S/ ${parseFloat(data.summary.total_ventas || 0).toFixed(2)}`);
                        $('#cantidadPedidos').text(data.summary.cantidad_pedidos || 0);
                        $('#productoEstrella').text(data.summary.producto_estrella || 'N/A');

                        // 2. Actualizar DataTable de Productos
                        tablaProductos.clear().rows.add(data.productos_mas_vendidos || []).draw();

                        // 3. Actualizar DataTable de Clientes
                        tablaClientes.clear().rows.add(data.clientes_frecuentes || []).draw();

                        // 4. Actualizar Gráfico de Métodos de Pago
                        const chartData = {
                            labels: data.ventas_por_metodo_pago.map(item => item.metodo_pago),
                            datasets: [{
                                data: data.ventas_por_metodo_pago.map(item => item.total),
                                backgroundColor: ['#4f46e5', '#0d9488', '#d97706', '#64748b',
                                    '#db2777'
                                ], // Indigo, Teal, Amber, Slate, Pink
                            }]
                        };
                        if (metodosPagoChart) {
                            metodosPagoChart.data = chartData;
                            metodosPagoChart.update();
                        } else {
                            metodosPagoChart = new Chart(document.getElementById('metodosPagoChart'), {
                                type: 'pie',
                                data: chartData,
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                }
                            });
                        }

                        // 5. Actualizar enlaces de exportación
                        const exportParams = `fecha_inicio=${startDate}&fecha_fin=${endDate}`;
                        $('#exportProductosExcel').attr('href', `{{ route('admin.reportes.export.productos', 'xlsx') }}?${exportParams}`);
                        $('#exportProductosPdf').attr('href', `{{ route('admin.reportes.export.productos', 'pdf') }}?${exportParams}`);
                        $('#exportClientesExcel').attr('href', `{{ route('admin.reportes.export.clientes', 'xlsx') }}?${exportParams}`);
                        $('#exportClientesPdf').attr('href', `{{ route('admin.reportes.export.clientes', 'pdf') }}?${exportParams}`);
                        $('#exportMetodosExcel').attr('href', `{{ route('admin.reportes.export.metodosPago', 'xlsx') }}?${exportParams}`);
                        $('#exportMetodosPdf').attr('href', `{{ route('admin.reportes.export.metodosPago', 'pdf') }}?${exportParams}`);
                    })
                    .catch(error => {
                        console.error("Error al actualizar el dashboard:", error);
                        Swal.fire('Error', 'No se pudo cargar la información del dashboard.', 'error');
                    })
                    .finally(() => {
                        // Remover overlay de carga
                        $('.overlay').remove();
                    });
            }

            // Evento al cambiar el rango de fechas
            daterangeInput.on('apply.daterangepicker', function(ev, picker) {
                updateDashboard(picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
            });

            // Carga inicial de datos
            updateDashboard(daterangeInput.data('daterangepicker').startDate.format('YYYY-MM-DD'), daterangeInput
                .data('daterangepicker').endDate.format('YYYY-MM-DD'));
        });
    </script>
@stop

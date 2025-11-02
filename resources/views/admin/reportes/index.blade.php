@extends('adminlte::page')

@section('title', 'Reporte de Ventas')

@section('content_header')
    <h1 class="text-center">📊 Reporte de Ventas</h1>
@stop

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">📊 Generar Reportes de Ventas</h2>

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Opciones de Reporte</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="tipoReporte" class="form-label"><strong>Seleccionar tipo de reporte:</strong></label>
                    <select id="tipoReporte" class="form-control">
                        <option value="diario">📅 Diario</option>
                        <option value="semanal">📆 Semanal</option>
                        <option value="mensual">📊 Mensual</option>
                        <option value="rango">📌 Rango de Fechas</option>
                    </select>
                </div>

                <div id="filtros">
                    <div id="filtroDiario" class="form-group">
                        <label for="fechaDiaria" class="form-label">Selecciona una fecha:</label>
                        <input type="date" id="fechaDiaria" class="form-control">
                    </div>

                    <div id="filtroSemanal" class="form-group d-none">
                        <label for="inicioSemana" class="form-label">Selecciona el inicio de la semana:</label>
                        <input type="date" id="inicioSemana" class="form-control">
                    </div>

                    <div id="filtroMensual" class="form-group d-none">
                        <label for="mes" class="form-label">Selecciona un mes:</label>
                        <input type="month" id="mes" class="form-control">
                    </div>

                    <div id="filtroRango" class="row d-none">
                        <div class="col-md-6 form-group">
                            <label for="fechaInicio" class="form-label">Fecha Inicio:</label>
                            <input type="date" id="fechaInicio" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="fechaFin" class="form-label">Fecha Fin:</label>
                            <input type="date" id="fechaFin" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button id="btnGenerar" class="btn btn-primary btn-lg">🔍 Generar Reporte</button>
            </div>
        </div>


        <div id="resultado" class="mt-5 d-none">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">📌 Resultados del Reporte</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Total de Ventas:</strong> <span id="totalVentas"
                                class="float-right font-weight-bold"></span>
                        </li>
                        <li class="list-group-item" id="dailySalesAmount" style="display: none;">
                            <strong>Monto Vendido del Día:</strong> <span id="montoDiario"
                                class="float-right text-success font-weight-bold"></span>
                        </li>
                        <li class="list-group-item">
                            <strong>Producto más Vendido:</strong> <span id="productoMasVendido" class="float-right"></span>
                        </li>
                        <li class="list-group-item">
                            <strong>Clientes Frecuentes:</strong>
                            <ul id="clientesFrecuentes" class="list-group list-group-flush mt-2"></ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> {{-- Agregado para alertas más elegantes --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tipoReporte = document.getElementById("tipoReporte");
            const filtros = {
                diario: document.getElementById("filtroDiario"),
                semanal: document.getElementById("filtroSemanal"),
                mensual: document.getElementById("filtroMensual"),
                rango: document.getElementById("filtroRango"),
            };
            const resultadoDiv = document.getElementById("resultado");
            const totalVentasSpan = document.getElementById("totalVentas");
            const productoMasVendidoSpan = document.getElementById("productoMasVendido");
            const clientesFrecuentesUl = document.getElementById("clientesFrecuentes");
            const dailySalesAmountLi = document.getElementById("dailySalesAmount");
            const montoDiarioSpan = document.getElementById("montoDiario");


            // 🔹 Ocultar y mostrar filtros dinámicamente
            tipoReporte.addEventListener("change", function() {
                Object.values(filtros).forEach(filtro => filtro.classList.add("d-none"));
                filtros[this.value].classList.remove("d-none");
                resultadoDiv.classList.add("d-none"); // Ocultar resultados al cambiar el tipo de reporte
                dailySalesAmountLi.style.display = 'none'; // Ocultar monto diario por defecto
            });

            // Establecer la fecha actual por defecto para el reporte diario
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0'); // Meses son 0-index
            const day = String(today.getDate()).padStart(2, '0');
            document.getElementById("fechaDiaria").value = `${year}-${month}-${day}`;

            document.getElementById("btnGenerar").addEventListener("click", function() {
                const tipo = tipoReporte.value;
                let url = `/admin/reportes/${tipo}`;
                let params = {};

                if (tipo === "diario") {
                    params.fecha = document.getElementById("fechaDiaria").value;
                    if (!params.fecha) {
                        Swal.fire('Error', 'Por favor, selecciona una fecha para el reporte diario.',
                            'error');
                        return;
                    }
                } else if (tipo === "semanal") {
                    params.inicio_semana = document.getElementById("inicioSemana").value;
                    if (!params.inicio_semana) {
                        Swal.fire('Error', 'Por favor, selecciona la fecha de inicio de la semana.',
                            'error');
                        return;
                    }
                } else if (tipo === "mensual") {
                    const mesAnio = document.getElementById("mes").value.split("-");
                    if (mesAnio.length !== 2) {
                        Swal.fire('Error', 'Por favor, selecciona un mes y año válidos.', 'error');
                        return;
                    }
                    params.mes = mesAnio[1];
                    params.anio = mesAnio[0];
                } else if (tipo === "rango") {
                    params.fecha_inicio = document.getElementById("fechaInicio").value;
                    params.fecha_fin = document.getElementById("fechaFin").value;
                    if (!params.fecha_inicio || !params.fecha_fin) {
                        Swal.fire('Error', 'Por favor, selecciona ambas fechas para el rango.', 'error');
                        return;
                    }
                    if (new Date(params.fecha_inicio) > new Date(params.fecha_fin)) {
                        Swal.fire('Error', 'La fecha de inicio no puede ser posterior a la fecha de fin.',
                            'error');
                        return;
                    }
                }

                fetch(url + "?" + new URLSearchParams(params))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => mostrarResultados(data, tipo))
                    .catch(error => {
                        console.error("Error en el reporte:", error);
                        Swal.fire('Error',
                            'Hubo un problema al generar el reporte. Inténtalo de nuevo.', 'error');
                    });
            });

            function mostrarResultados(data, tipo) {
                resultadoDiv.classList.remove("d-none");

                totalVentasSpan.textContent = `$${parseFloat(data.total_ventas || 0).toFixed(2)}`;
                productoMasVendidoSpan.textContent = data.producto_mas_vendido || 'N/A';

                // Mostrar monto vendido por día solo en el reporte diario
                if (tipo === "diario") {
                    dailySalesAmountLi.style.display = 'block';
                    montoDiarioSpan.textContent = `$${parseFloat(data.total_ventas || 0).toFixed(2)}`;
                } else {
                    dailySalesAmountLi.style.display = 'none';
                }

                let clientesHTML = "";
                if (Array.isArray(data.clientes_frecuentes) && data.clientes_frecuentes.length > 0) {
                    data.clientes_frecuentes.forEach(cliente => {
                        clientesHTML += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                            ${cliente.nombre}
                                            <span class="badge badge-primary badge-pill">${cliente.total_compras} compras</span>
                                         </li>`;
                    });
                } else {
                    clientesHTML = `<li class="list-group-item">No hay clientes frecuentes para este período.</li>`;
                }
                clientesFrecuentesUl.innerHTML = clientesHTML;
            }

            // Inicializar la vista de filtros al cargar la página
            tipoReporte.dispatchEvent(new Event('change'));
        });
    </script>
@stop

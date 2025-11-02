@extends('adminlte::page')

@section('title', 'Nueva Venta')

@section('content_header')
    <h1>Nueva Venta</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">

        </div>
    </div>
@stop

@section('js')

    let productosAgregados = [];
    let productoSeleccionado = null;
    let totalVenta = 0;

    // Filtrar productos por categoría
    function filtrarProductos() {
    const categoriaSeleccionada = document.querySelector('#categoria').value.toLowerCase();
    const busqueda = document.querySelector('#buscar_producto').value.toLowerCase();
    const filas = document.querySelectorAll('#lista-productos tr');

    filas.forEach(fila => {
    const nombreProducto = fila.dataset.nombre.toLowerCase();
    const categoriaProducto = fila.dataset.categoria;
    const precioProducto = fila.dataset.precio.toLowerCase();
    const mostrarPorCategoria = categoriaSeleccionada === '' || categoriaProducto ===
    categoriaSeleccionada;
    const mostrarPorBusqueda = nombreProducto.includes(busqueda) || precioProducto.includes(busqueda);

    if (mostrarPorCategoria && mostrarPorBusqueda) {
    fila.style.display = '';
    } else {
    fila.style.display = 'none';
    }
    });
    }

    // Filtrar productos por selección de categoría o búsqueda de texto
    document.querySelector('#categoria').addEventListener('change', filtrarProductos);
    document.querySelector('#buscar_producto').addEventListener('input', filtrarProductos);

    // Seleccionar un producto para ver los detalles
    document.querySelectorAll('#lista-productos tr').forEach(row => {
    row.addEventListener('click', function() {
    const id = this.dataset.id;
    const nombre = this.dataset.nombre;
    const precio = parseFloat(this.dataset.precio);
    const imagen = this.dataset.imagen;

    // Mostrar los detalles
    document.querySelector('#detalle-producto').innerHTML = `
    <p><strong>Nombre:</strong> ${nombre}</p>
    <p><strong>Precio:</strong> $${precio.toFixed(2)}</p>
    ${imagen ? `<img src="${imagen}" alt="Imagen de ${nombre}" style="max-width: 40%; height: auto;">` : '<p class="text-muted">
        Sin imagen disponible</p>'}
    `;

    productoSeleccionado = {
    id,
    nombre,
    precio,
    cantidad: 1
    };

    document.querySelector('#agregar-producto').style.display = 'block';
    });
    });

    // Agregar producto a la tabla de productos agregados
    document.querySelector('#agregar-producto').addEventListener('click', function() {
    if (!productoSeleccionado) return;

    // Agregar el producto seleccionado
    productosAgregados.push(productoSeleccionado);
    productoSeleccionado = null;
    document.querySelector('#detalle-producto').innerHTML =
    `<p class="text-muted">Seleccione un producto para ver los detalles aquí.</p>`;
    this.style.display = 'none';
    actualizarTabla();
    });

    // Actualizar tabla de productos agregados
    function actualizarTabla() {
    const tbody = document.querySelector('#productos-agregados');
    tbody.innerHTML = '';
    totalVenta = 0;

    productosAgregados.forEach((producto, index) => {
    const total = producto.precio * producto.cantidad;
    totalVenta += total;

    tbody.innerHTML += `
    <tr>
        <td>${producto.nombre}</td>
        <td>$${producto.precio.toFixed(2)}</td>
        <td>
            <input type="number" min="1" value="${producto.cantidad}" class="form-control cantidad-producto"
                data-index="${index}">
        </td>
        <td>$${total.toFixed(2)}</td>
        <td>
            <button class="btn btn-danger btn-sm eliminar-producto" data-index="${index}">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
    `;
    });

    if (productosAgregados.length === 0) {
    tbody.innerHTML = `<tr>
        <td colspan="5" class="text-center">No hay productos agregados.</td>
    </tr>`;
    }

    document.querySelector('#total-venta').textContent = totalVenta.toFixed(2);
    asignarEventos();
    }

    // Eliminar producto y ajustar cantidades
    function asignarEventos() {
    document.querySelectorAll('.eliminar-producto').forEach(button => {
    button.addEventListener('click', function() {
    const index = this.dataset.index;
    productosAgregados.splice(index, 1);
    actualizarTabla();
    });
    });

    document.querySelectorAll('.cantidad-producto').forEach(input => {
    input.addEventListener('input', function() {
    const index = this.dataset.index;
    productosAgregados[index].cantidad = parseInt(this.value);
    actualizarTabla();
    });
    });
    }

    // Funcionalidad para guardar la venta
    document.querySelector('#guardar-venta').addEventListener('click', function() {
    if (productosAgregados.length === 0) {
    alert('Debe agregar al menos un producto.');
    return;
    }

    // Asegurarse de obtener el ID del cliente y no su nombre
    const clienteInput = document.querySelector('#buscar_cliente');
    const clienteId = clienteInput ? clienteInput.getAttribute('data-id') : null;

    if (!clienteId) {
    alert("Debe seleccionar un cliente válido.");
    return;
    }

    const total = totalVenta;

    const datosVenta = {
    cliente_id: clienteId, // Ahora es un ID, no un nombre
    total: total,
    productos: productosAgregados
    };

    console.log("Datos que se enviarán:", datosVenta); // Ver en la consola del navegador

    fetch("{{ route('admin.ventas.store') }}", {
    method: "POST",
    headers: {
    "Content-Type": "application/json",
    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
    'content')
    },
    body: JSON.stringify(datosVenta)
    })
    .then(response => {
    if (!response.ok) {
    throw new Error("Error en la respuesta del servidor.");
    }
    return response.json();
    })
    .then(data => {
    console.log("Respuesta del servidor:", data);
    if (data.success) {
    alert("Venta guardada exitosamente.");
    window.location.href = "{{ route('admin.ventas.index') }}";
    } else {
    alert("Error: " + data.message);
    }
    })
    .catch(error => {
    console.error("Error en la solicitud:", error);
    alert("Error en la conexión con el servidor.");
    });
    });
    </script> --}}
@stop

<div>
    <!-- Input de búsqueda -->
    <div class="row">
        <div class="col-md-6">
            <div class="card-header">
                <input wire:model.live="search" type="text" class="form-control" id="buscar_cliente"
                    data-id="{{ $cliente->id ?? '' }}" placeholder="Nombre o correo del usuario">
            </div>
            <small class="form-text text-muted">
                Ingrese un cliente existente o deje el campo vacío para usar el cliente por defecto.
            </small>
        </div>
    </div>

    <!-- Mostrar solo un cliente -->
    @if ($cliente)
        <div>
            <table class="table">

                <tbody>
                    <tr>
                        <td>{{ $cliente->nombre }}</td>
                        <td>{{ $cliente->email }}</td>
                        <td>
                            <button class="btn btn-primary" wire:click="setCliente('{{ $cliente->nombre }}')">
                                Seleccionar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @else
        <p>No se encontraron clientes.</p>
    @endif
</div>

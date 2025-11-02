<div>
    <div class="card">
        <!-- Campo de búsqueda -->
        <div class="card-header">
            <input wire:model.live="search" type="text" class="form-control" placeholder="Nombre o correo del usuario">
        </div>

        @if ($users->count())
            <div class="card-body" style="overflow: auto;">
                <table class="table table-bordered">
                    <thead class="text-center">
                        <tr>
                            <th>Codigo</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td width="10px">
                                    <a class="btn" href="{{ route('admin.users.edit', $user) }}"><i
                                            class="fas fa-edit fa-lg" style="color: blue;"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $users->links() }}
            </div>
        @else
            <div class="card-body">
                <strong>No se encontro lo que buscas</strong>
            </div>
        @endif

    </div>
    <div class="floating-btn-container">
        <!-- 🔙 Botón para Regresar -->
        <a href="{{ route('admin.home') }}" class="floating-btn back-btn" title="Regresar">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
</div>

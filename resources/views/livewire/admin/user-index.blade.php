@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('user-updated', (event) => {
                Swal.fire({
                    title: event.title,
                    text: event.message,
                    icon: event.type,
                    confirmButtonText: 'OK'
                });
            });
        });
    </script>
@endpush

<div>
    <div class="card card-tabs">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                <li class="nav-item">
                    <a wire:click="setTab('employees')" class="nav-link {{ $tab === 'employees' ? 'active' : '' }}" id="tab-employees-link" data-toggle="pill" href="#tab-employees" role="tab" aria-controls="tab-employees" aria-selected="{{ $tab === 'employees' ? 'true' : 'false' }}">Empleados</a>
                </li>
                <li class="nav-item">
                    <a wire:click="setTab('clients')" class="nav-link {{ $tab === 'clients' ? 'active' : '' }}" id="tab-clients-link" data-toggle="pill" href="#tab-clients" role="tab" aria-controls="tab-clients" aria-selected="{{ $tab === 'clients' ? 'true' : 'false' }}">Clientes</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="flex-grow-1 mr-2">
                    <input wire:model.live="search" type="text" class="form-control" placeholder="Buscar por nombre o correo...">
                </div>
                @if ($tab === 'employees')
                    <div class="d-flex">
                        <select wire:model.live="roleFilter" class="form-control mr-2">
                            <option value="">-- Todos los Roles --</option>
                            @foreach ($roles as $role)
                                @if ($role->name !== 'cliente')
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary" title="Crear Rol"><i class="fas fa-plus"></i></a>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary ml-2" title="Ver Roles"><i class="fas fa-user-tag"></i></a>
                    </div>
                @endif
            </div>

            @if ($users->count())
                <div style="overflow: auto;">
                    <table class="table table-striped table-hover table-bordered">
                        <thead class="thead-light text-center">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Estado</th>
                                <th colspan="2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="text-center">{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-center">
                                        @forelse ($user->roles as $role)
                                            <span class="badge badge-primary">{{ $role->name }}</span>
                                        @empty
                                            <span class="badge badge-secondary">Sin Rol</span>
                                        @endforelse
                                    </td>
                                    <td class="text-center">
                                        @if ($user->is_active)
                                            <span class="badge badge-success">Activo</span>
                                        @else
                                            <span class="badge badge-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td width="10px">
                                        <a class="btn btn-sm btn-primary" title="Asignar Rol" href="{{ route('admin.users.edit', $user) }}"><i class="fas fa-edit"></i></a>
                                    </td>
                                    <td width="10px">
                                        <button wire:click="toggleStatus({{ $user->id }})" class="btn btn-sm {{ $user->is_active ? 'btn-danger' : 'btn-success' }}" title="{{ $user->is_active ? 'Desactivar' : 'Activar' }}">
                                            <i class="fas fa-power-off"></i>
                                        </button>
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
                    <strong>No se encontraron usuarios con esos criterios.</strong>
                </div>
            @endif
        </div>
    </div>
    <div class="floating-btn-container">
        <a href="{{ route('admin.home') }}" class="floating-btn back-btn" title="Regresar">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
</div>

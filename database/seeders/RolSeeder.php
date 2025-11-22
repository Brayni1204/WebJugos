<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $AdminRol = Role::firstOrCreate(['name' => 'admin']);
        $VendedorRol = Role::firstOrCreate(['name' => 'Vendedor']);

        Permission::firstOrCreate(['name' => 'admin.home'])->syncRoles([$AdminRol, $VendedorRol]);

        Permission::firstOrCreate(['name' => 'admin.users.index'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.users.edit'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.users.update'])->syncRoles([$AdminRol]);

        Permission::firstOrCreate(['name' => 'admin.categoria.index'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.categoria.create'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.categoria.edit'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.categoria.destroy'])->syncRoles([$AdminRol]);

        Permission::firstOrCreate(['name' => 'admin.ventas.index'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.ventas.create'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.ventas.edit'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.ventas.destroy'])->syncRoles([$AdminRol]);

        Permission::firstOrCreate(['name' => 'admin.productos.index'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.productos.create'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.productos.edit'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.productos.destroy'])->syncRoles([$AdminRol]);

        Permission::firstOrCreate(['name' => 'admin.menubar.index'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.menubar.create'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.menubar.edit'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.menubar.destroy'])->syncRoles([$AdminRol]);

        Permission::firstOrCreate(['name' => 'admin.paginas.index'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.paginas.create'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.paginas.edit'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.paginas.destroy'])->syncRoles([$AdminRol]);

        Permission::firstOrCreate(['name' => 'admin.subtitulos.index'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.subtitulos.create'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.subtitulos.edit'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.subtitulos.destroy'])->syncRoles([$AdminRol]);

        Permission::firstOrCreate(['name' => 'admin.parrafos.create'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.parrafos.edit'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.parrafos.destroy'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.parrafos.index'])->syncRoles([$AdminRol]);

        Permission::firstOrCreate(['name' => 'admin.reportes.index'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.reportes.create'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.reportes.edit'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.reportes.destroy'])->syncRoles([$AdminRol]);

        Permission::firstOrCreate(['name' => 'admin.registros.index'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.registros.create'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.registros.edit'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.registros.destroy'])->syncRoles([$AdminRol]);

        Permission::firstOrCreate(['name' => 'admin.mesas.index'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.mesas.create'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.mesas.edit'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.mesas.destroy'])->syncRoles([$AdminRol]);

        Permission::firstOrCreate(['name' => 'admin.empresa.index'])->syncRoles([$AdminRol]);

        Permission::firstOrCreate(['name' => 'admin.roles.index'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.roles.create'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.roles.edit'])->syncRoles([$AdminRol]);
        Permission::firstOrCreate(['name' => 'admin.roles.destroy'])->syncRoles([$AdminRol]);

        Permission::firstOrCreate(['name' => 'admin.clients.index'])->syncRoles([$AdminRol]);

        Permission::firstOrCreate(['name' => 'admin.pedidos.index'])->syncRoles([$AdminRol,  $VendedorRol]);
        Permission::firstOrCreate(['name' => 'admin.pedidos.create'])->syncRoles([$AdminRol,  $VendedorRol]);
        Permission::firstOrCreate(['name' => 'admin.pedidos.edit'])->syncRoles([$AdminRol,  $VendedorRol]);
        Permission::firstOrCreate(['name' => 'admin.pedidos.destroy'])->syncRoles([$AdminRol,  $VendedorRol]);

        Permission::firstOrCreate(['name' => 'admin.producto.index'])->syncRoles([$AdminRol, $VendedorRol]);
        Permission::firstOrCreate(['name' => 'admin.producto.create'])->syncRoles([$AdminRol, $VendedorRol]);
        Permission::firstOrCreate(['name' => 'admin.producto.edit'])->syncRoles([$AdminRol, $VendedorRol]);
        Permission::firstOrCreate(['name' => 'admin.producto.destroy'])->syncRoles([$AdminRol, $VendedorRol]);
    }
}



/* Ya regreso voy a comer Xd */

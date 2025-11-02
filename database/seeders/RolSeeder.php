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
        $AdminRol = Role::create(['name' => 'Admin']);
        $VendedorRol = Role::create(['name' => 'Vendedor']);


        Permission::create(['name' => 'admin.home'])->syncRoles([$AdminRol, $VendedorRol]);

        Permission::create(['name' => 'admin.users.index'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.users.edit'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.users.update'])->syncRoles([$AdminRol]);


        Permission::create(['name' => 'admin.categoria.index'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.categoria.create'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.categoria.edit'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.categoria.destroy'])->syncRoles([$AdminRol]);


        Permission::create(['name' => 'admin.ventas.index'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.ventas.create'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.ventas.edit'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.ventas.destroy'])->syncRoles([$AdminRol]);


        Permission::create(['name' => 'admin.productos.index'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.productos.create'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.productos.edit'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.productos.destroy'])->syncRoles([$AdminRol]);


        Permission::create(['name' => 'admin.menubar.index'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.menubar.create'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.menubar.edit'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.menubar.destroy'])->syncRoles([$AdminRol]);


        Permission::create(['name' => 'admin.paginas.index'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.paginas.create'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.paginas.edit'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.paginas.destroy'])->syncRoles([$AdminRol]);


        Permission::create(['name' => 'admin.subtitulos.index'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.subtitulos.create'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.subtitulos.edit'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.subtitulos.destroy'])->syncRoles([$AdminRol]);

        Permission::create(['name' => 'admin.parrafos.create'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.parrafos.edit'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.parrafos.destroy'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.parrafos.index'])->syncRoles([$AdminRol]);


        Permission::create(['name' => 'admin.reportes.index'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.reportes.create'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.reportes.edit'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.reportes.destroy'])->syncRoles([$AdminRol]);

        Permission::create(['name' => 'admin.registros.index'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.registros.create'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.registros.edit'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.registros.destroy'])->syncRoles([$AdminRol]);


        Permission::create(['name' => 'admin.mesas.index'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.mesas.create'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.mesas.edit'])->syncRoles([$AdminRol]);
        Permission::create(['name' => 'admin.mesas.destroy'])->syncRoles([$AdminRol]);


        Permission::create(['name' => 'admin.empresa.index'])->syncRoles([$AdminRol]);


        Permission::create(['name' => 'admin.pedidos.index'])->syncRoles([$AdminRol,  $VendedorRol]);
        Permission::create(['name' => 'admin.pedidos.create'])->syncRoles([$AdminRol,  $VendedorRol]);
        Permission::create(['name' => 'admin.pedidos.edit'])->syncRoles([$AdminRol,  $VendedorRol]);
        Permission::create(['name' => 'admin.pedidos.destroy'])->syncRoles([$AdminRol,  $VendedorRol]);

        Permission::create(['name' => 'admin.producto.index'])->syncRoles([$AdminRol, $VendedorRol]);
        Permission::create(['name' => 'admin.producto.create'])->syncRoles([$AdminRol, $VendedorRol]);
        Permission::create(['name' => 'admin.producto.edit'])->syncRoles([$AdminRol, $VendedorRol]);
        Permission::create(['name' => 'admin.producto.destroy'])->syncRoles([$AdminRol, $VendedorRol]);
    }
}



/* Ya regreso voy a comer Xd */

<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Cliente;
use App\Models\User;

class CreateClienteForNewUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        // Get the newly registered user
        $user = $event->user;

        // Find a cliente with the user's email or prepare to create a new one
        $cliente = Cliente::firstOrNew(['email' => $user->email]);

        // If the cliente is new (doesn't exist in DB yet), populate the name
        if (!$cliente->exists) {
            $cliente->nombre = $user->name;
        }

        // Link the cliente to the new user account
        $cliente->user_id = $user->id;
        
        // Save the new or updated cliente record
        $cliente->save();

        // Assign the 'Cliente' role to the user
        // Make sure you have a 'Cliente' role created in your database
        // You can create it using Tinker: Spatie\Permission\Models\Role::create(['name' => 'Cliente']);
        $user->assignRole('Cliente');
    }
}
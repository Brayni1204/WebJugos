<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\MenuBar;
use Illuminate\Http\Request;

class MenuBarController extends Controller
{
    public function updateFavicon(Request $request, $id)
    {
        $menuBar = Empresa::findOrFail($id);

        if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            $path = $file->store('favicons', 'public'); // Guarda en storage/app/public/favicons

            $menuBar->favicon_url = $path;
            $menuBar->save();
        }

        return redirect()->back()->with('success', 'Favicon actualizado correctamente.');
    }
}

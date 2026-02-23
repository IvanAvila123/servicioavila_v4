<?php

use App\Models\Servicio;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/servicio/{id}/pdf', function ($id) {
    $servicio = Servicio::with(['cliente', 'detalles.inventario'])->findOrFail($id);
    
    // Convertimos la vista a un archivo PDF
    $pdf = Pdf::loadView('pdf.orden_servicio', compact('servicio'));
    
    // stream() lo abre en el navegador listo para imprimir
    return $pdf->stream('Orden-Servicio-'.$id.'.pdf'); 
})->name('servicio.pdf');

<?php

namespace App\Http\Controllers;

use App\Models\Traslado;
use App\Models\TrasladoSecundario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    protected string $view = 'formato';

    public function generarPDF($id)
    {
        // Obtener los datos del traslado
        $traslado = TrasladoSecundario::findOrFail($id);

        // Generar el PDF
        $pdf = Pdf::loadView('formato', compact('traslado'));
        return $pdf->stream('traslado.pdf');
    }
}

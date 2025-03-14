<?php

namespace App\Http\Controllers;

use App\Models\TrasladoSecundarioHistorico;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
class HistoricoController extends Controller
{
    protected string $view = 'formato';

    public function generarPDF($id)
    {
        // Obtener los datos del traslado
        $traslado = TrasladoSecundarioHistorico::findOrFail($id);

        // Generar el PDF
        $pdf = Pdf::loadView('formato', compact('traslado'));
        return $pdf->stream('traslado.pdf');
    }}

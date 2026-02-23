<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Servicio #{{ str_pad($servicio->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        /* ESTILOS GENERALES COMPATIBLES CON DOMPDF */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #374151;
            margin: 0;
            padding: 0;
        }
        
        /* UTILIDADES SIMULANDO TAILWIND */
        .w-full { width: 100%; border-collapse: collapse; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .mb-4 { margin-bottom: 1rem; }
        .p-2 { padding: 0.5rem; }
        
        /* COLORES */
        .bg-blue { background-color: #2563eb; color: white; }
        .bg-gray { background-color: #f3f4f6; }
        .text-blue { color: #1e40af; }
        .text-gray { color: #6b7280; }
        .text-red { color: #dc2626; }
        
        /* ESTRUCTURA DE TABLAS */
        td, th { vertical-align: top; padding: 5px; }
        
        /* BORDES Y CAJAS */
        .box {
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .box-header {
            padding: 8px 15px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
        
        /* TABLA DE ITEMS */
        .table-items thead th {
            background-color: #ea580c; /* Orange-600 */
            color: white;
            text-align: center;
            padding: 8px;
        }
        .table-items tbody td {
            border-bottom: 1px solid #eee;
            padding: 8px;
        }
        .row-even { background-color: #fff7ed; } /* Orange-50 */

        /* ESTADOS (Badges) */
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            display: inline-block;
        }
    </style>
</head>
<body>

    <table class="w-full mb-4">
        <tr>
            <td width="60%">
                <h1 class="font-size: 30px; margin: 0; color: #0000">SERVICIO AVILA</h1>
                @if($servicio->estado === 'Completado')
                <h2 style="font-size: 24px; margin: 0; color: #15803d;">ORDEN DE SERVICIO</h2>
                <p style="color: #6b7280; margin: 5px 0;">Comprobante de Pago y Garantía</p>
            @else
                <h2 style="font-size: 24px; margin: 0; color: #1e3a8a;">ORDEN DE RECEPCIÓN</h2>
                <p style="color: #6b7280; margin: 5px 0;">Presupuesto y Diagnóstico Inicial</p>
            @endif
            </td>
            <td width="40%" class="text-right">
                <div style="background-color: #eff6ff; border: 1px solid #2563eb; padding: 10px; border-radius: 8px;">
                    <span style="display: block; font-size: 10px; color: #1e40af;">FOLIO</span>
                    <strong style="font-size: 18px; color: #1e3a8a;">#{{ str_pad($servicio->id, 5, '0', STR_PAD_LEFT) }}</strong>
                    <br>
                    <span style="font-size: 11px;">Fecha: {{ $servicio->created_at->format('d/m/Y') }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="w-full mb-4" cellpadding="0" cellspacing="0">
        <tr>
            <td width="33%">
                <div style="border-left: 4px solid 
                    @if($servicio->estado === 'terminado') #22c55e 
                    @elseif($servicio->estado === 'en proceso') #3b82f6 
                    @else #eab308 @endif; 
                    background-color: #f9fafb; padding: 10px;">
                    <span class="text-gray" style="font-size: 10px;">ESTADO ACTUAL</span><br>
                    <strong class="uppercase" style="color: 
                        @if($servicio->estado === 'terminado') #15803d 
                        @elseif($servicio->estado === 'en proceso') #1d4ed8 
                        @else #a16207 @endif;">
                        {{ $servicio->estado }}
                    </strong>
                </div>
            </td>
            <td width="33%">
                <div style="border-left: 4px solid 
                    @if(in_array($servicio->prioridad, ['alta', 'urgente'])) #ef4444 
                    @elseif($servicio->prioridad === 'media') #f97316 
                    @else #22c55e @endif; 
                    background-color: #f9fafb; padding: 10px;">
                    <span class="text-gray" style="font-size: 10px;">PRIORIDAD</span><br>
                    <strong class="uppercase" style="color: 
                        @if(in_array($servicio->prioridad, ['alta', 'urgente'])) #b91c1c 
                        @elseif($servicio->prioridad === 'media') #c2410c 
                        @else #15803d @endif;">
                        {{ $servicio->prioridad }}
                    </strong>
                </div>
            </td>
            <td width="33%">
                <div style="border-left: 4px solid #a855f7; background-color: #f9fafb; padding: 10px;">
                    <span class="text-gray" style="font-size: 10px;">TIPO SERVICIO</span><br>
                    <strong style="color: #7e22ce;" class="uppercase">{{ $servicio->tipo }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <div class="box">
        <div class="box-header bg-blue">Información del Cliente</div>
        <table class="w-full">
            <tr>
                <td width="50%">
                    <span class="text-gray" style="font-size: 10px;">CLIENTE</span><br>
                    <strong>{{ $servicio->cliente->nombre }}</strong>
                </td>
                <td width="25%">
                    <span class="text-gray" style="font-size: 10px;">TELÉFONO</span><br>
                    {{ $servicio->cliente->telefono ?? 'N/A' }}
                </td>
                <td width="15%">
                    <span class="text-gray" style="font-size: 10px;">EMAIL</span><br>
                    {{ $servicio->cliente->email ?? 'N/A' }}
                </td>

                <td width="25%">
                    <span class="text-gray" style="font-size: 10px;">RFC</span><br>
                    {{ $servicio->cliente->rfc ?? 'N/A' }}
                </td>
            </tr>
            <tr>
                <td colspan="3" style="border-top: 1px solid #eee; padding-top: 8px;">
                    <span class="text-gray" style="font-size: 10px;">DIRECCIÓN</span><br>
                    {{ $servicio->cliente->direccion ?? 'No registrada' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="box" style="border-color: #22c55e;">
        <div class="box-header" style="background-color: #22c55e; color: white;">Datos del Servicio</div>
        <table class="w-full">
            <tr>
                <td width="60%">
                    <span class="text-gray" style="font-size: 10px;">EQUIPO / SERVICIO</span><br>
                    <strong style="font-size: 14px;">{{ $servicio->nombre }}</strong>
                </td>
                <td width="10%">
                    <span class="text-gray" style="font-size: 10px;">INICIO</span><br>
                    {{ optional($servicio->fecha_inicio)->format('d/m/Y') ?? 'N/A' }}
                </td>

                <td width="15%">
                    <span class="text-gray" style="font-size: 10px;">TERMINO</span><br>
                    {{ optional($servicio->fecha_fin)->format('d/m/Y') ?? 'N/A' }}
                </td>

                <td width="20%">
                    <span class="text-gray" style="font-size: 10px;">PROX MANTENIMIENTO.</span><br>
                    {{ optional($servicio->proximo_mantenimiento)->format('d/m/Y') ?? 'Pendiente' }}
                </td>
            </tr>
        </table>
        @if($servicio->descripcion)
        <div style="padding: 10px; border-top: 1px solid #eee; background-color: #f0fdf4;">
            <strong style="color: #15803d; font-size: 10px;">DIAGNÓSTICO / FALLA REPORTADA:</strong>
            <p style="margin: 5px 0 0 0; font-style: italic;">"{{ $servicio->descripcion }}"</p>
        </div>
        @endif
    </div>

    @if($servicio->detalles->count() > 0)
    <div class="box" style="border-color: #ea580c;">
        <div class="box-header" style="background-color: #ea580c; color: white;">Refacciones y Costos</div>
        <table class="w-full table-items" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="10%">CANT.</th>
                    <th width="50%" class="text-left">DESCRIPCIÓN</th>
                    <th width="20%" class="text-right">P. UNIT.</th>
                    <th width="20%" class="text-right">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($servicio->detalles as $index => $detalle)
                <tr class="{{ $index % 8 === 0 ? 'row-even' : '' }}">
                    <td class="text-center"><strong>{{ $detalle->cantidad }}</strong></td>
                    <td>
                        {{ $detalle->inventario->refaccion ?? 'Concepto general' }}
                        @if($detalle->descripcion)
                            <br><small style="color: #666;">({{ $detalle->descripcion }})</small>
                        @endif
                        @if($detalle->num_serie)
                            <br><small style="color: #666;">Num de serie: ({{ $detalle->num_serie }})</small>
                        @endif
                        @if($detalle->num_producto)
                            <br><small style="color: #666;">Num de producto: ({{ $detalle->num_producto }})</small>
                        @endif
                        @if($detalle->modelo)
                            <br><small style="color: #666;">Modelo: {{ $detalle->modelo }}</small>
                        @endif
                    </td>
                    <td class="text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="text-right"><strong>${{ number_format($detalle->subtotal, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <table class="w-full" style="margin-top: 20px;">
        <tr>
            <td width="60%" style="padding-right: 20px;">
                @if($servicio->notas)
                <div style="background-color: #fefce8; border: 1px solid #eab308; padding: 10px; border-radius: 5px;">
                    <strong style="color: #854d0e;">NOTAS IMPORTANTES:</strong>
                    <p style="font-size: 10px; margin-top: 5px;">
                        {!! nl2br(e($servicio->notas)) !!}
                    </p>
                </div>
                @endif
                
                <div style="margin-top: 40px; font-size: 10px; color: #666;">
                    <p>Firma de conformidad del cliente:</p>
                    <br><br>
                    <div style="border-bottom: 1px solid #000; width: 80%;"></div>
                    <p>{{ $servicio->cliente->nombre }}</p>
                </div>
            </td>

           <td width="40%" style="vertical-align: bottom;">
    <table class="w-full" style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
        <tr>
            <td class="text-right" style="padding: 5px 15px; background-color: white;">
                <span style="font-size: 10px; color: #666;">Total Refacciones:</span>
                <strong style="font-size: 12px; color: #374151;">
                    ${{ number_format($servicio->detalles->sum('subtotal'), 2) }}
                </strong>
            </td>
        </tr>
        
        <tr>
            <td class="text-right" style="padding: 5px 15px; background-color: white; border-bottom: 1px solid #eee;">
                <span style="font-size: 10px; color: #666;">Mano de Obra:</span>
                <strong style="font-size: 12px; color: #374151;">
                    ${{ number_format($servicio->costo_estimado, 2) }}
                </strong>
            </td>
        </tr>

        <tr>
            <td class="text-right" style="padding: 15px; background-color: #faf5ff;">
                <span style="display: block; font-size: 12px; color: #6b21a8;">TOTAL A PAGAR</span>
                <strong style="font-size: 24px; color: #581c87;">
                    {{-- Sumamos Refacciones + Mano de Obra --}}
                    ${{ number_format($servicio->detalles->sum('subtotal') + $servicio->costo_estimado, 2) }}
                </strong>
                <span style="display: block; font-size: 10px; color: #666;">Moneda Nacional (MXN)</span>
            </td>
        </tr>
                </table>
                
                <div style="margin-top: 30px; text-align: right; font-size: 10px; color: #666;">
                    <p>Atendido por:</p>
                    <strong>JESUS LEOPOLDO AVILA DIAZ</strong>
                </div>
            </td>
        </tr>
    </table>

    <div style="position: fixed; bottom: 0; width: 100%; text-align: center; border-top: 1px solid #eee; padding-top: 10px; font-size: 9px; color: #999;">
        Documento generado el {{ now()->format('d/m/Y H:i') }} | Orden #{{ str_pad($servicio->id, 5, '0', STR_PAD_LEFT) }}
    </div>

</body>
</html>
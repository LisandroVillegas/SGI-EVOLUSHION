@extends('adminlte::page')



@section('content_header')

<nav aria-label="breadcrumb" style="font-size: 18pt">

  <ol class="breadcrumb">

    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Inicio</a></li>

    <li class="breadcrumb-item"><a href="{{ url('/admin/turnos') }}">Turnos</a></li>

    <li class="breadcrumb-item active" aria-current="page">Detalle del Turno</li>

  </ol>

</nav>

<hr>

@stop



@section('content')

<div class="row">

    <div class="col-md-12">

        <div class="card card-outline card-info">

            <div class="card-header">

                <h3 class="card-title">Detalle del Turno #{{ $turno->id }}</h3>

                <div class="card-tools">

                    @php

                        // Calculamos la diferencia real para que la etiqueta superior concuerde perfectamente con el arqueo

                        $descuadreVisual = $diferenciaCalculada ?? ($turno->total_descuadre_dinero ?? 0);

                    @endphp



                    @if($turno->estado == 'abierto')

                        <span class="badge badge-primary p-2" style="font-size: 11pt">Abierto</span>

                    @elseif(abs($descuadreVisual) < 0.01)

                        <span class="badge badge-success p-2" style="font-size: 11pt">Cerrado OK</span>

                    @else

                        <span class="badge badge-warning p-2" style="font-size: 11pt">Cerrado c/ Descuadre</span>

                    @endif

                </div>

            </div>

           

            <div class="card-body">

                <!-- Información General y Finanzas -->

                <div class="row">

                    <div class="col-md-6">

                        <p><strong>Cajero / Usuario:</strong> {{ $turno->user->name ?? 'N/A' }}</p>

                        <p><strong>Fecha de Apertura:</strong> {{ $turno->created_at }}</p>

                        <p><strong>Fecha de Cierre:</strong> {{ $turno->fecha_cierre ?? 'Turno en curso' }}</p>

                        <p><strong>Base Inicial de Caja:</strong> ${{ number_format($turno->base_caja, 0, ',', '.') }}</p>

                    </div>

                    <div class="col-md-6">

                        <p><strong>Total Ventas en Efectivo:</strong> <span class="text-success font-weight-bold">${{ number_format($totalVentasEfectivo ?? 0, 0, ',', '.') }}</span></p>

                        @if(($totalFiadoCobrado ?? 0) > 0)

                            <p><strong>Cobro de Fiados (Efectivo):</strong> <span class="text-info font-weight-bold">+${{ number_format($totalFiadoCobrado, 0, ',', '.') }}</span></p>

                        @endif

                        <p><strong>Total Compras de Insumos:</strong> <span class="text-danger font-weight-bold">${{ number_format($totalCompras ?? 0, 0, ',', '.') }}</span></p>

                        <p><strong>Sueldo / Pago Trabajadora:</strong> <span class="text-info font-weight-bold">${{ number_format($pagoTrabajadora ?? 0, 0, ',', '.') }}</span></p>

                        <p><strong>Dinero Esperado en Caja:</strong> <span class="text-primary font-weight-bold">${{ number_format($dineroEsperado ?? 0, 0, ',', '.') }}</span></p>

                    </div>

                </div>



                <hr>



                <!-- Resultados del Arqueo y Cuadre -->

                <div class="row bg-light p-3 rounded">

                    <div class="col-md-12">

                        <p class="mb-1"><strong>Efectivo Real Contado:</strong> ${{ number_format($efectivoReal ?? 0, 0, ',', '.') }}</p>

                        <p class="mb-0">

                            <strong>Diferencia / Descuadre:</strong>

                            @if(abs($descuadreVisual) < 0.01)

                                <span class="text-success font-weight-bold">$0 (Cuadre Perfecto)</span>

                            @elseif($descuadreVisual < 0)

                                <span class="text-danger font-weight-bold">-${{ number_format(abs($descuadreVisual), 0, ',', '.') }} (Faltante)</span>

                            @else

                                <span class="text-info font-weight-bold">+${{ number_format($descuadreVisual, 0, ',', '.') }} (Sobrante)</span>

                            @endif

                        </p>

                    </div>

                </div>



                <hr>



                <!-- Observaciones y Notas Unificadas -->

                <div class="row">

                    <div class="col-md-12">

                        <div class="form-group">

                            <label><strong>Notas y Resumen de Cierre del Turno:</strong></label>

                            <textarea class="form-control bg-light" rows="6" readonly>{{ $turno->notas ?? 'Sin notas registradas.' }}</textarea>

                        </div>

                    </div>

                </div>

            </div>



            <div class="card-footer text-right">

                <a href="{{ url('/admin/turnos') }}" class="btn btn-secondary">Volver</a>

            </div>

        </div>

    </div>

</div>

@stop 


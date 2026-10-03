@extends('adminlte::page')

@section('title', 'Turnos de Caja')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark font-weight-bold">
        <i class="fas fa-cash-register text-primary mr-2"></i>Turnos / Cajas
    </h1>
    {{-- El botón de acción se renderiza en el card-header según el estado del turno activo --}}
</div>
@stop

@section('content')
<div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
        <h3 class="card-title font-weight-bold m-0">
            <i class="fas fa-list mr-2 text-primary"></i>Turnos Registrados
        </h3>
        <div class="card-tools">
            @php
                $miTurnoActivo = $turnos->where('user_id', Auth::id())->where('estado', 'abierto')->first();
            @endphp
            @if($miTurnoActivo)
                <a class="btn btn-warning font-weight-bold shadow-sm" href="{{ url('/admin/turnos/'.$miTurnoActivo->id.'/edit') }}" title="Ir al arqueo y cierre de tu turno activo">
                    <i class="fas fa-lock mr-1"></i> Cerrar Mi Turno
                </a>
            @else
                <a class="btn btn-primary font-weight-bold shadow-sm" href="{{ url('/admin/turnos/create') }}">
                    <i class="fas fa-plus-circle mr-1"></i> Abrir Nuevo Turno
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <table id="tablaTurnos" class="table table-bordered table-striped table-hover text-center align-middle">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" style="width:50px">#</th>
                            <th class="text-center">Usuario</th>
                            <th class="text-center">Fecha Inicio</th>
                            <th class="text-center">Base Caja</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center" style="width:160px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($turnos as $turno)
                            <tr>
                                <td class="text-center font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                <td class="text-center font-weight-bold text-dark">
                                    {{ $turno->user->name }}
                                    @if($turno->user_id === Auth::id() && $turno->estado === 'abierto')
                                        <span class="badge badge-info ml-1 px-2 py-1"><i class="fas fa-user-check"></i> Tú</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $turno->fecha_inicio }}</td>
                                <td class="text-center font-weight-bold text-success">${{ number_format($turno->base_caja, 0, '', '') }}</td>
                                <td class="text-center">

                                    @if($turno->estado == 'abierto')
                                        <span class="badge badge-success px-2 py-1 shadow-sm font-weight-bold">
                                            <i class="fas fa-circle mr-1" style="font-size: 7px; vertical-align: middle;"></i> Abierto
                                        </span>
                                    @elseif(abs((float)$turno->total_descuadre_dinero) < 0.01)
                                        <span class="badge badge-success px-2 py-1">Cerrado OK</span>
                                    @else
                                        <span class="badge badge-warning px-2 py-1">Cerrado c/ Descuadre</span>
                                    @endif

                                </td>
                                <td class="text-center align-middle">
                                    <div style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                                        <a href="{{ url('/admin/turnos/'.$turno->id) }}" class="btn btn-info btn-sm font-weight-bold shadow-sm" title="Ver información del turno">
                                            <i class="fas fa-eye"></i><span class="btn-accion-texto ml-1"> Ver</span>
                                        </a>

                                        <form action="{{ url('/admin/turnos/'.$turno->id) }}" method="POST" class="d-inline form-secured" data-secured-message="¿Desea eliminar el Turno #{{ $turno->id }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm font-weight-bold shadow-sm text-white" title="Eliminar Turno">
                                                <i class="fas fa-trash-alt"></i><span class="btn-accion-texto ml-1"> Eliminar</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div><!-- /.table-responsive -->
        </div>
    </div>
</div>
@stop

@include('admin.partials._datatables', ['tableId' => 'tablaTurnos', 'entidad' => 'Turnos', 'conBotones' => true])

@section('js')
@include('admin.partials.pin-security')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop

@extends('adminlte::page')

@section('title', 'Compras')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
    <h1 class="m-0 text-dark font-weight-bold">
        <i class="fas fa-shopping-cart text-primary mr-2"></i>Compras / Egresos
    </h1>
    <div class="d-flex" style="gap:6px;">
        <button type="button" class="btn btn-warning font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalCompraRapida">
            <i class="fas fa-bolt mr-1"></i> Compra Rápida / Egreso
        </button>
        <a class="btn btn-primary font-weight-bold shadow-sm" href="{{ url('/admin/compras/create') }}">
            <i class="fas fa-plus-circle mr-1"></i> Nueva Compra (Inventariable)
        </a>
    </div>
</div>
@stop

@section('content')
<div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
        <h3 class="card-title font-weight-bold m-0">
            <i class="fas fa-list mr-2 text-primary"></i>Historial de Compras / Egresos
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaCompras" class="table table-bordered table-striped table-hover text-center align-middle">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Comprobante</th>
                        <th>Tipo</th>
                        <th class="text-left">Detalle / Concepto</th>
                        <th>Fecha</th>
                        <th class="text-right">Total</th>
                        <th style="width:130px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($compras as $compra)
                        <tr>
                            <td class="align-middle font-weight-bold text-muted">{{ $loop->iteration }}</td>
                            <td class="align-middle">
                                <code class="text-dark">{{ $compra->comprobante ?? 'S/N' }}</code>
                            </td>
                            <td class="align-middle">
                                @if (($compra->tipo ?? 'normal') === 'rapida')
                                    <span class="badge badge-warning px-2 py-1 font-weight-bold">
                                        <i class="fas fa-bolt mr-1"></i> Rápida
                                    </span>
                                @else
                                    <span class="badge badge-info px-2 py-1 font-weight-bold">
                                        <i class="fas fa-boxes mr-1"></i> Inventario
                                    </span>
                                @endif
                            </td>
                            <td class="align-middle text-left">
                                @if (($compra->tipo ?? 'normal') === 'rapida')
                                    <strong class="text-dark">{{ $compra->concepto ?? 'Sin concepto registrado' }}</strong>
                                @else
                                    @forelse ($compra->detalles as $det)
                                        <small class="d-block text-muted">• {{ $det->cantidad }}x {{ $det->producto->nombre ?? 'Producto Eliminado' }}</small>
                                    @empty
                                        <small class="text-muted fst-italic">Sin detalles de productos</small>
                                    @endforelse
                                @endif
                            </td>
                            <td class="align-middle">{{ $compra->fecha }}</td>
                            <td class="align-middle text-right font-weight-bold text-success">
                                ${{ number_format($compra->total, 0, ',', '.') }}
                            </td>
                            <td class="align-middle">
                                <div style="display:flex; justify-content:center; align-items:center; gap:5px;">
                                    <a href="{{ url('/admin/compras/'.$compra->id) }}"
                                       class="btn btn-info btn-sm font-weight-bold shadow-sm" title="Ver">
                                        <i class="fas fa-eye"></i><span class="btn-accion-texto ml-1"> Ver</span>
                                    </a>
                                    <form action="{{ url('/admin/compras/'.$compra->id) }}" method="POST"
                                          class="d-inline form-secured"
                                          data-secured-message="¿Desea eliminar esta compra?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-danger btn-sm font-weight-bold shadow-sm text-white"
                                                title="Eliminar">
                                            <i class="fas fa-trash-alt"></i><span class="btn-accion-texto ml-1"> Eliminar</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Compra Rápida / Egreso Operativo -->
<div class="modal fade" id="modalCompraRapida" tabindex="-1" role="dialog" aria-labelledby="modalCompraRapidaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold" id="modalCompraRapidaLabel">
                    <i class="fas fa-bolt mr-1"></i> Registrar Compra Rápida / Egreso
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('compras.storeRapida') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="concepto">¿En qué se gastó el dinero? (Concepto) <span class="text-danger">*</span></label>
                        <input type="text" name="concepto" id="concepto" class="form-control" placeholder="Ej: Bolsas de hielo, servilletas, aseo..." required>
                    </div>

                    <div class="form-group">
                        <label for="total">Monto Gastado ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                            </div>
                            <input type="number" step="any" min="0" name="total" id="total" class="form-control" placeholder="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fecha">Fecha <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" id="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Guardar Egreso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@include('admin.partials._datatables', ['tableId' => 'tablaCompras', 'entidad' => 'Compras', 'conBotones' => true])

@section('js')
@include('admin.partials.pin-security')
@stop
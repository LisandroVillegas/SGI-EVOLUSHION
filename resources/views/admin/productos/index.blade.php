@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark font-weight-bold">
        <i class="fas fa-box-open text-primary mr-2"></i>Productos / Cócteles
    </h1>
    <a href="{{ url('/admin/productos/create') }}" class="btn btn-primary font-weight-bold shadow-sm">
        <i class="fas fa-plus-circle mr-1"></i> Nuevo Producto
    </a>
</div>
@stop

@section('content')
<div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
        <h3 class="card-title font-weight-bold m-0">
            <i class="fas fa-list mr-2 text-primary"></i>Productos Registrados
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaProductos" class="table table-bordered table-striped table-hover text-center align-middle">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Código</th>
                        <th>Categoría</th>
                        <th class="text-left">Nombre</th>
                        <th class="text-right">Precio Venta</th>
                        <th>Stock</th>
                        <th style="width:190px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $producto)
                        <tr>
                            <td class="align-middle font-weight-bold text-muted">{{ $loop->iteration }}</td>
                            <td class="align-middle">
                                <code class="text-dark font-weight-bold">{{ $producto->codigo }}</code>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-secondary px-2 py-1">
                                    {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                </span>
                            </td>
                            <td class="align-middle text-left font-weight-bold text-dark">{{ $producto->nombre }}</td>
                            <td class="align-middle text-right font-weight-bold text-success">
                                ${{ number_format($producto->precio_venta, 0, ',', '.') }}
                            </td>
                            <td class="align-middle">
                                @if($producto->stock <= 0)
                                    <span class="badge badge-danger px-2 py-1 font-weight-bold">
                                        <i class="fas fa-times-circle mr-1"></i> Agotado ({{ $producto->stock }})
                                    </span>
                                @elseif($producto->stock <= 5)
                                    <span class="badge badge-warning px-2 py-1 font-weight-bold text-dark">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Bajo ({{ $producto->stock }})
                                    </span>
                                @else
                                    <span class="badge badge-success px-2 py-1 font-weight-bold">
                                        <i class="fas fa-check-circle mr-1"></i> {{ $producto->stock }}
                                    </span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div style="display:flex; justify-content:center; align-items:center; gap:5px;">
                                    <a href="{{ url('/admin/producto/'.$producto->id) }}"
                                       class="btn btn-info btn-sm font-weight-bold shadow-sm"
                                       title="Ver detalle">
                                        <i class="fas fa-eye"></i><span class="btn-accion-texto ml-1"> Ver</span>
                                    </a>
                                    <a href="{{ url('/admin/producto/'.$producto->id.'/edit') }}"
                                       class="btn btn-warning btn-sm font-weight-bold shadow-sm"
                                       title="Editar">
                                        <i class="fas fa-edit"></i><span class="btn-accion-texto ml-1"> Editar</span>
                                    </a>
                                    <form action="{{ url('/admin/producto/'.$producto->id) }}" method="POST"
                                          class="d-inline form-secured"
                                          data-secured-message="¿Desea eliminar el producto '{{ $producto->nombre }}'?">
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
@stop

@include('admin.partials._datatables', ['tableId' => 'tablaProductos', 'entidad' => 'Productos', 'conBotones' => false])

@section('js')
@include('admin.partials.pin-security')
@stop
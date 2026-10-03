@extends('adminlte::page')

@section('title', 'Categorías')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark font-weight-bold">
        <i class="fas fa-tags text-primary mr-2"></i>Categorías
    </h1>
    <a href="{{ url('/admin/categorias/create') }}" class="btn btn-primary font-weight-bold shadow-sm">
        <i class="fas fa-plus-circle mr-1"></i> Nueva Categoría
    </a>
</div>
@stop

@section('content')
<div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
        <h3 class="card-title font-weight-bold m-0">
            <i class="fas fa-list mr-2 text-primary"></i>Categorías Registradas
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tablaCategorias" class="table table-bordered table-striped table-hover text-center align-middle">
                <thead class="thead-dark">
                    <tr>
                        <th style="width:50px">#</th>
                        <th class="text-left">Nombre</th>
                        <th class="text-left">Descripción</th>
                        <th style="width:190px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categorias as $categoria)
                        <tr>
                            <td class="align-middle font-weight-bold text-muted">{{ $loop->iteration }}</td>
                            <td class="align-middle text-left font-weight-bold text-dark">
                                <i class="fas fa-tag mr-1 text-primary"></i>
                                {{ $categoria->nombre }}
                            </td>
                            <td class="align-middle text-left text-muted">
                                {{ $categoria->descripcion ?: '—' }}
                            </td>
                            <td class="align-middle">
                                <div style="display:flex; justify-content:center; align-items:center; gap:5px;">
                                    <a href="{{ url('/admin/categoria/'.$categoria->id) }}"
                                       class="btn btn-info btn-sm font-weight-bold shadow-sm"
                                       title="Ver detalle">
                                        <i class="fas fa-eye"></i><span class="btn-accion-texto ml-1"> Ver</span>
                                    </a>
                                    <a href="{{ url('/admin/categoria/'.$categoria->id.'/edit') }}"
                                       class="btn btn-warning btn-sm font-weight-bold shadow-sm"
                                       title="Editar">
                                        <i class="fas fa-edit"></i><span class="btn-accion-texto ml-1"> Editar</span>
                                    </a>
                                    <form action="{{ url('/admin/categoria/'.$categoria->id) }}" method="POST"
                                          class="d-inline form-secured"
                                          data-secured-message="¿Desea eliminar la categoría '{{ $categoria->nombre }}'?">
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

@include('admin.partials._datatables', ['tableId' => 'tablaCategorias', 'entidad' => 'Categorías', 'conBotones' => false])

@section('js')
@include('admin.partials.pin-security')
@stop
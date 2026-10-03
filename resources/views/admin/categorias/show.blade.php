@extends('adminlte::page')

@section('title', 'Detalle de Categoría')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark font-weight-bold">
        <i class="fas fa-tags text-info mr-2"></i>Detalle de Categoría
    </h1>
    <a href="{{ url('/admin/categorias') }}" class="btn btn-secondary font-weight-bold shadow-sm">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Categorías
    </a>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
                <div class="card  card-info">
                  <div class="card-header">
                    <h3 class="card-title">Llenar Datos de Formulario</h3>

                    
                    <!-- /.card-tools -->
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body" style="box-sizing: border-box; display: block;">
        
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nombre">Nombre de la Categoria <b style="color: red">(*)</b></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-tags"></i></span>

                                        </div>
                                        <input type="text" class="form-control" id="nombre" name="nombre" 
                                        placeholder="Ingrese el nombre de la Categoria" value="{{ $categoria->nombre }}"
                                         readonly>

                                    </div>
                                    @error('nombre')
                                    <small style="color:red">{{ $message }}</small>
                                    @enderror
                                    
                                    


                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nombre">Descripción de la Categoria (opcional)</label>
                                    <textarea class="form-control" name="descripcion" id="descripcion" rows="3" 
                                    placeholder="Ingrese una breve descripción de la Categoria" readonly >{{ $categoria->descripcion }}</textarea>
                                   
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <a href="{{ url('/admin/categorias') }}" class="btn btn-secondary ">Volver</a>
                                
                                </div>
                                
                        
                            </div>

                        </div>






                    

                   
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
</div>
   
@stop

@section('css')
    

@stop

@section('js')
   
@stop

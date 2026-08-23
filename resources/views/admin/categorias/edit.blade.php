@extends('adminlte::page')



@section('content_header')
<nav aria-label="breadcrumb" style="font-size: 18pt">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/categorias') }}">Categorias</a></li>
    <li class="breadcrumb-item active" aria-current="page">Editar  Categorias</li>
  </ol>
</nav>
<hr>
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
                <div class="card  card-success">
                  <div class="card-header">
                    <h3 class="card-title">Llenar Datos de Formulario</h3>

                    
                    <!-- /.card-tools -->
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body" style="box-sizing: border-box; display: block;">
                    <form action="{{ url('/admin/categoria/'.$categoria->id)  }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nombre">Nombre de la Categoria <b style="color: red">(*)</b></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-tags"></i></span>

                                        </div>
                                        <input type="text" value="{{ $categoria->nombre }}" class="form-control" id="nombre" name="nombre" 
                                        placeholder="Ingrese el nombre de la Categoria" required>

                                    </div>
                                    @error('nombre')
                                    <small style="color:red">{{ $message }}</small>
                                    @enderror
                                    
                                    


                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nombre">Descripción de la Categoria (opcional)</label>
                                    <textarea class="form-control"  name="descripcion" id="descripcion" rows="3" 
                                    placeholder="Ingrese una breve descripción de la Categoria">{{ $categoria->descripcion }}</textarea>
                                   
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <a href="{{ url('/admin/categorias') }}" class="btn btn-secondary ">Cancelar</a>
                                <button type="submit" class="btn btn-success ">Actualizar</button>
                                </div>
                                
                        
                            </div>

                        </div>






                    </form>
                    

                   
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

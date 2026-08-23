@extends('adminlte::page')

@section('content_header')
<nav aria-label="breadcrumb" style="font-size: 18pt">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/compras') }}">Compras</a></li>
    <li class="breadcrumb-item active" aria-current="page">Nueva Compra</li>
  </ol>
</nav>
<hr>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Datos del Ingreso de Stock</h3>
            </div>
            <form action="{{ url('/admin/compras') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="comprobante">N° Comprobante / Factura:</label>
                            <input type="text" name="comprobante" class="form-control" placeholder="Ej: FAC-00123 (Dejar en blanco para autogenerar)">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="fecha">Fecha de Ingreso:</label>
                            <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <hr>
                    <h5><strong>Productos a Ingresar</strong></h5>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="tabla-productos">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Producto</th>
                                    <th style="width: 150px;">Cantidad</th>
                                    <th style="width: 200px;">Precio Compra</th>
                                    <th style="width: 80px; text-align: center;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="productos[0][producto_id]" class="form-control" required>
                                            <option value="">-- Seleccionar Producto --</option>
                                            @foreach($productos as $prod)
                                                <option value="{{ $prod->id }}">{{ $prod->nombre }} (Stock actual: {{ $prod->stock }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="productos[0][cantidad]" class="form-control" min="1" value="1" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="productos[0][precio_compra]" class="form-control" placeholder="0.00" required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-success btn-sm mb-3" id="add-row">+ Agregar Producto</button>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Guardar e Incrementar Inventario</button>
                    <a href="{{ url('/admin/compras') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    let rowIndex = 1;
    document.getElementById('add-row').addEventListener('click', function() {
        let table = document.getElementById('tabla-productos').getElementsByTagName('tbody')[0];
        let newRow = table.rows[0].cloneNode(true);

        newRow.innerHTML = newRow.innerHTML.replaceAll('[0]', '[' + rowIndex + ']');
        newRow.querySelector('input[type="number"]').value = 1;
        
        let inputs = newRow.querySelectorAll('input');
        inputs.forEach(input => {
            if(input.type === 'number' && input.name.includes('precio_compra')) {
                input.value = '';
            }
        });

        table.appendChild(newRow);
        rowIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target && (e.target.classList.contains('remove-row') || e.target.parentElement.classList.contains('remove-row'))) {
            let row = e.target.closest('tr');
            let table = document.getElementById('tabla-productos').getElementsByTagName('tbody')[0];
            if (table.rows.length > 1) {
                row.remove();
            }
        }
    });
</script>
@stop
@extends('adminlte::page')

@section('title', 'Nueva Compra')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark font-weight-bold">
        <i class="fas fa-boxes text-primary mr-2"></i>Nueva Compra de Inventario
    </h1>
    <a href="{{ url('/admin/compras') }}" class="btn btn-secondary font-weight-bold shadow-sm">
        <i class="fas fa-arrow-left mr-1"></i> Volver al historial
    </a>
</div>
@stop

@section('content')
<div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
        <h3 class="card-title font-weight-bold">
            <i class="fas fa-clipboard-list mr-2 text-primary"></i>Datos del Ingreso de Stock
        </h3>
    </div>
    <form action="{{ url('/admin/compras') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="comprobante">
                        <i class="fas fa-file-invoice mr-1 text-muted"></i>
                        N° Comprobante / Factura
                        <small class="text-muted">(opcional)</small>
                    </label>
                    <input type="text" name="comprobante" class="form-control" placeholder="Ej: FAC-00123 (dejar en blanco para autogenerar)">
                </div>
                <div class="col-md-6 form-group">
                    <label for="fecha">
                        <i class="fas fa-calendar-alt mr-1 text-muted"></i>
                        Fecha de Ingreso <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="fecha" id="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <hr>
            <h5 class="font-weight-bold text-dark mb-3">
                <i class="fas fa-list mr-1 text-primary"></i> Productos a Ingresar
            </h5>

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle" id="tabla-productos">
                    <thead class="thead-dark">
                        <tr>
                            <th>Producto (Categoría)</th>
                            <th style="width:130px" class="text-center">Cantidad</th>
                            <th style="width:180px" class="text-center">Precio Compra ($)</th>
                            <th style="width:60px" class="text-center">Quitar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="productos[0][producto_id]" class="form-control" required>
                                    <option value="">-- Seleccionar Producto --</option>
                                    @foreach($productos as $prod)
                                        <option value="{{ $prod->id }}">{{ $prod->nombre }} ({{ $prod->categoria->nombre ?? 'Sin Categoría' }}) &mdash; Stock: {{ $prod->stock }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="productos[0][cantidad]" class="form-control text-center" min="1" value="1" required>
                            </td>
                            <td>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                    <input type="number" step="1" min="0" name="productos[0][precio_compra]" class="form-control" placeholder="0" required>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-row" title="Quitar fila">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" class="btn btn-outline-success btn-sm mb-3" id="add-row">
                <i class="fas fa-plus mr-1"></i> Agregar otro producto
            </button>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted"><span class="text-danger">*</span> Campos obligatorios</small>
            <div>
                <a href="{{ url('/admin/compras') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary font-weight-bold">
                    <i class="fas fa-save mr-1"></i> Guardar e Incrementar Inventario
                </button>
            </div>
        </div>
    </form>
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
@extends('adminlte::page')

@section('title', 'Punto de Venta - POS')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark font-weight-bold"><i class="fas fa-calculator text-primary mr-2"></i>Punto de Venta (POS)</h1>
    <span class="badge badge-success px-3 py-2 font-weight-bold" style="font-size: 11pt;">
        <i class="fas fa-door-open mr-1"></i> Turno Activo #{{ $turnoActivo->id }}
    </span>
</div>
@stop

@section('content')
<div class="row">
    {{-- COLUMNA IZQUIERDA: CATÁLOGO DE PRODUCTOS Y FILTROS (Estructura original) --}}
    <div class="col-lg-7 col-md-6">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header bg-light">
                {{-- Filtro por Categorías --}}
                <div class="d-flex flex-wrap gap-2 mb-2" id="contenedor-categorias">
                    <button type="button" class="btn btn-primary btn-sm font-weight-bold btn-categoria mr-1 mb-1 active" data-id="todas">
                        Todas
                    </button>
                    @foreach($categorias as $cat)
                        <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold btn-categoria mr-1 mb-1" data-id="{{ $cat->id }}">
                            {{ $cat->nombre }}
                        </button>
                    @endforeach
                </div>
                {{-- Buscador Dinámico de Productos --}}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" id="inputBuscadorProducto" class="form-control" placeholder="Buscar producto por nombre...">
                </div>
            </div>
            <div class="card-body p-3" style="max-height: 65vh; overflow-y: auto;">
                <div class="row" id="grilla-productos">
                    @foreach($productos as $prod)
                        <div class="col-xl-4 col-lg-6 col-md-12 col-6 mb-3 tarjeta-producto" data-categoria="{{ $prod->categoria_id }}">
                            <div class="card h-100 shadow-sm border rounded hover-shadow cursor-pointer select-producto-btn" 
                                   data-id="{{ $prod->id }}" 
                                   data-nombre="{{ $prod->nombre }}" 
                                   data-precio="{{ $prod->precio_venta }}"
                                   data-stock="{{ $prod->stock }}"
                                   data-categoria-id="{{ $prod->categoria_id }}"
                                   data-categoria-nombre="{{ strtolower($prod->categoria->nombre ?? '') }}">
                                <div class="card-body p-3 text-center d-flex flex-column justify-content-between">
                                    <div>
                                        <small class="text-muted d-block text-uppercase font-weight-bold mb-1">{{ $prod->categoria->nombre ?? 'Sin Categ.' }}</small>
                                        <h6 class="font-weight-bold text-dark mb-2">{{ $prod->nombre }}</h6>
                                    </div>
                                    <div>
                                        <h5 class="font-weight-bold text-success m-0">${{ number_format($prod->precio_venta, 0, ',', '.') }}</h5>
                                        <span class="badge {{ $prod->stock > 5 ? 'badge-light' : 'badge-danger' }} border mt-2">
                                            Stock: <span class="stock-num">{{ $prod->stock }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- COLUMNA DERECHA: CARRITO, CÁLCULO DE CAMBIO Y COBRO --}}
    <div class="col-lg-5 col-md-6">
        <div class="card card-outline card-success shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold m-0"><i class="fas fa-shopping-cart text-success mr-1"></i> Detalle del Pedido</h3>
                <button type="button" class="btn btn-outline-danger btn-sm font-weight-bold" id="btnVaciarCarrito">
                    <i class="fas fa-trash-alt mr-1"></i> Vaciar
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 35vh; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" id="tabla-carrito">
                        <thead class="bg-light">
                            <tr class="small font-weight-bold text-muted">
                                <th class="pl-3">Producto</th>
                                <th class="text-center" style="width: 100px;">Cant.</th>
                                <th class="text-right" style="width: 90px;">Subtotal</th>
                                <th class="text-center" style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="fila-vacia">
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-hand-pointer fa-2x mb-2 d-block opacity-50"></i>
                                    Selecciona productos para iniciar la venta
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-light p-3 border-top">
                {{-- Switch de Promoción Activable por el Cajero --}}
                <div class="form-group mb-3 bg-white p-2 border rounded">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox" class="custom-control-input" id="switchPromocion">
                        <label class="custom-control-label font-weight-bold text-dark cursor-pointer" for="switchPromocion">
                            <i class="fas fa-tags text-warning mr-1"></i> Aplicar Promociones
                        </label>
                    </div>
                </div>

                {{-- Resumen Total y Descuentos --}}
                <div class="bg-white rounded border p-2 mb-3">
                    <div class="d-flex justify-content-between align-items-center small text-muted mb-1" id="box-descuento-promo" style="display: none !important;">
                        <span>Descuento Promocional:</span>
                        <span class="font-weight-bold text-danger" id="texto-descuento">-$0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 font-weight-bold m-0 text-muted">TOTAL A PAGAR:</span>
                        <span class="h3 font-weight-bold m-0 text-success" id="texto-total">$0</span>
                    </div>
                </div>

                {{-- Método de Pago --}}
                <div class="form-group mb-2">
                    <label class="font-weight-bold small text-muted">Método de Pago:</label>
                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                        <label class="btn btn-outline-success active font-weight-bold">
                            <input type="radio" name="metodo_pago" value="efectivo" checked> <i class="fas fa-money-bill-wave mr-1"></i> Efectivo
                        </label>
                        <label class="btn btn-outline-primary font-weight-bold">
                            <input type="radio" name="metodo_pago" value="transferencia"> <i class="fas fa-mobile-alt mr-1"></i> Transf.
                        </label>
                        <label class="btn btn-outline-warning font-weight-bold">
                            <input type="radio" name="metodo_pago" value="fiado"> <i class="fas fa-user-clock mr-1"></i> Fiado
                        </label>
                    </div>
                </div>

                {{-- Sección de Calculadora de Cambio (Solo visible en Efectivo) --}}
                <div id="seccion-calculadora-cambio">
                    <div class="form-group mb-2">
                        <label for="pago_recibido" class="font-weight-bold small text-muted">Efectivo Recibido:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold">$</span>
                            </div>
                            <input type="number" id="pago_recibido" class="form-control form-control-lg font-weight-bold text-primary" placeholder="0" min="0" step="100">
                        </div>
                    </div>

                    {{-- Botones de Acceso Rápido a Billetes --}}
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold mr-1 mb-1 btn-billete" data-valor="exacto">Monto Exacto</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold mr-1 mb-1 btn-billete" data-valor="10000">$10.000</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold mr-1 mb-1 btn-billete" data-valor="20000">$20.000</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold mr-1 mb-1 btn-billete" data-valor="50000">$50.000</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold mr-1 mb-1 btn-billete" data-valor="100000">$100.000</button>
                    </div>

                    {{-- Indicador de Cambio / Devolución --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-white rounded border" id="box-cambio">
                        <span class="h6 font-weight-bold m-0 text-muted">CAMBIO / DEVUELTO:</span>
                        <span class="h4 font-weight-bold m-0 text-primary" id="texto-cambio">$0</span>
                    </div>
                </div>

                <button type="button" class="btn btn-success btn-lg btn-block font-weight-bold shadow-sm" id="btnProcesarVenta" disabled>
                    <i class="fas fa-check-circle mr-1"></i> COBRAR Y REGISTRAR
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .cursor-pointer { cursor: pointer; }
    .hover-shadow:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; transition: all .2s ease-in-out; }
    .select-producto-btn:active { transform: scale(0.97); }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const promocionesRegistradas = @json($promociones ?? []);

    $(document).ready(function() {
        let carrito = [];
        let totalAcumulado = 0;
        let descuentoTotalPromocion = 0;
        let ultimoCambio = 0;

        $('#inputBuscadorProducto').on('keyup input', function() {
            let term = $(this).val().toLowerCase().trim();
            let catActiva = $('.btn-categoria.active').data('id');

            $('.tarjeta-producto').each(function() {
                let nombre = $(this).find('.select-producto-btn').data('nombre').toString().toLowerCase();
                let catId = $(this).data('categoria');

                let coincideCategoria = (catActiva === 'todas' || catId == catActiva);
                let coincideTexto = nombre.includes(term);

                if (coincideCategoria && coincideTexto) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        $('#switchPromocion').on('change', function() {
            renderizarCarrito();
        });

        $('.btn-categoria').on('click', function() {$('.btn-categoria').removeClass('active btn-primary').addClass('btn-outline-secondary');
            $(this).addClass('active btn-primary').removeClass('btn-outline-secondary');$('#inputBuscadorProducto').trigger('keyup');
        });

        $('.select-producto-btn').on('click', function() {
            let id = $(this).data('id');
            let nombre = $(this).data('nombre');
            let precio = parseFloat($(this).data('precio'));
            let stock = parseInt($(this).data('stock'));
            let categoriaId = $(this).data('categoria-id');
            let categoriaNombre = $(this).data('categoria-nombre');

            let itemExistente = carrito.find(p => p.id === id);

            if (itemExistente) {
                if (itemExistente.cantidad + 1 > stock) {
                    Swal.fire('Stock insuficiente', 'No hay más unidades disponibles de este producto.', 'warning');
                    return;
                }
                itemExistente.cantidad++;
            } else {
                if (stock < 1) {
                    Swal.fire('Sin stock', 'Este producto está agotado.', 'warning');
                    return;
                }
                carrito.push({ id, nombre, precio, stock, categoriaId, categoriaNombre, cantidad: 1 });
            }

            renderizarCarrito();
        });

        $(document).on('click', '.btn-sumar', function() {
            let idx = $(this).data('index');
            if (carrito[idx].cantidad + 1 > carrito[idx].stock) {
                Swal.fire('Stock insuficiente', 'No puedes agregar más del stock disponible.', 'warning');
                return;
            }
            carrito[idx].cantidad++;
            renderizarCarrito();
        });

        $(document).on('click', '.btn-restar', function() {
            let idx = $(this).data('index');
            if (carrito[idx].cantidad > 1) {
                carrito[idx].cantidad--;
            } else {
                carrito.splice(idx, 1);
            }
            renderizarCarrito();
        });

        $(document).on('click', '.btn-eliminar', function() {
            let idx = $(this).data('index');
            carrito.splice(idx, 1);
            renderizarCarrito();
        });

        $('#btnVaciarCarrito').on('click', function() {
            if (carrito.length === 0) return;

            Swal.fire({
                title: '¿Vaciar detalle?',
                text: 'Se eliminarán todos los productos seleccionados.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, vaciar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    carrito = [];
                    renderizarCarrito();
                }
            });
        });

        $('.btn-billete').on('click', function() {
            let val = $(this).data('valor');
            if (val === 'exacto') {
                $('#pago_recibido').val(totalAcumulado);
            } else {
                $('#pago_recibido').val(parseFloat(val));
            }
            calcularCambio();
        });

        $('input[name="metodo_pago"]').on('change', function() {
            let metodoPago = $(this).val();

            if (metodoPago === 'efectivo') {
                $('#seccion-calculadora-cambio').slideDown();
            } else {
                $('#seccion-calculadora-cambio').slideUp();
            }
            calcularCambio();
        });

        $('#pago_recibido').on('input keyup change', function() {
            calcularCambio();
        });

        function renderizarCarrito() {
            let tbody = $('#tabla-carrito tbody');
            tbody.empty();

            if (carrito.length === 0) {
                tbody.append(`
                    <tr id="fila-vacia">
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="fas fa-hand-pointer fa-2x mb-2 d-block opacity-50"></i>
                            Selecciona productos para iniciar la venta
                        </td>
                    </tr>
                `);
                totalAcumulado = 0;
                descuentoTotalPromocion = 0;
                ultimoCambio = 0;
                $('#texto-total').text('$0');$('#texto-descuento').text('-$0');$('#box-descuento-promo').attr('style', 'display: none !important;');
                $('#pago_recibido').val('');
                calcularCambio();
                return;
            }

            let subtotalGeneral = 0;
            let cantidadesPorProducto = {};
            let cantidadesPorCategoria = {};

            carrito.forEach((prod, index) => {
                let subtotalFila = prod.precio * prod.cantidad;
                subtotalGeneral += subtotalFila;

                cantidadesPorProducto[prod.id] = (cantidadesPorProducto[prod.id] || 0) + prod.cantidad;
                if (prod.categoriaId) {
                    cantidadesPorCategoria[prod.categoriaId] = (cantidadesPorCategoria[prod.categoriaId] || 0) + prod.cantidad;
                }

                tbody.append(`
                    <tr>
                        <td class="align-middle pl-3">
                            <strong class="d-block text-dark">${prod.nombre}</strong>
                            <small class="text-muted">$${prod.precio.toLocaleString('es-CO')}</small>
                        </td>
                        <td class="align-middle text-center">
                            <div class="input-group input-group-sm mx-auto" style="max-width: 90px;">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-secondary btn-restar" data-index="${index}"><i class="fas fa-minus"></i></button>
                                </div>
                                <input type="text" class="form-control text-center px-1 font-weight-bold" value="${prod.cantidad}" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary btn-sumar" data-index="${index}"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle text-right font-weight-bold text-dark">
                            $${subtotalFila.toLocaleString('es-CO')}
                        </td>
                        <td class="align-middle text-center pr-2">
                            <button class="btn btn-link text-danger p-0 btn-eliminar" data-index="${index}">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            descuentoTotalPromocion = 0;
            if ($('#switchPromocion').is(':checked')) {
                promocionesRegistradas.forEach(promocion => {
                    let cantidadComprada = 0;
                    if (promocion.producto_id) {
                        cantidadComprada = cantidadesPorProducto[promocion.producto_id] || 0;
                    } else if (promocion.categoria_id) {
                        cantidadComprada = cantidadesPorCategoria[promocion.categoria_id] || 0;
                    }

                    if (cantidadComprada >= promocion.cantidad_minima && promocion.cantidad_minima > 0) {
                        let vecesAplicable = Math.floor(cantidadComprada / promocion.cantidad_minima);
                        descuentoTotalPromocion += vecesAplicable * parseFloat(promocion.descuento);
                    }
                });
            }

            if (descuentoTotalPromocion > 0) {
                $('#texto-descuento').text('-$' + descuentoTotalPromocion.toLocaleString('es-CO'));$('#box-descuento-promo').removeAttr('style');
            } else {
                $('#box-descuento-promo').attr('style', 'display: none !important;');
            }

            totalAcumulado = Math.max(0, subtotalGeneral - descuentoTotalPromocion);
            $('#texto-total').text('$' + totalAcumulado.toLocaleString('es-CO'));

            calcularCambio();
        }

        function calcularCambio() {
            let metodoPago = $('input[name="metodo_pago"]:checked').val();

            if (carrito.length === 0) {
                $('#texto-cambio').text('$0').removeClass('text-danger text-primary').addClass('text-muted');$('#btnProcesarVenta').prop('disabled', true);
                ultimoCambio = 0;
                return;
            }

            // Para transferencia o fiado, el botón está habilitado apenas hay productos
            if (metodoPago !== 'efectivo') {
                $('#btnProcesarVenta').prop('disabled', false);
                ultimoCambio = 0;
                return;
            }

            // Efectivo
            let recibido = parseFloat($('#pago_recibido').val()) || 0;
            let cambio = recibido - totalAcumulado;
            ultimoCambio = cambio >= 0 ? cambio : 0;

            if (recibido === 0) {
                $('#texto-cambio').text('$0').removeClass('text-danger text-primary').addClass('text-muted');$('#btnProcesarVenta').prop('disabled', true);
            } else if (cambio < 0) {
                $('#texto-cambio').text('Falta $' + Math.abs(cambio).toLocaleString('es-CO')).removeClass('text-muted text-primary').addClass('text-danger');$('#btnProcesarVenta').prop('disabled', true);
            } else {
                $('#texto-cambio').text('$' + cambio.toLocaleString('es-CO')).removeClass('text-muted text-danger').addClass('text-primary');$('#btnProcesarVenta').prop('disabled', false);
            }
        }

        function ejecutarEnvioVenta(dataToSend) {
            $('#btnProcesarVenta').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Procesando...');

            $.ajax({
                url: '{{ route("ventas.store") }}',
                type: 'POST',
                data: dataToSend,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Venta Registrada!',
                            text: 'La venta se ha guardado correctamente.',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Ocurrió un error al procesar la venta.', 'error');
                        $('#btnProcesarVenta').prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> COBRAR Y REGISTRAR');
                    }
                },
                error: function(xhr) {
                    let msg = 'Ocurrió un error inesperado al procesar la venta.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                    $('#btnProcesarVenta').prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> COBRAR Y REGISTRAR');
                }
            });
        }

        // 5. Procesar Venta: Alertas dinámicas al hacer clic en Cobrar y Registrar
        $('#btnProcesarVenta').on('click', function() {
            if (carrito.length === 0) return;

            let metodoPago = $('input[name="metodo_pago"]:checked').val();
            let aplicaPromocion = $('#switchPromocion').is(':checked');

            if (metodoPago === 'transferencia') {
                Swal.fire({
                    title: 'Registrar Transferencia',
                    input: 'text',
                    inputPlaceholder: 'Ingrese el nombre de quien transfiere (Nequi/Banco)',
                    showCancelButton: true,
                    confirmButtonText: 'Registrar Venta',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#6c757d',
                    inputValidator: (value) => {
                        if (!value || value.trim() === '') {
                            return '¡Debe ingresar el nombre de la persona que realiza la transferencia!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        let dataToSend = {
                            _token: '{{ csrf_token() }}',
                            metodo_pago: 'transferencia',
                            aplica_promocion: aplicaPromocion,
                            cliente_fiado: "Transf: " + result.value.trim(),
                            pago_efectivo: 0,
                            productos: carrito.map(p => ({ id: p.id, cantidad: p.cantidad }))
                        };
                        ejecutarEnvioVenta(dataToSend);
                    }
                });
            } else if (metodoPago === 'fiado') {
                Swal.fire({
                    title: 'Registrar Venta Fiada',
                    html: `
                        <div class="alert alert-warning text-left small mb-3 border-warning text-dark">
                            <i class="fas fa-exclamation-triangle mr-1"></i> <strong>Nota Contable:</strong> Descuenta inventario físico pero <strong>NO ingresa efectivo</strong> a la gaveta.
                        </div>
                    `,
                    input: 'text',
                    inputPlaceholder: 'Nombre del Cliente / Deudor',
                    showCancelButton: true,
                    confirmButtonText: 'Registrar Fiado',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    customClass: {
                        confirmButton: 'text-dark font-weight-bold'
                    },
                    inputValidator: (value) => {
                        if (!value || value.trim() === '') {
                            return '¡Debe ingresar el nombre del cliente o deudor!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        let dataToSend = {
                            _token: '{{ csrf_token() }}',
                            metodo_pago: 'fiado',
                            aplica_promocion: aplicaPromocion,
                            cliente_fiado: result.value.trim(),
                            pago_efectivo: 0,
                            productos: carrito.map(p => ({ id: p.id, cantidad: p.cantidad }))
                        };
                        ejecutarEnvioVenta(dataToSend);
                    }
                });
            } else {
                // Efectivo
                let pagoEfectivo = parseFloat($('#pago_recibido').val()) || 0;
                Swal.fire({
                    title: '¿Registrar Venta en Efectivo?',
                    html: `
                        <div class="text-left bg-light p-3 rounded border">
                            <p class="mb-1 d-flex justify-content-between"><span>Total a pagar:</span> <strong>$${totalAcumulado.toLocaleString('es-CO')}</strong></p>
                            <p class="mb-1 d-flex justify-content-between"><span>Efectivo recibido:</span> <strong>$${pagoEfectivo.toLocaleString('es-CO')}</strong></p>
                            <hr class="my-2">
                            <p class="mb-0 d-flex justify-content-between h5 text-success"><span>Cambio a entregar:</span> <strong>$${ultimoCambio.toLocaleString('es-CO')}</strong></p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, registrar venta',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let dataToSend = {
                            _token: '{{ csrf_token() }}',
                            metodo_pago: 'efectivo',
                            aplica_promocion: aplicaPromocion,
                            cliente_fiado: null,
                            pago_efectivo: pagoEfectivo,
                            productos: carrito.map(p => ({ id: p.id, cantidad: p.cantidad }))
                        };
                        ejecutarEnvioVenta(dataToSend);
                    }
                });
            }
        });
    });
</script>
@stop
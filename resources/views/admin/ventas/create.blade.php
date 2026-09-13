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
    {{-- COLUMNA IZQUIERDA: CATÁLOGO DE PRODUCTOS Y FILTROS --}}
    <div class="col-lg-7 col-md-6">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header bg-light">
                <div class="d-flex flex-wrap gap-2" id="contenedor-categorias">
                    <button type="button" class="btn btn-primary btn-sm font-weight-bold btn-categoria mr-1 mb-1 active" data-id="todas">
                        Todas
                    </button>
                    @foreach($categorias as $cat)
                        <button type="button" class="btn btn-outline-secondary btn-sm font-weight-bold btn-categoria mr-1 mb-1" data-id="{{ $cat->id }}">
                            {{ $cat->nombre }}
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="card-body p-3" style="max-height: 70vh; overflow-y: auto;">
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
                {{-- Switch de Promoción Cócteles --}}
                <div class="form-group mb-3 bg-white p-2 border rounded">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox" class="custom-control-input" id="switchPromocion">
                        <label class="custom-control-label font-weight-bold text-dark cursor-pointer" for="switchPromocion">
                            <i class="fas fa-cocktail text-warning mr-1"></i> Aplicar Promo Cócteles 
                        </label>
                    </div>
                </div>

                {{-- Resumen Total --}}
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-white rounded border">
                    <span class="h5 font-weight-bold m-0 text-muted">TOTAL A PAGAR:</span>
                    <span class="h3 font-weight-bold m-0 text-success" id="texto-total">$0</span>
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

                {{-- Campo para Nombre del Cliente (Solo visible cuando es Fiado) --}}
                <div class="form-group mb-3" id="seccion-cliente-fiado" style="display: none;">
                    <label for="cliente_fiado" class="font-weight-bold small text-warning"><i class="fas fa-user-tag mr-1"></i> Nombre del Cliente / Deudor:</label>
                    <input type="text" id="cliente_fiado" class="form-control border-warning font-weight-bold" placeholder="Ej: Juan Pérez / Mesa 3">
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
    $(document).ready(function() {
        let carrito = [];
        let totalAcumulado = 0;

        // Evento switch promoción
        $('#switchPromocion').on('change', function() {
            renderizarCarrito();
        });

        // 1. Filtro por Categorías
        $('.btn-categoria').on('click', function() {
            $('.btn-categoria').removeClass('active btn-primary').addClass('btn-outline-secondary');
            $(this).addClass('active btn-primary').removeClass('btn-outline-secondary');

            let catId = $(this).data('id');
            if (catId === 'todas') {
                $('.tarjeta-producto').show();
            } else {
                $('.tarjeta-producto').hide();
                $('.tarjeta-producto[data-categoria="' + catId + '"]').show();
            }
        });

        // 2. Click en producto para agregar al carrito
        $('.select-producto-btn').on('click', function() {
            let id = $(this).data('id');
            let nombre = $(this).data('nombre');
            let precio = parseFloat($(this).data('precio'));
            let stock = parseInt($(this).data('stock'));
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
                carrito.push({ id, nombre, precio, stock, categoriaNombre, cantidad: 1 });
            }

            renderizarCarrito();
        });

        // 3. Renderizar vista del carrito y calcular totales
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
                $('#texto-total').text('$0');
                $('#pago_recibido').val('');
                calcularCambio();
                return;
            }

            totalAcumulado = 0;
            let promoActiva = $('#switchPromocion').is(':checked');

            carrito.forEach((prod, index) => {
                let subtotal = prod.precio * prod.cantidad;

                // Aplicar descuento de $4.000 por cada pareja solo si el switch está ON y la categoría contiene "coctel"
                if (promoActiva && prod.categoriaNombre.includes('coctel') && prod.cantidad >= 2) {
                    let parejas = Math.floor(prod.cantidad / 2);
                    let descuento = parejas * 4000;
                    subtotal -= descuento;
                }

                totalAcumulado += subtotal;

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
                            $${subtotal.toLocaleString('es-CO')}
                        </td>
                        <td class="align-middle text-center pr-2">
                            <button class="btn btn-link text-danger p-0 btn-eliminar" data-index="${index}">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            $('#texto-total').text('$' + totalAcumulado.toLocaleString('es-CO'));
            calcularCambio();
        }

        // 4. Lógica de Cambio de Efectivo y Visibilidad de Secciones
        function calcularCambio() {
            let metodoPago = $('input[name="metodo_pago"]:checked').val();

            if (carrito.length === 0) {
                $('#texto-cambio').text('$0').removeClass('text-danger text-primary').addClass('text-muted');
                $('#btnProcesarVenta').prop('disabled', true);
                $('#seccion-cliente-fiado').slideUp();
                return;
            }

            if (metodoPago === 'fiado') {
                $('#seccion-calculadora-cambio').slideUp();
                $('#seccion-cliente-fiado').slideDown();
                
                let nombreCliente = $('#cliente_fiado').val().trim();
                $('#btnProcesarVenta').prop('disabled', nombreCliente === '');
                return;
            } else {
                $('#seccion-cliente-fiado').slideUp();
            }

            if (metodoPago === 'transferencia') {
                $('#seccion-calculadora-cambio').slideUp();
                $('#btnProcesarVenta').prop('disabled', false);
                return;
            } else {
                $('#seccion-calculadora-cambio').slideDown();
            }

            let recibido = parseFloat($('#pago_recibido').val()) || 0;
            let cambio = recibido - totalAcumulado;

            if (recibido === 0) {
                $('#texto-cambio').text('$0').removeClass('text-danger').addClass('text-primary');
                $('#btnProcesarVenta').prop('disabled', true);
            } else if (cambio < 0) {
                $('#texto-cambio').text('Faltan $' + Math.abs(cambio).toLocaleString('es-CO')).removeClass('text-primary').addClass('text-danger');
                $('#btnProcesarVenta').prop('disabled', true);
            } else {
                $('#texto-cambio').text('$' + cambio.toLocaleString('es-CO')).removeClass('text-danger').addClass('text-primary');
                $('#btnProcesarVenta').prop('disabled', false);
            }
        }

        // Eventos para Cambio y Nombre de Deudor
        $('#pago_recibido').on('input keyup change', calcularCambio);
        $('#cliente_fiado').on('input keyup change', calcularCambio);

        $('input[name="metodo_pago"]').on('change', function() {
            calcularCambio();
        });

        // Botones rápidos de Billetes
        $('.btn-billete').on('click', function() {
            let valor = $(this).data('valor');
            if (valor === 'exacto') {
                $('#pago_recibido').val(totalAcumulado);
            } else {
                $('#pago_recibido').val(parseFloat(valor));
            }
            calcularCambio();
        });

        // 5. Modificar cantidades
        $(document).on('click', '.btn-sumar', function() {
            let idx = $(this).data('index');
            if (carrito[idx].cantidad + 1 > carrito[idx].stock) {
                Swal.fire('Stock Límite', 'No hay más unidades en stock.', 'info');
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
            carrito = [];
            $('#pago_recibido').val('');
            $('#cliente_fiado').val('');
            renderizarCarrito();
        });

        // 6. Procesar Venta AJAX
        $('#btnProcesarVenta').on('click', function() {
            let metodoPago = $('input[name="metodo_pago"]:checked').val();
            let pagoRecibido = parseFloat($('#pago_recibido').val()) || totalAcumulado;
            let cambio = pagoRecibido - totalAcumulado;
            let aplicaPromocion = $('#switchPromocion').is(':checked') ? 1 : 0;
            let clienteFiado = $('#cliente_fiado').val().trim();

            if (metodoPago === 'fiado' && clienteFiado === '') {
                Swal.fire('Atención', 'Por favor ingresa el nombre del cliente para registrar la deuda.', 'warning');
                return;
            }

            let mensajeModal = "Se registrará la venta y se descontarán las unidades del inventario.";
            if (metodoPago === 'efectivo' && cambio > 0) {
                mensajeModal += `<br><br><strong class="h5 text-primary">Entregar Cambio: $${cambio.toLocaleString('es-CO')}</strong>`;
            } else if (metodoPago === 'fiado') {
                mensajeModal += `<br><br><strong class="h5 text-warning">Deudor: ${clienteFiado}</strong><br><small class="text-muted">El dinero no se sumará a la caja del turno hasta que el cliente salde la cuenta.</small>`;
            }

            Swal.fire({
                title: metodoPago === 'fiado' ? '¿Registrar Venta Fiada?' : '¿Confirmar cobro?',
                html: mensajeModal,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('ventas.store') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            metodo_pago: metodoPago,
                            pago_efectivo: metodoPago === 'efectivo' ? pagoRecibido : 0,
                            cliente_fiado: clienteFiado,
                            aplica_promocion: aplicaPromocion,
                            productos: carrito.map(p => ({ id: p.id, cantidad: p.cantidad }))
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Venta Realizada!',
                                    html: metodoPago === 'efectivo' && cambio > 0 
                                          ? `Venta exitosa.<br><strong class="h4 text-success">Cambio: $${cambio.toLocaleString('es-CO')}</strong>` 
                                          : response.message,
                                    timer: 2500,
                                    showConfirmButton: true,
                                    confirmButtonText: 'Aceptar'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error al procesar la venta.';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@stop
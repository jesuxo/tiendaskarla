{{-- resources/views/productos-grupos/index.blade.php --}}
@extends('layouts.master')
@section('title')
    Asignación de Productos a Grupos de Descuento
@endsection
@section('css')
    <style>
        .drag-producto {
            cursor: all-scroll !important;
            padding: 10px;
            margin: 5px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .drag-producto:hover {
            background-color: #e9ecef;
            transform: scale(1.02);
            cursor: grab;
        }
        .drag-producto:active {
            cursor: grabbing;
        }
        .drag-producto.asignado {
            opacity: 0.5;
            background-color: #e9ecef;
            cursor: not-allowed;
            border-color: #28a745;
            position: relative;
        }
        .drag-producto.asignado:after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);

            color: white;
            font-weight: bold;
            white-space: nowrap;
            z-index: 10;
        }
        .drag-producto.asignado:hover {
            transform: none;
            cursor: not-allowed;
        }

        .grupo-dropzone {
            min-height: 300px;
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s;
        }
        .grupo-dropzone.drag-over {
            background-color: #e3f2fd;
            border-color: #2196f3;
        }
        .producto-item {
            padding: 10px;
            margin: 8px 0;
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-quitar-producto {
            color: #dc3545;
            cursor: pointer;
            font-size: 18px;
            text-decoration: none;
        }
        .btn-quitar-producto:hover {
            color: #c82333;
        }
        .grupo-card {
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }
        .grupo-header {
            background-color: #0072c5;
            color: white;
            padding: 12px 15px;
            cursor: pointer;
        }
        .grupo-header h6 {
            margin: 0;
            color: white;
            display: inline-block;
        }
        .grupo-content {
            padding: 15px;
            display: none;
        }
        .grupo-content.active {
            display: block;
        }
        .categoria-filtro {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .badge-info-producto {
            font-size: 10px;
            margin: 2px;
            display: inline-block;
        }
        .precio-usd-info {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .empty-message {
            text-align: center;
            color: #6c757d;
            padding: 20px;
        }
        kbd {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 12px;
            font-family: 'Courier New', monospace;
            font-weight: 600;
            box-shadow: inset 0 -1px 0 #dee2e6;
            display: inline-block;
        }
        .keyboard-hint {
            background-color: #f8f9fa;
            border-left: 3px solid #0072c5;
        }
        .keyboard-hint kbd {
            background-color: #fff;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            margin-left: 8px;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .producto-asignado-badge {
            display: inline-block;
            background-color: #28a745;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 3px;
            margin-left: 8px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6>Arrastra un producto a los grupos de descuento para asignarlo</h6>
                    <p class="text-muted mb-0">Los productos pueden estar en múltiples grupos con precios especiales</p>
                </div>
                <div class="card-body">
                    <div class="row" style="min-height: 600px;">
                        <div class="col-md-4">
                            <div class="categoria-filtro">
                                <div class="form-group mb-2">
                                    <label>🔍 Buscar producto:</label>
                                    <input type="text" id="buscar-producto" class="form-control"
                                           placeholder="Buscar por código, nombre, referencia, marca o categoría...">
                                </div>
                                <div class="form-group d-none">
                                    <label>📂 Filtrar por categoría:</label>
                                    <select id="filtro-categoria" class="form-control">
                                        <option value="">Todas las categorías</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->codinst }}">{{ $categoria->descrip }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mt-2 d-none">
                                    <label>🎯 Filtrar productos NO asignados a:</label>
                                    <select id="filtro-grupo-exclusion" class="form-control">
                                        <option value="">Todos los grupos (mostrar todos)</option>
                                    </select>
                                    <small class="text-muted">Muestra solo productos que aún NO están en el grupo seleccionado</small>
                                </div>
                                <button id="btn-buscar" class="btn btn-primary btn-sm btn-block">
                                    <i class="mdi mdi-magnify"></i> Buscar
                                </button>
                            </div>
                            <h5 class="mb-3">Productos Disponibles</h5>
                            <div id="productos-container" style="max-height: 500px; overflow-y: auto;">
                                <div class="text-center text-muted">
                                    <i class="mdi mdi-information-outline"></i>
                                    <p>Ingresa un término de búsqueda para encontrar productos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h5 class="mb-3">Grupos de Descuento</h5>
                            <div id="grupos-container" style="max-height: 600px; overflow-y: auto;">
                                <div class="text-center">Cargando grupos...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPrecio" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Configurar Precio Especial</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <div id="info-producto"></div>
                    <div class="form-group">
                        <label>💵 Tasa de Cambio (USD a COP)</label>
                        <input type="number" id="tasa-cambio" class="form-control" step="1" required>
                        <small class="text-muted">Tasa actual sugerida: <span id="tasa-sugerida"></span></small>
                    </div>
                    <div class="form-group">
                        <label>💰 Precio Final en Pesos Colombianos (COP)</label>
                        <input type="number" id="precio-final" class="form-control" step="100" required>
                        <small class="text-muted">
                            Precio USD: <span id="precio-usd"></span> × Tasa
                            <span id="descuento-indicador" class="text-success"></span>
                        </small>
                    </div>
                    <div id="calculo-precio" class="precio-usd-info"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmar-asignacion">Confirmar Asignación</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">

    <script>
        let productoSeleccionado = null;
        let grupoSeleccionado = null;
        let tasaActual = 3600;
        let timeoutBusqueda;
        let grupoSeleccionadoData = null;
        let productosAsignadosCache = {};

        $(document).ready(function() {
            cargarGrupos();

            $('#buscar-producto').on('keyup', function() {
                clearTimeout(timeoutBusqueda);
                timeoutBusqueda = setTimeout(() => {
                    buscarProductos();
                }, 500);
            });

            $('#filtro-categoria').on('change', function() {
                buscarProductos();
            });

            $('#btn-buscar').on('click', function() {
                buscarProductos();
            });
        });

        function cargarGrupos() {
            $.ajax({
                url: '{{ route("productos-grupos.grupos") }}',
                type: 'GET',
                success: function(grupos) {
                    window.gruposData = grupos;
                    var html = '';
                    var optionsHtml = '<option value="">Todos los grupos (mostrar todos)</option>';

                    if (grupos.length === 0) {
                        html = '<div class="empty-message">No hay grupos registrados</div>';
                    } else {
                        grupos.forEach(function(grupo) {
                            optionsHtml += `<option value="${grupo.id}">${grupo.nombre}</option>`;
                            html += `
                                <div class="grupo-card" data-grupo-id="${grupo.id}" data-porcentaje="${grupo.porcentaje_descuento || 0}">
                                    <div class="grupo-header" data-grupo-id="${grupo.id}">
                                        <h6>🏷️ ${grupo.nombre} ${grupo.porcentaje_descuento > 0 ? `<span class="badge badge-warning ml-2">${grupo.porcentaje_descuento}% OFF</span>` : ''}</h6>
                                        <i class="mdi mdi-chevron-down float-right"></i>
                                    </div>
                                    <div class="grupo-content" id="grupo-${grupo.id}">
                                        <div class="grupo-dropzone" data-grupo-id="${grupo.id}">
                                            <div id="productos-grupo-${grupo.id}">
                                                <div class="text-center"><small>Cargando productos...</small></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    $('#grupos-container').html(html);
                    $('#filtro-grupo-exclusion').html(optionsHtml);

                    $('#filtro-grupo-exclusion').on('change', function() {
                        buscarProductos();
                    });

                    $('.grupo-header').click(function() {
                        const grupoId = $(this).data('grupo-id');
                        $(this).next('.grupo-content').toggleClass('active');
                        $(this).find('i').toggleClass('mdi-chevron-down mdi-chevron-up');

                        if ($(this).next('.grupo-content').hasClass('active')) {
                            cargarProductosDelGrupo(grupoId);
                        }
                    });

                    $('.grupo-dropzone').each(function() {
                        const grupoId = $(this).data('grupo-id');
                        const grupoCard = $(`.grupo-card[data-grupo-id="${grupoId}"]`);
                        const porcentaje = grupoCard.data('porcentaje') || 0;

                        $(this).droppable({
                            accept: '.drag-producto:not(.asignado)',
                            drop: function(event, ui) {
                                const dragElement = $(ui.draggable);
                                if (dragElement.hasClass('asignado')) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Producto ya asignado',
                                        text: 'Este producto ya está asignado a este grupo',
                                        confirmButtonColor: '#0072c5'
                                    });
                                    return;
                                }

                                productoSeleccionado = {
                                    codprod: dragElement.data('codprod'),
                                    precioUsd: dragElement.data('precio-usd'),
                                    nombre: dragElement.find('div').first().text(),
                                    color: dragElement.find('div').first().text()
                                };
                                grupoSeleccionado = grupoId;
                                grupoSeleccionadoData = {
                                    id: grupoId,
                                    porcentaje: porcentaje,
                                    nombre: grupoCard.find('.grupo-header h6').text().replace(/[0-9%OFF]/g, '').trim()
                                };
                                mostrarModalPrecio();
                            },
                            over: function() {
                                $(this).addClass('drag-over');
                            },
                            out: function() {
                                $(this).removeClass('drag-over');
                            }
                        });
                    });
                }
            });
        }

        function buscarProductos() {
            const search = $('#buscar-producto').val();
            const categoriaId = $('#filtro-categoria').val();
            const grupoExclusion = $('#filtro-grupo-exclusion').val();

            if (!search && !categoriaId && !grupoExclusion) {
                $('#productos-container').html(`
            <div class="text-center text-muted">
                <i class="mdi mdi-information-outline"></i>
                <p>Ingresa un término de búsqueda o selecciona una categoría</p>
            </div>
        `);
                return;
            }

            $('#productos-container').html('<div class="text-center">Buscando productos...</div>');

            $.ajax({
                url: '{{ route("productos-grupos.productos") }}',
                type: 'GET',
                data: {
                    search: search,
                    categoria_id: categoriaId,
                    grupo_id: grupoExclusion
                },
                success: function(productos) {
                    if (grupoExclusion && grupoExclusion !== '') {
                        verificarYRenderizarProductos(productos, grupoExclusion);
                    } else {
                        renderizarProductos(productos, null);
                    }
                },
                error: function() {
                    $('#productos-container').html('<div class="alert alert-danger">Error al buscar productos</div>');
                }
            });
        }

        function verificarYRenderizarProductos(productos, grupoId) {
            renderizarProductos(productos, grupoId);
        }

        function renderizarProductos(productos, grupoIdFiltro) {
            var html = '';
            if (!productos || productos.length === 0) {
                html = '<div class="empty-message">No se encontraron productos</div>';
            } else {
                let productosMostrados = 0;

                productos.forEach(function(producto) {
                    // Usar los datos que ya vienen del controlador
                    const yaAsignado = producto.ya_asignado === true;

                    // Si hay filtro de grupo y el producto ya está asignado, lo omitimos
                    if (grupoIdFiltro && yaAsignado) {
                        return;
                    }

                    productosMostrados++;

                    const claseAsignado = yaAsignado ? 'asignado' : '';
                    const badgeAsignado = yaAsignado ? '<span class="producto-asignado-badge">✓ Ya asignado</span>' : '';

                    let precioHtml = '';
                    let preciodatos = '';
                    if (yaAsignado && producto.precio_asignado) {
                        // Producto ya asignado - mostrar precio asignado y grupo
                        precioHtml = `
                    <div class="resultado-busqueda mt-1">
                        <small>
                            🇨🇴 <strong class="text-success">Precio asignado: ${Math.round(producto.precio_asignado).toLocaleString()} COP</strong>
                            ${producto.grupo_asignado_nombre ? `<br>📌 Grupo: ${producto.grupo_asignado_nombre}` : ''}
                        </small>
                    </div>
                `;
                    } else {
                        // Producto no asignado - mostrar precio original
                        preciodatos =`
                            ${producto.descrip2 ? `<small class="text-muted">${producto.descrip2}</small>` : ''}
                            ${producto.refere ? `<div><small class="text-muted">Ref: ${producto.refere}</small></div>` : ''}
                        `;
                        precioHtml = `
                            <div class="resultado-busqueda mt-1">
                                <small>
                                    💵 USD: ${parseFloat(producto.costod3).toFixed(2)} |
                                    🇨🇴 COP: ${Math.round(producto.precio_cop_estimado || 0).toLocaleString()}
                                </small>
                            </div>
                        `;
                    }

                    html += `
                <div class="drag-producto ${claseAsignado}" data-codprod="${producto.codprod}"
                     data-precio-usd="${producto.costod3}">
                    <strong>📦 ${producto.codprod}</strong>
                    ${badgeAsignado}
                    <div>${producto.descrip} - ${producto.color || ''}</div>
                    ${preciodatos}
                    ${precioHtml}
                </div>
            `;
                });

                if (productosMostrados === 0 && grupoIdFiltro) {
                    html = '<div class="empty-message">✅ Todos los productos ya están asignados a este grupo</div>';
                }
            }
            $('#productos-container').html(html);

            $('.drag-producto:not(.asignado)').draggable({
                revert: 'invalid',
                helper: 'clone',
                cursor: 'move',
                opacity: 0.6,
                zIndex: 100
            });
        }

        function cargarProductosDelGrupo(grupoId, page = 1) {
            const perPage = $('#select-per-page').val() || 20;

            $.ajax({
                url: `/productos-grupos/productos-por-grupo/${grupoId}`,
                type: 'GET',
                data: {
                    page: page,
                    per_page: perPage
                },
                success: function(data) {
                    $(`#productos-grupo-${grupoId}`).html(data);
                    $(`#productos-grupo-${grupoId} .pagination a`).click(function(e) {
                        e.preventDefault();
                        const url = $(this).attr('href');
                        const pageNum = new URL(url, window.location.href).searchParams.get('page');
                        cargarProductosDelGrupo(grupoId, pageNum);
                    });
                }
            });
        }

        function mostrarModalPrecio() {
            $.ajax({
                url: '{{ route("productos-grupos.tasa-actual") }}',
                type: 'GET',
                success: function(data) {
                    tasaActual = data.tasa;
                    $('#tasa-sugerida').text(tasaActual.toLocaleString());
                    $('#tasa-cambio').val(tasaActual);

                    const precioUsd = parseFloat(productoSeleccionado.precioUsd);
                    const precioBaseCop = precioUsd * tasaActual;
                    let porcentajeGrupo = grupoSeleccionadoData.porcentaje || 0;
                    let precioConDescuento = precioBaseCop;

                    if (porcentajeGrupo > 0) {
                        const descuentoAplicado = precioBaseCop * (porcentajeGrupo / 100);
                        precioConDescuento = precioBaseCop - descuentoAplicado;
                        precioConDescuento = Math.round(precioConDescuento);
                    } else {
                        precioConDescuento = Math.round(precioBaseCop);
                    }

                    $('#precio-usd').text(precioUsd.toFixed(2));
                    $('#precio-final').val(precioConDescuento);

                    $('#info-producto').html(`
                        <div class="alert alert-info">
                            <strong>📦 Producto:</strong> ${productoSeleccionado.nombre}<br>
                            <strong>💵 Precio USD:</strong> $${precioUsd.toFixed(2)}<br>
                            <strong>🔑 Código:</strong> ${productoSeleccionado.codprod}<br>
                            <strong>🏷️ Grupo:</strong> ${grupoSeleccionadoData.nombre} ${porcentajeGrupo > 0 ? `(${porcentajeGrupo}% OFF)` : ''}
                        </div>
                    `);

                    calcularPrecioConDescuento();
                    $('#precio-final').focus();
                    $('#modalPrecio').modal('show');
                }
            });
        }

        function calcularPrecioConDescuento() {
            const tasa = parseFloat($('#tasa-cambio').val()) || 0;
            const precioUsd = parseFloat(productoSeleccionado.precioUsd);
            const precioBase = precioUsd * tasa;
            const porcentajeGrupo = grupoSeleccionadoData.porcentaje || 0;

            let precioFinal = precioBase;
            let calculoHtml = '';

            if (porcentajeGrupo > 0) {
                const descuento = precioBase * (porcentajeGrupo / 100);
                precioFinal = precioBase - descuento;
                calculoHtml = `
                    <div class="alert alert-secondary mb-0">
                        <strong>📊 Cálculo con descuento:</strong><br>
                        <small>
                            ${precioUsd.toFixed(2)} USD × ${tasa.toLocaleString()} COP = ${Math.round(precioBase).toLocaleString()} COP<br>
                            - ${porcentajeGrupo}% de descuento = -${Math.round(descuento).toLocaleString()} COP<br>
                            <strong>= ${Math.round(precioFinal).toLocaleString()} COP</strong>
                        </small>
                    </div>
                `;
            } else {
                calculoHtml = `
                    <div class="alert alert-secondary mb-0">
                        <strong>📊 Cálculo automático:</strong><br>
                        <small>
                            ${precioUsd.toFixed(2)} USD × ${tasa.toLocaleString()} COP =
                            <strong>${Math.round(precioFinal).toLocaleString()} COP</strong>
                        </small>
                    </div>
                `;
            }

            $('#calculo-precio').html(calculoHtml);
            $('#precio-final').val(Math.round(precioFinal));
        }

        $('#tasa-cambio').on('input', function() {
            calcularPrecioConDescuento();
        });

        $('#precio-final').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                confirmarAsignacion();
            }
        });

        function confirmarAsignacion() {
            const precioFinal = parseFloat($('#precio-final').val());
            const tasaCambio = parseFloat($('#tasa-cambio').val());

            if (!precioFinal || precioFinal <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Precio inválido',
                    text: 'Por favor ingrese un precio válido',
                    confirmButtonColor: '#0072c5'
                });
                $('#precio-final').focus();
                return;
            }

            if (!tasaCambio || tasaCambio <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tasa inválida',
                    text: 'Por favor ingrese una tasa de cambio válida',
                    confirmButtonColor: '#0072c5'
                });
                $('#tasa-cambio').focus();
                return;
            }

            $('#confirmar-asignacion').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Asignando...');

            $.ajax({
                url: '{{ route("productos-grupos.asignar") }}',
                type: 'POST',
                data: {
                    codprod: productoSeleccionado.codprod,
                    grupo_id: grupoSeleccionado,
                    precio_final: precioFinal,
                    tasa_cambio: tasaCambio,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#modalPrecio').modal('hide');
                        cargarProductosDelGrupo(grupoSeleccionado);
                        buscarProductos();

                        Swal.fire({
                            icon: 'success',
                            title: '¡Asignado!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        productoSeleccionado = null;
                        grupoSeleccionado = null;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonColor: '#0072c5'
                        });
                    }
                },
                error: function(xhr) {
                    let mensaje = 'Error al asignar el producto';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        mensaje = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: mensaje,
                        confirmButtonColor: '#0072c5'
                    });
                },
                complete: function() {
                    $('#confirmar-asignacion').prop('disabled', false).html('Confirmar Asignación');
                }
            });
        }

        $('#confirmar-asignacion').click(function() {
            confirmarAsignacion();
        });

        $(document).on('keydown', function(e) {
            if (e.ctrlKey && e.which === 13 && $('#modalPrecio').hasClass('show')) {
                e.preventDefault();
                confirmarAsignacion();
            }
        });

        function quitarProducto(codprod, grupoId) {
            Swal.fire({
                title: '¿Está seguro?',
                text: "¿Desea quitar este producto del grupo?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, quitar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("productos-grupos.quitar") }}',
                        type: 'POST',
                        data: {
                            codprod: codprod,
                            grupo_id: grupoId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                cargarProductosDelGrupo(grupoId);
                                buscarProductos();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Quitado',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        }
                    });
                }
            });
        }

        function vaciarGrupo(grupoId) {
            const grupoNombre = $(`#grupo-${grupoId}`).closest('.grupo-card').find('.grupo-header h6').text();

            Swal.fire({
                title: '⚠️ ¿Vaciar grupo completo?',
                html: `Esta acción eliminará <strong>TODOS</strong> los productos del grupo "${grupoNombre}".<br><br>Esta operación no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, vaciar grupo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/productos-grupos/vaciar-grupo/${grupoId}`,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                cargarProductosDelGrupo(grupoId);
                                buscarProductos();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Grupo vaciado',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection

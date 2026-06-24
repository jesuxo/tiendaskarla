@extends('layouts.master')
@section('title')
    Actualizaci&oacute;n de producto
@endsection
@section('css')
    <style>
        .grupo-descuento-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            transition: all 0.3s;
            background-color: #fff;
        }
        .grupo-descuento-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .grupo-descuento-card.asignado {
            border-left: 4px solid #28a745;
            background-color: #f8fff8;
        }
        .grupo-descuento-card.no-asignado {
            border-left: 4px solid #dee2e6;
            opacity: 0.85;
        }
        .btn-asignar-grupo {
            min-width: 100px;
        }
        .precio-asignado {
            font-size: 14px;
            font-weight: bold;
            color: #28a745;
        }
        .badge-grupo-asignado {
            background-color: #28a745;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
        }
        .badge-grupo-no-asignado {
            background-color: #6c757d;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
        }
        .loading-grupos {
            text-align: center;
            padding: 20px;
        }
        .spinner-sm {
            width: 1.5rem;
            height: 1.5rem;
            border-width: 0.2em;
        }
        .fecha-asignacion {
            font-size: 10px;
            color: #6c757d;
        }
        .tasa-asignacion {
            font-size: 11px;
            color: #6c757d;
        }
    </style>
@endsection

@section('content')
    <x-breadcrumb title="Modificacion de producto" pagetitle="Productos" />
    <form id="editproduct-form" autocomplete="off" class="needs-validation" method="post"
          novalidate action="{{route('productos.update',$id)}}">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-xl-9 col-lg-8">
                <!-- Información del producto (existente) -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">Informaci&oacute;n</h5>
                                <p class="text-muted mb-0">Ingrese/Modifique los datos del producto.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div>
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <label class="form-label">Instancia de inventario</label>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="/instancias" class="float-end text-decoration-underline">+1 Instancia</a>
                                </div>
                            </div>
                            <div>
                                <select onchange="$('.error-msg').hide();" class="form-select" data-choices required id="choices-category-input" name="codinst">
                                    @foreach($instancias as $instancia)
                                        <option {{($instancia->codinst == $producto->codinst)?'selected':''}} style="margin-left: {{($instancia->nivel-1) * 14}}px !important;" value="{{$instancia->codinst}}">{!! $instancia->label !!}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-msg mt-1">Por favor, seleccione una instancia del inventario para clasificar este producto.</div>
                        </div>
                    </div>
                </div>

                <!-- Datos del producto (existente) -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" id="invalidcodprod" for="codprod">C&oacute;digo</label>
                                    <input type="text" class="form-control" id="codprod" name="codprod" disabled value="{{$producto->codprod}}" placeholder="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="refere">Referencia</label>
                                    <input type="text" class="form-control" id="refere" name="refere" value="{{$producto->refere}}" placeholder="Ej: C&oacute;digo Barra">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="descrip">Nombre del producto</label>
                            <input type="hidden" class="form-control" id="formAction" name="formAction" value="edit">
                            <input type="hidden" class="form-control" id="isadmin" name="isadmin" value="{{(Auth::user() and auth()->user()->type == 'admin')? 1: 0}}">
                            <input type="text" class="form-control" id="descrip" value="{{$producto->descrip}}" placeholder="Descripcion principal" name="descrip" required>
                            <div class="invalid-feedback">Por favor, ingrese el nombre/descripci&oacute;n del producto</div>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip2" name="descrip2" value="{{$producto->descrip2}}" placeholder="Descripci&oacute;n 2">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip3" name="descrip3" value="{{$producto->descrip3}}" placeholder="Descripci&oacute;n 3">
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="marca">Marca</label>
                                    <div class="position-relative">
                                        <input type="text"
                                               class="form-control"
                                               id="marca"
                                               name="marca"
                                               value="{{ $producto->marca }}"
                                               placeholder="Escribe para buscar marcas..."
                                               autocomplete="off">
                                        <div id="marca-sugerencias" class="list-group position-absolute w-100"
                                             style="display: none; max-height: 200px; overflow-y: auto; z-index: 1000;
                        background: white; border: 1px solid #ddd; border-radius: 0 0 4px 4px;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-search"></i> Escribe y selecciona una marca existente
                                    </small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="color">Color</label>
                                    <input type="text" class="form-control" id="color" value="{{$producto->color}}" name="color" placeholder="Ej: NEGRO">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="unidad">Unidad de medida</label>
                                    <input type="text" class="form-control" id="unidad" name="unidad" value="{{$producto->unidad}}" placeholder="Ej: Kg">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @if(Auth::user() and auth()->user()->type == 'admin')
                                <div class="col-lg-6">
                                    <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                        <input type="checkbox" class="form-check-input" id="esexento" name="esexento" {{($producto->esexento)?'checked':''}} value="1">
                                        <label class="form-check-label" for="esexento">Este producto es Exento?</label>
                                    </div>
                                </div>
                            @endif
                            <div class="col-lg-6">
                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="exdecimal" name="exdecimal" {{($producto->exdecimal)?'checked':''}} value="1">
                                    <label class="form-check-label" for="exdecimal">Uso de decimales para este producto?</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- SECCIÓN: GRUPOS DE DESCUENTO                 -->
                <!-- ============================================ -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle bg-success text-white fs-20">
                                        <i class="bi bi-tags"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">Grupos de Descuento</h5>
                                <p class="text-muted mb-0">Asigne este producto a grupos de descuento con precios especiales</p>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="cargarGruposDescuento()">
                                    <i class="bi bi-arrow-repeat"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="grupos-descuento-container">
                            <div class="loading-grupos">
                                <div class="spinner-border spinner-sm text-success" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <span class="ms-2">Cargando grupos de descuento...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FIN SECCIÓN GRUPOS DE DESCUENTO -->
                <!-- ============================================ -->

                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-success w-sm">Modificar</button>
                </div>
            </div>
            <!-- end col -->

            <!-- Columna derecha (existente) -->
            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Condici&oacute;n</h5>
                    </div>
                    <div class="card-body">
                        <select class="form-select" id="choices-publish-visibility-input" data-choices data-choices-search-false name="activo">
                            <option value="1" {{( $producto->activo)?'selected':''}}>Activo</option>
                            <option value="0" {{(!$producto->activo)?'selected':''}}>Inactivo</option>
                        </select>
                    </div>
                </div>

                @if(Auth::user() and auth()->user()->type == 'admin')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-3">Informaci&oacute;n adicional</h5>
                        </div>
                        <div class="card-body">
                            <label class="form-label" for="preciod">Costo</label>
                            <input type="text" class="form-control" id="preciod" value="{{$producto->preciod}}" name="preciod" placeholder="Ej: 15" required>

                            <label class="form-label" for="costod">Precio1</label>
                            <input type="text" class="form-control" id="costod" value="{{$producto->costod}}" name="costod" placeholder="Ej: 18">

                            <label class="form-label" for="costod2">Precio2</label>
                            <input type="text" class="form-control" id="costod2" value="{{$producto->costod2}}" name="costod2" placeholder="Ej: 20">

                            <label class="form-label" for="costod3">Precio3</label>
                            <input type="text" class="form-control" id="costod3" value="{{$producto->costod3}}" name="costod3" placeholder="Ej: 22.5" required>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-2">Opcional</p>
                        <textarea class="form-control" name="observaciones" placeholder="Ej: solo vender en condiciones especificas" rows="3">{{$producto->observaciones}}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal para asignar precio especial -->
    <div class="modal fade" id="modalPrecioGrupo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Configurar Precio Especial</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <div id="info-producto-grupo"></div>
                    <div class="form-group">
                        <label>💵 Tasa de Cambio (USD a COP)</label>
                        <input type="number" id="tasa-cambio-grupo" class="form-control" step="1" required>
                        <small class="text-muted">Tasa actual sugerida: <span id="tasa-sugerida-grupo"></span></small>
                    </div>
                    <div class="form-group">
                        <label>💰 Precio Final en Pesos Colombianos (COP)</label>
                        <input type="number" id="precio-final-grupo" class="form-control" step="100" required>
                        <small class="text-muted">
                            Precio USD: <span id="precio-usd-grupo"></span> × Tasa
                            <span id="descuento-indicador-grupo" class="text-success"></span>
                        </small>
                    </div>
                    <div id="calculo-precio-grupo" class="precio-usd-info"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmar-asignacion-grupo">Confirmar Asignación</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/libs/dropzone/dropzone-min.js') }}"></script>
    <script src="{{ URL::asset('build/js/backend/edit-product.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        let gruposData = [];
        let asignacionesData = {};
        let productoActual = {
            codprod: '{{ $producto->codprod }}',
            precioUsd: {{ $producto->costod3 ?? 0 }},
            nombre: '{{ addslashes($producto->descrip) }}'
        };
        let grupoSeleccionadoId = null;
        let grupoSeleccionadoData = null;
        let tasaActual = 3600;

        $(document).ready(function() {
            cargarDatosIniciales();
        });

        function cargarDatosIniciales() {
            $('#grupos-descuento-container').html(`
            <div class="loading-grupos">
                <div class="spinner-border spinner-sm text-success" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <span class="ms-2">Cargando grupos de descuento...</span>
            </div>
        `);

            // 1. Obtener todos los grupos
            $.ajax({
                url: '{{ route("productos-grupos.grupos") }}',
                type: 'GET',
                success: function(grupos) {
                    gruposData = grupos;
                    cargarAsignacionesProducto();
                },
                error: function() {
                    $('#grupos-descuento-container').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        Error al cargar los grupos de descuento.
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="cargarDatosIniciales()">
                            Reintentar
                        </button>
                    </div>
                `);
                }
            });
        }

        function cargarAsignacionesProducto() {
            const codprod = productoActual.codprod;

            // Usar URL directa en lugar de route() para evitar problemas con parámetros
            $.ajax({
                url: '/productos-grupos/asignaciones-producto/' + codprod,
                type: 'GET',
                success: function(response) {
                    asignacionesData = {};
                    if (response.asignaciones && response.asignaciones.length > 0) {
                        response.asignaciones.forEach(function(asig) {
                            asignacionesData[asig.grupo_id] = {
                                asignado: true,
                                precio: asig.precio_final,
                                tasa_cambio: asig.tasa_cambio_usd,
                                fecha: asig.created_at,
                                grupo_nombre: asig.grupo_nombre,
                                porcentaje: asig.porcentaje_descuento
                            };
                        });
                    }
                    renderizarGrupos();
                },
                error: function(xhr, status, error) {
                    console.error('Error cargando asignaciones:', error);
                    asignacionesData = {};
                    renderizarGrupos();
                }
            });
        }

        function cargarGruposDescuento() {
            cargarDatosIniciales();
        }

        function renderizarGrupos() {
            const container = $('#grupos-descuento-container');

            if (!gruposData || gruposData.length === 0) {
                container.html(`
                <div class="text-center text-muted py-4">
                    <i class="bi bi-tags fs-1 d-block"></i>
                    <p class="mt-2">No hay grupos de descuento registrados</p>
                    <small>Contacte al administrador para crear grupos</small>
                </div>
            `);
                return;
            }

            let html = '<div class="row">';
            let asignadosCount = 0;

            gruposData.forEach(function(grupo) {
                const asignacion = asignacionesData[grupo.id] || null;
                const estaAsignado = asignacion !== null;

                if (estaAsignado) asignadosCount++;

                const claseCard = estaAsignado ? 'asignado' : 'no-asignado';
                const badgeStatus = estaAsignado
                    ? '<span class="badge-grupo-asignado"><i class="bi bi-check-circle"></i> Asignado</span>'
                    : '<span class="badge-grupo-no-asignado"><i class="bi bi-circle"></i> No asignado</span>';

                let precioHTML = '';
                if (estaAsignado && asignacion.precio) {
                    precioHTML = `
                    <div class="mt-2">
                        <span class="precio-asignado">
                            <i class="bi bi-coin"></i> $${Number(asignacion.precio).toLocaleString()} COP
                        </span>
                        ${asignacion.tasa_cambio ? `<div class="tasa-asignacion">💱 Tasa: $${Number(asignacion.tasa_cambio).toLocaleString()}</div>` : ''}
                        ${asignacion.fecha ? `<div class="fecha-asignacion">📅 ${new Date(asignacion.fecha).toLocaleDateString('es-CO')}</div>` : ''}
                    </div>
                `;
                }

                const botonAccion = estaAsignado
                    ? `<button type="button" class="btn btn-sm btn-danger btn-asignar-grupo" onclick="quitarProductoGrupo(${grupo.id})">
                    <i class="bi bi-x-circle"></i> Quitar
                   </button>`
                    : `<button type="button" class="btn btn-sm btn-success btn-asignar-grupo" onclick="asignarProductoGrupo(${grupo.id})">
                    <i class="bi bi-plus-circle"></i> Asignar
                   </button>`;

                const porcentajeHTML = grupo.porcentaje_descuento > 0
                    ? `<span class="badge bg-warning text-dark ms-2">${grupo.porcentaje_descuento}% OFF</span>`
                    : '';

                html += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="grupo-descuento-card ${claseCard}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-0">
                                    🏷️ ${grupo.nombre}
                                    ${porcentajeHTML}
                                </h6>
                                <small class="text-muted">ID: ${grupo.id}</small>
                            </div>
                            ${badgeStatus}
                        </div>
                        ${precioHTML}
                        <div class="mt-2 d-flex gap-2">
                            ${botonAccion}
                        </div>
                    </div>
                </div>
            `;
            });

            html += '</div>';

            // Mostrar resumen
            const totalGrupos = gruposData.length;
            html = `
            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-info-circle"></i>
                <strong>Resumen:</strong> ${asignadosCount} de ${totalGrupos} grupos asignados.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            ${html}
        `;

            container.html(html);
        }

        function asignarProductoGrupo(grupoId) {
            const grupo = gruposData.find(g => g.id === grupoId);
            if (!grupo) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Grupo no encontrado'
                });
                return;
            }

            grupoSeleccionadoId = grupoId;
            grupoSeleccionadoData = grupo;

            $.ajax({
                url: '{{ route("productos-grupos.tasa-actual") }}',
                type: 'GET',
                success: function(data) {
                    tasaActual = data.tasa;
                    mostrarModalPrecioGrupo();
                },
                error: function() {
                    tasaActual = 3600;
                    mostrarModalPrecioGrupo();
                }
            });
        }

        function mostrarModalPrecioGrupo() {
            const precioUsd = parseFloat(productoActual.precioUsd) || 0;
            const tasa = tasaActual || 3600;
            const precioBase = precioUsd * tasa;
            const porcentajeGrupo = grupoSeleccionadoData.porcentaje_descuento || 0;

            let precioConDescuento = precioBase;
            if (porcentajeGrupo > 0) {
                const descuento = precioBase * (porcentajeGrupo / 100);
                precioConDescuento = precioBase - descuento;
                precioConDescuento = Math.round(precioConDescuento);
            } else {
                precioConDescuento = Math.round(precioBase);
            }

            $('#tasa-sugerida-grupo').text(tasa.toLocaleString());
            $('#tasa-cambio-grupo').val(tasa);
            $('#precio-usd-grupo').text(precioUsd.toFixed(2));
            $('#precio-final-grupo').val(precioConDescuento);

            const descuentoTexto = porcentajeGrupo > 0
                ? `<span class="text-success">(${porcentajeGrupo}% descuento aplicado)</span>`
                : '';

            $('#info-producto-grupo').html(`
            <div class="alert alert-info">
                <strong>📦 Producto:</strong> ${productoActual.nombre}<br>
                <strong>💵 Precio USD:</strong> $${precioUsd.toFixed(2)}<br>
                <strong>🔑 Código:</strong> ${productoActual.codprod}<br>
                <strong>🏷️ Grupo:</strong> ${grupoSeleccionadoData.nombre} ${descuentoTexto}
            </div>
        `);

            calcularPrecioGrupo();
            $('#modalPrecioGrupo').modal('show');
        }

        function calcularPrecioGrupo() {
            const tasa = parseFloat($('#tasa-cambio-grupo').val()) || 0;
            const precioUsd = parseFloat(productoActual.precioUsd) || 0;
            const precioBase = precioUsd * tasa;
            const porcentajeGrupo = grupoSeleccionadoData.porcentaje_descuento || 0;

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

            $('#calculo-precio-grupo').html(calculoHtml);
            $('#precio-final-grupo').val(Math.round(precioFinal));
        }

        $('#tasa-cambio-grupo').on('input', function() {
            calcularPrecioGrupo();
        });

        $('#precio-final-grupo').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                confirmarAsignacionGrupo();
            }
        });

        $('#confirmar-asignacion-grupo').click(function() {
            confirmarAsignacionGrupo();
        });

        function confirmarAsignacionGrupo() {
            const precioFinal = parseFloat($('#precio-final-grupo').val());
            const tasaCambio = parseFloat($('#tasa-cambio-grupo').val());

            if (!precioFinal || precioFinal <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Precio inválido',
                    text: 'Por favor ingrese un precio válido',
                    confirmButtonColor: '#0072c5'
                });
                $('#precio-final-grupo').focus();
                return;
            }

            if (!tasaCambio || tasaCambio <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tasa inválida',
                    text: 'Por favor ingrese una tasa de cambio válida',
                    confirmButtonColor: '#0072c5'
                });
                $('#tasa-cambio-grupo').focus();
                return;
            }

            $('#confirmar-asignacion-grupo').prop('disabled', true).html('<i class="bi bi-arrow-repeat bi-spin"></i> Asignando...');

            $.ajax({
                url: '{{ route("productos-grupos.asignar") }}',
                type: 'POST',
                data: {
                    codprod: productoActual.codprod,
                    grupo_id: grupoSeleccionadoId,
                    precio_final: precioFinal,
                    tasa_cambio: tasaCambio,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#modalPrecioGrupo').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: '¡Asignado!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => {
                            cargarAsignacionesProducto();
                        }, 500);
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
                    $('#confirmar-asignacion-grupo').prop('disabled', false).html('Confirmar Asignación');
                }
            });
        }

        function quitarProductoGrupo(grupoId) {
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
                            codprod: productoActual.codprod,
                            grupo_id: grupoId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Quitado',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                setTimeout(() => {
                                    cargarAsignacionesProducto();
                                }, 500);
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al quitar el producto del grupo',
                                confirmButtonColor: '#0072c5'
                            });
                        }
                    });
                }
            });
        }

        $(document).on('keydown', function(e) {
            if (e.ctrlKey && e.which === 13 && $('#modalPrecioGrupo').hasClass('show')) {
                e.preventDefault();
                confirmarAsignacionGrupo();
            }
        });


        document.addEventListener('DOMContentLoaded', function() {
            const inputMarca = document.getElementById('marca');
            const sugerenciasContainer = document.getElementById('marca-sugerencias');

            let timeoutId = null;
            let marcasCache = [];
            let selectedIndex = -1;
            let isNavigating = false;

            // Función para mostrar sugerencias
            function mostrarSugerencias(marcas, query) {
                sugerenciasContainer.innerHTML = '';
                selectedIndex = -1;

                if (!marcas || marcas.length === 0) {
                    if (query && query.length > 0) {
                        // Si no hay coincidencias, mostrar opción para crear nueva
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action text-success';
                        item.innerHTML = `<i class="bi bi-plus-circle"></i> Crear "${query}"`;
                        item.addEventListener('click', function() {
                            inputMarca.value = query;
                            ocultarSugerencias();
                        });
                        sugerenciasContainer.appendChild(item);
                        sugerenciasContainer.style.display = 'block';
                    } else {
                        sugerenciasContainer.style.display = 'none';
                    }
                    return;
                }

                // Mostrar marcas coincidentes
                marcas.forEach(function(marca, index) {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = marca;
                    item.dataset.index = index;

                    // Resaltar coincidencia
                    if (query && query.length > 0) {
                        const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                        item.innerHTML = marca.replace(regex, '<strong style="color: #007bff;">$1</strong>');
                    }

                    item.addEventListener('mouseenter', function() {
                        selectedIndex = parseInt(this.dataset.index);
                        actualizarSeleccion();
                    });

                    item.addEventListener('click', function() {
                        inputMarca.value = marca;
                        ocultarSugerencias();
                        inputMarca.focus();
                    });

                    sugerenciasContainer.appendChild(item);
                });

                sugerenciasContainer.style.display = 'block';
            }

            function ocultarSugerencias() {
                sugerenciasContainer.style.display = 'none';
                selectedIndex = -1;
                isNavigating = false;
            }

            function actualizarSeleccion() {
                const items = sugerenciasContainer.querySelectorAll('.list-group-item-action');
                items.forEach(function(item, index) {
                    if (index === selectedIndex) {
                        item.classList.add('active');
                        item.style.backgroundColor = '#e8f0fe';
                        item.style.borderColor = '#007bff';
                        // Scroll al elemento seleccionado
                        item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    } else {
                        item.classList.remove('active');
                        item.style.backgroundColor = '';
                        item.style.borderColor = '';
                    }
                });
            }

            // Evento input - búsqueda en tiempo real
            inputMarca.addEventListener('input', function() {
                const query = this.value.trim();

                clearTimeout(timeoutId);

                if (query.length === 0) {
                    ocultarSugerencias();
                    return;
                }

                if (query.length < 1) {
                    sugerenciasContainer.style.display = 'none';
                    return;
                }

                // Buscar en caché primero
                const coincidencias = marcasCache.filter(function(marca) {
                    return marca.toLowerCase().includes(query.toLowerCase());
                });

                if (coincidencias.length > 0 && coincidencias.length <= 10) {
                    mostrarSugerencias(coincidencias, query);
                    return;
                }

                // Si no hay suficientes en caché, buscar en servidor
                timeoutId = setTimeout(function() {
                    fetch(`{{ route('saprod.marcas') }}?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            marcasCache = data;
                            mostrarSugerencias(data, query);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }, 300);
            });

            // Evento keydown - navegación con teclado
            inputMarca.addEventListener('keydown', function(e) {
                const items = sugerenciasContainer.querySelectorAll('.list-group-item-action');

                if (items.length === 0) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    isNavigating = true;
                    selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                    actualizarSeleccion();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    isNavigating = true;
                    selectedIndex = Math.max(selectedIndex - 1, 0);
                    actualizarSeleccion();
                } else if (e.key === 'Enter') {
                    if (selectedIndex >= 0 && selectedIndex < items.length) {
                        e.preventDefault();
                        const item = items[selectedIndex];
                        const texto = item.textContent.trim();
                        // Limpiar si tiene el ícono de crear
                        const valor = texto.replace('✚ Crear', '').trim();
                        inputMarca.value = valor || texto;
                        ocultarSugerencias();
                        inputMarca.focus();
                    }
                } else if (e.key === 'Escape') {
                    ocultarSugerencias();
                    inputMarca.blur();
                }
            });

            // Evento blur - ocultar sugerencias al salir del campo
            inputMarca.addEventListener('blur', function() {
                setTimeout(function() {
                    ocultarSugerencias();
                }, 200);
            });

            // Evento focus - mostrar sugerencias al hacer focus
            inputMarca.addEventListener('focus', function() {
                const query = this.value.trim();
                if (query.length >= 2) {
                    // Disparar búsqueda
                    this.dispatchEvent(new Event('input'));
                } else {
                    // Mostrar marcas populares si no hay texto
                    if (marcasCache.length === 0) {
                        fetch(`{{ route('saprod.marcas') }}?q=`)
                            .then(response => response.json())
                            .then(data => {
                                marcasCache = data;
                                if (data.length > 0) {
                                    mostrarSugerencias(data.slice(0, 10), '');
                                }
                            });
                    } else {
                        mostrarSugerencias(marcasCache.slice(0, 10), '');
                    }
                }
            });

            // Cargar marcas al inicio para caché
            fetch(`{{ route('saprod.marcas') }}?q=`)
                .then(response => response.json())
                .then(data => {
                    marcasCache = data;
                })
                .catch(error => {
                    console.error('Error cargando marcas:', error);
                });
        });

        // Función global para crear nueva marca desde cualquier lugar (opcional)
        function crearMarca(texto) {
            const input = document.getElementById('marca');
            if (input) {
                input.value = texto;
                document.getElementById('marca-sugerencias').style.display = 'none';
                input.focus();
            }
        }
    </script>
@endsection

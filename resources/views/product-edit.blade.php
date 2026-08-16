@extends('layouts.master')
@section('title')
    Actualización de producto
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

        /* Estilos para la galería de imágenes */
        .dropzone-wrapper {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .dropzone-wrapper:hover {
            border-color: #0d6efd;
            background-color: #e9ecef;
        }

        .dropzone-wrapper.dragover {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }

        .galeria-imagen .imagen-card {
            position: relative;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .galeria-imagen .imagen-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .galeria-imagen .imagen-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .galeria-imagen .imagen-card .badge-tipo {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 10px;
        }

        .galeria-imagen .imagen-card .acciones {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            padding: 8px;
            display: flex;
            justify-content: center;
            gap: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .galeria-imagen .imagen-card:hover .acciones {
            opacity: 1;
        }

        .galeria-imagen .imagen-card .acciones .btn {
            padding: 2px 8px;
            font-size: 12px;
        }

        /* Animación para la imagen del header */
        #imagenPrincipalPreview {
            transition: all 0.3s ease;
        }
        #imagenPrincipalPreview:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
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
                <!-- ============================================ -->
                <!-- INFORMACIÓN DEL PRODUCTO                      -->
                <!-- ============================================ -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">


                            <!-- Imagen del producto con tooltip -->
                            <div class="flex-shrink-0 me-3">
                                @php
                                    $imagenUrl = null;
                                    if($producto->imagenPrincipal) {
                                        $imagenUrl = asset($producto->imagenPrincipal->ruta);
                                    } else {
                                        $imagenUrl = asset('build/images/noimagen.jpg');
                                    }
                                @endphp
                                <img src="{{ $imagenUrl }}"
                                     alt="{{ $producto->descrip }}"
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid #e9ecef; cursor: pointer;"

                                     id="imagenPrincipalPreview">
                            </div>

                            <!-- Título y descripción -->
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">
                                    Información
                                    @if($producto->imagenPrincipal)
                                        <span class="badge bg-success ms-2">
                                            <i class="bi bi-image"></i> Con imagen
                                        </span>
                                    @else
                                        <span class="badge bg-warning ms-2">
                                            <i class="bi bi-exclamation-triangle"></i> Sin imagen
                                        </span>
                                    @endif
                                </h5>
                                <p class="text-muted mb-0">
                                    <strong>Código:</strong> {{ $producto->codprod }}
                                    @if($producto->marca)
                                        | <strong>Marca:</strong> {{ $producto->marca }}
                                    @endif
                                    @if($producto->color)
                                        | <span class="badge bg-secondary">{{ $producto->color }}</span>
                                    @endif
                                </p>
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

                <!-- ============================================ -->
                <!-- DATOS DEL PRODUCTO                            -->
                <!-- ============================================ -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" id="invalidcodprod" for="codprod">Código</label>
                                    <input type="text" class="form-control" id="codprod" name="codprod" disabled value="{{$producto->codprod}}" placeholder="">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="refere">Referencia</label>
                                    <input type="text" class="form-control" id="refere" name="refere" value="{{$producto->refere}}" placeholder="Ej: Código Barra">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="descrip">Nombre del producto</label>
                            <input type="hidden" class="form-control" id="formAction" name="formAction" value="edit">
                            <input type="hidden" class="form-control" id="isadmin" name="isadmin" value="{{(Auth::user() and auth()->user()->type == 'admin')? 1: 0}}">
                            <input type="text" class="form-control" id="descrip" value="{{$producto->descrip}}" placeholder="Descripcion principal" name="descrip" required>
                            <div class="invalid-feedback">Por favor, ingrese el nombre/descripción del producto</div>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip2" name="descrip2" value="{{$producto->descrip2}}" placeholder="Descripción 2">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip3" name="descrip3" value="{{$producto->descrip3}}" placeholder="Descripción 3">
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
                <!-- SECCIÓN: IMÁGENES DEL PRODUCTO                -->
                <!-- ============================================ -->
                <div class="card mt-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar-sm">
                                        <div class="avatar-title rounded-circle bg-info text-white fs-20">
                                            <i class="bi bi-images"></i>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="card-title mb-0">Imágenes del Producto</h5>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" id="btnAgregarImagenes">
                                <i class="bi bi-plus-circle"></i> Agregar Imágenes
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Drop zone para subir imágenes -->
                        <div class="dropzone-wrapper mb-3" id="dropzoneWrapper">
                            <div class="dropzone-area" id="dropzoneArea">
                                <div class="text-center">
                                    <i class="bi bi-cloud-upload" style="font-size: 48px;"></i>
                                    <h5>Arrastra y suelta imágenes aquí</h5>
                                    <p class="text-muted">o haz clic para seleccionar archivos</p>
                                    <p class="text-muted small">Formatos: JPG, PNG, GIF, WebP (max 5MB)</p>
                                    <input type="file" id="fileInput" multiple accept="image/*" style="display: none;">
                                </div>
                            </div>
                        </div>

                        <!-- Galería de imágenes -->
                        <div id="galeriaImagenes" class="row g-3">
                            <!-- Aquí se cargarán las imágenes vía JavaScript -->
                        </div>

                        <!-- Barra de progreso -->
                        <div id="progressBar" style="display: none;" class="mt-3">
                            <div class="progress">
                                <div id="progressBarInner" class="progress-bar progress-bar-striped progress-bar-animated"
                                     role="progressbar" style="width: 0%">0%</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FIN SECCIÓN IMÁGENES -->

                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-success w-sm">Modificar</button>
                </div>
            </div>
            <!-- end col -->

            <!-- ============================================ -->
            <!-- COLUMNA DERECHA                               -->
            <!-- ============================================ -->
            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Condición</h5>
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
                            <h5 class="card-title mb-3">Información adicional</h5>
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
        // ============================================
        // GRUPOS DE DESCUENTO
        // ============================================
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

        // ============================================
        // IMÁGENES
        // ============================================
        let cargandoImagenes = false;

        $(document).ready(function() {
            cargarDatosIniciales();
            cargarImagenes();
            inicializarTooltips();
        });

        // ============================================
        // FUNCIONES PARA TOOLTIPS
        // ============================================
        function inicializarTooltips() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    html: true,
                    placement: 'right'
                });
            });
        }

        // ============================================
        // FUNCIONES PARA ACTUALIZAR IMAGEN DEL HEADER
        // ============================================
        function actualizarImagenHeader(url) {
            const imgPreview = document.getElementById('imagenPrincipalPreview');
            if (imgPreview) {
                imgPreview.src = url;

                // Actualizar el tooltip
                if (imgPreview.getAttribute('data-bs-toggle') === 'tooltip') {
                    const tooltip = bootstrap.Tooltip.getInstance(imgPreview);
                    if (tooltip) {
                        tooltip.dispose();
                    }
                    imgPreview.setAttribute('title', `<img src='${url}' style='width: 250px; height: 250px; object-fit: cover; border-radius: 8px;'>`);
                    new bootstrap.Tooltip(imgPreview, {
                        html: true,
                        placement: 'right'
                    });
                }
            }

            // Actualizar el badge
            const cardTitle = document.querySelector('.card-header .card-title');
            if (cardTitle) {
                const hasImage = url.indexOf('noimagen.jpg') === -1;
                const badgeHtml = hasImage
                    ? '<span class="badge bg-success ms-2"><i class="bi bi-image"></i> Con imagen</span>'
                    : '<span class="badge bg-warning ms-2"><i class="bi bi-exclamation-triangle"></i> Sin imagen</span>';

                // Reemplazar el badge existente
                const existingBadge = cardTitle.querySelector('.badge');
                if (existingBadge) {
                    existingBadge.outerHTML = badgeHtml;
                } else {
                    cardTitle.innerHTML += ' ' + badgeHtml;
                }
            }
        }

        // ============================================
        // FUNCIONES DE GRUPOS DE DESCUENTO
        // ============================================
        function cargarDatosIniciales() {
            $('#grupos-descuento-container').html(`
                <div class="loading-grupos">
                    <div class="spinner-border spinner-sm text-success" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <span class="ms-2">Cargando grupos de descuento...</span>
                </div>
            `);

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

        // ============================================
        // FUNCIONES PARA IMÁGENES
        // ============================================

        // Configuración inicial para imágenes
        $(document).ready(function() {
            const dropzoneArea = $('#dropzoneArea');
            const fileInput = $('#fileInput');
            const galeria = $('#galeriaImagenes');
            const progressBar = $('#progressBar');
            const progressBarInner = $('#progressBarInner');

            // Eventos de dropzone
            dropzoneArea.on('click', function(e) {
                if (!$(e.target).closest('#fileInput').length) {
                    fileInput.click();
                }
            });

            dropzoneArea.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('dragover');
            });

            dropzoneArea.on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            });

            dropzoneArea.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    subirImagenes(files);
                }
            });

            // Manejar el cambio del input file
            fileInput.on('change', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (this.files && this.files.length > 0) {
                    const files = this.files;
                    const inputElement = this;
                    setTimeout(function() {
                        inputElement.value = '';
                    }, 10);
                    subirImagenes(files);
                }
            });

            $('#btnAgregarImagenes').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileInput.click();
            });

            // Event delegation para botones de imágenes
            $(document).on('click', '.set-principal', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (id) {
                    setPrincipal(id);
                }
            });

            $(document).on('click', '.set-thumbnail', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (id) {
                    setThumbnail(id);
                }
            });

            $(document).on('click', '.set-icono', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (id) {
                    setIcono(id);
                }
            });

            $(document).on('click', '.eliminar-imagen', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (id) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción no se puede deshacer",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            eliminarImagen(id);
                        }
                    });
                }
            });
        });

        function cargarImagenes() {
            const codprod = $('#codprod').val();
            const urlBase = '/productos-imagenes';

            if (cargandoImagenes) {
                return;
            }
            cargandoImagenes = true;

            $.ajax({
                url: urlBase + '/' + codprod,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        renderizarGaleria(response);

                        // Actualizar la imagen del header
                        if (response.imagenes) {
                            const principal = response.imagenes.find(img => img.tipo === 'principal' && img.activo === 1);
                            if (principal) {
                                actualizarImagenHeader('/' + principal.ruta);
                            } else {
                                actualizarImagenHeader('{{ asset('build/images/noimagen.jpg') }}');
                            }
                        }
                    }
                    cargandoImagenes = false;
                },
                error: function(xhr) {
                    console.error('Error al cargar imágenes:', xhr);
                    mostrarError('Error al cargar las imágenes');
                    cargandoImagenes = false;
                }
            });
        }

        function renderizarGaleria(data) {
            const galeria = $('#galeriaImagenes');
            const galeriaElement = galeria[0];

            if (galeriaElement) {
                galeriaElement.innerHTML = '';
            }

            if (!data.imagenes || data.imagenes.length === 0) {
                galeria.html(`
                    <div class="col-12 text-center text-muted py-4">
                        <i class="bi bi-image" style="font-size: 48px;"></i>
                        <p>No hay imágenes para este producto</p>
                    </div>
                `);
                return;
            }

            let html = '';
            data.imagenes.forEach(function(imagen) {
                const esPrincipal = imagen.tipo === 'principal' && imagen.activo === 1;
                const esThumbnail = imagen.tipo === 'thumbnail' && imagen.activo === 1;
                const esIcono = imagen.tipo === 'icono' && imagen.activo === 1;

                let badges = '';
                if (esPrincipal) badges += '<span class="badge bg-success me-1">Principal</span>';
                if (esThumbnail) badges += '<span class="badge bg-info me-1">Thumbnail</span>';
                if (esIcono) badges += '<span class="badge bg-warning me-1">Icono</span>';
                if (!esPrincipal && !esThumbnail && !esIcono && imagen.tipo === 'secundaria') {
                    badges += '<span class="badge bg-secondary me-1">Secundaria</span>';
                }

                let botones = '';

                if (!esPrincipal) {
                    botones += `<button class="btn btn-sm btn-success set-principal" data-id="${imagen.id}" title="Establecer como principal">
                        <i class="bi bi-star"></i>
                    </button>`;
                } else {
                    botones += `<button class="btn btn-sm btn-outline-success" disabled title="Ya es principal">
                        <i class="bi bi-star-fill"></i>
                    </button>`;
                }

                if (!esPrincipal && !esThumbnail) {
                    botones += `<button class="btn btn-sm btn-info set-thumbnail" data-id="${imagen.id}" title="Establecer como thumbnail">
                        <i class="bi bi-image"></i>
                    </button>`;
                } else if (esThumbnail) {
                    botones += `<button class="btn btn-sm btn-outline-info" disabled title="Ya es thumbnail">
                        <i class="bi bi-image-fill"></i>
                    </button>`;
                }

                if (!esPrincipal && !esIcono) {
                    botones += `<button class="btn btn-sm btn-warning set-icono" data-id="${imagen.id}" title="Establecer como icono">
                        <i class="bi bi-square"></i>
                    </button>`;
                } else if (esIcono) {
                    botones += `<button class="btn btn-sm btn-outline-warning" disabled title="Ya es icono">
                        <i class="bi bi-square-fill"></i>
                    </button>`;
                }

                botones += `<button class="btn btn-sm btn-danger eliminar-imagen" data-id="${imagen.id}" title="Eliminar">
                    <i class="bi bi-trash"></i>
                </button>`;

                html += `
                    <div class="col-md-3 col-sm-4 col-6 galeria-imagen">
                        <div class="imagen-card">
                            <img src="/${imagen.ruta}" alt="${imagen.nombre_original}" loading="lazy"
                                 onerror="this.src='{{ asset('images/no-image.png') }}'">
                            ${badges ? `<div class="badge-tipo">${badges}</div>` : ''}
                            <div class="acciones">
                                ${botones}
                            </div>
                            ${imagen.orden !== undefined ? `<small class="text-muted d-block text-center">Orden: ${imagen.orden}</small>` : ''}
                        </div>
                    </div>
                `;
            });

            galeria.html(html);

            // Actualizar el contador de imágenes
            const totalActivas = data.imagenes.filter(img => img.activo === 1).length;
            $('.card-header .card-title').each(function() {
                const text = $(this).text();
                if (text.includes('Imágenes del Producto')) {
                    $(this).text(`Imágenes del Producto (${totalActivas})`);
                }
            });
        }

        function subirImagenes(files) {
            const codprod = $('#codprod').val();
            const urlBase = '/productos-imagenes';

            if (files.length > 10) {
                mostrarError('Solo puedes subir máximo 10 imágenes a la vez');
                return;
            }

            const formData = new FormData();
            formData.append('codprod', codprod);

            let archivosValidos = 0;
            $.each(files, function(index, file) {
                if (file.size > 5 * 1024 * 1024) {
                    mostrarError(`El archivo ${file.name} excede el tamaño máximo de 5MB`);
                    return;
                }
                formData.append('imagenes[]', file);
                archivosValidos++;
            });

            if (archivosValidos === 0) {
                return;
            }

            $('#progressBar').show();
            $('#progressBarInner').css('width', '0%');
            $('#progressBarInner').text('0%');

            $.ajax({
                url: urlBase + '/upload-multiple',
                type: 'POST',
                data: formData,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: false,
                xhr: function() {
                    const xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            $('#progressBarInner').css('width', percent + '%');
                            $('#progressBarInner').text(percent + '%');
                        }
                    });
                    return xhr;
                },
                success: function(response) {
                    if (response.success) {
                        $('#progressBarInner').css('width', '100%');
                        $('#progressBarInner').text('100%');
                        setTimeout(() => {
                            $('#progressBar').hide();
                            $('#progressBarInner').css('width', '0%');
                        }, 1500);

                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        cargarImagenes();
                    } else {
                        $('#progressBar').hide();
                        if (response.errores && response.errores.length > 0) {
                            mostrarError('Errores: ' + response.errores.join(', '));
                        } else {
                            mostrarError('Error al subir las imágenes');
                        }
                    }
                },
                error: function(xhr) {
                    $('#progressBar').hide();
                    let mensaje = 'Error al subir las imágenes';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errores = Object.values(xhr.responseJSON.errors).flat();
                        mensaje = errores.join(', ');
                    }
                    mostrarError(mensaje);
                }
            });
        }

        function setPrincipal(id) {
            const urlBase = '/productos-imagenes';

            Swal.fire({
                title: 'Actualizando...',
                text: 'Estableciendo imagen como principal',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: urlBase + '/' + id + '/set-principal',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        cargarImagenes();
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    console.error('Error:', xhr);
                    let mensaje = 'Error al establecer como principal';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        mensaje = xhr.responseJSON.error;
                    }
                    mostrarError(mensaje);
                }
            });
        }

        function setThumbnail(id) {
            const urlBase = '/productos-imagenes';

            Swal.fire({
                title: 'Actualizando...',
                text: 'Estableciendo imagen como thumbnail',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: urlBase + '/' + id + '/set-thumbnail',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        cargarImagenes();
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    console.error('Error:', xhr);
                    let mensaje = 'Error al establecer como thumbnail';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        mensaje = xhr.responseJSON.error;
                    }
                    mostrarError(mensaje);
                }
            });
        }

        function setIcono(id) {
            const urlBase = '/productos-imagenes';

            Swal.fire({
                title: 'Actualizando...',
                text: 'Estableciendo imagen como icono',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: urlBase + '/' + id + '/set-icono',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        cargarImagenes();
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    console.error('Error:', xhr);
                    let mensaje = 'Error al establecer como icono';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        mensaje = xhr.responseJSON.error;
                    }
                    mostrarError(mensaje);
                }
            });
        }

        function eliminarImagen(id) {
            const urlBase = '/productos-imagenes';

            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: urlBase + '/' + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        cargarImagenes();
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    console.error('Error:', xhr);
                    let mensaje = 'Error al eliminar la imagen';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        mensaje = xhr.responseJSON.error;
                    }
                    mostrarError(mensaje);
                }
            });
        }

        function mostrarError(mensaje) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje,
                timer: 3000,
                showConfirmButton: true
            });
        }

        // ============================================
        // BÚSQUEDA DE MARCAS
        // ============================================

        document.addEventListener('DOMContentLoaded', function() {
            const inputMarca = document.getElementById('marca');
            const sugerenciasContainer = document.getElementById('marca-sugerencias');

            let timeoutId = null;
            let marcasCache = [];
            let selectedIndex = -1;
            let isNavigating = false;

            function mostrarSugerencias(marcas, query) {
                sugerenciasContainer.innerHTML = '';
                selectedIndex = -1;

                if (!marcas || marcas.length === 0) {
                    if (query && query.length > 0) {
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

                marcas.forEach(function(marca, index) {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = marca;
                    item.dataset.index = index;

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
                        item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    } else {
                        item.classList.remove('active');
                        item.style.backgroundColor = '';
                        item.style.borderColor = '';
                    }
                });
            }

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

                const coincidencias = marcasCache.filter(function(marca) {
                    return marca.toLowerCase().includes(query.toLowerCase());
                });

                if (coincidencias.length > 0 && coincidencias.length <= 10) {
                    mostrarSugerencias(coincidencias, query);
                    return;
                }

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

            inputMarca.addEventListener('blur', function() {
                setTimeout(function() {
                    ocultarSugerencias();
                }, 200);
            });

            inputMarca.addEventListener('focus', function() {
                const query = this.value.trim();
                if (query.length >= 2) {
                    this.dispatchEvent(new Event('input'));
                } else {
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

            fetch(`{{ route('saprod.marcas') }}?q=`)
                .then(response => response.json())
                .then(data => {
                    marcasCache = data;
                })
                .catch(error => {
                    console.error('Error cargando marcas:', error);
                });
        });
    </script>
@endsection

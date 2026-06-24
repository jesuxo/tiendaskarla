@extends('layouts.master')
@section('title')
    Crear nuevo producto
@endsection
@section('css')
    <!-- extra css -->
    <style>
        /* Estilos para el autocompletado de marcas */
        #marca-sugerencias {
            margin-top: 2px;
            border-top: none !important;
            border-radius: 0 0 4px 4px !important;
            max-height: 200px !important;
            overflow-y: auto !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
            z-index: 9999 !important;
        }

        #marca-sugerencias .list-group-item {
            padding: 8px 12px;
            border: none;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.15s ease;
            background: white;
        }

        #marca-sugerencias .list-group-item:last-child {
            border-bottom: none;
        }

        #marca-sugerencias .list-group-item:hover {
            background-color: #e8f0fe !important;
            color: #0056b3;
        }

        #marca-sugerencias .list-group-item.active {
            background-color: #007bff !important;
            color: white !important;
            border-color: #007bff;
        }

        #marca-sugerencias .list-group-item.text-success:hover {
            background-color: #d4edda !important;
        }

        #marca-sugerencias .list-group-item .highlight {
            color: #007bff;
            font-weight: 600;
        }

        #marca-sugerencias .list-group-item.active .highlight {
            color: white;
        }

        /* Estilo para el input cuando tiene sugerencias */
        #marca:focus {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        /* Scroll personalizado */
        #marca-sugerencias::-webkit-scrollbar {
            width: 6px;
        }

        #marca-sugerencias::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        #marca-sugerencias::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        #marca-sugerencias::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .position-relative {
            position: relative;
        }
    </style>

    <script>
        function verUltimoProd(codinst){
            $('#invalidcodprod').html('C&oacute;digo ');
            $('#codprod').val('');
            $('.datosprod').fadeOut();

            $.ajax({
                type: 'POST',
                url: '/sainsta/check/lastprod/'+codinst,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{ },
                success: function (data) {
                    lastprod = data.last;
                    if(lastprod) {
                        $('#codprod').val(lastprod);
                        $('.datosprod').fadeIn();
                    }
                }
            });
        }
    </script>
@endsection

@section('content')
    <x-breadcrumb title="Crear nuevo producto" pagetitle="Productos" />

    <form id="createproduct-form" autocomplete="off" class="needs-validation" method="post" novalidate action="{{route('productos.store')}}">
        @method('POST')
        @csrf
        <div class="row">
            <div class="col-xl-9 col-lg-8">
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
                                <p class="text-muted mb-0">Ingrese los datos del producto.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" onclick="$('.datosprod').fadeOut();">
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
                                <select onchange="$('.error-msg').hide(); verUltimoProd(this.value)"
                                        onclick="$('.datosprod').fadeOut();"
                                        class="form-select" data-choices required
                                        id="choices-category-input" name="codinst">
                                    <option value=""> Seleccionar </option>
                                    @foreach($instancias as $instancia)
                                        <option style="margin-left: {{($instancia->nivel-1) * 14}}px !important;" value="{{$instancia->codinst}}">{!! $instancia->label !!}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-msg mt-1">Por favor, seleccione una instancia del inventario para clasificar este producto.</div>
                        </div>
                    </div>
                </div>

                <div class="card datosprod" style="display: none">
                    <div class="card-body" style="background-color: #f3f4f4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" id="invalidcodprod" for="codprod">C&oacute;digo</label>
                                    <input type="text" class="form-control" id="codprod" maxlength="15" name="codprod"
                                           placeholder="" required value=""
                                           onclick="$('#invalidcodprod').html('C&oacute;digo');
                                                    $('#invalidcodprod').removeClass('text-danger');">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="refere">Referencia</label>
                                    <input type="text" class="form-control" id="refere" name="refere"
                                           placeholder="Ej: C&oacute;digo Barra">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="descrip">Nombre del producto</label>
                            <input type="hidden" class="form-control" id="formAction" name="formAction" value="add">
                            <input type="text" class="form-control" id="descrip" value=""
                                   placeholder="Descripcion principal" name="descrip" required>
                            <div class="invalid-feedback">Por favor, ingrese el nombre/descripci&oacute;n del producto</div>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip2" name="descrip2" value=""
                                   placeholder="Descripci&oacute;n 2">
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="descrip3" name="descrip3" value=""
                                   placeholder="Descripci&oacute;n 3">
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
                                    <label class="form-label" for="unidad">Unidad de medida</label>
                                    <input type="text" class="form-control" id="unidad" name="unidad"
                                           placeholder="Ej: UND">
                                </div>
                            </div>
                        </div>

                        <div class="row d-none" >
                            <div class="col-lg-6">
                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="esexento" name="esexento" value="1" checked>
                                    <label class="form-check-label" for="esexento">Este producto es Exento?</label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                                    <input type="checkbox" class="form-check-input" id="exdecimal" name="exdecimal" value="1">
                                    <label class="form-check-label" for="exdecimal">Uso de decimales para este producto?</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" style="display: none">
                    <div class="card-header">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                        <i class="bi bi-images"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">Product Gallery</h5>
                                <p class="text-muted mb-0">Add product gallery images.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="dropzone my-dropzone">
                            <div class="dz-message">
                                <div class="mb-3">
                                    <i class="display-4 text-muted ri-upload-cloud-2-fill"></i>
                                </div>
                                <h5>Drop files here or click to upload.</h5>
                            </div>
                        </div>
                        <div class="error-msg mt-1">Please add a product images.</div>
                    </div>
                </div>

                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-success w-sm">Enviar</button>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Condici&oacute;n</h5>
                    </div>
                    <div class="card-body">
                        <select class="form-select" id="choices-publish-visibility-input" data-choices data-choices-search-false name="activo">
                            <option value="1" selected>Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Colores</h5>
                    </div>
                    <div class="card-body">
                        <div>
                            <table id="items-table" class="tdline">
                                <thead>
                                <tr>
                                    <td height="31" align="center" class="tdline titulo">Color</td>
                                    <td align="center" class="actions tdline titulo">Acciones</td>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- Fila inicial -->
                                <tr data-index="0">
                                    <td>
                                        <input name="itemscolores[0][color]" class="form-control" required type="text" />
                                    </td>
                                    <td class="actions"></td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="">
                                <button type="button" id="addItem" class="btn btn-success w-sm" style="border-radius:5px; margin-bottom:10px;">Agregar +1</button>
                            </div>
                            <script>
                                let itemCount = 1;

                                function removeRow(index) {
                                    $(`tr[data-index="${index}"]`).remove();
                                }

                                $('#addItem').click(function() {
                                    const newIndex = itemCount;
                                    itemCount++;

                                    const newRow = `
                                        <tr data-index="${newIndex}">
                                            <td>
                                                <input name="itemscolores[${newIndex}][color]" class="form-control" required type="text" />
                                            </td>
                                            <td class="actions">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${newIndex})">Eliminar</button>
                                            </td>
                                        </tr>
                                    `;
                                    $('#items-table tbody').append(newRow);
                                });
                            </script>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="{{ URL::asset('build/js/backend/create-product.init.js') }}?version={{rand(0,500)}}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <!-- Script para autocompletado de marcas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputMarca = document.getElementById('marca');
            const sugerenciasContainer = document.getElementById('marca-sugerencias');

            if (!inputMarca || !sugerenciasContainer) return;

            let timeoutId = null;
            let marcasCache = [];
            let selectedIndex = -1;

            // Función para mostrar sugerencias
            function mostrarSugerencias(marcas, query) {
                sugerenciasContainer.innerHTML = '';
                selectedIndex = -1;

                if (!marcas || marcas.length === 0) {
                    if (query && query.length >= 2) {
                        // Si no hay coincidencias, mostrar opción para crear nueva
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action text-success';
                        item.innerHTML = `<i class="bi bi-plus-circle"></i> Crear "<strong>${query}</strong>"`;
                        item.addEventListener('click', function() {
                            inputMarca.value = query;
                            ocultarSugerencias();
                            inputMarca.focus();
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
                    item.dataset.index = index;

                    // Resaltar coincidencia
                    if (query && query.length > 0) {
                        const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                        item.innerHTML = marca.replace(regex, '<span class="highlight">$1</span>');
                    } else {
                        item.textContent = marca;
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
            }

            function actualizarSeleccion() {
                const items = sugerenciasContainer.querySelectorAll('.list-group-item-action');
                items.forEach(function(item, index) {
                    if (index === selectedIndex) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
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

                if (query.length < 2) {
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
                    selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                    actualizarSeleccion();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    selectedIndex = Math.max(selectedIndex - 1, 0);
                    actualizarSeleccion();
                } else if (e.key === 'Enter') {
                    if (selectedIndex >= 0 && selectedIndex < items.length) {
                        e.preventDefault();
                        const item = items[selectedIndex];
                        // Extraer el texto sin el ícono ni etiquetas HTML
                        const texto = item.textContent.trim();
                        const valor = texto.replace('✚ Crear', '').replace(/["]/g, '').trim();
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
    </script>
@endsection

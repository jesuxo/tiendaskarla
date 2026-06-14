{{-- resources/views/productos-grupos/partials/productos-por-grupo.blade.php --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <div class="mb-2 mb-sm-0">
        <span class="badge badge-primary">Total: {{ $productos->total() }} productos</span>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <select id="select-per-page-{{ $grupoId }}" class="form-control form-control-sm" style="width: auto;" onchange="cambiarPerPage({{ $grupoId }}, this.value)">
            <option value="10">10 por página</option>
            <option value="20" selected>20 por página</option>
            <option value="50">50 por página</option>
            <option value="100">100 por página</option>
            <option value="200">200 por página</option>
            <option value="500">500 por página</option>
        </select>
        <button type="button"
                onclick="vaciarGrupo({{ $grupoId }})"
                class="btn btn-danger btn-sm"
                title="Eliminar todos los productos de este grupo">
            <i class="mdi mdi-delete-empty"></i> Vaciar Grupo
        </button>
    </div>
</div>

<!-- Buscador dentro del grupo -->
<div class="mb-3">
    <div class="input-group input-group-sm">
        <span class="input-group-text bg-light border-end-0">
            <i class="mdi mdi-magnify"></i>
        </span>
        <input type="text"
               id="buscar-en-grupo-{{ $grupoId }}"
               class="form-control border-start-0"
               placeholder="🔍 Buscar productos en este grupo por código, nombre, referencia o marca..."
               autocomplete="off">
        <button class="btn btn-outline-secondary" type="button" onclick="limpiarBusqueda({{ $grupoId }})">
            <i class="mdi mdi-close"></i>
        </button>
    </div>
    <small class="text-muted" id="resultado-busqueda-{{ $grupoId }}"></small>
</div>

<!-- Indicador de carga -->
<div id="loading-grupo-{{ $grupoId }}" class="text-center py-3" style="display: none;">
    <div class="spinner-border spinner-border-sm text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
    <span class="ms-2">Cargando productos...</span>
</div>

<!-- Lista de productos -->
<div id="productos-lista-{{ $grupoId }}">
    @if($productos->count() > 0)
        <div class="productos-lista" style="max-height: 500px; overflow-y: auto;">
            @foreach($productos as $producto)
                <div class="producto-item" data-codprod="{{ $producto->codprod }}">
                    <div>
                        <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                            <strong>{{ $producto->codprod }}</strong>
                            @if($producto->marca)
                                <span class="badge badge-info">{{ $producto->marca }}</span>
                            @endif
                        </div>
                        <div class="mb-1">{{ $producto->descrip }}</div>
                        @if($producto->descrip2)
                            <div><small class="text-muted">{{ $producto->descrip2 }}</small></div>
                        @endif
                        @if($producto->instancia)
                            <div><small class="text-muted">📂 {{ $producto->instancia->descrip }}</small></div>
                        @endif
                        <div class="mt-2">
                            <small class="text-muted">
                                🇨🇴 <strong class="text-success">${{ number_format($producto->pivot->precio_final, 0) }} COP</strong>
                                <span class="mx-1">|</span>
                                💱 Tasa: ${{ number_format($producto->pivot->tasa_cambio_usd, 0) }}
                                <span class="mx-1">|</span>
                                📅 {{ \Carbon\Carbon::parse($producto->pivot->created_at)->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                    <div>
                        <a href="javascript:void(0)"
                           onclick="quitarProducto('{{ $producto->codprod }}', {{ $grupoId }})"
                           class="btn-quitar-producto"
                           title="Quitar producto del grupo">
                            <i class="mdi mdi-close-circle fs-5"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        @if($productos->hasPages())
            <div class="mt-3 d-flex justify-content-center">
                {{ $productos->appends(['per_page' => request('per_page', 20)])->links() }}
            </div>
        @endif
    @else
        <div class="empty-message text-center text-muted py-5">
            <i class="mdi mdi-inbox fs-1"></i>
            <p class="mt-2 mb-0">No hay productos asignados a este grupo</p>
            <small>Arrastra productos desde la columna izquierda para asignarlos</small>
        </div>
    @endif
</div>

<script>
    // Variables para controlar el timeout de búsqueda
    let timeoutBusqueda{{ $grupoId }};

    // Buscar productos dentro del grupo
    $('#buscar-en-grupo-{{ $grupoId }}').on('keyup', function() {
        clearTimeout(timeoutBusqueda{{ $grupoId }});
        const search = $(this).val();

        if (search.length === 0) {
            $('#resultado-busqueda-{{ $grupoId }}').html('');
            cargarProductosDelGrupo({{ $grupoId }});
            return;
        }

        if (search.length < 2) {
            $('#resultado-busqueda-{{ $grupoId }}').html('<span class="text-warning">Escribe al menos 2 caracteres para buscar</span>');
            return;
        }

        $('#resultado-busqueda-{{ $grupoId }}').html('<span class="text-info">Buscando...</span>');

        timeoutBusqueda{{ $grupoId }} = setTimeout(() => {
            buscarEnGrupo({{ $grupoId }}, search);
        }, 500);
    });

    function buscarEnGrupo(grupoId, search) {
        $('#loading-grupo-' + grupoId).show();
        $('#productos-lista-' + grupoId).hide();

        $.ajax({
            url: '/productos-grupos/buscar-en-grupo/' + grupoId,
            type: 'GET',
            data: { search: search },
            success: function(response) {
                $('#productos-lista-' + grupoId).html(response);
                $('#resultado-busqueda-' + grupoId).html('<span class="text-success">✓ Resultados encontrados</span>');

                // Actualizar el contador si hay elementos
                const count = $('#productos-lista-' + grupoId + ' .producto-item').length;
                if (count === 0) {
                    $('#resultado-busqueda-' + grupoId).html('<span class="text-warning">No se encontraron productos con: "' + search + '"</span>');
                } else {
                    $('#resultado-busqueda-' + grupoId).html('<span class="text-success">✓ ' + count + ' producto(s) encontrado(s)</span>');
                }
            },
            error: function() {
                $('#resultado-busqueda-' + grupoId).html('<span class="text-danger">Error en la búsqueda</span>');
            },
            complete: function() {
                $('#loading-grupo-' + grupoId).hide();
                $('#productos-lista-' + grupoId).show();
            }
        });
    }

    function limpiarBusqueda(grupoId) {
        $('#buscar-en-grupo-' + grupoId).val('');
        $('#resultado-busqueda-' + grupoId).html('');
        cargarProductosDelGrupo(grupoId);
    }

    function cambiarPerPage(grupoId, perPage) {
        $('#loading-grupo-' + grupoId).show();
        $('#productos-lista-' + grupoId).hide();

        $.ajax({
            url: '/productos-grupos/productos-por-grupo/' + grupoId,
            type: 'GET',
            data: {
                per_page: perPage
            },
            success: function(data) {
                $('#productos-grupo-' + grupoId).html(data);
            },
            complete: function() {
                $('#loading-grupo-' + grupoId).hide();
                $('#productos-lista-' + grupoId).show();
            }
        });
    }

    // Re-inicializar eventos de paginación
    $(document).on('click', '#productos-grupo-{{ $grupoId }} .pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const pageNum = new URL(url, window.location.href).searchParams.get('page');
        const perPage = $('#select-per-page-{{ $grupoId }}').val();

        $('#loading-grupo-{{ $grupoId }}').show();
        $('#productos-lista-{{ $grupoId }}').hide();

        $.ajax({
            url: '/productos-grupos/productos-por-grupo/{{ $grupoId }}',
            type: 'GET',
            data: {
                page: pageNum,
                per_page: perPage
            },
            success: function(data) {
                $('#productos-grupo-{{ $grupoId }}').html(data);
            },
            complete: function() {
                $('#loading-grupo-{{ $grupoId }}').hide();
                $('#productos-lista-{{ $grupoId }}').show();
            }
        });
    });
</script>

<style>
    .producto-item {
        padding: 12px;
        margin: 8px 0;
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
    }

    .producto-item:hover {
        background-color: #f8f9fa;
        border-color: #0072c5;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-quitar-producto {
        color: #dc3545;
        cursor: pointer;
        font-size: 18px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-quitar-producto:hover {
        color: #c82333;
        transform: scale(1.1);
    }

    .badge-info {
        background-color: #17a2b8;
        color: white;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 10px;
    }

    .badge-primary {
        background-color: #0072c5;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
    }

    .empty-message {
        text-align: center;
        color: #6c757d;
        padding: 40px 20px;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .flex-wrap {
        flex-wrap: wrap;
    }

    .productos-lista {
        scroll-behavior: smooth;
    }

    .productos-lista::-webkit-scrollbar {
        width: 6px;
    }

    .productos-lista::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .productos-lista::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .productos-lista::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>

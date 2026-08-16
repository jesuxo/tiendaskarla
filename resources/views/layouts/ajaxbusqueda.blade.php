<div class="table-responsive table-card" style="padding-bottom:20px; margin-top: 0px; ">
    <table class="table table-bordered table-centered align-middle table-nowrap mb-0" style="font-size: 13px;">
        <thead class="text-muted table-light">
        <tr>
            <th width="5%" class="text-center">Imagen</th>  <!-- NUEVA COLUMNA -->
            <th width="5%" class="text-center">Código</th>
            <th width="35%" class="text-center">Producto</th>  <!-- Ajusté el ancho -->
            <th width="8%" class="text-center">Costo</th>
            <th width="8%" class="text-center">Precio3</th>
            <th width="25%" class="text-center">Existencias por Sucursal</th>
            <th width="5%" class="text-center">Total</th>
        </tr>
        </thead>
        <tbody>
        @php $pros = 0; @endphp
        @foreach($productos as $producto)
            @php
                $pros++;
                $totalExistencias = 0;
                $existenciasHtml = [];

                // Obtener la URL de la imagen principal
                $imagenUrl = null;
                if($producto->imagenPrincipal) {
                    $imagenUrl = asset($producto->imagenPrincipal->ruta);
                } else {
                    $imagenUrl = asset('build/images/noimagen.jpg'); // Imagen por defecto
                }
            @endphp

            @if(isset($producto->existencias_por_sucursal) and count($producto->existencias_por_sucursal) > 0)
                @foreach($producto->existencias_por_sucursal as $array)
                    @php
                        $existencia = $array->existen ?? 0;
                        $totalExistencias += $existencia;

                        if($existencia > 0 and isset($array->deposito)) {
                            $color = 'primary';
                            $existenciasHtml[] = "<span class='badge bg-{$color} bg-opacity-10 text-{$color}' title='{$array->deposito->descrip}'>" .
                                                str_replace('SARA','',$array->deposito->descrip) . ": " . number_format($existencia, 0) .
                                                "</span>";
                        }
                    @endphp
                @endforeach
            @endif

            <tr>
                <!-- NUEVA COLUMNA DE IMAGEN -->
                <td class="text-center align-middle">
                    <img src="{{ $imagenUrl }}"
                         alt="{{ $producto->descrip }}"
                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #e9ecef;">
                </td>

                <td class="align-middle">
                    <a href="{{ route('productos.edit', $producto->id) }}" class="fw-medium link-primary">
                        {{ $producto->codprod }}
                    </a>
                </td>
                <td class="align-middle">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('productos.edit', $producto->id) }}" class="fw-medium link-primary">
                            {{ $producto->descrip }} - Color:
                            {{ $producto->color }}
                        </a>
                        <a href="{{ route('productos.edit', $producto->id) }}" class="ms-2">
                            <i class="bi-pencil-square text-primary"></i>
                        </a>
                    </div>
                </td>
                <td class="text-end align-middle">
                    ${{ number_format($producto->preciod, 2, ',', '.') }}
                </td>
                <td class="text-end align-middle">
                    ${{ number_format($producto->costod3, 2, ',', '.') }}
                </td>

                <!-- Existencias resumidas -->
                <td class="align-middle">
                    <div style="width: 100%; max-height: 60px; overflow: auto;">
                        @if(!empty($existenciasHtml))
                            <div style="display: flex; flex-wrap: wrap; gap: 3px;">
                                @foreach($existenciasHtml as $html)
                                    {!! $html !!}
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">Sin stock</span>
                        @endif
                    </div>
                </td>

                <td class="text-center align-middle fw-bold">
                    <span class="text-primary">
                        {{ number_format($totalExistencias, 0) }}
                    </span>
                </td>
            </tr>
        @endforeach

        @if($pros == 0)
            <tr>
                <td colspan="7" class="text-center py-4">  <!-- Cambié el colspan a 7 -->
                    <div class="text-muted">
                        <i class="bi bi-box-arrow-down fs-1 d-block mb-2"></i>
                        <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="bi-plus-circle me-1"></i> Crear nuevo producto
                        </a>
                    </div>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<style>
    .badge {
        font-size: 11px;
        padding: 4px 6px;
        border-radius: 12px;
        white-space: nowrap;
    }

    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }

    /* Estilo adicional para las imágenes en la tabla */
    .table img {
        transition: transform 0.2s ease;
    }

    .table img:hover {
        transform: scale(1.5);
        z-index: 100;
        position: relative;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
</style>

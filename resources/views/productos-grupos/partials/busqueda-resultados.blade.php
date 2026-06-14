{{-- resources/views/productos-grupos/partials/busqueda-resultados.blade.php --}}
@if($productos->count() > 0)
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
@else
    <div class="text-center text-muted py-4">
        <i class="mdi mdi-file-search-outline fs-1"></i>
        <p class="mt-2 mb-0">No se encontraron productos</p>
    </div>
@endif

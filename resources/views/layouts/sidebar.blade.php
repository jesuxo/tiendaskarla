<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="/dashboard" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="50">
            </span>
        </a>
        <a href=/dashboard" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="50">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar" style="background: #f2f2f2;"  >
        <div class="container-fluid" >

            <div id="two-column-menu">
            </div>

            <ul class="navbar-nav" id="navbar-nav" >

                <li class="menu-title">
                    <span data-key="t-menu">{{ __('t-menu') }}</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarPanel" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarPanel">
                        <i class="bi bi-speedometer2"></i> <span data-key="t-products">Panel</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarPanel">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item"  >
                                <a href="/" class="nav-link" data-key="t-create-product">Inicio</a>
                            </li>
                            <li class="nav-item"  >
                                <a href="/reporte/venta" class="nav-link" data-key="t-create-product">Reporte de ventas</a>
                            </li>
                            <li class="nav-item"  >
                                <a href="/reporte/instpagobs" class="nav-link" data-key="t-create-product">Rep. Inst Pago Bs</a>
                            </li>
                            <li class="nav-item"  >
                                <a href="/reporte/instpagodolares" class="nav-link" data-key="t-create-product">Rep. Inst Pago Dolares</a>
                            </li>
                            <li class="nav-item"  >
                                <a href="/ventas/productos/sucursales" class="nav-link" data-key="t-create-product">Venta por sucursal</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarProducts" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarProducts">
                        <i class="bi bi-box-seam"></i> <span data-key="t-products">Inventario</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarProducts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="/productos" class="nav-link" data-key="t-list-view">Productos</a>
                            </li>
                            <li class="nav-item">
                                <a href="/saprod/export" class="nav-link" data-key="t-list-view">Exportar Productos</a>
                            </li>
                            @if(Auth::user() and auth()->user()->type == 'admin')
                            <li class="nav-item">
                                <a href="/existencias" class="nav-link" data-key="t-list-view">Existencias</a>
                            </li>
                            @endif
                            <li class="nav-item">
                                <a href="/instancias" class="nav-link" data-key="t-sub-categories">{{ __('t-sub-categories') }}</a>
                            </li>
                            @if(Auth::user()  and auth()->user()->can('menu_productos_creardepositos') )
                                <li class="nav-item">
                                    <a href="/depositos" class="nav-link" data-key="t-list-view">Dep&oacute;sitos</a>
                                </li>
                            @endif

                            @if(Auth::user() and auth()->user()->can('menu_grupos_descuento'))
                                <li class="nav-item">
                                    <a class="nav-link menu-link" href="/productos-grupos">
                                        <i class="bi bi-tags"></i> <span data-key="t-grupos-descuento">Grupos de Descuento</span>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>

                <!-- Proveedores -->
                @if(Auth::user() and auth()->user()->can('menu_proveedores'))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarProveedores" data-bs-toggle="collapse"
                           role="button" aria-expanded="false" aria-controls="sidebarProveedores">
                            <i class="bi bi-truck"></i> <span data-key="t-suppliers">Proveedores</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarProveedores">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="/proveedores" class="nav-link" data-key="t-suppliers-list">
                                        <i class="bi bi-search me-2"></i>Buscar Proveedor
                                    </a>
                                </li>
                                @if(session('comercialid') == 6)
                                    <li class="nav-item ">
                                        <a href="{{ route('pagos-proveedores.index') }}" class="nav-link" data-key="t-list-view">
                                            <i class="ri-motorbike-fill me-2"></i>Motos
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                <li class="nav-item"  >
                    <a class="nav-link menu-link" href="#sidebarClientes" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarClientes">
                        <i class="bi bi-person-bounding-box"></i> <span data-key="t-orders">Clientes</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarClientes">
                        <ul class="nav nav-sm flex-column">

                            @if(Auth::user() and auth()->user()->type == 'admin')
                                <li class="nav-item">
                                    <a href="/clientes" class="nav-link" data-key="t-list-view">Listado Clientes</a>
                                </li>
                                <li class="nav-item">
                                    <a href="/cxc" class="nav-link" data-key="t-list-view">Cuentas x Cobrar</a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>
                @if(Auth::user() and auth()->user()->can('menu_vendedores'))
                <li class="nav-item">
                    <a class="nav-link menu-link" href="/vendedores"  >
                        <i class="bi bi-binoculars"></i> Vendedores
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link menu-link" href="/instpago"  >
                        <i class="bi bi-cash-coin"></i> Inst de Pago
                    </a>
                </li>
                @if(Route::is('dashboard') and Auth::user()  and auth()->user()->can('menu_changeTasa'))
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="javascript:;" data-bs-target="#changeTasa" data-bs-toggle="modal"  >
                            <i class="bi bi-layers"></i> <span data-key="t-components">Cambiar Tasa COP</span>
                            @if(isset($tasascambiadas) and $tasascambiadas == 1)
                                <span class="badge badge-pill bg-success small" data-key="t-v1.0"><i class="bi bi-check"></i></span>
                                @php
                                    session(['tasascambiadas'=>'']);
                                @endphp
                            @endif
                        </a>
                    </li>
                @endif

                @if(Auth::user()  and auth()->user()->can('menu_token') )
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="/tokens"   >
                            <i class="bi bi-key"></i> <span data-key="t-sellers">Tokens</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>

<div class="vertical-overlay"></div>


<div id="changeTasa" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" id="close-removecategoryModal" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="modal-body p-md-5">
                <div class="text-center">
                    <div class="mt-2 mb-2 fs-15">
                        <div class="d-flex" style="align-items: center;">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                        <i class="bi bi-layers"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="h4 mb-0"> Cambio de tasa de pesos x dolar</p>
                            </div>
                        </div>
                    </div>
                    <div>

                        <form action="/cambiotasas"  name="form22" id="form22"
                              onsubmit="return checkSubmitTasas()" method="POST"
                              autocomplete="off" class="needs-validation tasas-form"   novalidate>
                            @method('post')
                            @csrf

                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="card">
                                        <div class="card-header">
                                            <p class="text-muted mb-0">Ingrese ambas tasas las cuales son requeridas para hacer
                                                el cambio del c&aacute;lculo de precios en relaci&oacute;n al peso colombiano.</p>
                                        </div>
                                        <div class="card-body">
                                            <div class="row ">
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"  for="oldtasa">Tasa Actual</label>
                                                        @php
                                                            $comercialid = session('comercialid');
                                                            if(!$comercialid) {
                                                                session(['comercialid' => 1]);
                                                                $comercialid = 1;
                                                            }
                                                            $tasapeso = \App\Models\Sacomercial::find($comercialid);

                                                        @endphp
                                                        <input type="text" readonly class="form-control" id="oldtasa" maxlength="50" name="oldtasa"
                                                               placeholder="" required value="{{number_format($tasapeso->tasapeso,0,'.','')}}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="newtasa">Tasa nueva</label>
                                                        <input type="text" class="form-control" id="newtasa" name="newtasa"
                                                               placeholder="Ej: 3900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                                <button type="button" class="btn w-sm btn-light btn-hover"
                                        data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn w-sm btn-primary btn-hover" id="remove-category">
                                    Actualizar Tasa
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<script>
    function checkSubmitTasas() {
        var campos = [
            '#oldtasa', '#newtasa'
        ];

        var vacios = [];

        $(campos.join(',')).each(function() {
            if (!$(this).val().trim()) {
                vacios.push($(this).attr('name'));
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });

        if (vacios.length > 0) {
          //  alert('Complete los campos: ' + vacios.join(', '));
            return false;
        }

        return true;
    }

</script>

<style>
    .error {
        border: 2px solid red !important;
        background-color: #ffe6e6;
    }

    .error:focus {
        outline: none;
        border-color: #ff0000;
        box-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
    }
</style>

@extends('layouts.master')
@section('title')
    Inicio
@endsection
@section('css')
    <style>
        .botoncal{
            background: transparent;
            border: none;
            color: white;
        }
        .botoncal:hover{
            font-size: 13px;
        }
        .linkunderline:hover{
            text-decoration: underline;
        }

        /* Nuevos estilos para el dashboard mejorado */
        .dashboard-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .card-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .dashboard-card:hover .card-icon-wrapper {
            transform: scale(1.1);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 600;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            margin-bottom: 8px;
        }

        .trend-indicator {
            font-size: 0.8rem;
            padding: 3px 8px;
            border-radius: 20px;
            background: #e8f5e9;
            color: #2e7d32;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .trend-indicator.down {
            background: #ffebee;
            color: #c62828;
        }

        .quick-actions {
            background: #0072c5;
            color: white;
            border-radius: 4px;
            padding: 20px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .quick-action-btn {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .quick-action-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
            color: white;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            color: #667eea;
        }

        /* Colores personalizados para cada tarjeta */
        .bg-soft-primary { background-color: rgba(102, 126, 234, 0.1); color: #667eea; }
        .bg-soft-success { background-color: rgba(72, 187, 120, 0.1); color: #48bb78; }
        .bg-soft-warning { background-color: rgba(255, 159, 67, 0.1); color: #ff9f43; }
        .bg-soft-danger { background-color: rgba(245, 101, 101, 0.1); color: #f56565; }
        .bg-soft-info { background-color: rgba(66, 153, 225, 0.1); color: #4299e1; }
        .bg-soft-purple { background-color: rgba(159, 122, 234, 0.1); color: #9f7aea; }

        /* Estilos para el modal de documento */
        .modal-xl {
            max-width: 90%;
        }

        .modal-dialog-scrollable .modal-content {
            max-height: 90vh;
        }

        .modal-dialog-scrollable .modal-body {
            overflow-y: auto;
            padding: 0;
        }

        #documentView {
            padding: 20px;
            background: #f8f9fa;
            min-height: 400px;
        }

        /* Estilos para la impresión dentro del modal */
        @media print {
            .modal {
                position: absolute;
                left: 0;
                top: 0;
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100%;
            }

            .modal-dialog {
                margin: 0;
                width: 100%;
                max-width: 100%;
            }

            .modal-content {
                border: none;
                box-shadow: none;
            }

            .modal-header, .modal-footer {
                display: none;
            }

            .modal-body {
                padding: 0;
            }
        }
    </style>
@endsection
@section('content')
    <!-- Sección de Bienvenida -->
    <div class="row  ">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white border-0"  style="margin: 0px !important;">
                <div class="card-body  " style="padding: 0px !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">¡Bienvenido de nuevo, {{ Auth::user()->first_name ?? 'Usuario' }}!</h3>
                            <p class="mb-0 opacity-75">Aquí tienes un resumen de tu negocio</p>
                        </div>
                        <div class="text-end">
                            <div class="h4 mb-1">{{ now()->format('l, d F Y') }}</div>
                            <div class="opacity-75">{{ now()->format('h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 1: KPIs Principales -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card  ">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Total Ventas</div>
                            <a href="/resumenVentas" class="stretched-link text-decoration-none">
                                <div class="mt-3 text-muted small">
                                    Ver resumen completo <i class="ri-arrow-right-line"></i>
                                </div>
                            </a>
                        </div>
                        <div class="card-icon-wrapper bg-soft-primary">
                            <i class="ph-wallet fs-3 text-primary"></i>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card ">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Costo Inventario</div>
                            <a href="/existencias" class="stretched-link text-decoration-none"> <!--/existencias-->
                                <div class="mt-3 text-muted small">
                                    Ver reporte inventario <i class="ri-arrow-right-line"></i>
                                </div>
                            </a>
                        </div>
                        <div class="card-icon-wrapper bg-soft-success">
                            <i class="ph-sketch-logo fs-3 text-success"></i>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Cuentas x Cobrar</div>
                            <a href="/cxc" class="stretched-link text-decoration-none"> <!--/cxc-->
                                <div class="mt-3 text-muted small">
                                    Ver reporte cxc <i class="ri-arrow-right-line"></i>
                                </div>
                            </a>
                        </div>
                        <div class="card-icon-wrapper bg-soft-warning">
                            <i class="ph-currency-dollar-bold fs-3 text-warning"></i>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card dashboard-card ">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Unidades Vendidas</div>
                            <a href="/ventas/productos/sucursales" class="stretched-link text-decoration-none">
                                <div class="mt-3 text-muted small">
                                    Ver reporte <i class="ri-arrow-right-line"></i>
                                </div>
                            </a>
                        </div>
                        <div class="card-icon-wrapper bg-soft-danger">
                            <i class="bi-box fs-3 text-danger"></i>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Fila 2: Reportes y Módulos Especiales -->
    <div class="row g-4  ">
        <div class="col-lg-4">
            <div class="card dashboard-card  ">
                <div class="card-header bg-transparent border-0">
                    <h5 class="section-title mb-0">
                        <i class="ph-chart-line"></i>
                        Resultado General
                    </h5>
                </div>

                <div class="card-footer bg-transparent border-0">
                    <a href="/ventas/resultado" class="btn btn-outline-primary w-100">
                        Ver detalle completo
                    </a>
                </div>
            </div>
        </div>
        @if(session('comercialid') == 1)
            <div class="col-lg-4">
                <div class="card dashboard-card  ">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="section-title mb-0">
                            <i class="ri-cellphone-fill"></i>
                            Existencias de zapatos
                        </h5>
                    </div>

                    <div class="card-footer bg-transparent border-0">
                        <a href="#" class="btn btn-outline-warning w-100"><!--/existencia/celulares-->
                            ver existencias
                        </a>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-lg-4">
            <div class="card dashboard-card ">
                <div class="card-header bg-transparent border-0">
                    <h5 class="section-title mb-0">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                        Reporte de Compras
                    </h5>
                </div>

                @if(Auth::user() and auth()->user()->can('menu_compras'))
                    <div class="card-footer bg-transparent border-0">
                        <a href="/reporte/compra" class="btn btn-outline-secondary w-100">
                            Ver reporte completo
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row">
        <div class="col-12">
            <div class="quick-actions">
                <h5 class="mb-3 text-white">Acciones Rápidas</h5>
                <div class="d-flex flex-wrap gap-2">
                    @if(Auth::user() and auth()->user()->can('menu_transferencias'))
                        <a href="/transferencias/create" class="quick-action-btn">
                            <i class="ri-add-line"></i>Ingresar Transferencia
                        </a>
                    @endif

                    <a href="/reporte/instpagobs" class="quick-action-btn">
                        <i class="ri-file-chart-line"></i> Rep InstPago BS
                    </a>

                    <a href="/reporte/instpagodolares" class="quick-action-btn">
                        <i class="ri-file-chart-line"></i> Rep InstPago USD
                    </a>

                    <a href="/seriales/historial" class="quick-action-btn">
                        <i class="bi bi-list-ul"></i> Historial Seriales
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection

@extends('layouts.master')
@section('title')
    DASHBOARD DE VENTAS - ANÁLISIS COMPLETO
@endsection
@section('css')
    <style>
        .kpi-card {

            border-radius: 12px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .kpi-value { font-size: 28px; font-weight: bold; }
        .kpi-label { font-size: 14px; opacity: 0.9; }
        .trend-up { color: #28a745; }
        .trend-down { color: #dc3545; }
        .top-product {
            background: #f8f9fa;
            border-left: 4px solid #0072c5;
            margin-bottom: 8px;
            padding: 10px;
            border-radius: 4px;
        }
        .badge-ranking {
            background: #0072c5;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: inline-block;
            text-align: center;
            line-height: 24px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 8px;
        }
        .profit-positive { color: #28a745; font-weight: bold; }
        .profit-negative { color: #dc3545; font-weight: bold; }
        .chart-container {
            height: 300px;
            margin-bottom: 30px;
        }
        .analytic-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .analytic-card .card-header {
            background: white;
            border-bottom: 2px solid #0072c5;
            font-weight: bold;
        }

        .tdline{
            border:1px solid #0072c5 !important;
            font-size: 12px;
        }
        .tdlineff{
            border-left:1px solid #fff !important;
            font-size: 12px;
            color: white !important;
            background-color: #0072c5 !important;
        }
        .choices__list,.choices__item,.choices__item--selectable{
            font-size: 12px !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-bar-chart-steps me-2"></i>
                        ANALISIS DE VENTAS - INTELIGENCIA COMERCIAL
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Filtros (igual que antes) -->
                    <form method="post" name="form1" id="form1" action="/ventas/productos/sucursales">
                        <div class="row">
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Sucursal</label>
                                <select class="form-select" onChange="$('#form1').submit()" name="fksucursal">
                                    <option value="">Todas las Sucursales</option>
                                    @foreach($allsucursales as $sucu)
                                        <option value="{{$sucu->id}}" {{($sucu->id == $fksucursal)?'selected':''}}>
                                            {{ $sucu->descrip }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Categoría</label>
                                <select class="form-select" onChange="$('#form1').submit()" name="codinst">
                                    <option value="">Todas las Categorías</option>
                                    @foreach($instancias as $instancia)
                                        <option value="{{$instancia->codinst}}" {{($instancia->codinst == $codinst)?'selected':''}}>
                                            {{ str_repeat('--', $instancia->nivel-1) }} {{ $instancia->descrip }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Período Principal</label>
                                <input type="text" class="form-control" data-provider="flatpickr"
                                       data-range-date="true" data-date-format="d/m/Y"
                                       placeholder="Rango de fechas" name="fechasreport"
                                       value="{{$fechasreport}}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Período Comparativo</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" data-provider="flatpickr"
                                           data-range-date="true" data-date-format="d/m/Y"
                                           name="fechasreport2" value="{{$fechasreport2 ?? ''}}" placeholder="Opcional">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-chart-line"></i> ANALIZAR
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Mostrar existencias</label>
                                <select class="form-select" name="existenciaact">
                                    <option value="si" {{($existenciaact=='si')?'selected':''}}>Sí</option>
                                    <option value="no" {{($existenciaact=='no')?'selected':''}}>No</option>
                                </select>
                            </div>
                            @csrf @method('POST')
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(isset($fechasreport) && $fechasreport != '')
        <!-- KPI CARDS - MÉTRICAS CLAVE -->
        <div class="row">
            <div class="col-md-3">
                <div class="kpi-card bg-success">
                    <div class="kpi-value">{{ number_format($totalVendido ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-label">📦 TOTAL UNIDADES VENDIDAS</div>
                    <small>Promedio diario: {{ number_format($promedioDiario ?? 0, 0, ',', '.') }} und</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card bg-success"  >
                    <div class="kpi-value">{{ number_format($totalProductosVendidos ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-label">🏷️ PRDTS DIFERENTES VENDIDOS</div>
                    <small>Rotación de inventario</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card bg-success"  >
                    <div class="kpi-value">$ {{ number_format($totalVentasValor ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-label">💰 VALOR TOTAL VENDIDO</div>
                    <small>Ingreso bruto por ventas</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card bg-success"  >
                    <div class="kpi-value">$ {{ number_format($margenBruto ?? 0, 0, ',', '.') }}</div>
                    <div class="kpi-label">📈 MARGEN BRUTO</div>
                    <small>Ganancia: {{ number_format($margenPorcentaje ?? 0, 1) }}%</small>
                </div>
            </div>
        </div>

        <!-- GRÁFICOS DE TENDENCIA Y COMPARACIÓN -->
        <div class="row">
            <div class="col-md-6">
                <div class="card analytic-card">
                    <div class="card-header">
                        <i class="bi bi-graph-up me-2"></i> TOP 10 PRODUCTOS MÁS VENDIDOS
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="topProductsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card analytic-card">
                    <div class="card-header">
                        <i class="bi bi-pie-chart me-2"></i> VENTAS POR CATEGORÍA
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ANÁLISIS DE PRODUCTOS ESTRELLA Y BAJO RENDIMIENTO -->
        <div class="row">
            <div class="col-md-6">
                <div class="card analytic-card">
                    <div class="card-header text-success">
                        ⭐ PRODUCTOS ESTRELLA (Alta rotación)
                    </div>
                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                        @foreach($topProductos as $index => $prod)
                            <div class="top-product">
                                <span class="badge-ranking">{{ $index+1 }}</span>
                                <strong>{{ $prod['nombre'] ?? 'Producto sin nombre' }}</strong>
                                <div class="float-end">{{ number_format($prod['cantidad'] ?? 0, 0, ',', '.') }} und</div>
                                <br>
                                <small class="text-muted">
                                    {{ $prod['categoria'] ?? 'Sin categoría' }} |
                                    Participación: {{ number_format($prod['participacion'] ?? 0, 1) }}%
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card analytic-card">
                    <div class="card-header text-warning">
                        ⚠️ PRODUCTOS CON BAJA ROTACIÓN (Inventario lento)
                    </div>
                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                        @foreach($bottomProductos as $prod)
                            <div class="top-product">
                                <strong>{{ $prod['nombre'] ?? 'Producto sin nombre' }}</strong>
                                <div class="float-end">{{ number_format($prod['cantidad'] ?? 0, 0, ',', '.') }} und</div>
                                <br>
                                <small class="text-muted">
                                    Días sin ventas: {{ $prod['diasSinVenta'] ?? 'N/A' }}
                                </small>
                            </div>
                        @endforeach
                        @if(count($bottomProductos) == 0)
                            <p class="text-muted">No hay productos con baja rotación en este período</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- REPORTE DE COMPARACIÓN CON PERÍODO ANTERIOR -->
        @if(isset($fechasreport2) && $fechasreport2 != '')
            <div class="row">
                <div class="col-12">
                    <div class="card analytic-card">
                        <div class="card-header">
                            📊 ANÁLISIS COMPARATIVO: {{ $fecha1 ?? '' }} vs {{ $fecha2Comparativo ?? '' }}
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                    <tr class="bg-primary text-white">
                                        <th>Métrica</th>
                                        <th>Período Actual</th>
                                        <th>Período Comparativo</th>
                                        <th>Variación</th>
                                        <th>Tendencia</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Unidades Vendidas</td>
                                        <td class="text-end">{{ number_format($totalVendido ?? 0, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($totalVendidoComparativo ?? 0, 0, ',', '.') }}</td>
                                        <td class="text-end {{ ($variacionUnidades ?? 0) > 0 ? 'profit-positive' : 'profit-negative' }}">
                                            {{ number_format($variacionUnidades ?? 0, 0, ',', '.') }}
                                            ({{ number_format($porcentajeVariacionUnidades ?? 0, 1) }}%)
                                        </td>
                                        <td class="text-center">
                                            @if(($variacionUnidades ?? 0) > 0)
                                                <i class="bi bi-arrow-up-circle-fill trend-up" style="font-size: 20px;"></i>
                                            @else
                                                <i class="bi bi-arrow-down-circle-fill trend-down" style="font-size: 20px;"></i>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Valor de Ventas</td>
                                        <td class="text-end">$ {{ number_format($valorTotalVentas ?? 0, 0, ',', '.') }}</td>
                                        <td class="text-end">$ {{ number_format($valorTotalVentasComparativo ?? 0, 0, ',', '.') }}</td>
                                        <td class="text-end">$ {{ number_format($variacionValor ?? 0, 0, ',', '.') }}
                                            ({{ number_format($porcentajeVariacionValor ?? 0, 1) }}%)
                                        </td>
                                        <td class="text-center">
                                            @if(($variacionValor ?? 0) > 0)
                                                <i class="bi bi-arrow-up-circle-fill trend-up" style="font-size: 20px;"></i>
                                            @else
                                                <i class="bi bi-arrow-down-circle-fill trend-down" style="font-size: 20px;"></i>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Productos Diferentes</td>
                                        <td class="text-end">{{ number_format($totalProductosVendidos ?? 0, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($totalProductosVendidosComparativo ?? 0, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($variacionProductos ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            @if(($variacionProductos ?? 0) > 0)
                                                <i class="bi bi-arrow-up-circle-fill trend-up"></i>
                                            @else
                                                <i class="bi bi-arrow-down-circle-fill trend-down"></i>
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- TABLA DETALLADA (la original que ya tenías) -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-table me-2"></i> DETALLE DE VENTAS POR SUCURSAL
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-card mt-3">
                            <table  border="0" width="100%"  style="border-radius: 5px !important;" class="table table-borderless table-centered align-middle table-nowrap mb-0 mt-3" >

                                @php  $ttt = 0; $tttc = 0; $tttsuc=[];$tttsucc=[]; $n = $ttexisten = 0; @endphp
                                @foreach($itemventas as $index => $item)

                                    @php   $vectorsuc = [];  $vectorsucc = [];  $subttexisten = 0; @endphp
                                    <tr bgcolor="#f5f5f5">
                                        <td align="center" height="30px" class="tdline" colspan="2"> Producto</td>

                                        @foreach($sucursales as $indexs  => $vals)
                                            <td align="center" class="tdlineff titulo"   >
                                                {{$vals}}
                                            </td>
                                        @endforeach
                                        @if(count($sucursales)>1)
                                            <td align="center" class=" tdline"  > </td>
                                            <td align="center" class="tdline"   >Totales        </td>
                                        @endif
                                        @if(isset($fechasreport2) and $fechasreport2 != '')
                                            <td align="center" class="tdline"  > </td>
                                            <td align="center" class="tdline"   >Comparaci&oacute;n        </td>
                                        @endif

                                        @if($existenciaact == 'si')
                                            <td align="center" class="tdline"  > </td>
                                            <td align="center" class="tdlineff"  >ExAct</td>
                                        @endif
                                    </tr>

                                    @foreach($item as $index2 => $productos)
                                        @php  $totalline  = 0;
                            $exdecimal = $productos['exdecimal'];
                                        @endphp
                                        <tr @php if(($n%2)!=0){  echo 'bgcolor="#eeeeee"';} @endphp>
                                            <td align="left" class="tdline" colspan="2">{{ $productos['descrip'] }}</td>

                                            @foreach($sucursales as $indexs => $vals)
                                                @php
                                                    $key = $index2.$indexs;
                                                    $totalline += (isset($cantidadprod[$key]))?$cantidadprod[$key]:0;
                                                    $cantsuc = (isset($cantidadprod[$key]))?$cantidadprod[$key]:0;
                                                    if(!isset($vectorsuc[$indexs]))
                                                            $vectorsuc[$indexs] = 0;
                                                    $vectorsuc[$indexs] += $cantsuc;
                                                @endphp
                                                <td align="center" class="tdline  " style="font-size:11px"  >
                                                    @if(isset($cantsuc) and $cantsuc>0)
                                                        {{($exdecimal)? number_format($cantsuc,3,',','.'): number_format($cantsuc,0,',','.')}}
                                                    @endif
                                                </td>
                                            @endforeach
                                            @if(count($sucursales)>1)
                                                <td align="right" class="tdline" style="font-size:11px" > </td>
                                                <td align="center" class="tdline" style="font-size:11px" >
                                                    @if(isset($totalline) and $totalline>0)
                                                        {{($exdecimal)? number_format($totalline,3,',','.'): number_format($totalline,0,',','.')}}
                                                    @endif
                                                </td>
                                            @endif
                                            @if(isset($fechasreport2) and $fechasreport2 != '' and isset($indexs))
                                                <td align="center" class="tdline"  > </td>
                                                <td align="center" class="tdline"  style="font-size:11px"  >
                                                    @php
                                                        $key = $index2;
                                                        $cantsucc = (isset($cantidadprod2[$key]))?$cantidadprod2[$key]:0;
                                                        if(!isset($vectorsucc[$indexs]))
                                                                $vectorsucc[$indexs] = 0;
                                                        $vectorsucc[$indexs] += $cantsucc;
                                                    @endphp
                                                    {{($exdecimal)? number_format($cantsucc,3,',','.'): number_format($cantsucc,0,',','.')}}
                                                </td>
                                            @endif
                                            @if($existenciaact == 'si')
                                                @php
                                                    $ttexisten    +=  (isset($productos['existen']))?$productos['existen']:0;
                                                    $subttexisten +=  (isset($productos['existen']))?$productos['existen']:0;
                                                @endphp
                                                <td align="center" class="tdline"  > </td>
                                                <td align="center" class="tdline"  >{{ (isset($productos['existen']))?$productos['existen']:'--'}}</td>
                                            @endif
                                        </tr>
                                        @php    $n++; @endphp
                                    @endforeach
                                    <tr @php if(($n%2)!=0){  echo 'bgcolor="#eeeeee"';} @endphp>
                                        <td align="left" class="tdline" colspan="2">Total {{ $index }}</td>
                                        @php  $totalline  = 0; @endphp
                                        @foreach($sucursales as $indexs => $vals)
                                            <td  align="center" class="tdline titulo"  >
                                                @if(isset($vectorsuc[$indexs]) and $vectorsuc[$indexs]>0)
                                                    {{ $vectorsuc[$indexs]  }}
                                                    @php
                                                        if(!isset($tttsuc[$indexs]))
                                                                $tttsuc[$indexs] =0;
                                                        $tttsuc[$indexs] +=$vectorsuc[$indexs];
                                                        $totalline+=$vectorsuc[$indexs]; $ttt+=$vectorsuc[$indexs];
                                                    @endphp
                                                @endif
                                            </td>
                                        @endforeach
                                        @if(count($sucursales)>1)
                                            <td align="right" class="tdline"> </td>
                                            <td align="center" class="tdline">{{$totalline +0 }} </td>
                                        @endif
                                        @if(isset($fechasreport2) and $fechasreport2 != '' and isset($indexs))
                                            <td align="center" class=" tdline"  > </td>
                                            <td align="center" class="tdline"   >
                                                @php
                                                    if(!isset($tttsucc[$indexs]))
                                                            $tttsucc[$indexs] =0;
                                                    $tttsucc[$indexs] += $vectorsucc[$indexs];
                                                    $tttc += $vectorsucc[$indexs];
                                                @endphp
                                                {{ $vectorsucc[$indexs] +0}}
                                            </td>
                                        @endif
                                        @if($existenciaact == 'si')
                                            <td align="center" class="tdline"  > </td>
                                            <td align="center" class="tdline"  >{{$subttexisten}}</td>
                                        @endif
                                    </tr>
                                    <tr >
                                        <td align="left"  colspan="2"> </td>

                                        @foreach($sucursales as $indexs => $vals)
                                            <td align="center" class="  titulo"  >
                                                &nbsp;
                                            </td>
                                        @endforeach
                                        @if(count($sucursales)>1)
                                            <td align="center" > </td>
                                            <td align="center" > </td>
                                        @endif
                                        @if(isset($fechasreport2) and $fechasreport2 != '')
                                            <td align="center" class="  "  > </td>
                                            <td align="center" class=" "   >         </td>
                                        @endif
                                        @if($existenciaact == 'si')
                                            <td align="center" class=" "  > </td>
                                            <td align="center" class=" "  > </td>
                                        @endif
                                    </tr>

                                @endforeach

                                <tr bgcolor="#f5f5f5">
                                    <td align="center" height="30px" class="tdline" colspan="2">  </td>

                                    @foreach($sucursales as $indexs  => $vals)
                                        <td align="center" class="tdlineff titulo"   >
                                            {{$vals}}
                                        </td>
                                    @endforeach
                                    @if(count($sucursales)>1)
                                        <td align="center" class="tdline"  > </td>
                                        <td align="center" class="tdline"   >Totales        </td>
                                    @endif
                                    @if(isset($fechasreport2) and $fechasreport2 != '' and isset($indexs))
                                        <td align="center" class="tdline "  > </td>
                                        <td align="center" class="tdline"   >Total Comparado        </td>
                                    @endif
                                    @if($existenciaact == 'si')
                                        <td align="center" class="tdline"  > </td>
                                        <td align="center" class="tdlineff"  >ExAct</td>
                                    @endif
                                </tr>
                                <tr >
                                    <td align="left"  colspan="2" class="tdline">Totales </td>

                                    @foreach($sucursales as $indexs => $vals)
                                        <td align="center" class=" tdline titulo"  >
                                            {{ (isset($tttsuc[$indexs]))?  $tttsuc[$indexs]+0 : '' }}
                                        </td>
                                    @endforeach
                                    @if(count($sucursales)>1)
                                        <td align="center" class="tdline " > </td>
                                        <td align="center" class="tdline" > {{ $ttt  +0 }}</td>
                                    @endif
                                    @if(isset($fechasreport2) and $fechasreport2 != '' and isset($indexs))
                                        <td align="center" class="tdline "  >  </td>
                                        <td align="center" class="tdline"   >   {{ (isset($tttsucc[$indexs]))? $tttsucc[$indexs]+0 : '' }}      </td>
                                    @endif
                                    @if($existenciaact == 'si')
                                        <td align="center" class="tdline"  > </td>
                                        <td align="center" class="tdline"  > {{$ttexisten}}</td>
                                    @endif
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico de Top Productos
        const topCtx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(topCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($topProductosLabels ?? []) !!},
                datasets: [{
                    label: 'Unidades Vendidas',
                    data: {!! json_encode($topProductosData ?? []) !!},
                    backgroundColor: 'rgba(0, 114, 197, 0.7)',
                    borderColor: '#0072c5',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { callbacks: { label: (ctx) => `${ctx.raw.toLocaleString()} unidades` } }
                }
            }
        });

        // Gráfico de Categorías
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoriasLabels ?? []) !!},
                datasets: [{
                    data: {!! json_encode($categoriasData ?? []) !!},
                    backgroundColor: ['#0072c5', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6610f2'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${ctx.raw.toLocaleString()} unidades (${ctx.percent}%)` } }
                }
            }
        });
    </script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection

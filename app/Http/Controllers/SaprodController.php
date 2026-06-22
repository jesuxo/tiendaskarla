<?php

namespace App\Http\Controllers;

use App\Exports\SaprodExport;
use App\Imports\SaprodUpdate;
use App\Models\Saexis;
use App\Models\Sainsta;
use App\Models\Saitemfac;
use App\Models\Saprod;
use App\Models\Saprodsucursal;
use App\Models\Saserv;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;

class SaprodController extends Controller
{
    public function saprodexport($codalte)
    {
        $file = Excel::download(new SaprodExport($codalte), 'productos.xlsx');

        return $file;
    }

    public function inventarios(Request $request){
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $sqlcostoinv = "SELECT sum((a.preciod + a.preciod2)*b.existen) as suma, c.descrip
								from   saprod a , saexis b, sasucursal c
								where  a.codprod = b.codprod
                                and b.fk_sucursal = c.id
								and a.comercial = $comercialid
                                and c.fk_comercial = $comercialid
                            group by  c.descrip order by c.descrip
								";

        $costoinven = DB::select($sqlcostoinv);

        return view('reporteInventarios', compact('costoinven') );
    }

    public function updateSaprodData(Request $request)
    {
        $request->validate([
            'import_file' => [
                'required',
                'file'
            ],
        ]);

        Excel::import(new SaprodUpdate(), $request->file('import_file'));

        return redirect()->back()->with('status', 'Archivo Procesado Exitosamente');
    }

    public function index()
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte")
                               ->with(['padre','hijos',  'productos'])
                               ->where('comercial',$comercial)
                               ->orderBy('codalte','asc')
                               ->get();

        return view('product-list', compact('instancias') );
    }

    public function existencias(Request $request)
    {
        $comercialid  = session('comercialid') ;
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $fksucursal    = (isset($request->fksucursal ))? $request->fksucursal : '';
        $allsucursales = Sasucursal::where('fk_comercial', $comercialid)->orderBy('descrip','asc')->get();
        $instancias    = Sainsta::selectRaw("Descrip as label, descrip, id, nivel, codinst , codalte, insPadre")
            ->where('comercial', $comercialid)
            ->orderBy('codalte','asc')
            ->get();

        $sucursales = Sasucursal::where("fk_comercial", $comercialid)->orderBy('descrip');
        if($fksucursal)
            $sucursales = $sucursales->where('id',$fksucursal);

        $sucursales = $sucursales->get();

        return view('existenciasInstancias', compact( 'fksucursal', 'allsucursales', 'sucursales', 'instancias', 'comercialid') );
    }

    public function existenciasphp(Request $request)
    {
        $codinst    = $request->codinst;
        $fksucursal = $request->fksucursal;

        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte, insPadre")
            ->where('comercial',$comercial)
            ->orderBy('descrip','asc')
            ->get();
        $insPadre = 0;

        $sucursales = Sasucursal::where("fk_comercial", $comercial);

        if($fksucursal)
            $sucursales = $sucursales->where('id',$fksucursal);

        $sucursales = $sucursales->get();

        $instanciaselected = '';
        foreach ($instancias as $instancia){
            if($instancia->codinst == $codinst){
                $instanciaselected = $instancia;
                $insPadre = $instancia->insPadre;
                break;
            }
        }
        return view('existenciasInstanciasphp', compact('fksucursal', 'insPadre', 'codinst', 'sucursales', 'instancias', 'instanciaselected', 'comercial') )->render();
    }

    public function json()
    {
        $comercial  = session('comercialid') ;
        $all = Saprod::where('comercial',$comercial)->with(['instancia'])->orderBy('descrip','asc')->get();
        $aux = [];
        $productos = [];
        $noimage = URL::asset('build/images/noimagen.jpg');
        foreach ($all as $item){
            $aux = [
                "id"            => "$item->id",
                "price"         => "$item->costod3",
                "exdecimal"     => "$item->exdecimal",
                "image"         => (isset($item->productImg))? '': $noimage,
                "productTitle"  => "$item->descrip",
                "category"      => $item->instancia->descrip
            ];

            array_push($productos,$aux);
        }
        return response()->json($productos );
    }

    public function productossucursales(Request $request)
    {
        $dato = isset($request->dato) ? $request->dato : '';

        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",", $arraysucursales);

        $comercialid = session('comercialid');

        if (!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $instancias = Sainsta::porComercial($comercialid)
            ->whereIn('nivel', [1])
            ->orderBy('descrip', 'asc')
            ->get();

        $allsucursales = Sasucursal::where('fk_comercial', $comercialid)
            ->whereRaw("id in ($arraysucursales)")
            ->orderBy('descrip','asc')
            ->get();

        $existenciaact = $request->existenciaact ?? 'si';
        $fksucursal    = $request->fksucursal ?? '';
        $codinst       = $request->codinst ?? '';
        $fechasreport  = $request->fechasreport;
        $fechasreport2 = $request->fechasreport2 ?? '';

        // Inicializar variables
        $fecha1 = $fecha2 = '';
        $fecha1_mostrar = $fecha2_mostrar = '';
        $itemventas = [];
        $sucursales = [];
        $cantidadprod = [];
        $total_costo = [];
        $total_venta = [];

        $itemventas2 = [];
        $sucursales2 = [];
        $cantidadprod2 = [];
        $total_costo2 = [];
        $total_venta2 = [];

        // Procesar período principal
        if($fechasreport && $codinst){
            $fechasaux = str_replace(' ', '', $fechasreport);

            if (strpos($fechasaux, "to")) {
                list($fec1, $fec2) = explode("to", $fechasaux);
            } else {
                $fec1 = $fechasreport;
                $fec2 = $fechasreport;
            }

            if($fec1){
                list($d1, $m1, $y1) = explode("/", $fec1);
                list($d2, $m2, $y2) = explode("/", $fec2);
                $fecha1 = "$y1-$m1-$d1";
                $fecha2 = "$y2-$m2-$d2";

                $fecha1_mostrar = "$d1/$m1/$y1";
                $fecha2_mostrar = "$d2/$m2/$y2";

                $listado = $this->obtenerVentasPeriodo($comercialid, $fecha1, $fecha2, $fksucursal, $codinst);

                if($listado && count($listado) > 0) {
                    list($sucursales, $cantidadprod, $itemventas, $total_costo, $total_venta) = $this->procesarDatosVentas($listado, 1);
                }
            }
        }

        // Procesar período comparativo
        if($fechasreport2 && $codinst){
            $fechasaux2 = str_replace(' ', '', $fechasreport2);

            if (strpos($fechasaux2, "to")) {
                list($fec12, $fec22) = explode("to", $fechasaux2);
            } else {
                $fec12 = $fechasreport2;
                $fec22 = $fechasreport2;
            }

            if($fec12){
                list($d12, $m12, $y12) = explode("/", $fec12);
                list($d22, $m22, $y22) = explode("/", $fec22);
                $fecha12 = "$y12-$m12-$d12";
                $fecha22 = "$y22-$m22-$d22";

                $listadoComparativo = $this->obtenerVentasPeriodo($comercialid, $fecha12, $fecha22, $fksucursal, $codinst);

                if($listadoComparativo && count($listadoComparativo) > 0) {
                    list($sucursales2, $cantidadprod2, $itemventas2, $total_costo2, $total_venta2) = $this->procesarDatosVentas($listadoComparativo, 0);
                }
            }
        }

        // ============================================
        // CÁLCULOS DE ANALÍTICAS - PERÍODO PRINCIPAL
        // ============================================
        $totalVendido = array_sum($cantidadprod);
        $totalVentasValor = array_sum($total_venta);        // costod = precio de venta
        $totalCostoValor = array_sum($total_costo);         // preciod = costo del producto
        $margenBruto = $totalVentasValor - $totalCostoValor;
        $margenPorcentaje = $totalCostoValor > 0 ? ($margenBruto / $totalCostoValor * 100) : 0;

        // Total de productos diferentes vendidos
        $totalProductosVendidos = 0;
        foreach($itemventas as $productos) {
            $totalProductosVendidos += count($productos);
        }

        // Promedio diario
        $promedioDiario = 0;
        if(isset($fecha1) && isset($fecha2) && $totalVendido > 0) {
            $fechaInicio = \Carbon\Carbon::parse($fecha1);
            $fechaFin = \Carbon\Carbon::parse($fecha2);
            $dias = $fechaInicio->diffInDays($fechaFin) + 1;
            $promedioDiario = $dias > 0 ? $totalVendido / $dias : 0;
        }

        // ============================================
        // CÁLCULOS DE ANALÍTICAS - PERÍODO COMPARATIVO
        // ============================================
        $totalVendidoComparativo = array_sum($cantidadprod2);
        $totalVentasValorComparativo = array_sum($total_venta2);
        $totalCostoValorComparativo = array_sum($total_costo2);

        $totalProductosVendidosComparativo = 0;
        foreach($itemventas2 as $productos) {
            $totalProductosVendidosComparativo += count($productos);
        }

        // ============================================
        // VARIACIONES
        // ============================================
        $variacionUnidades = $totalVendido - $totalVendidoComparativo;
        $variacionValor = $totalVentasValor - $totalVentasValorComparativo;
        $variacionProductos = $totalProductosVendidos - $totalProductosVendidosComparativo;

        $porcentajeVariacionUnidades = $totalVendidoComparativo > 0 ? ($variacionUnidades / $totalVendidoComparativo * 100) : 0;
        $porcentajeVariacionValor = $totalVentasValorComparativo > 0 ? ($variacionValor / $totalVentasValorComparativo * 100) : 0;

        // ============================================
        // TOP PRODUCTOS
        // ============================================
        $topProductos = [];
        $productosTemp = [];
        foreach($itemventas as $categoria => $productos) {
            foreach($productos as $codigo => $prod) {
                $cantidadTotal = 0;
                foreach($sucursales as $sucId => $sucNombre) {
                    $key = $codigo . $sucId;
                    $cantidadTotal += $cantidadprod[$key] ?? 0;
                }
                if($cantidadTotal > 0) {
                    $productosTemp[] = [
                        'nombre' => $prod['descrip'] ?? 'Producto',
                        'categoria' => $categoria,
                        'cantidad' => $cantidadTotal
                    ];
                }
            }
        }
        usort($productosTemp, function($a, $b) {
            return $b['cantidad'] - $a['cantidad'];
        });
        $topProductos = array_slice($productosTemp, 0, 10);
        $topProductosLabels = array_column($topProductos, 'nombre');
        $topProductosData = array_column($topProductos, 'cantidad');

        // ============================================
        // CATEGORÍAS PARA GRÁFICO
        // ============================================
        $categoriasVentas = [];
        foreach($itemventas as $categoria => $productos) {
            $totalCat = 0;
            foreach($productos as $codigo => $prod) {
                foreach($sucursales as $sucId => $sucNombre) {
                    $key = $codigo . $sucId;
                    $totalCat += $cantidadprod[$key] ?? 0;
                }
            }
            if($totalCat > 0) {
                $categoriasVentas[$categoria] = $totalCat;
            }
        }
        arsort($categoriasVentas);
        $categoriasLabels = array_keys(array_slice($categoriasVentas, 0, 8));
        $categoriasData = array_values(array_slice($categoriasVentas, 0, 8));

        // Bottom productos (los que menos se venden)
        $bottomProductos = array_slice(array_reverse($productosTemp), 0, 10);

        // ============================================
        // RETORNAR VISTA CON TODAS LAS VARIABLES
        // ============================================
        return view('productosSucursales', [
            // Fechas
            'fecha1_mostrar' => $fecha1_mostrar,
            'fecha2_mostrar' => $fecha2_mostrar,
            'fechasreport' => $fechasreport,
            'fechasreport2' => $fechasreport2,

            // Filtros y datos básicos
            'instancias' => $instancias,
            'sucursales' => $sucursales,
            'sucursales2' => $sucursales2,
            'itemventas' => $itemventas,
            'itemventas2' => $itemventas2,
            'codinst' => $codinst,
            'cantidadprod' => $cantidadprod,
            'cantidadprod2' => $cantidadprod2,
            'fksucursal' => $fksucursal,
            'allsucursales' => $allsucursales,
            'existenciaact' => $existenciaact,

            // Analíticas período principal
            'totalVendido' => $totalVendido,
            'totalVentasValor' => $totalVentasValor,
            'totalCostoValor' => $totalCostoValor,
            'margenBruto' => $margenBruto,
            'margenPorcentaje' => $margenPorcentaje,
            'totalProductosVendidos' => $totalProductosVendidos,
            'promedioDiario' => $promedioDiario,

            // Analíticas período comparativo
            'totalVendidoComparativo' => $totalVendidoComparativo,
            'totalVentasValorComparativo' => $totalVentasValorComparativo,
            'totalCostoValorComparativo' => $totalCostoValorComparativo,
            'totalProductosVendidosComparativo' => $totalProductosVendidosComparativo,

            // Variaciones
            'variacionUnidades' => $variacionUnidades,
            'variacionValor' => $variacionValor,
            'variacionProductos' => $variacionProductos,
            'porcentajeVariacionUnidades' => $porcentajeVariacionUnidades,
            'porcentajeVariacionValor' => $porcentajeVariacionValor,

            // Gráficos
            'topProductos' => $topProductos,
            'topProductosLabels' => $topProductosLabels,
            'topProductosData' => $topProductosData,
            'bottomProductos' => $bottomProductos,
            'categoriasLabels' => $categoriasLabels,
            'categoriasData' => $categoriasData,
        ]);
    }

    public function resultadosucursales(Request $request)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",",$arraysucursales);

        $comercialid = session('comercialid');

        if (!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $instancias = Sainsta::porComercial($comercialid)
            ->whereIn('nivel', [1 ]) // Niveles 1 y 2
            ->where('tipoins',0)
            ->orderBy('descrip', 'asc')
            ->get();

        $allsucursales  = Sasucursal::where('fk_comercial', $comercialid)->whereRaw("id in ($arraysucursales)")->orderBy('descrip','asc')->get();
        $existenciaact  = (isset($request->existenciaact ))? $request->existenciaact : '';
        $fksucursal     = (isset($request->fksucursal    ))? $request->fksucursal    : '';
        $codinst        = (isset($request->codinst       ))? $request->codinst       : '';
        $fechasreport   = $request->fechasreport;
        $fechasreport2  = (isset($request->fechasreport2) )? $request->fechasreport2 :'';

        $fechasaux      = str_replace(' ', '', $fechasreport);
        $fechasaux2     = str_replace(' ', '', $fechasreport2);
        $fec1  = $fec2  = $fecha1 = $fecha2 = '';
        $d22   = $m22 = $y22 = $d12   = $m12 =$y12 = $fec12 = $fec22 = '';
        $listadoMesAnterior = collect();

        $itemventas    = [];
        $sucursales    = [];
        $sucursales2   = [];
        $itemventas2   = [];
        $cantidadprod  = [];
        $cantidadprod2 = [];
        $preciodprod   = [];
        $costodprod    = [];
        if (strpos($fechasaux, "to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        } else {
            if($fechasreport !=''){
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }
        }

        if($fec1 != ''){

            list($d1, $m1, $y1) = explode("/", $fec1);
            list($d2, $m2, $y2) = explode("/", $fec2);

            $fecha1 = $fec1;
            $fecha2 = $fec2;

            $fec1 = "$y1-$m1-$d1";
            $fec2 = "$y2-$m2-$d2";


            $listado = $this->obtenerVentasPeriodo($comercialid, $fec1, $fec2, $fksucursal, $codinst);

            $fec12 = $fec22 = '';
            if (strpos($fechasaux2, "to")) {
                list($fec12, $fec22) = explode("to", $fechasaux2);
            } else {
                if($fechasreport2 != ''){
                    list($d12, $m12, $y12) = explode("/", $fechasreport2);
                    $fec12 = "$d12/$m12/$y12";
                    $fechasreport2= "$fec12 to $fec12";
                }
            }

            if (strpos($fec12, "/")) {
                list($d12, $m12, $y12) = explode("/", $fec12);
                if(!$fec22)
                    $fec22= $fec12;
                list($d22, $m22, $y22) = explode("/", $fec22);
                $fec12 = "$y12-$m12-$d12";
                $fec22 = "$y22-$m22-$d22";
            }

        }

        if(  $fec1 != '' and $codinst){

            if ($fec22 != '') {
                $listadoMesAnterior = $this->obtenerVentasPeriodo($comercialid, $fec12, $fec22, $fksucursal, $codinst);
                list($sucursales2, $cantidadprod2, $itemventas2, $costodprod, $preciodprod) = $this->procesarDatosVentas($listadoMesAnterior, 0);
            }

            list($sucursales, $cantidadprod, $itemventas, $costodprod, $preciodprod) = $this->procesarDatosVentas($listado, 1);

            asort($sucursales);

            if(!isset($sucursales)) $sucursales = [];

            if(!isset($cantidadprod))  $cantidadprod = [];
            if(!isset($cantidadprod2))  $cantidadprod2 = [];

            if(isset($cantidadprod) and count($cantidadprod) > 0)
                foreach($cantidadprod as $index => $val){
                    if(!isset($cantidadprod2[$index]))
                        $cantidadprod2[$index] = $val;
                }

            if(isset($sucursales2) and count($sucursales2) > 0){
                foreach($sucursales2 as $index => $val){
                    if(!isset($sucursales[$index])){
                        $sucursales[$index] = $val;
                    }
                }
            }

            if(isset($cantidadprod2) and count($cantidadprod2) > 0)
                foreach($cantidadprod2 as $index => $val){
                    if(!isset($cantidadprod[$index])){
                        $cantidadprod[$index] = $val;
                    }
                }

            foreach($itemventas2 as $index => $items){
                foreach($items as $index2 => $arr){
                    if(!isset($itemventas[$index][$index2])){
                        $itemventas[$index][$index2] = $arr;
                    }
                }
            }

            foreach($itemventas as $index => $items){
                foreach($items as $index2 => $arr){
                    if(!isset($itemventas2[$index][$index2])){
                        $itemventas2[$index][$index2] = $arr;
                    }
                }
            }

        }

        return view('resultadosucursales', compact(
            'fecha1',
            'fecha2',
            'codinst',
            'fechasreport',
            'fechasreport2',
            'sucursales',
            'itemventas',
            'itemventas2',
            'cantidadprod',
            'costodprod',
            'preciodprod',
            'cantidadprod2',
            'fksucursal',
            'allsucursales',
            'instancias',
            'existenciaact'
        ));
    }

    private function obtenerVentasPeriodo($comercialid, $fechaInicio, $fechaFin, $fksucursal, $codinst)
    {
        $arraysucursales = auth()->user()->getSucursalesIdsComercialActual();
        $arraysucursales = implode(",", $arraysucursales);

        $datainst = '';
        $codalte  = '';
        if(isset($codinst) and $codinst >0){
            $instancia = Sainsta::where('codinst', $codinst)->first();
            $codalte   = (isset($instancia->codalte))? $instancia->codalte : '';
        }

        $datainst = " descomp = 0 ";
        if($codalte !='')
            $datainst .= " and codalte like '$codalte%' ";

        $datos = Saitemfac::whereRaw("TipoFac in ('A','B')")
            ->selectRaw("
            fk_sucursal,
            CodItem,
            SUM(Cantidad * Signo) as salidas,
            SUM(Cantidad * preciod * Signo) as total_costo,        -- preciod = costo del producto
            SUM(Cantidad * costod * Signo) as total_venta           -- costod = precio de venta
        ")
            ->with(['sucursal', 'producto.instancia' => function($q) use($datainst) {
                if($datainst != ''){
                    $q->whereRaw($datainst);
                }
                $q = $q->orderBy('codalte', 'asc');
            }])
            ->whereRaw("esserv = 0 and fk_sucursal in ($arraysucursales)")
            ->whereHas('sucursal.comercial', function($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            })
            ->whereBetween('FechaE', [$fechaInicio . ' 00:00:00.00', $fechaFin . ' 23:58:22.00'])
            ->groupBy(['fk_sucursal', 'CodItem'])
            ->orderBy('fk_sucursal');

        if($comercialid == 1 and $codalte != ''){
            $datos = $datos->whereRaw("CodItem in (select y.codprod from saprod y, sainsta z where z.codinst = y.codinst and z.codalte like '$codalte%')");
        }

        if(isset($fksucursal) and $fksucursal != '' and $fksucursal > 0){
            $datos = $datos->where('fk_sucursal', $fksucursal);
        }

        $datos = $datos->get();

        return $datos;
    }

    private function procesarDatosVentas($listado, $agruparsucu): array
    {
        $sucursales   = [];
        $itemventas   = [];
        $cantidadprod = [];
        $total_costo  = [];   // preciod (costo del producto)
        $total_venta  = [];   // costod (precio de venta)

        if($agruparsucu == 1){
            // Agrupar por sucursal (para período principal)
            if (isset($listado) && count($listado) > 0) {
                foreach ($listado as $prodsuc) {
                    if(!isset($prodsuc->sucursal)) continue;

                    $sucursalId = $prodsuc->sucursal->id;
                    $sucursalNombre = $prodsuc->sucursal->descrip;

                    if (!isset($sucursales[$sucursalId]))
                        $sucursales[$sucursalId] = $sucursalNombre;

                    $key = $prodsuc->CodItem . $sucursalId;

                    if (!isset($cantidadprod[$key]))
                        $cantidadprod[$key] = 0;
                    if (!isset($total_costo[$key]))
                        $total_costo[$key] = 0;
                    if (!isset($total_venta[$key]))
                        $total_venta[$key] = 0;

                    if(!isset($prodsuc->producto)) continue;
                    if(!isset($prodsuc->producto->instancia)) continue;

                    $categoria = $prodsuc->producto->instancia->descrip ?? 'Sin Categoría';
                    $codItem = $prodsuc->CodItem;

                    if (!isset($itemventas[$categoria])) {
                        $itemventas[$categoria] = [];
                    }

                    if (!isset($itemventas[$categoria][$codItem])) {
                        $itemventas[$categoria][$codItem] = [
                            'descrip'   => $prodsuc->producto->descrip ?? 'Producto sin nombre',
                            'color'     => $prodsuc->producto->color ?? 'sin color',
                            'exdecimal' => $prodsuc->producto->exdecimal ?? 0,
                            'existen'   => $prodsuc->producto->existen ?? 0                    ];
                    }

                    $cantidadprod[$key] += floatval($prodsuc->salidas ?? 0);
                    $total_costo[$key] += floatval($prodsuc->total_costo ?? 0);   // preciod
                    $total_venta[$key] += floatval($prodsuc->total_venta ?? 0);   // costod
                }
            }
            return [$sucursales, $cantidadprod, $itemventas, $total_costo, $total_venta];

        } else {
            // NO agrupar por sucursal (para período comparativo)
            if (isset($listado) && count($listado) > 0) {
                foreach ($listado as $prodsuc) {
                    if(!isset($prodsuc->sucursal)) continue;

                    $sucursalId = $prodsuc->sucursal->id;
                    $sucursalNombre = $prodsuc->sucursal->descrip;

                    if (!isset($sucursales[$sucursalId]))
                        $sucursales[$sucursalId] = $sucursalNombre;

                    if (!isset($cantidadprod[$prodsuc->CodItem]))
                        $cantidadprod[$prodsuc->CodItem] = 0;
                    if (!isset($total_costo[$prodsuc->CodItem]))
                        $total_costo[$prodsuc->CodItem] = 0;
                    if (!isset($total_venta[$prodsuc->CodItem]))
                        $total_venta[$prodsuc->CodItem] = 0;

                    if(!isset($prodsuc->producto)) continue;
                    if(!isset($prodsuc->producto->instancia)) continue;

                    $categoria = $prodsuc->producto->instancia->descrip ?? 'Sin Categoría';
                    $codItem = $prodsuc->CodItem;

                    if (!isset($itemventas[$categoria])) {
                        $itemventas[$categoria] = [];
                    }

                    if (!isset($itemventas[$categoria][$codItem])) {
                        $itemventas[$categoria][$codItem] = [
                            'descrip' => $prodsuc->producto->descrip ?? 'Producto sin nombre',
                            'color'   => $prodsuc->producto->color ?? 'sin color',
                            'exdecimal' => $prodsuc->producto->exdecimal ?? 0
                        ];
                    }

                    $cantidadprod[$prodsuc->CodItem] += floatval($prodsuc->salidas ?? 0);
                    $total_costo[$prodsuc->CodItem] += floatval($prodsuc->total_costo ?? 0);
                    $total_venta[$prodsuc->CodItem] += floatval($prodsuc->total_venta ?? 0);
                }
            }
            return [$sucursales, $cantidadprod, $itemventas, $total_costo, $total_venta];
        }
    }

    public function busquedaHomeProd(Request $request)
    {
        $busqueda = $request->busqueda;
        $busqueda = str_replace("\"", "", $busqueda);
        $busqueda = str_replace("'", "", $busqueda);
        $busqueda = str_replace("*", " ", $busqueda);
        $vector = explode(" ", $busqueda);

        if ($vector) {
            $numerito = 0;
            $cadena   = '';
            foreach ($vector as $value) {
                if ($numerito > 0) {
                    $cadena  .= ' AND ';
                }
                $cadena  .= "(codprod like '%$value%' or descrip like '%$value%' or refere like '%$value%' or marca like '%$value%' or descrip2 like '%$value%')";
                $numerito++;
            }
        }

        $comercial = session('comercialid');

        // Obtener los productos
        $productos = Saprod::where('comercial', $comercial)
            ->whereRaw($cadena)
            ->orderBy('updated_at', 'desc')
            ->limit(60)
            ->get();

        // Obtener las sucursales del comercial
        $sucursales = Sasucursal::where('fk_comercial', $comercial)
            ->orderBy('descrip')
            ->get();

        // Para cada producto, obtener las existencias por sucursal
        foreach ($productos as $producto) {
            if(!isset($producto->existencias_por_sucursal))
                $producto->existencias_por_sucursal = [];

            $existencias = Saexis::where('codprod', $producto->codprod)
                ->whereIn('fk_sucursal', $sucursales->pluck('id'))
                ->where('existen','<>',0)
                ->with('deposito')
                ->get();

            $producto->existencias_por_sucursal = $existencias;
        }


        return view('layouts.ajaxbusqueda', compact('productos', 'sucursales'))->render();
    }

    public function saprodsucursal(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $productos = $request->productos;
        $productos = json_decode($productos);

        if (isset($productos))
            foreach ($productos as $producto){
                $aux = Saprodsucursal::where(['codprod' => $producto->codprod, 'fk_sucursal'=>$sucursalid])->first();
                if(!$aux){
                    $rel              = new Saprodsucursal();
                    $rel->codprod     = $producto->codprod;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }
            }

        return response()->json(['success'=>'success']);
    }

    public function buscarproductoget($codprod, $comercial){

        $producto   = Saprod::where(['codprod'=>$codprod, "comercial" => $comercial])->first();
        session(['comercialid' => $comercial]);
        if(isset($producto) and isset($producto->id)){
            $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst ")
                ->with(['padre'])
                ->where('comercial',$comercial)
                ->orderBy('codalte','asc')->get();

            $id = $producto->id;
            return view('product-edit', compact('instancias','producto', 'id'));
        }else{
            return response()->redirectTo('index');
        }

    }

    public function correlativo(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $codprod    = $request->codprod;
        $correlativo= $request->correlativo;

        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;

        if (isset($codprod) and $codprod != '') {
            $producto = Saprod::where(['codprod' => $codprod, 'comercial' => $comercial])->first();
            if (isset($producto)) {
                $producto->correlativo = $correlativo;
                $producto->save();
            }

            $prodsucursal = Saprodsucursal::with('productocodprod')->where('codprod', $codprod)->get();

            if(isset($prodsucursal) and $prodsucursal->count() > 0){}
                foreach ($prodsucursal as $item){
                    if($item->productocodprod->comercial == $comercial)
                        $item->delete();
                }

        }


        return response()->json(['success'=>'success', 'correlativo' => $correlativo]);
    }

    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;

        $productos = Saprod::where('comercial',$comercial)
            ->whereRaw("codprod not in (select codprod from saprodsucursal where fk_sucursal=$sucursalid )")
            ->get()
            ->take(150);

        // Obtener los códigos de productos para consultar sus precios especiales
        $codprods = $productos->pluck('codprod')->toArray();

        // Consultar precios especiales (grupos de descuento) para estos productos
        $preciosEspeciales = [];
        if (!empty($codprods)) {
            $preciosEspeciales = DB::table('producto_grupo_descuento')
                ->whereIn('codprod', $codprods)
                ->get(['codprod', 'precio_final', 'grupo_id', 'tasa_cambio_usd'])
                ->groupBy('codprod')
                ->map(function($items) {
                    // Si hay múltiples grupos, tomar el precio más bajo o el más reciente
                    $mejorPrecio = $items->sortBy('precio_final')->first();
                    return [
                        'precio_especial' => $mejorPrecio->precio_final,
                        'grupo_id'        => $mejorPrecio->grupo_id,
                        'tasa_cambio'     => $mejorPrecio->tasa_cambio_usd
                    ];
                })
                ->toArray();
        }

        // Enriquecer los productos con su precio especial si existe
        $productosConPrecio = $productos->map(function($producto) use ($preciosEspeciales) {
            $productoArray = $producto->toArray();

            if (isset($preciosEspeciales[$producto->codprod])) {
                $productoArray['tiene_precio_especial'] = true;
                $productoArray['precio_especial']    = $preciosEspeciales[$producto->codprod]['precio_especial'];
                $productoArray['grupo_descuento_id'] = $preciosEspeciales[$producto->codprod]['grupo_id'];
                $productoArray['tasa_cambio_usd']    = $preciosEspeciales[$producto->codprod]['tasa_cambio'];

                // Opcional: Sobrescribir costod3 con el precio especial
                // $productoArray['costod3'] = $preciosEspeciales[$producto->codprod]['precio_especial'];
            } else {
                $productoArray['tiene_precio_especial'] = false;
                $productoArray['precio_especial']       = null;
            }

            return $productoArray;
        });

        $servicios = Saserv::where('comercial',$comercial)
            ->whereRaw("codserv not in (select codserv from saservsucursal where fk_sucursal=$sucursalid )")
            ->limit(50)
            ->get();

        return response()->json([
            'success' => 'success',
            'newproductos' => $productosConPrecio,
            'newservicios' => $servicios
        ]);
    }

    public function productosinstsancias(Request $request)
    {
        $sucursalid  = str_replace("300","",$request->sucursal);
        $sucursal    = Sasucursal::find($sucursalid);
        $comercialid = $sucursal->fk_comercial;
        $codinst     = $request->codinst;

        $sqlcostoinv = "SELECT a  a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from   saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where  a.codprod = b.codprod
                                and b.fk_sucursal = c.id
								and b.codubic = e.codubic
                                and c.fk_comercial = $comercialid
								and d.codinst = a.codinst
                                and a.codinst = $codinst
								and e.comercial = $comercialid
								and b.existen > 0
                        order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        return response()->json(['success'=>'success', 'listado' => $listado]);

    }

    public function productosinstsanciascodalte(Request $request)
    {
        $sucursalid  = str_replace("300","",$request->sucursal);
        $sucursal    = Sasucursal::find($sucursalid);
        $comercialid = $sucursal->fk_comercial;
        $codalte     = $request->codalte;
        $len         = strlen($codalte);

        $sqlcostoinv = "SELECT   a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from   saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where  a.codprod = b.codprod
                                and b.fk_sucursal = c.id
								and b.codubic   = e.codubic
                                and c.fk_comercial = $comercialid
								and a.comercial = $comercialid
								and d.comercial = $comercialid
								and e.comercial = $comercialid
								and d.codinst   = a.codinst
                                and left(d.codalte,$len) = '$codalte'
								and b.existen > 0
                                order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        return response()->json(['success'=>'success', 'listado' => $listado, 'sqlcostoinv' => $sqlcostoinv]);

    }

    public function viewprodinstsanciascodalte(Request $request)
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $codalte     = $request->codalte;
        $len         = strlen($codalte);

        $sqlcostoinv = "SELECT a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where a.codprod    = b.codprod
                                and b.fk_sucursal  = c.id
								and b.codubic      = e.codubic
                                and c.fk_comercial = $comercial
								and a.comercial    = $comercial
								and d.comercial    = $comercial
								and e.comercial    = $comercial
								and d.codinst      = a.codinst
                                and left(d.codalte,$len) = '$codalte'
								and b.existen <> 0
                                order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        $productos    = [];
        $deposito     = [];
        $existencias  = [];

        foreach($listado as $producto){

            if(!isset($productos[$producto->codprod]))
                $productos[$producto->codprod] = [];

            $productos[$producto->codprod]['descrip']    = $producto->descrip;
            $productos[$producto->codprod]['color']      = $producto->color;
            $productos[$producto->codprod]['preciod']    = $producto->preciod;

            if(!isset($deposito[$producto->codubic]))
                $deposito[$producto->codubic] = $producto->deposito;

            if(!isset($existencias[$producto->codprod][$producto->codubic]))
                $existencias[$producto->codprod][$producto->codubic] = 0;

            $existencias[$producto->codprod][$producto->codubic] = $producto->existen;
        }

        return view('productosallinstsancias', compact('productos', 'deposito', 'existencias') )->render();


    }

    public function listprodubic(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;
        $codprod    = $request->codprod;

        $allsucursa = Sasucursal::where('fk_comercial',$comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }
        $auxsucu = implode(',' , $auxsucu);

        $existencias = Saexis::whereRaw("fk_sucursal in ($auxsucu) and codprod='$codprod' and existen > 0")
            ->orderBy('codubic')->get();

        return response()->json(['success'=>'success', 'existencias' => $existencias]);
    }

    public function listprodubicinv(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;
        $codprod    = $request->codprod;

        $allsucursa = Sasucursal::where('fk_comercial',$comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }
        $auxsucu = implode(',' , $auxsucu);

        $existencias = Saexis::whereRaw("fk_sucursal in ($auxsucu) and codprod='$codprod' and existen > 0")
            ->orderBy('codubic')->get();

        return response()->json(['success'=>'success', 'existencias' => $existencias]);
    }

    public function create()
    {
        $comercial  = session('comercialid') ;
        $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst ")
            ->with(['padre'])
            ->where('comercial',$comercial)
            ->orderBy('codalte','asc')->get();

        return view('product-create', compact('instancias') );
    }

    public function checkcodprod($codprod)
    {
        $check   = 0;
        $comercial = session('comercialid') ;

        if($codprod != '')
            $product = Saprod::where(['codprod' => $codprod, 'comercial' => $comercial])->first();
            if(isset($product) and $product->codprod != '')
                $check = 0;
            else
                $check = 1;

        return response()->json(['check' => $check ]);
    }

    public function lastprod($codinst)
    {
        $last       = 0;
        $comercial  = session('comercialid') ;
        $instancia  = Sainsta::where('codinst', $codinst)->first();
        $product    = Saprod::where(['comercial'=> $comercial, 'codinst' => $codinst])->orderBy('id', 'desc')->first();

        list($padre, $codinsta) = explode('.', $instancia->codalte);

        if(isset($product) and $product->codprod != ''){
            $last = $product->codprod;
            $last = substr($last, 3, 4);
        }

        $last = $last + 1;

        $sqlcheck = "select lpad('$last', 4, '0') as cadena ";
        $resquery = DB::select($sqlcheck);
        $numprx   = $resquery[0]->cadena;
        $numprx   = "$codinsta$numprx";

        return   $numprx  ;
    }

    public function store(Request $request)
    {
        $comercial = session('comercialid') ;

        $itemscolores = $request->itemscolores;

        if(isset($itemscolores)){

            foreach ($itemscolores as $itemscolore) {
                $color = $itemscolore['color'];
                if(isset($color) and $color !='' and strlen($color)>0){

                    $codprod = $this->lastprod($request->codinst);
                    $newprod = new Saprod();
                    $newprod->fill($request->all());
                    $newprod->codprod   = $codprod;
                    $newprod->color     = $color;
                    $newprod->comercial = $comercial;

                    if($comercial == 1)
                        $newprod->esexento = 1;

                    $newprod->save();
                }

            }

        }



        return redirect()->route('productos.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $producto   = Saprod::find($id);
        $comercial = session('comercialid') ;
        $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst ")
            ->with(['padre'])
            ->where('comercial',$comercial)
            ->orderBy('codalte','asc')->get();

        return view('product-edit', compact('instancias','producto', 'id'));
    }

    public function update(Request $request, $id)
    {
        $comercial = session('comercialid') ;

        $producto  = Saprod::find($id);
        $producto->fill($request->all());
        $producto->esexento = 1;  //// luego ver como manejamos esto

        if(isset($request->preciod)) {
            $preciod = $request->preciod;
            $coma = substr_count($preciod, ',');
            $punto = substr_count($preciod, '.');

            if ($coma > 0 and $punto > 0) {
                $preciod = str_replace(".", '', $preciod);
                $preciod = str_replace(",", '.', $preciod);
            }
            if ($coma > 0 and !$punto)
                $preciod = str_replace(",", '.', $preciod);
            $producto->preciod = $preciod;
        }
        ////////////////////////////////////////////////////////////////////////////
        if(isset($request->costod)){
            $costod =  $request->costod;

            $coma  = strpos($costod, ',');
            $punto = strpos($costod, '.');

            if($coma>0 and $punto>0){
                $costod = str_replace(".",'',$costod);
                $costod = str_replace(",",'.',$costod);
            }
            if($coma>0  and !$punto)
                $costod = str_replace(",",'.',$costod);

            $producto->costod = $costod;
        }
        ////////////////////////////////////////////////////////////////////////////
        if(isset($request->costod2)){
            $costod2 =  $request->costod2;
            $coma  = substr_count($costod2, ',');
            $punto = substr_count($costod2, '.');

            if($coma>0 and $punto>0){
                $costod2 = str_replace(".",'',$costod2);
                $costod2 = str_replace(",",'.',$costod2);
            }
            if($coma>0  and !$punto)
                $costod3 = str_replace(",",'.',$costod2);
            $producto->costod2 = $costod2;
        }
        //////////////////////////////////////////////////////////////////////////////
        if(isset($request->costod3)) {
            $costod3 = $request->costod3;
            $coma = substr_count($costod3, ',');
            $punto = substr_count($costod3, '.');

            if ($coma > 0 and $punto > 0) {
                $costod3 = str_replace(".", '', $costod3);
                $costod3 = str_replace(",", '.', $costod3);
            }
            if ($coma > 0 and !$punto)
                $costod3 = str_replace(",", '.', $costod3);
            $producto->costod3 = $costod3;
        }
        ////////////////////////////////////////////////////////////////////////////

        if(!$request->exdecimal)
            $producto->exdecimal = 0;

        if(!$request->activo)
            $producto->activo = 0;

        $producto->save();

        $prodsucursal = Saprodsucursal::with('producto')->where('codprod', $producto->codprod)->get();

        if($prodsucursal)
            foreach ($prodsucursal as $item){
                if($item->producto->comercial == $comercial)
                    $item->delete();
            }

        return redirect()->route('productos.edit',$id);
    }

    public function destroy($id)
    {
        //
    }
}

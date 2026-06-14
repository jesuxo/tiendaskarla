<?php

namespace App\Http\Controllers;

use App\Models\Safact;
use App\Models\Saipacxc;
use App\Models\Saipavta;
use App\Models\Sasucursal;
use App\Models\Satarj;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SatarjController extends Controller
{
    public function instpagobs(Request $request)
    {
        $comercialid  = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $fechasreport = $request->fechasreport;
        $fechashoy    =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;

        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        }else{
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $montos = DB::table('saipavta as a')
            ->select([ 'a.fk_sucursal', 'b.codtarj', 'b.clase',
                DB::raw("SUM(CASE a.tipofac WHEN 'A' THEN a.monto WHEN 'B' THEN (a.monto * -1) ELSE 0 END) as bs"),
                DB::raw("b.descrip as tarjeta"),
                DB::raw("c.descrip as sucursal")
            ])
            ->join('satarj as b', 'a.codpago', '=', 'b.codtarj')
            ->join('sasucursal as c', function($join) use ($comercialid) {
                $join->on('a.fk_sucursal', '=', 'c.id')
                    ->where('c.fk_comercial', '=', $comercialid);
            })
            ->where('b.bs', 1)
            ->whereBetween('a.fechae', [
                Carbon::parse($fec1)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($fec2)->endOfDay()->format('Y-m-d H:i:s')
            ])
            ->groupBy('a.fk_sucursal', 'b.codtarj', 'a.tipofac', 'b.descrip', 'c.descrip', 'b.clase')
            ->get();

        $clases     = [];
        $sucursales = [];
        $listado    = [];

        if(isset($montos))
            foreach ($montos as $monto) {

                if(!isset($sucursales[$monto->sucursal][$monto->tarjeta.' '.$monto->codtarj])){
                    $sucursales[$monto->sucursal][$monto->tarjeta.' '.$monto->codtarj] = $monto->tarjeta.' '.$monto->codtarj;
                }

                if(!isset($clases[$monto->clase])){
                    $clases[$monto->clase] = $monto->codtarj;
                }

                if(!isset($listado[$monto->sucursal][$monto->tarjeta.' '.$monto->codtarj][$monto->clase]))
                    $listado[$monto->sucursal][$monto->tarjeta.' '.$monto->codtarj][$monto->clase] = 0;

                $listado[$monto->sucursal][$monto->tarjeta.' '.$monto->codtarj][$monto->clase] += $monto->bs;

            }



      $montos = DB::table('saipacxc as a')
            ->select([
                'a.fk_sucursal', 'b.codtarj', 'b.clase',
                DB::raw('SUM(a.monto) as bs'),
                DB::raw("b.descrip as tarjeta"),
                DB::raw("c.descrip as sucursal")
            ])
            ->join('satarj as b', 'a.CodPago', '=', 'b.codtarj')
            ->join('sasucursal as c', 'a.fk_sucursal', '=', 'c.id')
            ->where('c.fk_comercial', $comercialid)
            ->where('b.bs', 1)
            ->whereRaw("a.created_at >= '$fec1 00:00:00' and a.created_at <= '$fec2 23:59:00'" )
            ->groupBy('a.fk_sucursal', 'b.codtarj', 'b.clase', 'b.descrip', 'c.descrip')
            ->get();

        if(isset($montos))
            foreach ($montos as $monto) {

                if(!isset($sucursales[$monto->sucursal][$monto->tarjeta.' '.$monto->codtarj])){
                    $sucursales[$monto->sucursal][$monto->tarjeta.' '.$monto->codtarj] = $monto->sucursal;
                }

                if(!isset($clases[$monto->clase])){
                    $clases[$monto->clase] = $monto->codtarj;
                }

                if(!isset($listado[$monto->sucursal][$monto->tarjeta.' '.$monto->codtarj][$monto->clase]))
                    $listado[$monto->sucursal][$monto->tarjeta.' '.$monto->codtarj][$monto->clase] = 0;

                $listado[$monto->sucursal][$monto->tarjeta.' '.$monto->codtarj][$monto->clase] += $monto->bs;

            }

        ksort($sucursales);
        ksort($clases);

        return view('reporteInstPago', compact('fechasreport','clases', 'sucursales', 'fecha1', 'fecha2', 'listado'));
    }

    public function instpagodolares(Request $request)
    {
        $comercialid  = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $fechasreport = $request->fechasreport;
        $fechashoy    =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;

        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        }else{
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $montos = DB::table('saipavta as a')
            ->select([ 'a.fk_sucursal', 'b.codtarj', 'b.clase',
                DB::raw("SUM(CASE a.tipofac WHEN 'A' THEN a.dolares WHEN 'B' THEN (a.dolares * -1) ELSE 0 END) as dolares"),
                DB::raw("b.descrip as tarjeta"),
                DB::raw("c.descrip as sucursal")
            ])
            ->join('satarj as b', 'a.codpago', '=', 'b.codtarj')
            ->join('sasucursal as c', function($join) use ($comercialid) {
                $join->on('a.fk_sucursal', '=', 'c.id')
                    ->where('c.fk_comercial', '=', $comercialid);
            })
            ->where('b.dolares', 1)
            ->whereBetween('a.fechae', [
                Carbon::parse($fec1)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($fec2)->endOfDay()->format('Y-m-d H:i:s')
            ])
            ->groupBy('a.fk_sucursal', 'b.codtarj', 'a.tipofac', 'b.descrip', 'c.descrip', 'b.clase')
            ->get();

        $clases     = [];
        $sucursales = [];
        $listado    = [];

        if(isset($montos))
            foreach ($montos as $monto) {

                if(!isset($sucursales[$monto->sucursal][$monto->tarjeta.'/'.$monto->codtarj])){
                    $sucursales[$monto->sucursal][$monto->tarjeta.'/'.$monto->codtarj] = $monto->tarjeta.' '.$monto->codtarj;
                }

                if(!isset($clases[$monto->clase])){
                    $clases[$monto->clase] = $monto->codtarj;
                }

                if(!isset($listado[$monto->sucursal][$monto->tarjeta.'/'.$monto->codtarj][$monto->clase]))
                    $listado[$monto->sucursal][$monto->tarjeta.'/'.$monto->codtarj][$monto->clase] = 0;

                $listado[$monto->sucursal][$monto->tarjeta.'/'.$monto->codtarj][$monto->clase] += $monto->dolares;

            }


        $montos = DB::table('saipacxc as a')
            ->select([
                'a.fk_sucursal', 'b.codtarj', 'b.clase',
                DB::raw('SUM(a.dolares) as dolares'),
                DB::raw("b.descrip as tarjeta"),
                DB::raw("c.descrip as sucursal")
            ])
            ->join('satarj as b', 'a.CodPago', '=', 'b.codtarj')
            ->join('sasucursal as c', 'a.fk_sucursal', '=', 'c.id')
            ->where('c.fk_comercial', $comercialid)
            ->where('b.dolares', 1)
            ->whereRaw("a.created_at >= '$fec1 00:00:00' and a.created_at <= '$fec2 23:59:00'" )
            ->groupBy('a.fk_sucursal', 'b.codtarj', 'b.clase', 'b.descrip', 'c.descrip')
            ->get();


        if(isset($montos))
            foreach ($montos as $monto) {

                if(!isset($sucursales[$monto->sucursal][$monto->tarjeta.'/'.$monto->codtarj])){
                    $sucursales[$monto->sucursal][$monto->tarjeta.'/'.$monto->codtarj] = $monto->sucursal;
                }

                if(!isset($clases[$monto->clase])){
                    $clases[$monto->clase] = $monto->codtarj;
                }

                if(!isset($listado[$monto->sucursal][$monto->tarjeta.'/'.$monto->codtarj][$monto->clase]))
                    $listado[$monto->sucursal][$monto->tarjeta.'/'.$monto->codtarj][$monto->clase] = 0;

                $listado[$monto->sucursal][$monto->tarjeta.'/'.$monto->codtarj][$monto->clase] += $monto->dolares;

            }


        ksort($sucursales);
        ksort($clases);

        return view('reporteInstPagodolares', compact('fechasreport','clases', 'sucursales', 'fecha1', 'fecha2', 'listado'));
    }

    public function detinstpagodolares(Request $request)
    {
        $comercialid  = session('comercialid');

        $fechasreport = $request->fechasr;
        $fk_sucursal  = $request->fksucu;
        $codpago      = $request->codpago;
        list($descrip, $codtarj) = explode('/', $codpago);
        $fechashoy    =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;

        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        }else{
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $montos = DB::table('saipavta as a')
            ->select([ 'a.fk_sucursal', 'b.codtarj', 'b.clase', 'f.numerod', 'f.codclie',
                DB::raw(" (CASE a.tipofac WHEN 'A' THEN a.dolares WHEN 'B' THEN (a.dolares * -1) ELSE 0 END) as dolares"),
                DB::raw("b.descrip as tarjeta"),
                DB::raw("c.descrip as sucursal"),
                DB::raw("f.descrip as cliente")
            ])
            ->join('satarj as b', 'a.codpago', '=', 'b.codtarj')
            ->join('sasucursal as c', function($join) use ($comercialid) {
                $join->on('a.fk_sucursal', '=', 'c.id')
                    ->where('c.fk_comercial', '=', $comercialid);
            })
            ->join('safact as f', function($join) {
                $join->on('a.numerod', '=', 'f.numerod')
                    ->on('a.tipofac', '=', 'f.tipofac')
                    ->on('a.fk_sucursal', '=', 'f.fk_sucursal');
            })
            ->where('b.dolares', 1)
            ->where('b.codtarj', $codtarj)
            ->where('c.descrip', $fk_sucursal)
            ->whereBetween('a.fechae', [
                Carbon::parse($fec1)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($fec2)->endOfDay()->format('Y-m-d H:i:s')
            ])->get();
      //  dd($montos->toSql(), $montos->getBindings(), $fec1,$fec2);
        $clases     = [];
        $clientes   = [];
        $listado    = [];

        if(isset($montos))
            foreach ($montos as $monto) {

                if(!isset($clientes[$monto->codclie.'*'.$monto->cliente.'*Fact-'.$monto->numerod][$monto->tarjeta.'/'.$monto->codtarj])){
                    $clientes[$monto->codclie.'*'.$monto->cliente.'*Fact-'.$monto->numerod][$monto->tarjeta.'/'.$monto->codtarj] = $monto->cliente;
                }

                if(!isset($clases[$monto->clase])){
                    $clases[$monto->clase] = $monto->codtarj;
                }

                if(!isset($listado[$monto->codclie.'*'.$monto->cliente.'*Fact-'.$monto->numerod][$monto->tarjeta.'/'.$monto->codtarj][$monto->clase]))
                    $listado[$monto->codclie.'*'.$monto->cliente.'*Fact-'.$monto->numerod][$monto->tarjeta.'/'.$monto->codtarj][$monto->clase] = 0;

                $listado[$monto->codclie.'*'.$monto->cliente.'*Fact-'.$monto->numerod][$monto->tarjeta.'/'.$monto->codtarj][$monto->clase] += $monto->dolares;

            }


        $montos = DB::table('saipacxc as a')
            ->select([
                'a.fk_sucursal', 'b.codtarj', 'b.clase', 'g.codclie', 'd.numerod',
                DB::raw(' (a.dolares) as dolares'),
                DB::raw("b.descrip as tarjeta"),
                DB::raw("c.descrip as sucursal"),
                DB::raw("g.descrip as cliente")
            ])
            ->join('satarj as b', 'a.CodPago', '=', 'b.codtarj')
            ->join('sasucursal as c', 'a.fk_sucursal', '=', 'c.id')
            ->join('saacxc as d', 'a.NroPpal', '=', 'd.nrounico')
            ->join('saclie as g', 'd.codclie', '=', 'g.codclie')
            ->where('c.fk_comercial', $comercialid)
            ->where('b.codtarj', $codtarj)
            ->where('c.descrip', $fk_sucursal)
            ->where('b.dolares', 1)
            ->whereBetween('d.fechae', [
                Carbon::parse($fec1)->startOfDay(),
                Carbon::parse($fec2)->endOfDay()
            ])
            ->get();


        if(isset($montos))
            foreach ($montos as $monto) {

                if(!isset($clientes[$monto->codclie.'*'.$monto->cliente.'*ReciboIng-'.$monto->numerod][$monto->tarjeta.' '.$monto->codtarj])){
                    $clientes[$monto->codclie.'*'.$monto->cliente.'*ReciboIng-'.$monto->numerod][$monto->tarjeta.' '.$monto->codtarj] = $monto->cliente;
                }

                if(!isset($clases[$monto->clase])){
                    $clases[$monto->clase] = $monto->codtarj;
                }

                if(!isset($listado[$monto->codclie.'*'.$monto->cliente.'*ReciboIng--'.$monto->numerod][$monto->tarjeta.' '.$monto->codtarj][$monto->clase]))
                    $listado[$monto->codclie.'*'.$monto->cliente.'*ReciboIng--'.$monto->numerod][$monto->tarjeta.' '.$monto->codtarj][$monto->clase] = 0;

                $listado[$monto->codclie.'*'.$monto->cliente.'*ReciboIng--'.$monto->numerod][$monto->tarjeta.' '.$monto->codtarj][$monto->clase] += $monto->dolares;

            }
        ksort($clientes);
        ksort($clases);

        return view('detalleInstPagodolares', compact('fechasreport','clases', 'clientes', 'fecha1', 'fecha2', 'listado'))->render();
    }

    public function index()
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $tarjetas  = Satarj::where('comercial',$comercialid)->get();
        return view('tarjetas-list-view', compact('tarjetas') );
    }

    public function tarjetas()
    {

        return view('tarjetas');
    }

    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercialid  = $sucursal->fk_comercial;

        $tarjetas  = Satarj::where('comercial',$comercialid)->get();
        foreach ($tarjetas as &$tarjeta){
            $tarjeta->activo = 0;
            if($tarjeta->multiple or $tarjeta->fk_sucursal == $sucursalid)
                $tarjeta->activo = 1;
        }
        return response()->json(['success'=>'success', 'tarjetas' => $tarjetas], 200);
    }

    public function json()
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $all = Satarj::where('comercial',$comercialid)->orderBy('descrip','asc')->get();
        $aux = [];
        $tajretass = [];
        foreach ($all as $item){
            $aux = [
                "id"            => "$item->id",
                "codtarj"       => "$item->codtarj",
                "descrip"       => "$item->descrip",
                "bs"            => ($item->bs       == 1)? "1": "0",
                "dolares"       => ($item->dolares  == 1)? "1": "0",
                "pesos"         => ($item->pesos    == 1)? "1": "0",
                "activo"        => ($item->activo       )? "Activo":"Inactivo",
                "multiple"      => ($item->multiple     )? "Si":"No"
            ];
            array_push($tajretass,$aux);
        }
        return response()->json($tajretass );
    }

    public function create()
    {
        //
    }

    public function quitarubicado(Request $request)
    {
        $idsucu  = $request->idsucu;
        $codtarj = $request->codtarj;

        $tarjeta = Satarj::where('codtarj', $codtarj)->first();
        $tarjeta->fk_sucursal = 0;
        $tarjeta->save();

        $sucursales = Sasucursal::with('tarjetas')->where('id', $idsucu)->get();
        return view('tarjetas-contentubicados', compact('sucursales'))->render();
    }

    public function contentubicado(Request $request)
    {
        $idsucu = $request->idsucu;
        $sucursales = Sasucursal::with('tarjetas')->where('id',$idsucu)->get();
        return view('tarjetas-contentubicados', compact('sucursales'))->render();
    }

    public function ubicados()
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $sucursales = Sasucursal::with('tarjetas')
            ->where("fk_comercial", $comercialid)
            ->orderBy('descrip')->get();


        return view('tarjetas-ubicados', compact('sucursales'))->render();
    }

    public function noubicado(Request $request)
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $idsucu  = $request->idsucu;
        $codtarj = $request->codtarj;

        if($idsucu > 0 and $codtarj > 0){
            $tarjeta = Satarj::where('codtarj', $codtarj)->first();
            $tarjeta->fk_sucursal = $idsucu;
            $tarjeta->save();
        }

        $tarjetas = Satarj::where(['comercial'=>$comercialid, 'fk_sucursal'=> 0, 'multiple'=>0])->get();


        return view('tarjetas-noubicado', compact('tarjetas'))->render();
    }

    public function store(Request $request)
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $tarjeta = Satarj::where(['codtarj' => $request->codtarj, 'comercial' => $comercialid])->first();

        if(!isset($tarjeta->codubic)){
            $newTarj = new Satarj();
            $newTarj->fill($request->all());
            $newTarj->bs      = ($request->bs)     ? 1: 0;
            $newTarj->pesos   = ($request->pesos)  ? 1: 0;
            $newTarj->dolares = ($request->dolares)? 1: 0;
            $newTarj->multiple= ($request->multiple)? 1: 0;
            $newTarj->activo  = 1;
            $newTarj->comercial  = $comercialid;
            $newTarj->save();
            return response()->json(['success'=>'success' ]);
        }else{
            return response()->json(['error'=>'error' ]);
        }


    }

    public function show(Satarj $Satarj)
    {
        //
    }

    public function edit(Satarj $Satarj)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $Instpago = Satarj::find($id);
        $Instpago->descrip   = $request->descrip;
        $Instpago->bs        = ($request->bs           == 1 )? 1: 0;
        $Instpago->dolares   = ($request->dolares      == 1 )? 1: 0;
        $Instpago->pesos     = ($request->pesos        == 1 )? 1: 0;
        $Instpago->multiple  = ($request->multiple     == '1'     )? 1: 0;
        $Instpago->activo    = ($request->activo       == 'Activo')? 1: 0;
        $Instpago->save();

        response()->json(['success'=>'success',"actializado"=>111]);
    }

    public function destroy(Satarj $Satarj)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Safact;
use App\Models\Saipavta;
use App\Models\Saitemfac;
use App\Models\Saseprfac;
use Illuminate\Http\Request;

class SafactController extends Controller
{

    public function documentoSafact(Request $request)
    {
        $tipofac = $request->tipofac;
        $numerod = $request->numerod;

        $documento = Safact::selectRaw("date_format(fechat,'%d/%m/%Y') as fecha, date_format(fechat,'%h:%i %p') as hora")->where(['numerod'=>  $numerod, 'tipofac'=> $tipofac])->first();

        return view('documentoventa', compact('numerod','tipofac', 'documento'));
    }

    public function documentoAjax(Request $request)
    {
        $tipofac = $request->tipofac;
        $numerod = $request->numerod;
        $fk_sucu = $request->fksucu;

        $documento = Safact::selectRaw("date_format(fechat,'%d/%m/%Y') as fecha, date_format(fechat,'%h:%i %p') as hora")->where(['numerod'=>  $numerod, 'tipofac'=> $tipofac,  'fk_sucursal'=> $fk_sucu])->first();

        return view('layouts.documento', compact('numerod','tipofac', 'documento'))->render();
    }

    public function documento(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $facturas = $request->facturas;
        $facturas = json_decode($facturas);
        $vector   = [];

        if(isset($facturas)){
            foreach ($facturas as $fac){

                if(isset($fac->nrounico)){
                    $record = Safact::where(['nrounico'=>  $fac->nrounico, 'fk_sucursal'=> $sucursalid])->first();

                    $additems = 0;
                    $aux = (array) $fac;

                    if(isset($record) and isset($record->id) and $record->id >0){
                        $record = Safact::find($record->id);
                        $record->fill($aux) ;
                        $record->save();
                        $vector[$fac->nrounico] = 1;
                    }else{

                        $record = new Safact();
                        $record->fill($aux) ;
                        $additems = 1;

                        $record->fk_sucursal = $sucursalid ;

                        if($additems and isset($fac->allitems)){
                            foreach ($fac->allitems as $allitem){
                                $newitem = new Saitemfac();
                                $auxitem = (array) $allitem;
                                $newitem->fill($auxitem);
                                $newitem->fk_sucursal = $sucursalid ;
                                $newitem->save();
                            }
                        }

                        if($additems and isset($fac->tarjetas)){
                            foreach ($fac->tarjetas as $tarjeta){
                                $newtar  = new Saipavta();
                                $auxitem = (array) $tarjeta;
                                $newtar->fill($auxitem);
                                $newtar->fk_sucursal = $sucursalid ;
                                $newtar->save();
                            }
                        }

                        if($additems and isset($fac->seriales)){
                            foreach ($fac->seriales as $seriales){
                                $newser = new Saseprfac();
                                $auxser = (array) $seriales;
                                $newser->fill($auxser);
                                $newser->fk_sucursal = $sucursalid ;
                                $newser->save();
                            }
                        }
                        $record->save();
                        $vector[$fac->nrounico] = 1;
                    }
                }
            }
            return response()->json(['success' => 'success', 'vector' => $vector], 200);
        }

        return response()->json(['json' => 'json', ], 200);
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Safact $safact)
    {
        //
    }

    public function edit(Safact $safact)
    {
        //
    }

    public function update(Request $request, Safact $safact)
    {
        //
    }

    public function destroy(Safact $safact)
    {
        //
    }
}

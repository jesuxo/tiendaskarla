<?php

namespace App\Http\Controllers;

use App\Models\Saprod;
use App\Models\Sainsta;
use App\Models\GrupoDescuento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoGrupoDescuentoController extends Controller
{
    public function index()
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $grupos = GrupoDescuento::porComercial($comercialid)->activo()->get();
        $categorias = Sainsta::porComercial($comercialid)->nivel1()->orderBy('descrip')->get();

        return view('productos-grupos.index', compact('grupos', 'categorias'));
    }

    public function getProductos(Request $request)
    {
        $search = $request->get('search', '');
        $categoriaId = $request->get('categoria_id', '');
        $grupoId = $request->get('grupo_id', '');
        $comercialid = session('comercialid') ?: 1;

        $query = Saprod::where('comercial', $comercialid)
            ->where('activo', 1);

        // Búsqueda avanzada
        if (!empty($search)) {
            $sql = '';
            $vector = explode(' ', $search);
            foreach ($vector as $index => $item) {
                if ($index > 0) {
                    $sql .= " and ";
                }
                $sql .= " ( saprod.codprod like '%$item%' or saprod.descrip like '%$item%' or saprod.descrip2 like '%$item%'  or saprod.marca like '%$item%'  or saprod.refere like '%$item%' or saprod.color like '%$item%' ) ";
            }
            $query = $query->whereRaw("($sql)");
        }

        // Filtro por categoría
        if (!empty($categoriaId)) {
            $instancia = Sainsta::where('codinst', $categoriaId)->first();
            list($padre, $codinsta) = explode('.', $instancia->codalte);
            $padre = $padre . ".";
            $query->WhereHas('instancia', function($cat) use ($padre) {
                $cat->where('codalte', 'LIKE', "{$padre}%");
            });
        }

        $productos = $query->select('codprod', 'color', 'descrip', 'descrip2', 'descrip3', 'refere', 'marca', 'costod3', 'codinst')
            ->orderBy('descrip')
            ->limit(100)
            ->get();

        $tasaActual = $this->getTasaCambioActual();

        // Obtener TODAS las asignaciones de estos productos a CUALQUIER grupo
        $codprods = $productos->pluck('codprod')->toArray();

        $todasAsignaciones = DB::table('producto_grupo_descuento')
            ->whereIn('codprod', $codprods)
            ->get(['codprod', 'grupo_id', 'precio_final']);

        // Agrupar por producto
        $asignacionesPorProducto = [];
        foreach ($todasAsignaciones as $asignacion) {
            if (!isset($asignacionesPorProducto[$asignacion->codprod])) {
                $asignacionesPorProducto[$asignacion->codprod] = [];
            }
            $asignacionesPorProducto[$asignacion->codprod][] = [
                'grupo_id' => $asignacion->grupo_id,
                'precio' => $asignacion->precio_final
            ];
        }

        // Obtener nombres de grupos para mostrar
        $gruposInfo = GrupoDescuento::whereIn('id', $todasAsignaciones->pluck('grupo_id')->unique())
            ->get(['id', 'nombre'])
            ->keyBy('id');

        foreach ($productos as $producto) {
            $producto->precio_cop_estimado = $producto->costod3 * $tasaActual;
            $producto->categoria_nombre = $producto->instancia ? $producto->instancia->descrip : 'Sin categoría';

            $asignaciones = $asignacionesPorProducto[$producto->codprod] ?? [];

            if (!empty($asignaciones)) {
                $producto->tiene_asignacion = true;
                // Si se filtró por un grupo específico, mostrar ese precio
                if (!empty($grupoId)) {
                    $asignacionEspecifica = collect($asignaciones)->firstWhere('grupo_id', $grupoId);
                    $producto->ya_asignado = !is_null($asignacionEspecifica);
                    $producto->precio_asignado = $asignacionEspecifica['precio'] ?? null;
                    $producto->grupo_asignado_nombre = $producto->ya_asignado ? ($gruposInfo[$grupoId]->nombre ?? '') : '';
                } else {
                    // Si no hay filtro, mostrar el precio más bajo entre todos los grupos
                    $precios = collect($asignaciones)->pluck('precio');
                    $producto->ya_asignado = true;
                    $producto->precio_asignado = $precios->min(); // Precio más bajo
                    $grupoConMejorPrecio = collect($asignaciones)->firstWhere('precio', $producto->precio_asignado);
                    $producto->grupo_asignado_nombre = $gruposInfo[$grupoConMejorPrecio['grupo_id']]->nombre ?? 'Desconocido';
                }
            } else {
                $producto->tiene_asignacion = false;
                $producto->ya_asignado = false;
                $producto->precio_asignado = null;
                $producto->grupo_asignado_nombre = '';
            }
        }

        return response()->json($productos);
    }

    public function obtenerPreciosAsignados(Request $request)
    {
        $request->validate([
            'codprods' => 'required|array',
            'codprods.*' => 'exists:saprod,codprod',
            'grupo_id' => 'required|exists:grupos_descuento,id'
        ]);

        $precios = DB::table('producto_grupo_descuento')
            ->whereIn('codprod', $request->codprods)
            ->where('grupo_id', $request->grupo_id)
            ->get(['codprod', 'precio_final'])
            ->keyBy('codprod')
            ->map(function($item) {
                return $item->precio_final;
            })
            ->toArray();

        return response()->json(['precios' => $precios]);
    }

    public function getGrupos()
    {
        $comercialid = session('comercialid') ?: 1;
        $grupos = GrupoDescuento::porComercial($comercialid)->activo()->get(['id', 'nombre', 'porcentaje_descuento']);
        return response()->json($grupos);
    }

    public function getProductosPorGrupo(Request $request, $grupoId)
    {
        $grupo = GrupoDescuento::findOrFail($grupoId);
        $perPage = $request->get('per_page', 20); // 20, 50, 100 productos por página

        $productos = $grupo->productos()
            ->select('saprod.codprod', 'saprod.descrip', 'saprod.descrip2',
                'saprod.marca', 'saprod.refere', 'saprod.costod3',
                'producto_grupo_descuento.precio_final',
                'producto_grupo_descuento.tasa_cambio_usd',
                'producto_grupo_descuento.created_at')
            ->with('instancia')
            ->orderBy('saprod.descrip')
            ->paginate($perPage);

        // Si es una petición AJAX, devolver solo el HTML
        if ($request->ajax()) {
            return view('productos-grupos.partials.productos-por-grupo',
                compact('productos', 'grupoId'))->render();
        }

        return view('productos-grupos.partials.productos-por-grupo',
            compact('productos', 'grupoId'));
    }

    public function vaciarGrupo($grupoId)
    {
        try {
            DB::beginTransaction();

            // Verificar que el grupo existe
            $grupo = GrupoDescuento::findOrFail($grupoId);

            // Eliminar todas las relaciones del grupo
            $eliminados = DB::table('producto_grupo_descuento')
                ->where('grupo_id', $grupoId)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Se han eliminado {$eliminados} productos del grupo '{$grupo->nombre}'",
                'eliminados' => $eliminados
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al vaciar el grupo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function buscarEnGrupo(Request $request, $grupoId)
    {
        $search = $request->get('search', '');
        $grupo = GrupoDescuento::findOrFail($grupoId);

        $query = $grupo->productos()
            ->select('saprod.codprod', 'saprod.descrip', 'saprod.descrip2',
                'saprod.marca', 'saprod.refere', 'saprod.costod3',
                'producto_grupo_descuento.precio_final',
                'producto_grupo_descuento.tasa_cambio_usd',
                'producto_grupo_descuento.created_at')
            ->with('instancia');

        if (!empty($search)) {

            $sql      = '';
            $vector   = explode(' ', $search);
            foreach ($vector as $index => $item){
                if($index > 0){
                    $sql .= " and ";
                }
                $sql .= " ( saprod.codprod like '%$item%' or saprod.descrip like '%$item%' or saprod.descrip2 like '%$item%'  or saprod.marca like '%$item%'  or saprod.refere like '%$item%' or saprod.color like '%$item%' ) ";
            }

            $query = $query->whereRaw("($sql)");

        }

        $productos = $query->orderBy('saprod.descrip')->get();

        return view('productos-grupos.partials.busqueda-resultados',
            compact('productos', 'grupoId'));
    }

    public function asignar(Request $request)
    {
        $request->validate([
            'codprod' => 'required|exists:saprod,codprod',
            'grupo_id' => 'required|exists:grupos_descuento,id',
            'precio_final' => 'required|numeric|min:0',
            'tasa_cambio' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Verificar si ya existe la relación
            $existe = DB::table('producto_grupo_descuento')
                ->where('codprod', $request->codprod)
                ->where('grupo_id', $request->grupo_id)
                ->exists();

            $data = [
                'precio_final' => $request->precio_final,
                'tasa_cambio_usd' => $request->tasa_cambio,
                'updated_at' => now()
            ];

            if (!$existe) {
                $data['creado_por'] = auth()->id();
                $data['created_at'] = now();
                DB::table('producto_grupo_descuento')->insert(array_merge([
                    'codprod' => $request->codprod,
                    'grupo_id' => $request->grupo_id,
                ], $data));
            } else {
                DB::table('producto_grupo_descuento')
                    ->where('codprod', $request->codprod)
                    ->where('grupo_id', $request->grupo_id)
                    ->update($data);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Producto asignado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verificarAsignacion(Request $request)
    {
        $request->validate([
            'codprod' => 'required|exists:saprod,codprod',
            'grupo_id' => 'required|exists:grupos_descuento,id'
        ]);

        $asignado = DB::table('producto_grupo_descuento')
            ->where('codprod', $request->codprod)
            ->where('grupo_id', $request->grupo_id)
            ->exists();

        return response()->json(['asignado' => $asignado]);
    }

    public function verificarMultiplesAsignaciones(Request $request)
    {
        $request->validate([
            'codprods' => 'required|array',
            'codprods.*' => 'exists:saprod,codprod',
            'grupo_id' => 'required|exists:grupos_descuento,id'
        ]);

        $asignados = DB::table('producto_grupo_descuento')
            ->whereIn('codprod', $request->codprods)
            ->where('grupo_id', $request->grupo_id)
            ->pluck('codprod')
            ->toArray();

        $resultado = [];
        foreach ($request->codprods as $codprod) {
            $resultado[$codprod] = in_array($codprod, $asignados);
        }

        return response()->json(['asignados' => $resultado]);
    }

    public function quitar(Request $request)
    {
        $request->validate([
            'codprod' => 'required|exists:saprod,codprod',
            'grupo_id' => 'required|exists:grupos_descuento,id'
        ]);

        try {
            DB::table('producto_grupo_descuento')
                ->where('codprod', $request->codprod)
                ->where('grupo_id', $request->grupo_id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto removido del grupo correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al remover: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getTasaCambioActual()
    {
        $tasa = DB::table('sacomercial')->value('tasapeso');
        return $tasa ? floatval($tasa) : 3600;
    }

    public function getCategorias(Request $request)
    {
        $comercialid = session('comercialid') ?: 1;
        $nivel = $request->get('nivel', 1);

        $categorias = Sainsta::porComercial($comercialid)
            ->where('nivel', $nivel)
            ->orderBy('descrip')
            ->get(['codinst', 'descrip', 'nivel']);

        return response()->json($categorias);
    }

    public function getTasaActual()
    {
        return response()->json(['tasa' => $this->getTasaCambioActual()]);
    }
}

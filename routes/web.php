<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\CwtokenController;
use App\Http\Controllers\SavendController;
use App\Http\Controllers\TonerController;
use App\Http\Controllers\UserSucursalController;
use App\Http\Controllers\SacompController;
use App\Http\Controllers\SainstaController;
use App\Http\Controllers\SaprovController;
use App\Http\Controllers\ProductoGrupoDescuentoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Auth Route::get('signup', 'App\Http\Controllers\Auth\RegisterController@signup')->name('signup');*/

Route::get('signuptiendaskarla', 'App\Http\Controllers\Auth\RegisterController@signup')->name('signup');

Route::get('/panelclientes',  [App\Http\Controllers\SiteController::class, 'panelclientes']  )->name('panelclientes');

Route::match(['get','post'],'/',  [App\Http\Controllers\SiteController::class, 'index']  )->name('site');
Route::get('/unsubscribe',  [App\Http\Controllers\SiteController::class, 'index'] )->name('unsubscribe');
Route::get('/promociones',  [App\Http\Controllers\SiteController::class, 'promociones'] )->name('promociones');
Route::get( '/producto/{id}',    [App\Http\Controllers\SiteController::class, 'index'] )->name('ver.producto');
Route::get( '/promoid/{id}',     [App\Http\Controllers\SiteController::class, 'promoid'] )->name('ver.promoid');
Route::get( '/promocion/{id}',   [App\Http\Controllers\SiteController::class, 'promo'] )->name('ver.promo');
Route::get( '/busqueda/{id?}',   [App\Http\Controllers\SiteController::class, 'index'] )->name('url.busqueda');
Route::get( '/promociones/{busqueda?}',  [App\Http\Controllers\SiteController::class, 'promociones'] )->name('url.busquedapromo');
Route::post( '/gourlpromo',  [App\Http\Controllers\SiteController::class, 'gourlpromo'] )->name('gourlpromo');
Route::post( '/gourl',  [App\Http\Controllers\SiteController::class, 'gourl'] )->name('gourl');
Route::get( '/instancia/{id}',  [App\Http\Controllers\SiteController::class, 'index'] )->name('url.instancia');
Route::get( '/pagar/{amount}',  [App\Http\Controllers\SiteController::class, 'pago'] )->name('pagar.monto');
Route::put( '/procesar/pago',  [App\Http\Controllers\SiteController::class, 'procesar'] )->name('procesar.pago');
Route::put( '/encoded/msg',  [App\Http\Controllers\SiteController::class, 'encoded'] )->name('encode.msg');
Route::get('/agregar/producto',  [App\Http\Controllers\ComprasController::class, 'agregar']  )->name('agregar.producto');
Route::get('/actualizar/agregados',  [App\Http\Controllers\SiteController::class, 'agregados'] )->name('actualizar.agregados');
Route::get('/abrir/lista',  [App\Http\Controllers\ComprasController::class, 'abrirlista']  )->name('abrir.lista');
Route::post('/update/csrf',  [App\Http\Controllers\SiteController::class, 'tokencsrf'] )->name('update.csrf');



Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

Auth::routes();

// Route::post('login', 'Auth\LoginController@login')->name('login');
// Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::post('register', 'Auth\RegisterController@register')->name('register');
// Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
// Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');


Auth::routes(['verify' => true]);

Route::group(['prefix' => 'error'], function(){
    Route::get('404', function () { return view('error.404'); });
    Route::get('500', function () { return view('error.500'); });
});


Route::middleware(['auth'])->group(function () {

    Route::prefix('usersucursal')->group(function () {
        Route::get('/', [UserSucursalController::class, 'index'])->name('usersucursal.index');
        Route::get('/usuarios', [UserSucursalController::class, 'getUsersConSucursales'])->name('usersucursal.usuarios');
        Route::get('/sucursales', [UserSucursalController::class, 'getAllSucursales'])->name('usersucursal.sucursales');
        Route::get('/sucursales-asignadas/{userId}', [UserSucursalController::class, 'getSucursalesAsignadasPorUsuario']);
        Route::get('/usuarios-por-sucursal/{sucursalId}', [UserSucursalController::class, 'getUsuariosPorSucursal']);
        Route::post('/asignar', [UserSucursalController::class, 'asignarSucursal'])->name('usersucursal.asignar');
        Route::post('/quitar', [UserSucursalController::class, 'quitarSucursal'])->name('usersucursal.quitar');
    });


    Route::resource('tokens',  CwtokenController::class);
    Route::prefix('tokens')->group(function () {
        Route::get('/', [CwtokenController::class, 'reportetokens'])->name('reportetokens');
        Route::post('/', [CwtokenController::class, 'reportetokens']);
        Route::post('/store', [CwtokenController::class, 'store'])->name('tokens.store');
        Route::post('/update', [CwtokenController::class, 'tokenupdate'])->name('token.update');
        Route::post('/generar-auto', [CwtokenController::class, 'generarTokenAuto']);
        Route::post('/new', [CwtokenController::class, 'newtoken']);
        Route::get('/export', [CwtokenController::class, 'export']);
        Route::delete('/{id}', [CwtokenController::class, 'destroy']);
        Route::post('/bulk-delete', [CwtokenController::class, 'bulkDelete']);
    });

    Route::get('/verpermisos/{id?}', [PermissionController::class, 'showForm'])->name('permissions.assign');
    Route::post('/verpermisos', [PermissionController::class, 'assign']);
    Route::post('/create/permissions', [PermissionController::class, 'create'])->name('permissions.create');
    Route::get('/revoke/{user}/{permiso}', [PermissionController::class, 'revokePermission'])->name('permissions.revoke');


    Route::post('/cambiotasas', [App\Http\Controllers\HomeController::class, 'cambiotasas'])->name('cambio.tasas');

    Route::get('/buscarproducto/{codprod}/{comercial}', [\App\Http\Controllers\SaprodController::class, 'buscarproductoget'])->name('buscarproductoget');

    Route::controller(\App\Http\Controllers\SafactController::class)->group(function () {
        Route::get('doc/{tipofac}/{numerod}', 'documentoSafact');
        Route::post('openDoc', 'documentoAjax');
    });

    Route::controller(\App\Http\Controllers\SaclieController::class)->group(function () {
        Route::match(['get','post'],'/clientes/{codclie?}/{tab?}', 'index')->name('buscarclientes');
    });

    Route::match(['get','post'],'/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    Route::get('logout',[\App\Http\Controllers\Auth\LoginController::class, 'logout']);

    Route::resource('compraitems', \App\Http\Controllers\CompraItemsController::class);

    //Route::get('{any}', [TonerController::class, 'index']);
    Route::get('/bienvenido', [\App\Http\Controllers\SiteController::class, 'bienvenido'])->name('bienvenido');
    Route::get('components/{any}', [TonerController::class, 'components']);


});


Route::middleware(['check.admin'])->group(function () {

    Route::match(['get','post'],'/resumenVentas', [App\Http\Controllers\HomeController::class, 'resumenVentas'])->name('resumenVentas');

    Route::get('/cambiarcomercial/{comercialid}', [App\Http\Controllers\HomeController::class, 'cambiarcomercial'])->name('cambiarcomercial');

    Route::post('saprod/update', [App\Http\Controllers\SaprodController::class, 'updateSaprodData']);
    Route::get('saprod/export/{codalte}', [App\Http\Controllers\SaprodController::class, 'saprodexport']);

    Route::resource('vendedores', SavendController::class);
    Route::controller(SavendController::class)->group(function () {
        Route::get('savend/json', 'json')->name('vendedores.json');
    });

    Route::resource('vehiculos', \App\Http\Controllers\CWVehiculoController::class);
    Route::controller(\App\Http\Controllers\CWVehiculoController::class)->group(function () {

    });

    Route::resource('instancias', SainstaController::class);
    Route::controller(SainstaController::class)->group(function () {
        Route::get('sainsta/json', 'json')->name('sainsta.json');
        Route::post('sainsta/check/lastprod/{codinst}', 'lastprod');
    });


    Route::resource('proveedores', SaprovController::class);

    Route::get ('/proveedores/debug/{codprov}/{codprod}', [SaprovController::class, 'debug'])->name('proveedores.debug');
    Route::post('/proveedores/buscarPredictivo', [SaprovController::class, 'buscarPredictivo'])->name('proveedores.buscarPredictivo');
    Route::get ('/proveedores/{codprov}/productos-panel', [SaprovController::class, 'productosPanel'])->name('proveedores.productos-panel');
    Route::post('/proveedores/{codprov}/productos-panel', [SaprovController::class, 'productosPanel'])->name('proveedores.productos-panel.post');

    Route::get('proveedores/{codprov}/cuentas-por-pagar', [SaprovController::class, 'getCuentasPorPagar'])
        ->name('proveedores.cuentas-por-pagar');

    Route::get('proveedores/cuentas-por-pagar/resumen-general', [SaprovController::class, 'getResumenGeneralCuentasPorPagar'])
        ->name('proveedores.cuentas-por-pagar.resumen-general');

    Route::controller(SaprovController::class)->group(function () {
        Route::get ('saprov/json', 'json');
        Route::match(['get','post'],'/proveedores/{codprov?}/{tab?}', 'index')->name('proveedores.index');
        Route::post('proveedoresupdate', 'proveedoresupdate')->name('proveedoresupdate');
        Route::post('/proveedores/marcar-pagado', 'marcarPagado')->name('proveedores.marcar-pagado');
    });

    Route::get('proveedores/pagos/pendientes', [SaprovController::class, 'pagosPendientes'])->name('proveedores.pagos-pendientes');
    Route::get('proveedores-json', [SaprovController::class, 'json'])->name('proveedores.json');
    Route::resource('productos', \App\Http\Controllers\SaprodController::class);
    Route::controller(\App\Http\Controllers\SaprodController::class)->group(function () {
        Route::get('/saprod/marcas', [\App\Http\Controllers\SaprodController::class, 'getMarcas'])->name('saprod.marcas');
        Route::post('saprod/listprodubiccompany', 'listprodubiccompany')->name('saprod.listprodubiccompany');
        Route::get('saprod/json', 'json');
        Route::post('saprod/check/codprod/{codprod}', 'checkcodprod');
        Route::post('saprod/home/busqueda', 'busquedaHomeProd');
        Route::match(['get','post'],'existencias', 'existencias');
        Route::post('reporte/existen/php', 'existenciasphp');
        Route::match(['get','post'],'ventas/resultado', 'resultadosucursales');
        Route::post('saprod/upload', 'upload');
        Route::match(['get','post'],'ventas/productos/sucursales', 'productossucursales');
        Route::post('saprod/viewprodinstsanciascodalte', 'viewprodinstsanciascodalte');
        Route::match(['get','post'],'mermas/sucursales', 'mermassucursales');
    });

    Route::controller(\App\Http\Controllers\SasucursalController::class)->group(function () {
        Route::post('sascursal/bancos', 'bancos');
    });

    Route::match(['get','post'],'/reporte/compra', [SacompController::class, 'reportecompra'])->name('reportecompra');
    Route::post('/compras/documento-ajax', [SacompController::class, 'documentoAjax'])->name('compras.documento-ajax');

    Route::resource('compras', SacompController::class);
    Route::controller(SacompController::class)->group(function () {
        Route::get('compra/{id}', 'documentoSacomp');
        Route::get('compra/seriales/{id}', 'documentoSerialesSacomp');
    });

    // En el grupo de rutas con middleware 'check.admin' o dentro del grupo autenticado
    Route::prefix('productos-grupos')->group(function () {
        Route::get('/', [ProductoGrupoDescuentoController::class, 'index'])->name('productos-grupos.index');
        Route::get('/productos', [ProductoGrupoDescuentoController::class, 'getProductos'])->name('productos-grupos.productos');
        Route::get('/grupos', [ProductoGrupoDescuentoController::class, 'getGrupos'])->name('productos-grupos.grupos');
        Route::get('/categorias', [ProductoGrupoDescuentoController::class, 'getCategorias'])->name('productos-grupos.categorias');
        Route::get('/productos-por-grupo/{grupoId}', [ProductoGrupoDescuentoController::class, 'getProductosPorGrupo'])->name('productos-grupos.productos-por-grupo');
        Route::post('/asignar', [ProductoGrupoDescuentoController::class, 'asignar'])->name('productos-grupos.asignar');
        Route::post('/quitar', [ProductoGrupoDescuentoController::class, 'quitar'])->name('productos-grupos.quitar');
        Route::get('/tasa-actual', [ProductoGrupoDescuentoController::class, 'getTasaActual'])->name('productos-grupos.tasa-actual');
        Route::delete('/vaciar-grupo/{grupoId}', [ProductoGrupoDescuentoController::class, 'vaciarGrupo'])->name('productos-grupos.vaciar-grupo');
        Route::get('/buscar-en-grupo/{grupoId}', [ProductoGrupoDescuentoController::class, 'buscarEnGrupo'])->name('productos-grupos.buscar-en-grupo');
        Route::get('/verificar-asignacion', [ProductoGrupoDescuentoController::class, 'verificarAsignacion'])->name('productos-grupos.verificar-asignacion');
        Route::post('/verificar-multiples', [ProductoGrupoDescuentoController::class, 'verificarMultiplesAsignaciones'])->name('productos-grupos.verificar-multiples');
        Route::post('/obtener-precios-asignados', [ProductoGrupoDescuentoController::class, 'obtenerPreciosAsignados'])->name('productos-grupos.obtener-precios-asignados');
        Route::get('/asignaciones-producto/{codprod}', [ProductoGrupoDescuentoController::class, 'getAsignacionesProducto'])
            ->name('productos-grupos.asignaciones-producto');
    });


    Route::resource('depositos', \App\Http\Controllers\SadepoController::class);
    Route::controller(\App\Http\Controllers\SadepoController::class)->group(function () {
        Route::get('sadepo/json', 'json');
    });

    Route::controller(\App\Http\Controllers\SaacxcController::class)->group(function () {
        Route::match(['get','post'],'cxc/{id?}', 'saacxc');
        Route::post('/cxclist', 'cxclist');
    });

    Route::resource('instpago', \App\Http\Controllers\SatarjController::class);
    Route::controller(\App\Http\Controllers\SatarjController::class)->group(function () {
        Route::get('satarj/json', 'json');
        Route::get('/tarjetas', 'tarjetas');
        Route::post('/tarjetas/content/ubicado', 'contentubicado');
        Route::post('/tarjetas/ubicados', 'ubicados');
        Route::post('/tarjetas/noubicado', 'noubicado');
        Route::post('/tarjetas/quitarubicado', 'quitarubicado');
    });
    Route::match(['get','post'],'/reporte/instpagobs',      [App\Http\Controllers\SatarjController::class, 'instpagobs'])->name('instpagobs');
    Route::match(['get','post'],'/reporte/instpagodolares', [App\Http\Controllers\SatarjController::class, 'instpagodolares'])->name('instpagodolares');
    Route::post( '/reporte/detinstpagodolares', [App\Http\Controllers\SatarjController::class, 'detinstpagodolares'])->name('detinstpagodolares');

    Route::match(['get','post'],'/reporte/venta', [App\Http\Controllers\HomeController::class, 'reporteventa'])->name('reporteventa');

    Route::post('/reporte/venta/sucu', [App\Http\Controllers\HomeController::class, 'reporteventasucu'])->name('reporteventasucu');

    //Route::match(['get','post'],'/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

});



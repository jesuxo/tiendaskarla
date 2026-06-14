<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="author" content="Jesus Celis - celisweb" />
    <meta name="copyright" content="INNOVA STATE" />
    <link rel="stylesheet" href="assets/css/plugins/aos.css">
    <link rel="stylesheet" href="assets/css/plugins/bootstrap.min.css">
    <link rel="stylesheet" href="assets/sass/style.css">
    <link rel="stylesheet" href="css/style.css">


    <link rel="stylesheet" href="https://unpkg.com/element-ui@2.5.4/lib/theme-chalk/index.css">

    @if(!isset($data->id))

        <title>TIENDAS KARLA</title>
        <meta name="description" content="">
        <link rel="canonical" href="https://www.tiendaskarla.com.ve">
        <meta property="og:title" content="TIENDAS KARLA">
        <meta property="og:description" content="">
        <meta property="og:type" content="WebPage">
        <meta property="og:image" content="http://tiendaskarla.com.ve/img/logo.png">
        <meta property="og:url" content="http://tiendaskarla.com.ve">

        <meta name="twitter:title" content="TIENDAS KARLA  - ">
        <meta name="twitter:description" content="">
        <meta name="twitter:site" content="@tiendaskarlave">
        <script type="application/ld+json">{"@context":"https://schema.org","@type":"WebPage","name":" TIENDAS KARLA ","description":"VENTA DE ZAPATOS DEPORTIVOS Y CASUALIES PARA DAMA, CABALLEROS, NIÑOS Y NIÑAS "}</script>


    @endif

    <style>
        body{

            background: white !important;
        }
        .display_none{
            display: none;
        }
        .nav-link, .btn, a, button {
            font-weight: 100 !important;
        }
        .topdetail{
            margin-top: 120px;
        }
        .pr_details{
            padding-top: 50px;
        }
        @media (max-width: 756px) {
            .swiper-container{
                height: 300px;
            }
            .btn-blocked{
                opacity: 0.3;
                color: red !important;
                border: 1px solid rgba(255,0,0,0.3);
                border-radius: 4px;
            }
            .row-reverse{
                flex-direction: column-reverse;
            }
        }
        .breadcrumb_area {
            background: url({{asset('img/bg.jpg')}}) no-repeat;
            background-size: cover;
            background-position: top center;
            position: relative;
            z-index: 1;
            padding: 235px 0px 125px;
            overflow: hidden;
        }
        .search-form button {
            position: absolute;
            background: 0 0;
            padding: 0;
            border: 0;
            right: 9px;
            top: 51%;
            -webkit-transform: translateY(-50%);
            -ms-transform: translateY(-50%);
            transform: translateY(-50%);
            font-size: 16px;
            color: #282835;
            padding-top: 10px;
            z-index: 10;
        }
        .amount{ padding-left: 10px; font-size: 16px }

        .curspor_pointer{
            cursor: pointer;
        }

        .header_area{
            animation: slide-down 0.7s;
        }

        @media (max-width: 560px){

            .modal-content{
                width: 93%;
                margin: auto;
            }
            .topdetail{
                margin-top: 0px;
            }
            .pr_details{
                padding-top: 0px;
            }
        }

        .form-control:focus{
            box-shadow:  none !important;
        }

        .menu > .nav-item.submenu .dropdown-menu.mega_menu_three > .nav-item > .dropdown-menu .nav-item {
            padding: 0px 10px;
        }

        .menu > .nav-item.submenu .dropdown-menu.mega_menu_three > .nav-item > .dropdown-menu .nav-item .nav-link .navdropdown_link .navdropdown_content h5:hover{
            color: #0071ba;
        }

        .small, small{
            font-weight: 100 !important;
        }

        .f_100{font-weight:100 !important;}

        /*.loadingoverlay{*/
        /*    align-items:unset !important;*/
        /*    padding-top: 20px !important;*/
        /*}*/

        .point_color{
            width: 40px;
            border-radius: 2px;
        }

        .chat-float{
            outline: none !important;
            visibility: visible !important;
            resize: none !important;
            box-shadow: none !important;
            overflow: visible !important;
            background: none !important;
            opacity: 1 !important;
            filter: alpha(opacity=100) !important;
            -mz-opacity: 1 !important;
            -khtml-opacity: 1 !important;
            top: auto !important;
            right: 16px !important;
            bottom: 20px !important;
            left: auto !important;
            position: fixed !important;
            border: 0 !important;
            min-height: 64px !important;
            min-width: 64px !important;
            max-height: 64px !important;
            max-width: 64px !important;
            padding: 0 !important;
            margin: 0 !important;
            -moz-transition-property: none !important;
            -webkit-transition-property: none !important;
            -o-transition-property: none !important;
            transition-property: none !important;
            transform: none !important;
            -webkit-transform: none !important;
            -ms-transform: none !important;
            width: 64px !important;
            height: 64px !important;
            z-index: 1001 !important;
            cursor: pointer !important;
            float: none !important;
            pointer-events: auto !important;
            clip: auto !important;
            background: #0071ba !important;
            border-radius: 50% !important;
            text-align: center;
            display: flex !important;
            justify-content: center;
            align-items: center;
        }

        .search-mobile{
            margin-top: 130px;
            display: none;
        }

        .search-field-mobile{
            width: 100% !important;
        }

        @media (max-width: 576px) {
            .search-mobile{
                display: block;
                width: 93%;
                margin: 80px auto 0 auto;
            }

            .menu-search{display: none}
        }

        @media (min-width: 577px) {
            .desktop-top{
                margin: 120px auto 0 auto;
            }
        }
    </style>

    @yield('css-section')
    @yield('og-section')

</head>

<body >

    @include('home.layouts.partials.contentlista')

    <div class="body_wrapper">

        @yield('content')

        <!--@ include('home.layouts.footer')-->
    </div>

    <div class="modal fade text-left" id="verproducto" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="width: 40px; right: 0; position: absolute; padding: 10px;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="modal-container" >
                    <div id="content_modal-producto" class="p-4">

                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!Auth::user())
        <div class="modal fade text-left" id="user_iframe" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenter" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document"  >
                <div class="modal-content" >
                    <button type="button" class="close actaualizar_token"  style="width: 40px; right: 0; position: absolute; padding: 10px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="modal-container" style="background: #fafafa;">
                        <iframe class="user_iframe" width="100%" height="100%" src="{{route('login')}}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
       /*
        $(document).ready(function() {

            var ocultar = $('.ocultar').length;

            if(ocultar > 0){
                $('.ocultar').addClass('display_none');
            }

            $(document).ajaxSend(function (event, jqxhr, settings) {
                $(settings.element_to_overlay).LoadingOverlay("show", {
                    imageColor: "#0071ba",
                    imageResizeFactor: 0.4,
                    imageAutoResize: false
                });
            });

            $(document).ajaxComplete(function (event, jqxhr, settings) {
               $(settings.element_to_overlay).LoadingOverlay("hide", true);

            });

        });
        */
    </script>

    <script>
      /*
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 3000
        });

        $('.gotoinstancia').on('click', function(){
            var selector = $(this).data('selector');
            $('html,body').animate({scrollTop : $('.'+selector).offset().top-100},1000);
        });

        window.notify = function notify(status, message) {
            Toast.fire({
                icon: status,
                title: message
            })
        }

        function msg(e) {
            e.preventDefault();

            var msg = $('.msgwhatsapp').val();

            $.ajax({
                type        : 'post',
                headers     : { 'X-CSRF-Token' : $('meta[name="csrf-token"]').attr('content') },
                url         : '{{route('encode.msg')}}',
                dataType    : "html",
                data        : { msg: msg},
                evalScripts : true,
                success     : function(encoded){
                    console.log(encoded)
                    //aqui no terminado

                },error:function () {

                }
            });

            window.location.href = ""+msgwhatsapp;
        }

        var related = $('.related_listed').length;

        if(related == 0)
            $('.related_div').hide();

        let modalopen        = 0;
        let precio1          = 0;
        let functionbusqueda = {{( isset($producto) and $producto )? 1 : 0}};
        let precio2          = 100;
        let vistaActual      = 0;
        let menuabierto      = 0;
        let buscarPorPrecios = 0;
        let vertodos         = 0;
        let ordenprecio      = '';
        let busquedaActual   = '{{(isset($gosearch) and $gosearch !='')? $gosearch: ''}}';
        let categoriaActual  = '{{(isset($gocategor) and $gocategor !='')? $gocategor : ''}}';
        let marcaActual      = '{{(isset($gomarca) and $gomarca !='')? $gomarca : ''}}';
        let route            = '{{route('site')}}';
        let origen           = '{{route('site')}}';
        let csrf             = $('meta[name="csrf-token"]').attr('content');

        if(screen.width < 760) {
            vistaActual = 1;
        }

        @if(isset($data->id))
            if(screen.width < 760){
                $('html,body').animate({scrollTop : $('.pr_title').offset().top-100},1000);
            }
        @endif


        if ($("#slider-range").length) {
            $("#slider-range").slider({
                range: true,
                min: 0,
                max: 500,
                values: [0, 100],
                slide: function (event, ui) {
                    var val = "$" + ui.values[0] + " - $" + ui.values[1];

                    precio1 = ui.values[0];
                    precio2 = ui.values[1];

                    if(ui.values[1] == 500){
                        val = val + "+";
                    }

                    $("#amount").val(val);
                }
            });
            $("#amount").val("$" + $("#slider-range").slider("values", 0) +
                " - $" + $("#slider-range").slider("values", 1));
        }

        function buscarProductos(loading) {
            $('.section-site').hide();
            $('#loadinggif').show();

            $('html, body').animate({ scrollTop: 0 },"fast");
            $('.display_products').html('');
            $('#instrender').html('');
            if(!functionbusqueda){
                $('.search_content').addClass('display_none');
                $('.product_details_area').slideUp().addClass('display_none');
                functionbusqueda = 1;
            }

            var precios  = '';

            if(buscarPorPrecios == 1){
                precios = {'precio1': precio1, 'precio2': precio2};
            }

                $.ajax({
                    type        : 'post',
                    headers     : { 'X-CSRF-Token' : $('meta[name="csrf-token"]').attr('content') },
                    url         : route,
                    dataType    : "html",
                    data        : { vista      : (vistaActual)?vistaActual:'',
                                    precios    : (precios)?precios:'',
                                    marcaprod  : (marcaActual)?marcaActual:'',
                                    vertodos   : (vertodos)?vertodos:'',
                                    ordenprecio: (ordenprecio)?ordenprecio:'',
                                    busqueda   : (busquedaActual)?busquedaActual:'',
                                    categoria  : (categoriaActual)?categoriaActual:''},
                    evalScripts : true,
                    success     : function(response){

                        $('.search_content').removeClass('display_none');
                        $('.searched_hide').addClass('display_none');

                        var json = JSON.parse(response);

                        $('.display_products').html(json.vista_prod);

                        $('#instrender').html(json.instancias);

                        var hidden = $('.shop_grid_area').hasClass('display_none');

                        if(hidden){
                            $('.shop_grid_area').slideDown().removeClass('display_none');
                            $('.product_details_area').slideUp().addClass('display_none');
                        }

                        if(modalopen == 1){
                            $('#verproducto').modal('hide');
                        }

                        listeners();
                        $('#loadinggif').hide();
                    },error:function () {
                        listeners();
                        $('#loadinggif').hide();
                    }
                });

            listeners();
        }
        var user_modal_open = 0;

        function user_modal() {

            user_modal_open = 1;
            if(screen.width < 760){
                $('.user_iframe').attr('height', '515px');
                $('.user_iframe').attr('width', '100%');
            }else{
                $('.user_iframe').attr('height', '515px');
                $('.user_iframe').attr('width', '800px');
            }

            $('#user_iframe').modal({backdrop:false});
        }

        function cerrarpopup() {
            setTimeout(function () {
                $('.popover-content').html('');
                $('.cart-popover').slideUp('slow');
            }, 3000);
        }

        function howagregados() {
            $.ajax({
                type        : 'get',
                headers     : { 'X-CSRF-Token' : $('meta[name="csrf-token"]').attr('content') },
                url         : '{{route('actualizar.agregados')}}',
                dataType    : "html",
                evalScripts : true,
                success     : function(response){
                    var json = JSON.parse(response);
                    $('.items-agregados').html(json.count).removeClass('display_none');
                }
            });
        }

        function listeners() {


            function getCookie(cname) {
                var name = cname + "=";
                var decodedCookie = decodeURIComponent(document.cookie);
                var ca = decodedCookie.split(';');
                for(var i = 0; i <ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }

            $('.actaualizar_token').unbind('click').bind('click',function(event) {

                var cookies = document.cookie;

                $('#user_iframe').modal('hide');

                if(cookies){
                    var mitoken = getCookie("micsrftoken");
                    console.log(mitoken);

                    $('meta[name="csrf-token"]').attr('content', mitoken);
                }

            });


            $('.close_btn-delete').unbind('click').bind('click',function(event) {

                var url = $(this).data('url');
                var id  = $(this).data('id');

                $.ajax({
                    type        : 'delete',
                    url         : url,
                    dataType    : "html",
                    headers     : { 'X-CSRF-Token' : $('meta[name="csrf-token"]').attr('content') },
                    evalScripts : true,
                    element_to_overlay : '.divitem' + id,
                    success     : function(response){
                        $('.divitem' + id).html('').hide();

                        var json = JSON.parse(response);

                        $('.items-agregados').html(json.count).removeClass('display_none');
                        $('.link_to_pay').attr('href', json.href);
                        $('.monto_total').html(json.monto);

                    },error: function () {

                    }
                });
            });

            $('.btn-action_cantidad').unbind('click').bind('click', function(event) {

                var blocked = $(this).hasClass( "btn-blocked" );

                if(!blocked){

                    $('.store_overlay').addClass('active');
                    $('.cart-container').removeClass('display_none');

                    var cant = $(this).data('cantidad');
                    var oper = $(this).data('oper');
                    var id   = $(this).data('id');

                    $.ajax({
                        type        : 'get',
                        url         : '{{route('compraitems.index')}}',
                        dataType    : "html",
                        headers     : { 'X-CSRF-Token' : $('meta[name="csrf-token"]').attr('content') },
                        data        : { id: id, oper: oper, cant: cant },
                        evalScripts : true,
                        element_to_overlay : '.content-btn-cambiar' + id,
                        success     : function(response){

                            var json = JSON.parse(response);

                            $('.cantidad'+id).html(json.cant);

                            $('.minus'+id).data('cantidad', json.cant);
                            $('.plus' +id).data('cantidad', json.cant);

                            if(json.cant == 1)
                                $('.minus'+id).addClass('btn-blocked');

                            if(json.cant > 1)
                                $('.minus'+id).removeClass('btn-blocked');

                            if(json.existencia == json.cant)
                                $('.plus'+id).addClass('btn-blocked');

                            if(json.existencia > json.cant)
                                $('.plus'+id).removeClass('btn-blocked');

                            $('.listaproductos').html(json.lista);
                            $('.link_to_pay').attr('href',json.href);
                            $('.monto_total').html(json.monto);

                            listeners();

                        },error: function () {
                            notify('warning','Item no encontrado, por favor recargue la pagina');
                        }
                    });

                }

            });

            $('.close_btn-cantidades').unbind('click').bind('click',function(event) {
                $('.botones-cambiar').addClass('display_none');
            });

            $('.btn-cantidad').unbind('click').bind('click',function(event) {
                var id = $(this).data('id');

                $('.botones-cambiar').addClass('display_none');
                $('.cambiar_cantidad'+id).removeClass('display_none');
            });

            $('.contenidopag').unbind('click').bind('click',function(event) {

                if(menuabierto)
                    $('.navbar-toggler').click();

            });

            $('#botonhamb').unbind('click').bind('click',function(event) {
                if(menuabierto)
                    menuabierto = 0
                else
                    menuabierto = 1
            });

            $('.cerrar_lista').unbind('click').bind('click',function(event) {
                $('.store_overlay').removeClass('active');
                $('.cart-container').addClass('display_none');
            });

            $('.store_overlay').unbind('click').bind('click',function(event) {
                $('.store_overlay').removeClass('active');
                $('.cart-container').addClass('display_none');
            });

            $('.abrir_lista').unbind('click').bind('click',function(event) {

                $('.store_overlay').addClass('active');
                $('.cart-container').removeClass('display_none');

                $.ajax({
                    type        : 'get',
                    url         : '{{route('abrir.lista')}}',
                    dataType    : "html",
                    headers     : { 'X-CSRF-Token' : $('meta[name="csrf-token"]').attr('content') },
                    evalScripts : true,
                    element_to_overlay : '.listaproductos',
                    success     : function(response){

                        var json = JSON.parse(response);

                        if(json.login == 1){
                            user_modal();
                            $('.store_overlay').removeClass('active');
                            $('.cart-container').addClass('display_none');
                            return true;
                        }

                        if(modalopen == 1) {
                            $('#verproducto').modal('hide');
                        }

                        $('.listaproductos').html(json.lista);
                        $('.items-agregados').html(json.count).removeClass('display_none');
                        $('.link_to_pay').attr('href',json.href);
                        $('.monto_total').html(json.monto);

                        listeners();

                    },error: function () {

                    }
                });
            });

            $('.comprar').unbind('click').bind('click',function(event) {

                var id  = $(this).data('id');

                $.ajax({
                    type        : 'get',
                    url         : '{{route('agregar.producto')}}',
                    dataType    : "html",
                    evalScripts : true,
                    element_to_overlay : '.btn-despuesdeagregar' + id,
                    headers     : { 'X-CSRF-Token' : $('meta[name="csrf-token"]').attr('content') },
                    data:       {  id: id},
                    success     : function(response){

                        var json = JSON.parse(response);

                        if(json.login == 1){
                            user_modal();
                            return true;
                        }

                        if(modalopen == 1){
                            $('#verproducto').modal('hide');
                        }

                        if($('.btn-despuesdeagregar' + id).length > 0){
                            $('.btn-despuesdeagregar' + id).addClass('display_none');
                            $('.btn-agregado' + id).removeClass('display_none');
                        }

                        if(json.preview){
                            $('.popover-content').html(json.preview);
                            $('.cart-popover').slideDown('slow');
                            cerrarpopup();
                            $('.items-agregados').html(json.count).removeClass('display_none');
                        }
                        listeners();

                    },error:function () {

                    }
                });
            });

            $('.abrirHijos').unbind('click').bind('click', function(event) {
                var nivel = $(this).data('nivel');
                var padre = $(this).data('codinst');

                for(var i = nivel; i < 20; i++)
                    $('.nivel'+i).addClass('display_none');

                $('.hijos'+padre).removeClass('display_none');

                $('html,body').animate({scrollTop : $('.categoria'+padre).offset().top-200}, 1000);
            });

            $('.loading_by_prices').unbind('click').bind('click', function(event) {
                busquedaActual = '';
                $('.input-busqueda').val('');
                if(screen.width > 760) {
                    $('.btn-search').removeClass('ti-close').addClass('ti-search');
                }
                buscarPorPrecios = 1;
                route = '{{route('site')}}';
                $('.remove_by_price').slideDown();
                buscarProductos('loading_by_prices');
            });

            $('.ti-close').unbind('click').bind('click', function(event) {
                busquedaActual = '';
                $('.input-busqueda').val('');
                categoriaActual = '';
                $('.remove_by_category').slideUp();
                marcaActual = '';
                $('.remove_by_marca').slideUp();
                buscarPorPrecios = '';
                $('.remove_by_price').slideUp();
                route = '{{route('site')}}';
                if(screen.width > 760) {
                    $('.btn-search').removeClass('ti-close').addClass('ti-search');
                }
                buscarProductos('');
            });

            $('.remove_by_price').unbind('click').bind('click', function(event) {
                buscarPorPrecios = 0;
                $('.remove_by_price').slideUp();
                buscarProductos('');
            });

            $('.remove_by_category').unbind('click').bind('click', function(event) {
                categoriaActual = ''; marcaActual = '';
                buscarPorPrecios = 0;
                $('.remove_by_category').slideUp();
                buscarProductos('');
            });

            $('.remove_by_marca').unbind('click').bind('click', function(event) {

                $('.remove_by_marca').slideUp();
                buscarProductos('');
            });

            $('.vertodos').unbind('click').bind('click', function(event) {
                vertodos = $(this).data('vertodos');
                route    = origen;
                buscarProductos('');
            });

            $('.ordenprecio').unbind('click').bind('click', function(event) {
                ordenprecio = $(this).data('ordenprecio');
                route       = origen;
                buscarProductos('');
            });



            $('.categoria').unbind('click').bind('click', function(event) {
                busquedaActual = '';
                marcaActual  = '';
                $('.input-busqueda').val('');
                if(screen.width > 760) {
                    $('.btn-search').removeClass('ti-close').addClass('ti-search');
                }
                categoriaActual = $(this).data('codinst');
                route = '{{route('site')}}';
                $('.remove_by_category').slideDown();

                if(menuabierto)
                    $('.navbar-toggler').click();

                buscarProductos('categoria'+categoriaActual);
            });

            $('.marca').unbind('click').bind('click', function(event) {
                busquedaActual = '';
                $('.input-busqueda').val('');
                if(screen.width > 760) {
                    $('.btn-search').removeClass('ti-close').addClass('ti-search');
                }
                marcaActual = $(this).data('marca');
                route = '{{route('site')}}';
                $('.remove_by_marca').slideDown();

                if(menuabierto)
                    $('.navbar-toggler').click();

                buscarProductos('marca'+marcaActual);
            });


            $('.productomodal').unbind('click').bind('click', function(event) {

                var url = $(this).data('url');

                modalopen = 1;

                $('#content_modal-producto').html('Cargando producto');

                $.ajax({
                    type        : 'get',
                    url         : url,
                    dataType    : "html",
                    headers     : { 'X-CSRF-Token' : $('meta[name="csrf-token"]').attr('content') },
                    evalScripts : true,
                    element_to_overlay : '#content_modal-producto',
                    success     : function(response){

                        var json = JSON.parse(response);
                        $('#content_modal-producto').html(json.data);

                        var pr_image = $('.pr_image');

                        if (pr_image.length) {
                            pr_image.owlCarousel({
                                loop: true,
                                items: 1,
                                autoplay: true,
                                dots: true,
                                thumbs: true,
                                thumbImage: true,
                            });
                        }
                        listeners();
                    },error:function () {
                        listeners();
                    }
                });

            });

            $('.paginar').unbind('click').bind('click', function(event) {
                var r = $(this).data('route');
                route = r;
                buscarProductos('');
            });

            $('.change_view').unbind('click').bind('click', function(event) {
                var vista = $(this).data('vista');
                vistaActual = vista;
                buscarProductos('change_view'+vista);
            });
        }

        listeners();

        if(!marcaActual)
            $('.remove_by_marca').slideUp();
       */

        @if((isset($gosearch) and $gosearch != '') or (isset($gocategor) and $gocategor != '') or ($noajax == 11 and Auth::user()) )
           //quitar buscarProductos('');
        @endif

        @if(!request()->routeIs('bienvenido')  and isset(auth()->user()->id))
          //quitar  howagregados();
        @endif


    </script>

    <script src="{{asset('js/jquery-3.2.1.min.js')}}"></script>
    <script src="{{asset('js/propper.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/main.js')}}"></script>
    <script src="{{asset('assets/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{asset('assets/js/sweet-alert.js')}}"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.6/dist/loadingoverlay.min.js"></script>


    @yield('js-section')

</body>

</html>

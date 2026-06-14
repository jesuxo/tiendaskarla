@extends('home.layouts.master')

@section('og-section')
    @if(isset($data->descrip1) and $data->descrip1)
        <meta property="og:title" content='{{str_replace("'", '', $data->descrip1)}}' />
        <meta property="og:type"  content="website" />
        <meta property="og:url"   content="{{route('ver.producto', $data->id)}}">
        @if(isset($data->imagen))
            <meta property="og:image" content="{{asset('img/productos/ogc'.$data->imagen)}}" />
        @else
            <meta property="og:image" content="{{asset('img/default.jpg')}}" />
        @endif
        <meta property="og:description" content='Modelo: {{str_replace("'", '', $data->referencia)}}' />
    @endif
@endsection

@section('css-section')


@endsection

@section('content')

    <div class=" search-form input-group search-mobile"    >

        <form method="post" name="busqueaform" action="{{route('gourl')}}">
            @csrf
            <input type="text"  autocomplete="off" name="busqueda" value="{{(isset($gosearch) and $gosearch !='')? $gosearch: ''}}" class="form-control search-field search-field-mobile input-busqueda"  placeholder="Busca productos aqui" style="line-height: 35px;border:1px solid aliceblue;padding-right: 35px; width: 250px; height: 40px !important; ">
            <span class="input-group-addon btn-header-search">
                <button type="button" >
                    <i class="ti-search cursor_pointer btn-search"></i>
                </button>
            </span>
            <input type="hidden" name="post_type" value="product">
        </form>
    </div>

    <div class="contenidopag" style="min-height: 250px; " >
        <img src="/img/loading.gif" id="loadinggif" style="width: 30px; display: none; margin-top: 104px;">

        <section class="section-site" @if($vista_prod != '') style="display: none" @endif>
        @if($routename != 'url.busqueda' and !Auth::user())

            <div class="container searched_hide section-header searched_hide" style="margin-top: 90px;">
                <div class=" " style="margin: 30px 0">
                    <div class=" ">
                        <div class="swiper-container" style="height: 250px;">
                            <div class="swiper-wrapper">
                                @if(isset($promo_dest2[0]))
                                    @foreach($promo_dest2 as $index => $promo)

                                        <div  class="swiper-slide"
                                              style="cursor: pointer;
                                             background: url({{asset('img/promociones/'.$promo->imagen_home)}}) center center no-repeat; background-size: cover">
                                            <div class="slide_caption align_left">

                                            </div>
                                        </div>

                                    @endforeach
                                @else
                                    <div class="swiper-slide" style="background: url({{asset('img/headerdefault.jpg')}}) center center no-repeat; background-size: cover"></div>
                                @endif

                            </div>
                            <!-- Add Pagination -->
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container   searched_hide">

                @php $i = 0; @endphp

                <div class="ht-tab container searched_hide group1 "     >
                    <div class="row">
                        <div class="tab-inst-izq  d-flex align-items-center"  style="width: 100% !important;">
                            <div class="developer_product_content">

                                <ul class="  develor_tab mt-4 mb-0">

                                        <li class="nav-item">
                                            <a class="nav-link open-tab  active " id="otab-00" data-group="group1"  data-tab="tab-00"   data-right="right-00"   >
                                                Productos en Promoci&oacuten
                                            </a>
                                        </li>
                                </ul>

                            </div>
                        </div>
                        <style>
                            .product_img td{border-top:  none !important;}
                            .product_img a{
                                font:200 15px "Gotham",sans-serif !important;
                                color: white !important;
                                text-shadow: 2px 1px 2px #555 !important;
                                line-height: 1.5;
                                font-weight: bold !important;
                            }
                            .product_img a:hover{
                                color: white !important;
                                text-decoration: underline;
                            }
                            .single_product_item:hover a{
                                color: white !important;
                            }

                        </style>
                        <div class="tab-inst-der"  style="width: 100% !important;">
                            <div class="tab_img_info" >
                                    <div class="div-der  active " id="right-00">
                                        @php
                                            $i++;

                                            $productosdestacados = \App\Models\Saprod::with(['instancia'])
                                                ->where(['activo' => 1, 'destacado' => 1])
                                                ->where('existen', '>', 0)->orderBy('destacado_at', 'desc')->get()->take(5);

                                        @endphp
                                        @if(isset($productosdestacados))

                                            <div class="container instanciahome mt-4  searched_hide " >

                                                <div class=" owl-carousel instancias_destacadas carousel">
                                                    @php $count = 0; @endphp
                                                    @foreach($productosdestacados as $prod)
                                                        <div class="single_product_item mt-0 studies_item"  style="color: #7f6d4f"  >

                                                            <div class="product_img" style="width: 100%; height: 320px; display: block;  padding-top: 196px;
                            background: url({{((isset($prod->imagen) and strlen($prod->imagen)>3))?  '/img/productos/th'.$prod->imagen :  ('img/default.jpg')}}  ) no-repeat center top">
                                                                <table class="prodtd" width="100%" border="0" style="border: none; font-size: 10px !important; font-weight: bold">
                                                                    <tr>
                                                                        <td style="line-height: 16px; padding-left: 15px  " align="left">{{$prod->descrip3}}</td>
                                                                        <td style="line-height: 16px;  padding-right: 15px  " align="right">{{$prod->descrip4}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2" style="line-height: 16px; padding: 0px 15px !important;  ">
                                                                            <a href="javascript:;" class="productomodal"
                                                                               data-id="{{$prod->id}}" data-url="{{route('ver.producto', $prod->id)}}"
                                                                               data-toggle="modal" data-target="#verproducto"
                                                                               style="font-size: 12px !important; height: 30px; overflow:hidden; display: block   " >
                                                                                {{substr($prod->descrip,0,80)}}
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="line-height: 16px; padding-left: 20px; font-size: 20px !important;  " align="left">
                                                                            <a href="javascript:;"
                                                                               style="font-size: 20px !important; height: 30px; overflow:hidden; display: block   " >
                                                                                ${{number_format($prod->costod3,2,',','.')}}
                                                                            </a>
                                                                        </td>
                                                                        <td style="line-height: 16px;  padding-right: 15px  " align="right">
                                                                            <button type="button" class="btn btn-primary" style='font: 200 16px "Gotham",sans-serif;
                                    font-weight: 200; margin-right: 10px'>Agregar </button>
                                                                        </td>
                                                                    </tr>

                                                                </table>

                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin: 30px 0">
                    <div class="col-md-12">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                            @if(isset($promo_dest[0]))
                                @foreach($promo_dest as $index => $promo)

                                        <div  class="swiper-slide"
                                             style="cursor: pointer;
                                             background: url({{asset('img/promociones/'.$promo->imagen_home)}}) center center no-repeat; background-size: cover">
                                            <div class="slide_caption align_left">
{{--                                                <div class="caption_text"> onclick="window.location.href='{{route('promociones')}}'" --}}
{{--                                                    <?php--}}
{{--                                                    $go = 1;--}}

{{--                                                    if(Auth::user()){--}}
{{--                                                        $added = \App\CompraItems::whereHas('compra', function ($query) {--}}
{{--                                                            $query->where(['encurso'=> 1, 'fk_user' => auth()->id()]);--}}
{{--                                                        })->with('compra')->where(['fk_producto' => $promo->producto->id])->first();--}}

{{--                                                        if(isset($added->fk_producto)){--}}
{{--                                                            $go = 0;--}}
{{--                                                        }--}}
{{--                                                    }--}}

{{--                                                    ?>--}}

{{--                                                    <a class="btn  {{(!$go)? 'display_none': ''}} slider_btn comprar btn-despuesdeagregar{{$promo->producto->id}}" data-id="{{$promo->producto->id}}"  href="javascript:;">Comprar </a>--}}
{{--                                                    <a class="btn  {{(!$go)? '': 'display_none'}}  slider_btn btn-agregado{{$promo->producto->id}}" data-id="{{$promo->producto->id}}"  href="javascript:;">Agregado </a>--}}
{{--                                                </div>--}}
                                            </div>
                                        </div>

                                @endforeach
                            @else
                                    <div class="swiper-slide" style="background: url({{asset('img/headerdefault.jpg')}}) center center no-repeat; background-size: cover"></div>
                            @endif

                            </div>
                            <!-- Add Pagination -->
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>

                <div class="ht-tab container searched_hide group1 mb-5 "  >
                    <div class="row">
                        <div class="tab-inst-izq  d-flex align-items-center"  style="width: 100% !important;">
                            <div class="developer_product_content">

                                <ul class="  develor_tab mt-4 mb-0">

                                    <li class="nav-item">
                                        <a class="nav-link open-tab  active " id="otab-00" data-group="group1"  data-tab="tab-00"   data-right="right-00"   >
                                            Aliados Comerciales
                                        </a>
                                    </li>

                                </ul>

                            </div>
                        </div>
                        <div class="tab-inst-der"  style="width: 100% !important;">
                            <div class="tab_img_info" >

                                <div class="div-der  active " id="right-00">

                                        <div class="container   mt-4  searched_hide " >

                                            <div class=" owl-carousel instancias_destacadas carousel">

                                                <div class="single_product_item mt-0 studies_item">
                                                    <div class="product_img">
                                                        <a href="javascript:;" class="productomodal" > <img class="img-fluid" src="{{asset('img/proveedores/1.png')}}" alt=""  /></a>
                                                    </div>
                                                </div>
                                                <div class="single_product_item mt-0 studies_item">
                                                    <div class="product_img">
                                                        <a href="javascript:;" class="productomodal" > <img class="img-fluid" src="{{asset('img/proveedores/2.jpg')}}" alt=""  /></a>
                                                    </div>
                                                </div>
                                                <div class="single_product_item mt-0 studies_item">
                                                    <div class="product_img">
                                                        <a href="javascript:;" class="productomodal" > <img class="img-fluid" src="{{asset('img/proveedores/3.jpg')}}" alt=""  /></a>
                                                    </div>
                                                </div>
                                                <div class="single_product_item mt-0 studies_item">
                                                    <div class="product_img">
                                                        <a href="javascript:;" class="productomodal" > <img class="img-fluid" src="{{asset('img/proveedores/4.jpg')}}" alt=""  /></a>
                                                    </div>
                                                </div>
                                                <div class="single_product_item mt-0 studies_item">
                                                    <div class="product_img">
                                                        <a href="javascript:;" class="productomodal" > <img class="img-fluid" src="{{asset('img/proveedores/5.jpg')}}" alt=""  /></a>
                                                    </div>
                                                </div>
                                                <div class="single_product_item mt-0 studies_item">
                                                    <div class="product_img">
                                                        <a href="javascript:;" class="productomodal" > <img class="img-fluid" src="{{asset('img/proveedores/6.jpg')}}" alt=""  /></a>
                                                    </div>
                                                </div>
                                                <div class="single_product_item mt-0 studies_item">
                                                    <div class="product_img">
                                                        <a href="javascript:;" class="productomodal" > <img class="img-fluid" src="{{asset('img/proveedores/7.jpg')}}" alt=""  /></a>
                                                    </div>
                                                </div>
                                                <div class="single_product_item mt-0 studies_item">
                                                    <div class="product_img">
                                                        <a href="javascript:;" class="productomodal" > <img class="img-fluid" src="{{asset('img/proveedores/8.jpg')}}" alt=""  /></a>
                                                    </div>
                                                </div>
                                                <div class="single_product_item mt-0 studies_item">
                                                    <div class="product_img">
                                                        <a href="javascript:;" class="productomodal" > <img class="img-fluid" src="{{asset('img/proveedores/9.jpg')}}" alt=""  /></a>
                                                    </div>
                                                </div>
                                                <div class="single_product_item mt-0 studies_item">
                                                    <div class="product_img">
                                                        <a href="javascript:;" class="productomodal" > <img class="img-fluid" src="{{asset('img/proveedores/10.jpg')}}" alt=""  /></a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                </div>


                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class=" searched_hide section_collections_with_menu">
                <div class="container">
                    <div class=" ">

                        <div class="linklist_item linklist_big" style="height: 519px; overflow:auto;">
                            <div class="menu_wrap linklist_menu_item">
                                <h4 class="linklist_title">LABORATORIOS<span class="menu_trigger">
                                    <i class="arrow-up-down"></i>
                                </span>
                                </h4>
                                <ul>
                                    @foreach($marcas as $marca)
                                        <li class="link_item  overinst41"  style="background: url({{asset('img/instancias/icon1.png')}}) -20px -20px no-repeat ; background-size: 80px; padding-left: 50px">
                                            <a  href="/busqueda/{{$marca->marca}}" class="linklist_link  f_size_15" >
                                               {{$marca->marca}}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="  row">

                            <div class="col-md-3  ">
                                <a href="/busqueda/ampollas" class=" text-primary f_size_13" data-codinst="359">
                                    <div class="collection_item hover_image">
                                        <div class="layer_1" >
                                            <div class="img_placeholder__wrap" style="background-image: url({{ asset('img/home/AMPOLLAS.jpg')  }});"></div>
                                        </div>

                                        <div class="collection_title">

                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-3  ">
                                <a href="/busqueda/cremas" class=" text-primary f_size_13" data-codinst="427">
                                    <div class="collection_item hover_image">
                                        <div class="layer_1" >
                                            <div class="img_placeholder__wrap" style="background-image: url({{ asset('img/home/cremas.jpg')  }});"></div>
                                        </div>

                                        <div class="collection_title">
                                        </div>

                                    </div>
                                </a>
                            </div>

                            <div class="col-md-3   ">
                                <a href="/busqueda/gotas" class=" text-primary f_size_13" data-codinst="48">
                                    <div class="collection_item hover_image">
                                        <div class="layer_1" >
                                            <div class="img_placeholder__wrap" style="background-image: url({{ asset('img/home/gotas.jpg')  }});"></div>
                                        </div>
                                        <div class="collection_title">
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-3   ">
                                <a href="/busqueda/jarabes" class=" text-primary f_size_13" data-codinst="173">
                                    <div class="collection_item hover_image">
                                        <div class="layer_1" >
                                            <div class="img_placeholder__wrap" style="background-image: url({{ asset('img/home/jarabes.jpg')  }});"></div>
                                        </div>

                                        <div class="collection_title">
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-3  ">
                                <a href="/busqueda/material%20medico" class=" text-primary f_size_13" data-codinst="145">
                                    <div class="collection_item hover_image">
                                        <div class="layer_1" >
                                            <div class="img_placeholder__wrap" style="background-image: url({{ asset('img/home/materialmedico.jpg')  }});"></div>
                                        </div>
                                        <div class="collection_title">
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-3  ">
                                <a href="/busqueda/miscelaneos" class=" text-primary f_size_13" data-codinst="862">
                                    <div class="collection_item hover_image">
                                        <div class="layer_1" >
                                            <div class="img_placeholder__wrap" style="background-image: url({{ asset('img/home/miscelaneos.jpg')  }});"></div>
                                        </div>
                                        <div class="collection_title">
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3  ">
                                <a href="/busqueda/naturales" class=" text-primary f_size_13" data-codinst="173">
                                    <div class="collection_item hover_image">
                                        <div class="layer_1" >
                                            <div class="img_placeholder__wrap" style="background-image: url({{ asset('img/home/naturales.jpg')  }});"></div>
                                        </div>

                                        <div class="collection_title">
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-3  ">
                                <a href="/busqueda/tabletas" class=" text-primary f_size_13" data-codinst="145">
                                    <div class="collection_item hover_image">
                                        <div class="layer_1" >
                                            <div class="img_placeholder__wrap" style="background-image: url({{ asset('img/home/tabletas.jpg')  }});"></div>
                                        </div>
                                        <div class="collection_title">
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endif
        </section>

        <section class="shop_grid_area mb-5  search_content"
                 style="{{ ($vista_prod == '' and  !Auth::user()  and  $noajax != 11)? 'display:none;':''}}" >
            <div class=" "  style="min-height: 500px; padding: 0px 10px; ">
                <div class="row row-reverse  ">
                     <div class="col-lg-2">
                        @include('home.layouts.partials.sidebar')
                    </div>
                    <div class="col-lg-10 display_products mb-4">
                        {!! (isset($vista_prod))? $vista_prod : '' !!}
                    </div>
                </div>
            </div>
        </section>

        <section class="product_details_area mt-4 {{(isset($producto) and $producto == '' )? 'display_none' : ''}}">
            {!!( isset($producto) and $producto )? $producto : '' !!}
        </section>

    </div>

@endsection

@section('js-section')
    <script src="{{asset('js/swiper.min.js')}}"></script>
    <script>

        $('.open-tab').click(function () {
            var tab   = $(this).data('tab');
            var right = $(this).data('right');
            var group = $(this).data('group');

            $('.'+group+' .div-der').addClass('display_none').removeClass('show').removeClass('active');
            $('.'+group+' .div-izq').addClass('display_none').removeClass('show').removeClass('active');
            $('.'+group+' .open-tab').removeClass('show').removeClass('active');

            $('#o'+tab).addClass('show').addClass('active');

            $('#c'+tab).addClass('show').addClass('active').removeClass('display_none').slideDown();
            $('#'+right).removeClass('display_none').slideDown().addClass('show active');

        });


        var swiper = new Swiper('.swiper-container', {
                direction: 'vertical',

                autoplay: {
                    delay: 4500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,

                },
            });


        @if(!Auth::user())
            // $(document).ready(function() {
            //
            //     setTimeout(function () {
            //
            //         if(!user_modal_open) {
            //             // console.log('abriendo');
            //             // user_modal()
            //         }
            //     }, 15000);
            //
            // });
        @endif
    </script>
@endsection

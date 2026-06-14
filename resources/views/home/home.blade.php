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

    <section>
        <div class="landing-page-hero">
            <div class="container">
                <div class="ak-center position-absolute h-100 pe-3">
                    <div class="landing-page-info">
                        <div class="landing-title">
                            <span>
                               <a href="/login"><img src="img/logo.png" /></a>
                            </span>
                            <div class="ak-height-20 ak-height-lg-20"></div>
                            <h2 class="landing-main-title" data-swiper-parallax="300">TIENDAS KARLA</h2>
                            <h2 class="landing-main-title" data-swiper-parallax="100">
                               Tendr&aacute; Pagina Web</h2>
                            <p class="mini-title" data-swiper-parallax="200" style="color: white !important;">
                                Pronto tendremos un catalogo online para nuestros clientes
                            </p>
                        </div>
                        <div class="ak-height-45 ak-height-lg-30"></div>
                        <div data-swiper-parallax="300">
                            <a target="_blank" href="https://api.whatsapp.com/send/?phone=584247329670&text=Hola+Srs.+de+TIENDAS+KARLA+quisiera+informacion+sobre%3A+&type=phone_number&app_absent=0" class="common-btn">
                                CONTACTANOS.
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <video autoplay="" muted="" loop="" id="" style=" width: 100%;  height: 800px;  -o-object-fit: cover; object-fit: cover; padding: 0px; margin: 0px;">

            </video>
        </div>
    </section>


@endsection

@section('js-section')

@endsection

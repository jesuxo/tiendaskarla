
<style>
    .cursor-pointer, .u-cursorPointer {
        cursor: pointer;
    }
    .cart-popover {
        position: relative;
    }

    .cart-popover .popover .arrow {
        left: 335px;
        top: -10px;
        margin-left: -10px;
        position: absolute;
        display: inline-block;
        width: 0;
        height: 0;
        border-color: transparent transparent #fff;
        border-style: solid;
        border-width: 0 10px 10px;
    }

    .popover {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1060;
        display: block;
        max-width: 276px;
        padding: 1px;
        font-family: Helvetica Neue,Helvetica,Arial,sans-serif;
        font-style: normal;
        font-weight: 400;
        letter-spacing: normal;
        line-break: auto;
        line-height: 1.5;
        text-align: left;
        text-align: start;
        text-decoration: none;
        text-shadow: none;
        text-transform: none;
        white-space: normal;
        word-break: normal;
        word-spacing: normal;
        word-wrap: normal;
        font-size: .875rem;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid rgba(0,0,0,.2);
        border-radius: .3rem;
    }

    .popover {
        z-index: 1099;
        right: -56px;
        width: 400px;
        height: 100px;
        max-width: 412px;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 16px;
        border: 1px solid rgb(224, 224, 224);
        border-radius: 8px;
    }

    .cart-popover .popover {
        font-family: Open Sans,Helvetica Neue,Helvetica,sans-serif;
        top: 0;
        left: inherit;
        right: 0px;
        opacity: 1;
        width: 385px;
        max-width: 385px;
        box-shadow: 0 22px 70px 4px rgba(0,0,0,.56);
        padding: 0;
        border: 1px solid #ccc;
        overflow: inherit !important;
        transition: opacity .2s ease-out;
        -moz-transition: opacity .2s ease-out;
        -webkit-transition: opacity .2s ease-out;
        -o-transition: opacity .2s ease-out;
    }

    .items-agregados{
        background-color: #0071ba;
        color: rgb(255, 255, 255);
        line-height: 22px;
        position: absolute;
        right: -16px;
        top: -15px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        text-align: center;
        font-size: 11px;
        font-weight: 100;
    }


    @media (max-width: 576px){
        .cart-popover .popover {
            right: -56px;
        }
        .cart-popover .popover .arrow{
            left: 303px !important;
        }
    }

</style>
<header class="header_area header_stick">
    <nav class="navbar navbar-expand-lg menu_one menu_right">
        <div class="container">
            <a class="navbar-brand sticky_logo" href="{{route('site')}}">
                <img src="{{asset('img/logoscrolll.png')}}" alt="DROARCA DROGUERIA EL ARCA - drogueriaelarca@gmail.com" style="height: 60px;">
                <img src="{{asset('img/logoscrolll.png')}}" alt="DROARCA DROGUERIA EL ARCA - drogueriaelarca@gmail.com" style="height: 40px;">
            </a>
            <button class="navbar-toggler collapsed" id="botonhamb"  type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="menu_toggle">
                <span class="hamburger">
                <span></span>
                <span></span>
                <span></span>
                </span>
                <span class="hamburger-cross">
                <span></span>
                <span></span>
                </span>
                </span>
            </button>
            <div class="navbar-collapse collapse" id="navbarSupportedContent" style="">
                <ul id="menu-main-menu" class="navbar-nav menu ml-auto">

                    @if(isset($instPpales[0]))
                        <li itemscope="itemscope" id="menu-item-2397" @php echo (isset($gosearch) and $gosearch != '' or Auth::user())?'':'style="display: none"'; @endphp class=" menu-search menu-item menu-item-home  dropdown submenu nav-item mega_menu">
                            <div class=" search-form input-group"  >
                                <form name="formbusqueda" method="post" action="{{route('gourl')}}">
                                    @csrf
                                    <input type="text"  autocomplete="off" name="busqueda" value="{{(isset($gosearch) and $gosearch !='')? $gosearch: ''}}"
                                           class="form-control search-field input-busqueda" id="inputsearchinputsearch" placeholder="Busca productos aqui"
                                           style="line-height: 35px;border:1px solid aliceblue;padding-right: 35px; width: 250px; font-weight: 100 !important; height: 40px !important; ">
                                    <span class="input-group-addon btn-header-search">
                                        <button type="button" >
                                            <i class="ti-search cursor_pointer btn-search"></i>
                                        </button>
                                    </span>
                                    <input type="hidden" name="post_type" value="product">
                                </form>
                            </div>
                        </li>
                        @if(!Auth::user())
                        <li itemscope="itemscope"  class="menu-item menu-item-type-custom menu-item-object-custom  dropdown submenu nav-item">
                            <a href="https://wa.me/584123268315?text=Hola%20Srs.%20de%20DROARCA%20quisiera%20ser%20clientes%20de%20uds."
                               class="nav-link" target="_blank">Ser Cliente</a>
                        </li>
                        <li itemscope="itemscope"  class="menu-item menu-item-type-custom menu-item-object-custom  dropdown submenu nav-item">
                            <a href="https://wa.me/584123268315?text=Hola%20Srs.%20de%20DROARCA%20quisiera%20ser%20proveedor%20de%20uds."
                               class="nav-link" target="_blank">Ser Proveedor</a>
                        </li>
                        @endif
                        <li itemscope="itemscope" style="display:none;"  class="menu-item menu-item-type-custom menu-item-object-custom  dropdown submenu nav-item">
                            <a href="#" class="nav-link">Departamentos</a>
                            <ul class="dropdown-menu mega_menu_three">
                                <li class="nav-item">
                                    <ul class="dropdown-menu">
                                        @foreach($instPpales as $index => $inst)
                                            <li class="nav-item">
                                                <a href="{{route('url.instancia', $inst->codinst)}}" class="nav-link p-0 m-0">
                                                    <span class="navdropdown_link p-0 m-0 " data-codinst="{{$inst->codinst}}">
                                                        <span class="navdropdown_icon p-0 m-0 overinst41" style="background: url({{asset('img/instancias/icon1.png')}})-10px -10px no-repeat ; background-size: 60px; width: 60px; height: 40px ">
                                                                    <!--{{$inst->id}}  .$inst->icon-->
                                                        </span>
                                                        <span class="navdropdown_content " style="padding-top: 13px">
                                                            <h5> {{$inst->descrip}} </h5>
                                                        </span>
                                                    </span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        </li>


                    @else

                        <li itemscope="itemscope"  class="menu-item menu-item-type-custom menu-item-object-custom  nav-item">
                            <a href="{{route('site')}}" class="nav-link">Inicio</a>
                        </li>

                    @endif

                    @if(Auth::user())
                        <li itemscope="itemscope" id="menu-item-4644" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children  dropdown submenu nav-item">
                            <a href="#" class="nav-link"> <i class="ti-user" style="font-size: 27px"></i> <?php
                                $nombre = auth()->user()->name;
                                $split  = explode(" ", $nombre);
                                echo $split[0];
                                ?>
                            </a>
                            <ul role="menu" class="dropdown-menu menu-depth-2nd">
                                <li itemscope="itemscope" class="menu-item nav-item"><span  class="nav-link">Hola, {{auth()->user()->name}} </span></li>
                                <li itemscope="itemscope" class="menu-item dropdown-divider"> </li>
                                <li itemscope="itemscope" class="menu-item nav-item"><a href="{{url('logout')}}" class="nav-link"><i class="ti-power-off"></i> Cerrar Sesi&oacute;n </a></li>
                            </ul>
                        </li>
                            @if(isset($gosearch) and $gosearch == '' and !Auth::user())
                                <li itemscope="itemscope" onclick="$('.menu-search').slideToggle(); $(this).hide(); $('#inputsearch').focus()"  class="menu-item menu-item-type-custom menu-item-object-custom  nav-item">
                                    <a href="javascript:;"  class="nav-link">
                                        <i class="ti-search " style="font-size: 27px"></i>
                                    </a>
                                </li>
                            @endif
                    @else
                            <li itemscope="itemscope"  class="menu-item menu-item-type-custom menu-item-object-custom  nav-item">
                                <a href="/panelclientes" target="_blank" class="nav-link">Ingresar</a>
                            </li>
                            @if(!isset($gosearch) or $gosearch == '')
                                <li itemscope="itemscope" onclick="$('.menu-search').slideToggle(); $(this).hide(); $('#inputsearch').focus() " class="menu-item menu-item-type-custom menu-item-object-custom  nav-item">
                                    <a href="javascript:;"  class="nav-link">
                                        <i class="ti-search " style="font-size: 27px"></i>
                                    </a>
                                </li>
                            @endif
                    @endif

                </ul>
            </div>
            <div class="alter_nav search_exist">
                <ul class="navbar-nav search_cart menu">
                    <li class="nav-item search">
                        <a class="nav-link abrir_lista" href="javascript:void(0);">
                            <i class="ti-shopping-cart" style="font-size: 27px"></i>
                            <span class="items-agregados display_none"></span>
                        </a>
                    </li>
                </ul>

                <div class="cart-popover cursor-pointer display_none">
                    <div class="popover bottom " style="">
                        <div>
                            <div class="arrow" style="left: 365px;"></div>
                            <div class="popover-content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>


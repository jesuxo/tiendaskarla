<table class="table table-borderless table-centered align-middle table-nowrap mb-0">
    <thead class="text-muted table-light">
    <tr>
        <th width="5%" scope="col">Codigo</th>
        <th  width="70%"scope="col">Producto</th>

        <th  width="10%"scope="col">Costo</th>
        <th  width="10%"scope="col">Precio</th>
        <th  width="5%"scope="col">Existencia</th>
    </tr>
    </thead>
    <tbody>
    @foreach($productos as $producto)
        <tr>
            <td>
                <a href="{{route('productos.edit',$producto->id)}}" class="fw-medium link-primary">{{$producto->codprod}}</a>
            </td>
            <td>
                <a href="{{route('productos.edit',$producto->id)}}" class="fw-medium link-primary">{{substr($producto->descrip,0,40)}} {{$producto->color  }}</a>
                <a href="{{route('productos.edit',$producto->id)}}" class="fw-medium link-primary" style="float: right">
                    <i class="bi-pencil-square"></i>
                </a>
            </td>
            <td align="right"> {{number_format($producto->preciod,2,',','.')}}  </td>
            <td align="right"> {{number_format($producto->costod3,2,',','.')}}</td>
            <td align="center">{{$producto->existen+0}} </td>
        </tr><!-- end tr -->
    @endforeach
    </tbody>
</table>

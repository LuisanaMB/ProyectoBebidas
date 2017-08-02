@extends('plantillas.main')
@section('title', 'Solicitudes de Creación de Productos')

{!! Html::script('js/productores/confirmar_productos.js') !!}

@section('title-header')
   Nuevos Productos
@endsection

@section('title-complement')
   (Por Confirmar)
@endsection

@section('content-left')
   @section('alertas')
      @if (Session::has('msj'))
           <div class="alert alert-success alert-dismissable">
               <button type="button" class="close" data-dismiss="alert">&times;</button>
               <strong>¡Enhorabuena!</strong> {{Session::get('msj')}}.
           </div>
       @endif
   @endsection

   <div class="col-md-12">
      <ul class="timeline">
         @foreach($productos as $producto)
            <li>
               <i class="fa fa-hand-pointer-o bg-blue"></i>
               <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o"></i> {{ date('d-m-Y', strtotime($producto->created_at)) }}</span>
                  <h3 class="timeline-header">El producto <strong>{{ $producto->nombre }}</strong> ha sido agregado al catálogo de productos de tu marca <strong>{{ $producto->marca->nombre }}</strong>.</h3>

                  <div class="timeline-body">
                     <div class="panel panel-default panel-info">
                        <div class="panel-heading"><h5><b>Detalles</b></h5></div>
                        <ul class="list-group">
                           <li class="list-group-item"><b>Nombre SEO:</b> {{ $producto->nombre_seo }}</li>
                           <li class="list-group-item"><b>Tipo de Bebida:</b> {{ $producto->bebida->nombre }}</li>
                           <li class="list-group-item"><b>Características del Tipo de Bebida:</b> {{ $producto->bebida->caracteristicas }}</li>
                           <li class="list-group-item"><b>Clase de Bebida:</b> {{ $producto->clase_bebida->clase }}</li>
                           <li class="list-group-item"><b>Características de la Clase de Bebida:</b> {{ $producto->clase_bebida->caracteristicas }}</li>
                           <li class="list-group-item"><b>País Originario:</b> {{ $producto->pais->pais }}</li>
                           <li class="list-group-item"><b>Año de Producción:</b> {{ $producto->ano_produccion }}</li>
                        </ul>
                     </div>
                  </div>
                  
                  <div class="timeline-footer">
                     <a class="btn btn-primary btn-xs" href="{{ route('productor.confirmar-producto', [$producto->id, 'S']) }}">¡Confirmar!</a>
                     <a class="btn btn-danger btn-xs" href="{{ route('productor.confirmar-producto', [$producto->id, 'N']) }}">¡No Confirmar!</a>
                  </div>
               </div>
            </li>
         @endforeach
      </ul>
   </div>
@endsection

@section('paginacion')
   {{$productos->render()}}
@endsection

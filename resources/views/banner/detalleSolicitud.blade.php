@extends('plantillas.main')
@section('title', 'Banner Publicitario')

@section('items')
@endsection

@section('content-left')
   @section('title-header')
      <h3><b>Detalles de la Solicitud</b></h3>
   @endsection

   @if (Session::has('msj'))
      <div class="alert alert-success alert-dismissable">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <strong>¡Enhorabuena!</strong> {{Session::get('msj')}}.
      </div>
   @endif

   <div class="row">
      <div class="col-md-4"></div>
      <div class="col-sm-6 col-md-4">
         <div class="thumbnail"><img src="{{ asset('imagenes/banners/thumbnails') }}/{{ $solicitud->banner->imagen }}"></div>
      </div>
      <div class="col-md-4"></div>
   </div>

   <div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-4">
         <center>
            <b>Clics:</b> <small class="label bg-green">{{$solicitud->cantidad_clics}}</small>
         </center>
      </div>
      <div class="col-md-4"></div>
   </div><br>

   <div class="row">
      <div class="col-md-1"></div>
         
      <div class="col-md-10 col-xs-12"> 
         <div class="panel panel-default panel-success">
            <div class="panel-heading"><h4><b> 
               {{ $solicitud->banner->titulo }}</b></h4>
            </div>
             
            <ul class="list-group">
               <li class="list-group-item"><b>Fecha de Solicitud:</b> {{ $solicitud->created_at->format('d-m-Y') }}</li>
               <li class="list-group-item"><b>País Destino:</b> {{ $solicitud->pais->pais }}</li>
               <li class="list-group-item"><b>Tiempo de Publicación:</b> {{ $solicitud->tiempo_publicacion }} Días</li>
               <li class="list-group-item"><b>Status de Publicación:</b>
                  @if ($solicitud->publicado == '0')
                     Esperando Publicación
                  @else 
                     Publicado
                  @endif
               </li>
            </ul>
         </div>
      </div>
   </div>
@endsection
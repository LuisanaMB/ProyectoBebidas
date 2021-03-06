@extends('plantillas.main')
@section('title', 'Solicitudes')

@section('title-header')
   Solicitudes
@endsection

@section('title-complement')
   (Importación)
@endsection

@section('content-left')
  <?php 
      $not_dp = DB::table('notificacion_p')->select('id')
               ->where('productor_id', '=', session('perfilId'))
               ->where('tipo', '=', 'DP')->where('leida', '=', '0')->get();
      $dp=0;
      foreach($not_dp as $ndp){
         $dp++;
      }

      $not_db = DB::table('notificacion_p')->select('id')
               ->where('productor_id', '=', session('perfilId'))
               ->where('tipo', '=', 'DB')->where('leida', '=', '0')->get();
      $db=0;
      foreach($not_db as $ndb){
         $db++;
      }

      $not_si = DB::table('notificacion_p')->select('id')
               ->where('productor_id', '=', session('perfilId'))
               ->where('tipo', '=', 'SI')->where('leida', '=', '0')->get();
      $si=0;
      foreach($not_si as $nsi){
         $si++;
         DB::table('notificacion_p')->where('id', '=', $nsi->id)->update(['leida' => '1']);
      }

      $not_sd = DB::table('notificacion_p')->select('id')
               ->where('productor_id', '=', session('perfilId'))
               ->where('tipo', '=', 'SD')->where('leida', '=', '0')->get();
      $sd=0;
      foreach($not_sd as $nsd){
         $sd++;
      }
   ?>

   @section('alertas')
      @if (Session::has('msj'))
         <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>¡Enhorabuena!</strong> {{Session::get('msj')}}.
         </div>
      @endif

      <div class="alert alert-info">
         <strong>Elija el tipo "Marca / Bebida" en el filtro de Búsqueda para ver las solicitudes de distribución de marcas o bebidas disponibles.</strong>
      </div>
   @endsection  
   
   <ul class="nav nav-pills">
      <li class="btn btn-default">
         <a href="{{ route('demanda-producto.demandas-productos-disponibles') }}"><strong>PRODUCTO | 
         <small class="label bg-red">{{ $dp }}</small></strong></a>
      </li>
      <li class="btn btn-default">
         <a href="{{ route('demanda-producto.demandas-bebidas-disponibles') }}"><strong>BEBIDA | <small class="label bg-red">{{ $db }}</small></strong></a>
      </li>
      <li class="active btn btn-default">
         <a href="{{ route('solicitud-importacion.solicitudes') }}"><strong>IMPORTACIÓN | <small class="label bg-orange">{{ $si }}</small></strong></a>
      </li>
      <li class="btn btn-default">
         <a href="{{ route('solicitud-distribucion.solicitudes') }}"><strong>DISTRIBUCIÓN | <small class="label bg-red">{{ $sd }}</small></strong></a>
      </li>
   </ul>

   <div class="panel with-nav-tabs panel-primary">
      <div class="panel-heading"></div>
      <div class="panel-body">
         <div class="tab-content">
            <div class="tab-pane fade in active">
               <ul class="timeline">
                  @if ($cont > 0)
                     @foreach($demandasImportacion as $demandaImportacion)
                        <?php 
                           $relacion = DB::table('productor_solicitud_importacion')
                                       ->select('solicitud_importacion_id')
                                       ->where('solicitud_importacion_id', '=', $demandaImportacion->id)
                                       ->where('productor_id', '=', session('perfilId'))
                                       ->first();

                           $demanda = App\Models\Solicitud_importacion::find($demandaImportacion->id);
                        ?>
                        @if ($relacion == null)
                           <li>
                              @if ($demanda->marca_id != null)
                                 <i class="fa fa-hand-pointer-o bg-blue"></i>
                              @else 
                                 <i class="fa fa-hand-pointer-o bg-green"></i>
                              @endif

                              <div class="timeline-item">
                                 <span class="time"><i class="fa fa-clock-o"></i> {{ date('d-m-Y', strtotime($demanda->created_at)) }}</span>
                                    
                                 @if ($demanda->marca_id != null)
                                    <h3 class="timeline-header">Un importador está demandando la importación de tu marca.</h3>

                                    <div class="timeline-body">
                                       El importador <strong>{{ $demanda->importador->nombre }}</strong> ha indicado que quiere importar tu marca <strong>{{ $demanda->marca->nombre }}</strong> en su país...
                                    </div>
                                 @else 
                                    <h3 class="timeline-header">Un importador está demandando la importación de un tipo de bebida que tu posees.</h3>

                                    <div class="timeline-body">
                                       El importador <strong>{{ $demanda->importador->nombre }}</strong> ha indicado que quiere importar la  bebida <strong>{{ $demanda->bebida->nombre }}</strong> en su país...
                                    </div>
                                 @endif
                                    
                                 <div class="timeline-footer">
                                    <a class="btn btn-primary btn-xs" href="{{ route('solicitud-importacion.show', $demanda->id) }}">¡Más Detalles!</a>
                                    <a class="btn btn-danger btn-xs" href="{{ route('solicitud-importacion.marcar', [$demanda->id, '0']) }}">¡No Me Interesa!</a>
                                 </div>
                              </div>
                           </li>
                        @endif
                     @endforeach
                  @else
                     <strong>No existen solicitudes de importación disponibles.</strong>
                  @endif
               </ul>
            </div>
         </div>
      </div>
   </div>
@endsection

@section('content-right')
    <div class="panel with-nav-tabs panel-default">
         <div class="panel-heading">
            <h5><b><center>Filtros de Búsqueda</center></b></h5>
         </div>
         <div class="panel-body">
            <div class="tab-content">
               <div class="tab-pane fade in active">
                  @include('solicitudes.tabs.filtroImportacion')
               </div>
            </div>
         </div>
      </div>
@endsection

@section('paginacion')
   {{$demandasImportacion->appends(Request::only(['tipo']))->render()}}
@endsection


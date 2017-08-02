@extends('plantillas.main')
@section('title', 'Demandas de Interés')

{!! Html::script('js/credito/detalleGasto.js') !!}

@section('title-header')
   Demandas de Distribución
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
         <div class="box">
            <div class="box-header">
               <h3 class="box-title">Mis Demandas</h3>
               <div class="box-tools">
                  <div class="input-group input-group-sm" style="width: 150px;">
                     <input type="text" name="table_search" class="form-control pull-right" placeholder="Buscar">
                     <div class="input-group-btn">
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                     </div>
                  </div>
               </div>
            </div>

            <div class="box-body table-responsive no-padding table-bordered">
               <table class="table table-hover">
                  <thead>
                     <th><center>Fecha</center></th>
                     <th><center>Marca</center></th>
                     <th><center>Productor / Importador</center></th>
                     <th><center>Acción</center></th>
                  </thead>
                  <tbody>
                     @foreach ($demandas as $demanda) 
                        <?php 
                           if ($demanda->tipo_creador == 'P'){
                              $creador = DB::table('productor')
                                          ->select('nombre')
                                          ->where('id', '=', $demanda->creador_id)
                                          ->first();
                           }elseif ($demanda->tipo_creador == 'I'){
                              $creador = DB::table('importador')
                                          ->select('nombre')
                                          ->where('id', '=', $demanda->creador_id)
                                          ->first();
                           }
                         ?>
                        <tr>
                           <td><center>{{ date('d-m-Y', strtotime($demanda->fecha)) }}</td>
                           <td><center>{{ $demanda->marca->nombre}}</center></td>
                           <td><center>{{ $creador->nombre}} </center></td>
                           <td><center>
                              <a href="{{ route('demanda-distribuidor.show', $demanda->id) }}" class="btn btn-primary btn-xs"> Detalles <i class="fa fa-eye"></i></a></td>
                           </center></td>
                        </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>      
         </div>
   </div>
@endsection

@section('paginacion')
   {{$demandas->render()}}
@endsection
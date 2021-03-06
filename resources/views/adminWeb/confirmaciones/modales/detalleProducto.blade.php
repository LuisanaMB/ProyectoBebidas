<div class="modal fade" id="detalleModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Detalles del Producto <span id="nombre"></span></h4>
      </div>
      <div class="modal-body">
         <ul class="nav nav-stacked" id="detalles"></ul>
         {!! Form::open(['route' => ['admin.aprobar-producto', '0'], 'method' => 'GET']) !!}
            {!! Form::hidden('producto_id', '0', ['id' => 'producto_id']) !!}
               
      </div>
      <div class="modal-footer">
         {!! Form::button('Cancelar', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) !!}
         {!! Form::submit('Aprobar', ['class' => 'btn btn-primary']) !!}
      </div>
         {!! Form::close() !!}
            }
    </div>
  </div>
</div>


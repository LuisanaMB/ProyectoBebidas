@extends('plantillas.main')
@section('title', 'Mi Perfil')

@section('title-header')
   Editar Perfil
@endsection

@section('content-left')
	{!! Html::script('js/horecas/edit.js') !!}

	@section('alertas')
      	@if (Session::has('msj'))
           <div class="alert alert-success alert-dismissable">
               <button type="button" class="close" data-dismiss="alert">&times;</button>
               <strong>¡Enhorabuena!</strong> {{Session::get('msj')}}.
           </div>
      	@endif
   	@endsection

	@include('horeca.modales.modalAvatar')

	{!! Form::open(['route' => ['horeca.update', $horeca->id], 'method' => 'PUT']) !!}
		<div class="panel with-nav-tabs panel-info">
	        <div class="panel-heading">
	        	<ul class="nav nav-tabs">
	                <li class="active"><a href="#tab1default" data-toggle="tab">Datos Personales</a></li>
	                <li><a href="#tab2default" data-toggle="tab">Datos de Ubicación</a></li>
	                <li><a href="#tab3default" data-toggle="tab">Datos de Contacto</a></li>
	                <li><a href="#tab4default" data-toggle="tab">Redes Sociales</a></li> 
	            </ul>
	        </div>
	        <div class="panel-body">
	            <div class="tab-content">
	                <div class="tab-pane fade in active" id="tab1default">
	                	<div class="row">
	                		<div class="col-md-4"></div>
		       				<div class="col-sm-6 col-md-4">
		         				<a href="" class="thumbnail" data-toggle='modal' data-target="#myModal"><img src="{{ asset('imagenes/horecas/thumbnails') }}/{{ $horeca->logo }}"></a>
		       				</div>
		       				<div class="col-md-4"></div>
	                	</div>
	                	
	                	<div class="form-group">
							{!! Form::label('nombre', 'Nombre (*)') !!}
							{!! Form::text('nombre', $horeca->nombre, ['class' => 'form-control', 'required'] ) !!}
						</div>

						<div class="form-group">
							{!! Form::label('nombre_seo', 'Nombre SEO') !!}
							{!! Form::text('nombre_seo', $horeca->nombre_seo, ['class' => 'form-control'] ) !!}
						</div>

						<div class="form-group">
							{!! Form::label('descripcion', 'Descripción') !!}
							{!! Form::textarea('descripcion', $horeca->descripcion, ['class' => 'form-control', 'rows' => '5'] ) !!}
						</div>
	                </div>
	                <div class="tab-pane fade" id="tab2default">
						<div class="form-group">
							{!! Form::label('direccion', 'Dirección') !!}
							{!! Form::textarea('direccion', $horeca->direccion, ['class' => 'form-control', 'rows' => '5'] ) !!}
						</div>

						<div class="form-group">
							{!! Form::label('codigo_postal', 'Código Postal') !!}
							{!! Form::text('codigo_postal', $horeca->codigo_postal, ['class' => 'form-control'] ) !!}
						</div>
						
						<div class="form-group">
							{!! Form::label('pais', 'País (*)') !!}
							{!! Form::select('pais_id', $paises, $horeca->pais_id, ['class' => 'form-control', 'id' => 'pais_id', 'onchange' => 'cargarProvincias();', 'required']) !!}
						</div>

						<div class="form-group">
							{!! Form::label('provincias', 'Provincia / Estado (*)') !!}
							{!! Form::select('provincia_region_id', $provincias, $horeca->provincia_region_id, ['class' => 'form-control', 'id' => 'provincias', 'required']) !!}
						</div>
						<div class="form-group">
							{!! Form::label('latitud', 'Latitud') !!}
							{!! Form::text('latitud', $horeca->latitud, ['class' => 'form-control', 'placeholder' => '00.000'] ) !!}
						</div>

						<div class="form-group">
							{!! Form::label('longitud', 'Longitud') !!}
							{!! Form::text('longitud', $horeca->longitud, ['class' => 'form-control', 'placeholder' => '00.000'] ) !!}
						</div>
	                </div>
	                <div class="tab-pane fade" id="tab3default">
	            		<div class="form-group">
							{!! Form::label('persona_contacto', 'Persona de Contacto') !!}
							{!! Form::text('persona_contacto', $horeca->persona_contacto, ['class' => 'form-control'] ) !!}
						</div>
						
						<div class="form-group">
							{!! Form::label('telefono', 'Teléfono') !!}
							{!! Form::text('telefono', $horeca->telefono, ['class' => 'form-control'] ) !!}
						</div>

						<div class="form-group">
							{!! Form::label('telefono_opcional', 'Teléfono Opcional') !!}
							{!! Form::text('telefono_opcional', $horeca->telefono_opcional, ['class' => 'form-control'] ) !!}
						</div>

						<div class="form-group">
							{!! Form::label('email', 'Correo Electrónico (*)') !!}
							{!! Form::email('email', $horeca->email, ['class' => 'form-control', 'placeholder' => 'correo@servicio.com', 'required'] ) !!}
						</div>    	
	                </div>
	                <div class="tab-pane fade" id="tab4default">
	                	<div class="form-group">
							{!! Form::label('website', 'Website') !!}
							{!! Form::url('website', $horeca->website, ['class' => 'form-control', 'placeholder' => '(http://www.dominio.com)'] ) !!}
						</div>

						<div class="form-group">
							{!! Form::label('facebook', 'Facebook') !!}
							{!! Form::text('facebook', $horeca->facebook, ['class' => 'form-control'] ) !!}
						</div>

						<div class="form-group">
							{!! Form::label('twitter', 'Twitter') !!}
							{!! Form::text('twitter', $horeca->twitter, ['class' => 'form-control'] ) !!}
						</div>

						<div class="form-group">
							{!! Form::label('instagram', 'Instagram') !!}
							{!! Form::text('instagram', $horeca->instagram, ['class' => 'form-control'] ) !!}
						</div>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="form-group">
			{!! Form::submit('Actualizar Datos', ['class' => 'btn btn-primary pull-right']) !!}
		</div>
    {!! Form::close() !!}
@endsection
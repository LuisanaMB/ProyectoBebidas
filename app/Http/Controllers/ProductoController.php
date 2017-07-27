<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Pais; use App\Models\Provincia_Region;
use App\Models\Clase_Bebida;
use App\Models\Marca; use App\Models\Bebida;
use App\Models\Importador; use App\Models\Distribuidor;
use App\Models\Productor; use App\Models\Notificacion_P; use App\Models\Notificacion_Admin;
use DB; use Image; use Input; use Auth;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        if (session('perfilTipo') == 'AD'){
            $productos = Producto::orderBy('nombre')
                            ->paginate(7);

            $marca = '0';

            return view('adminWeb.listados.productos')->with(compact('productos', 'marca'));
        }

        $productos = DB::table('producto')
                        ->where('tipo_creador', '=', 'U')
                        ->where('creador_id', '=', Auth::user()->id)
                        ->orderBy('nombre')
                        ->paginate(6);

        return view('usuario.listados.productos')->with(compact('productos'));
    }

    public function create()
    {
        $usuario = '1';

        $id = 0; 
        $marca = 0;

        $marcas = DB::table('marca')
                    ->orderBy('nombre')
                    ->pluck('nombre', 'id');

        $paises = DB::table('pais')
                    ->orderBy('pais')
                    ->pluck('pais', 'id');

        $tipos_bebidas = DB::table('bebida')
                    ->orderBy('nombre')
                    ->pluck('nombre', 'id');

        if (session('perfilTipo') == 'AD'){
            return view('adminWeb.producto.create')->with(compact('marcas', 'paises', 'tipos_bebidas', 'marca'));
        }
        
        return view('producto.create')->with(compact('marcas', 'paises', 'tipos_bebidas', 'usuario', 'id', 'marca'));
    }

    public function agregar($id, $marca){
        $usuario = '0';

        $paises = DB::table('pais')
                    ->orderBy('pais')
                    ->pluck('pais', 'id');

        $tipos_bebidas = DB::table('bebida')
                    ->orderBy('nombre')
                    ->pluck('nombre', 'id');

        if (session('perfilTipo') == 'AD'){
            return view('adminWeb.producto.create')->with(compact('marcas', 'paises', 'tipos_bebidas', 'marca'));
        }

        return view('producto.create')->with(compact('id', 'marca', 'paises', 'tipos_bebidas', 'usuario'));
    }

    public function store(Request $request)
    {
        $fecha = new \DateTime();

        $file = Input::file('imagen');   
        $image = Image::make(Input::file('imagen'));

        $path = public_path().'/imagenes/productos/';
        $path2 = public_path().'/imagenes/productos/thumbnails/';
        $nombre = 'producto_'.time().'.'.$file->getClientOriginalExtension();

        $image->save($path.$nombre);
        $image->resize(240,200);
        $image->save($path2.$nombre);

        $producto = new Producto($request->all());
        $producto->imagen = $nombre;
        $producto->save();

        $ult_producto = DB::table('producto')
                            ->select('id')
                            ->orderBY('id', 'DESC')
                            ->get()
                            ->first();

        $notificaciones_admin = new Notificacion_Admin();
        $notificaciones_admin->creador_id = session('perfilId');
        $notificaciones_admin->tipo_creador = session('perfilTipo');
        $notificaciones_admin->titulo = session('perfilNombre') . ' ha creado un nuevo producto en la marca '. $request->marca_nombre;
        $notificaciones_admin->url='admin/productos-sin-aprobar';
        $notificaciones_admin->user_id = 0;
        $notificaciones_admin->descripcion = 'Nuevo Producto';
        $notificaciones_admin->color = 'bg-yellow';
        $notificaciones_admin->icono = 'fa fa-plus-square-o';
        $notificaciones_admin->fecha = $fecha;
        $notificaciones_admin->tipo = 'NP';
        $notificaciones_admin->leida = '0';
        $notificaciones_admin->save();            
    
        if ($request->usuario == '1'){
            return redirect('producto')->with('msj', 'Su producto ha sido agregado exitosamente');
        }else{
            return redirect('producto/listado-de-productos/'.$request->marca_id.'-'.$request->marca_nombre)->with('msj', 'Su producto ha sido agregado exitosamente');
        }           
    }

    public function listado($id, $marca){
        $productos = Marca::find($id)
                            ->productos()
                            ->paginate(8);

        if (session('perfilTipo') == 'AD'){
            return view('adminWeb.listados.productos')->with(compact('productos', 'marca'));
        }

        return view('producto.listado')->with(compact('productos', 'marca'));
    }

    public function seleccionar_productos($marca){
        $productos = Producto::where('marca_id', '=', $marca)
                        ->orderBy('nombre', 'ASC')
                        ->paginate();

        $nombre_marca = DB::table('marca')
                        ->select('nombre')
                        ->where('id', '=', $marca)
                        ->first();

        return view('producto.seleccionarProductos')->with(compact('productos','nombre_marca'));
    }

    public function asociar_productos(Request $request){
        foreach ($request->productos as $producto){
            if (session('perfilTipo') == 'I'){
                Importador::find(session('perfilId'))->productos()->attach($producto);
            }elseif (session('perfilTipo') == 'D'){
                Distribuidor::find(session('perfilId'))->productos()->attach($producto);
            }elseif (session('perfilTipo') == 'H'){
                Horeca::find(session('perfilId'))->productos()->attach($producto);
            }
        }
        return redirect('marca')->with('msj', 'Los productos han sido asociados a su lista exitosamente.');
    }

    public function show($id)
    {
        $tipo = explode('.', $id);

        if ($tipo[1] == '1'){
            //Mostrar Productos de una Marca específica
            $productos = DB::table('producto')
                    ->select('id', 'nombre')
                    ->orderBy('nombre')
                    ->where('marca_id', '=', $tipo[0])
                    ->get();
        }elseif ($tipo[1] == '2'){
            //Método para buscar un producto específico
            $productos = DB::table('producto')
                    ->select('producto.id', 'producto.nombre', 'producto.nombre_seo', 'producto.imagen', 'productor.id as productor')
                    ->join('marca', 'producto.marca_id', '=', 'marca.id')
                    ->join('productor', 'marca.productor_id', '=', 'productor.id')
                    ->orderBy('producto.nombre')
                    ->where('producto.nombre', 'ILIKE', '%'.$tipo[0].'%')
                    ->get();

            foreach ($productos as $producto){
                //Consulto los paises marcados como destino por el productor
                $paises_productor = DB::table('productor_pais')
                                ->select('pais_id')
                                ->where('productor_id', '=', $producto->productor)
                                ->get();
                $check = 0;
                $cont = 0;
                //Verifico si el país es destino laboral del productor
                foreach ($paises_productor as $pais){
                    $cont++;
                    if ($pais->pais_id == session('perfilPais')){
                        $check = 1;
                    }
                }

                //Si todavía el productor no ha marcado ningún país
                if ($cont == 0){
                    $check = 1;
                }

                $producto->check = $check;
            }
        }
       
        return response()->json(
            $productos->toArray()
        );
    }

    //Buscar productos por tipo de bebida y clase (opcional)
    public function productos_por_clase($bebida, $clase){
        if ($clase == '0'){
            $productos = DB::table('producto')
                    ->select('producto.id', 'producto.nombre', 'producto.nombre_seo', 'producto.imagen', 'productor.id as productor')
                    ->join('marca', 'producto.marca_id', '=', 'marca.id')
                    ->join('productor', 'marca.productor_id', '=', 'productor.id')
                    ->orderBy('producto.nombre')
                    ->where('producto.bebida_id', '=', $bebida)
                    ->get();
        }else{
            $productos = DB::table('producto')
                    ->select('producto.id', 'producto.nombre', 'producto.nombre_seo', 'producto.imagen', 'productor.id as productor')
                    ->join('marca', 'producto.marca_id', '=', 'marca.id')
                    ->join('productor', 'marca.productor_id', '=', 'productor.id')
                    ->orderBy('producto.nombre')
                    ->where('producto.clase_bebida_id', '=', $clase)
                    ->get();
        }

        foreach ($productos as $producto){
            //Consulto los paises marcados como destino por el productor
            $paises_productor = DB::table('productor_pais')
                                ->select('pais_id')
                                ->where('productor_id', '=', $producto->productor)
                                ->get();
            $check = 0;
            $cont = 0;
            //Verifico si el país es destino laboral del productor
            foreach ($paises_productor as $pais){
                $cont++;
                if ($pais->pais_id == session('perfilPais')){
                    $check = 1;
                }
            }

            //Si todavía el productor no ha marcado ningún país
            if ($cont == 0){
                $check = 1;
            }

            $producto->check = $check;
        }

        return response()->json(
            $productos->toArray()
        );
    }

    //Buscar productos por tipo de bebida y país
    public function productos_por_pais($bebida, $pais){
        $productos = DB::table('producto')
                    ->select('producto.id', 'producto.nombre', 'producto.nombre_seo', 'producto.imagen', 'productor.id as productor')
                    ->join('marca', 'producto.marca_id', '=', 'marca.id')
                    ->join('productor', 'marca.productor_id', '=', 'productor.id')
                    ->orderBy('producto.nombre')
                    ->where('producto.bebida_id', '=', $bebida)
                    ->where('producto.pais_id', '=', $pais)
                    ->get();

        foreach ($productos as $producto){
            //Consulto los paises marcados como destino por el productor
            $paises_productor = DB::table('productor_pais')
                                ->select('pais_id')
                                ->where('productor_id', '=', $producto->productor)
                                ->get();
            $check = 0;
            $cont = 0;
            //Verifico si el país es destino laboral del productor
            foreach ($paises_productor as $pais){
                $cont++;
                if ($pais->pais_id == session('perfilPais')){
                    $check = 1;
                }
            }

            //Si todavía el productor no ha marcado ningún país
            if ($cont == 0){
                $check = 1;
            }

            $producto->check = $check;
        }

        return response()->json(
            $productos->toArray()
        );
    }

    //Método para cargar los detalles de un producto
    //Para solicitarlo en importación o distribución
    public function verificar_producto($id){
        $producto = Producto::where('id', '=', $id)->with('bebida', 'clase_bebida', 'marca')
                    ->first()->toArray();

        return response()->json(
            $producto
        );
    }

    public function detalle(Request $request, $id){
        $producto = Producto::find($id);

        $productor = Productor::find($producto->marca->productor_id)
                        ->select('nombre')
                        ->get()
                        ->first();

        $comentarios = DB::table('opinion')
                        ->orderBy('fecha', 'DESC')
                        ->where('producto_id', '=', $id)
                        ->take(6)
                        ->get();

        $cont = 0;
        foreach ($comentarios as $comentario)
            $cont++;
            
        $comentarioPerfil = DB::table('opinion')
                            ->where('tipo_creador', '=', session('perfilTipo'))
                            ->where('creador_id', '=', session('perfilId'))
                            ->where('producto_id', '=', $id)
                            ->first();

        $existe = 0;
        if ( $comentarioPerfil != null)
            $existe = '1';

        //Mostrar los datos de un producto específico para aprobarlo por el AdminWeb
        if ($request->ajax()){
            $producto = Producto::where('id', '=', $id)->with('pais', 'provincia_region', 'marca', 'bebida', 'clase_bebida')
                        ->first();
            return response()->json(
                $producto->toArray()
            );
        }

        if (session('perfilTipo') == 'AD'){
            return view('adminWeb.producto.detalleProducto')->with(compact('producto', 'productor', 'comentarios', 'cont'));
        }else{
            return view('producto.show')->with(compact('producto', 'productor', 'comentarios', 'cont', 'comentarioPerfil', 'existe'));
        }
    }

    public function edit($id)
    {
       
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);
        $producto->fill($request->all());
        $producto->save();

        if (session('perfilTipo') == 'AD'){
            return redirect('admin/detalle-producto/'.$request->id)->with('msj-success', 'Los datos del producto han sido actualizados exitosamente');
        }

       return redirect('producto/detalle-de-producto/'.$request->id)->with('msj', 'Los datos de su producto han sido actualizados exitosamente');
    }

     public function updateImagen(Request $request){
        $file = Input::file('imagen');   
        $image = Image::make(Input::file('imagen'));

        $path = public_path().'/imagenes/productos/';
        $path2 = public_path().'/imagenes/productos/thumbnails/';
        $nombre = 'producto_'.time().'.'.$file->getClientOriginalExtension();

        $image->save($path.$nombre);
        $image->resize(240,200);
        $image->save($path2.$nombre);

        $actualizacion = DB::table('producto')
                            ->where('id', '=', $request->id)
                            ->update(['imagen' => $nombre ]);

        if (session('perfilTipo') == 'AD'){
            return redirect('admin/detalle-producto/'.$request->id)->with('msj-success', 'La imagen del producto ha sido actualizada exitosamente');
        }

        return redirect('producto/detalle-de-producto/'.$request->id)->with('msj', 'La imagen del producto ha sido actualizada exitosamente');
    }

    public function destroy($id)
    {

    }
}

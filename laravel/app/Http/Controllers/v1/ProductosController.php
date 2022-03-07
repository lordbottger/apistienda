<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\v1\Producto;
use Illuminate\Http\Request;



class ProductosController extends Controller
{

	function getAll(Request $request)
	{		
		$search = $request->search;

		$fecha_inicio = $request->fecha_inicio;
		$fecha_fin = $request->fecha_fin;

		$categoria = $request->categoria;

		if(!isset($categoria))
			$categoria="%";
		else
			$categoria="%".$categoria."%";

		if(!isset($fecha_inicio))
			$fecha_inicio="2022-01-01 00:00:00";

		if(!isset($fecha_fin))
			$fecha_fin="3000-01-01 23:59:59";


		if(!isset($search))
			$search="%";		
		else
			$search="%".$search."%";


		$response = new \stdClass();
		$response->success=true;

		//$productos = Producto::all(); //Recupera todos los elementos de la tabla

		$productos = Producto::where(function($q) use ($search){
			$q->where("productos.codigo","like",$search)
			->orWhere("productos.nombre","like",$search);
		})
		
		->select("productos.*")
		
		->with("categoria")
		
		->where("productos.created_at",">=",$fecha_inicio." 00:00:00")
		->where("productos.created_at","<=",$fecha_fin. " 23:59:59")
	
		->where(function($q) use($categoria){

			if($categoria!="%")
			{
				$q->where("categorias.nombre","like",$categoria);
			}
		})
		//->where("categorias.nombre","like",$categoria)	


		->leftJoin("categorias","productos.categoria_id","=","categorias.id")
		->get();

		$response->data=$productos;

		return response()->json($response,200);
	}

	function getItem($id)
	{
		$response = new \stdClass();
		$response->success=true;

		$producto = Producto::find($id);
		$response->data = $producto;

		return response()->json($response,200);
	}

	function store(Request $request)
	{
		$response = new \stdClass();
		$response->success=true;

		$producto=Producto::where("codigo","=",$request->codigo)
		->first();
		if($producto)
		{
			$response->success=false;
			$response->errors=[];
			$response->errors[]="Ya existe un producto con el código ".$request->codigo;
			return response()->json($response,400);
		}

		$producto = new Producto();
		$producto->codigo = $request->codigo;
		$producto->nombre = $request->nombre;
		$producto->precio = $request->precio;
		$producto->stock = $request->stock;
		$producto->categoria_id = $request->categoria_id;

		if($request->image)
		{
			$file_name="/home/apismiap/public_html/files/".$request->image["nombre"].".".$request->image["extension"];

			file_put_contents($file_name, base64_decode($request->image["data"]));

			$producto->image_path = "http://apis.miapp.syslacsdev.com/files/".$request->image["nombre"].".".$request->image["extension"];
		}
		
		$producto->save();

		$response->data=$producto;

		return response()->json($response,201);
	}

	function update(Request $request)
	{
		$response = new \stdClass();
		$response->success=true;

		$producto= Producto::find($request->id);

		$producto->codigo = $request->codigo;
		$producto->nombre = $request->nombre;
		$producto->precio = $request->precio;
		$producto->stock = $request->stock;
		$producto->categoria_id = $request->categoria_id;
		$producto->save();

		$response->data = $producto;

		return response()->json($response,200);
	}

	function patch(Request $request)
	{
		$response = new \stdClass();
		$response->success=true;

		$producto= Producto::find($request->id);

		if(isset($request->codigo))
		$producto->codigo = $request->codigo;

		if(isset($request->nombre))
		$producto->nombre = $request->nombre;

		if(isset($request->precio))
		$producto->precio = $request->precio;

		if(isset($request->stock))
		$producto->stock = $request->stock;

		if(isset($request->categoria_id))
		$producto->categoria_id = $request->categoria_id;


		$producto->save();

		$response->data = $producto;

		return response()->json($response,200);
	}


	function delete($id)
	{
		$response = new \stdClass();
		$response->success=true;

		$response_code;

		$producto = Producto::find($id);

		if($producto)
		{
			$producto->delete();
			$response_code=200;
		}
		else
		{
			$response->success=false;
			$response->errors = [];
			$response->errors[]="El elemento ya ha sido eliminado previamente";
			$response_code=400;
		}

		return response()->json($response,$response_code);
	}

}
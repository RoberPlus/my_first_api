<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CursoController extends Controller
{
    // Mostrar todos los registros
    public function index(Request $request)
    {
        // Token generado por la insercion del clinete id y la llave secreta.
        $token = $request->header('Authorization');

        // Treamos los datos de los clientes.
        $clientes = Cliente::all();

        // Comprobamos si hay coincidencia entre los token de los clientes registrados y el token del Authorization.
        foreach ($clientes as $cliente => $value) {

            if ("Basic ".base64_encode($value['id_cliente']. ":". $value["llave_secreta"]) == $token) {
                // Si es correcto mostramos los cursos
                $cursos = Curso::all();

                if (!empty($cursos)) {
                    $json = [
                        "status" => 200,
                        "total registros" => count($cursos),
                        "detalles" => $cursos
                    ];
                }else{
                    $json = [
                        "status" => 200,
                        "total registros" => 0,
                        "detalles" => "No hay ningun curso registrado"
                    ];
                }

            // Si no coinciden los token (No esta registrado)
            }else{
                $json = [
                    "status" => 404,
                    "detalles" => "No esta autorizado para recibir los registros."
                ];
            }
        }

        return json_encode($json, true);
    }

    // Crear un nuevo registro
    public function store(Request $request)
    {
        // Token generado por la insercion del clinete id y la llave secreta.
        $token = $request->header('Authorization');

        // Treamos los datos de los clientes.
        $clientes = Cliente::all();

        // Comprobamos si hay coincidencia entre los token de los clientes registrados y el token del Authorization.
        foreach ($clientes as $cliente => $value) {
            if ("Basic ".base64_encode($value['id_cliente']. ":". $value["llave_secreta"]) == $token) {

                // Recoger datos
                $datos = [
                    "titulo" => $request->input("titulo"),
                    "descripcion" => $request->input("descripcion"),
                    "instructor" => $request->input("instructor"),
                    "imagen" => $request->input("imagen"),
                    "precio" => $request->input("precio"),
                ];

                // Validar datos
                $validator = Validator::make($datos, [
                    "titulo" => 'required|string|max:255|unique:cursos',
                    "descripcion" => 'required|string|unique:cursos',
                    "instructor" => 'required|string|max:255',
                    "imagen" => 'required|string|unique:cursos',
                    "precio" => 'required|numeric',
                ]);

                // Si falla la validacion
                if ($validator->fails()) {
                    $json = [
                        "status" => 404,
                        "detalle" => "Registro con errores."
                    ];

                    return json_encode($json, true);

                // Si la validacion es correcta
                } else {

                    // Crear cliente y asignar datos
                    $curso = new Curso;
                    $curso->titulo = $datos["titulo"];
                    $curso->descripcion = $datos["descripcion"];
                    $curso->instructor = $datos["instructor"];
                    $curso->imagen = $datos["imagen"];
                    $curso->precio = $datos["precio"];
                    $curso->id_creador = $value["id"];
                    $curso->save();

                    $json = [
                        "status" => 200,
                        "detalle" => "Registro exitoso, su curso fue almacenado"
                    ];
                }
            }
        }

        return json_encode($json, true);
    }

    // Mostrar un solo registro
    public function show(Request $request, $id)
    {
        // Token generado por la insercion del clinete id y la llave secreta.
        $token = $request->header('Authorization');

        // Treamos los datos de los clientes.
        $clientes = Cliente::all();

        // Comprobamos si hay coincidencia entre los token de los clientes registrados y el token del Authorization.
        foreach ($clientes as $cliente => $value) {
            if ("Basic ".base64_encode($value['id_cliente']. ":". $value["llave_secreta"]) == $token) {
                // Si es correcto mostramos los cursos
                $curso = Curso::select('titulo', 'descripcion', 'instructor', 'imagen', 'precio')->where('id', $id)->first();
                $curso_creador = Curso::where('id', $id)->first()->creador;

                if (!empty($curso)) {
                    $json = [
                        "status" => 200,
                        "detalles" => $curso,
                        "detalles del creador" => [
                            "nombre" => $curso_creador->primer_nombre,
                            "apellido" => $curso_creador->primer_apellido,
                            "email" => $curso_creador->email,
                        ]
                    ];
                }else{
                    $json = [
                        "status" => 404,
                        "detalles" => "No hay ningun curso registrado con este id"
                    ];
                }

            // Si no coinciden los token (No esta registrado)
            }else{
                $json = [
                    "status" => 403,
                    "detalles" => "No esta autorizado para recibir los registros."
                ];
            }
        }
            return json_encode($json, true); 
    }

    // Editar registro
    public function update(Request $request, $id)
    {
        // Token generado por la insercion del clinete id y la llave secreta.
        $token = $request->header('Authorization');

        // Treamos los datos de los clientes.
        $clientes = Cliente::all();

        // Comprobamos si hay coincidencia entre los token de los clientes registrados y el token del Authorization.
        foreach ($clientes as $cliente => $value) {
            if ("Basic ".base64_encode($value['id_cliente']. ":". $value["llave_secreta"]) == $token) {

                // Recoger datos
                $datos = [
                    "titulo" => $request->input("titulo"),
                    "descripcion" => $request->input("descripcion"),
                    "instructor" => $request->input("instructor"),
                    "imagen" => $request->input("imagen"),
                    "precio" => $request->input("precio"),
                ];

                // Validar datos
                $validator = Validator::make($datos, [
                    "titulo" => 'required|string|max:255',
                    "descripcion" => 'required|string',
                    "instructor" => 'required|string|max:255',
                    "imagen" => 'required|string',
                    "precio" => 'required|numeric',
                ]);

                // Si falla la validacion
                if ($validator->fails()) {
                    $json = [
                        "status" => 404,
                        "detalle" => "Registro con errores."
                    ];

                    return json_encode($json, true);

                // Si la validacion es correcta
                } else {

                    // Trear curso y actualizar datos
                    $curso = Curso::where('id', $id)->first();

                    // Verificar si es el mismo creador y actualizar los datos.
                    if ($value["id"] == $curso["id_creador"]) {

                        $curso->update($datos);

                        $json = [
                            "status" => 200,
                            "detalle" => "Curso actualizado con exito."
                        ];
                    
                    // Si no es el mismo creador, 404
                    }else{
                        $json = [
                            "status" => 404,
                            "detalle" => "No esta autorizado para modificar este curso."
                        ];
                    }
                }
            }
        }

        return json_encode($json, true);
    }

    // Eliminar un registro
    public function destroy(Request $request, $id)
    {
        // Token generado por la insercion del clinete id y la llave secreta.
        $token = $request->header('Authorization');

        // Treamos los datos de los clientes.
        $clientes = Cliente::all();

        // Comprobamos si hay coincidencia entre los token de los clientes registrados y el token del Authorization.
        foreach ($clientes as $cliente => $value) {
            if ("Basic ".base64_encode($value['id_cliente']. ":". $value["llave_secreta"]) == $token) {
                // Trear curso
                $curso = Curso::where('id', $id)->first();

                if (!empty($curso)) {
                    // Verificar si es el mismo creador y eliminar el registro.
                    if ($value["id"] == $curso["id_creador"]) {

                        $curso->delete();

                        $json = [
                            "status" => 200,
                            "detalle" => "Curso eliminado con exito."
                        ];
                    
                    // Si no es el mismo creador, 404
                    }else{
                        $json = [
                            "status" => 403,
                            "detalle" => "No esta autorizado para eliminar este registro."
                        ];
                    }
                }else{
                    $json = [
                        "status" => 404,
                        "detalle" => "El curso no existe"
                    ];
                }
            }
        }

        return json_encode($json, true);
    }
}

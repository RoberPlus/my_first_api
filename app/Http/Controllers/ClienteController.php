<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function index()
    {
        $json = [
            "detalle" => "No encontrado."
        ];

        return json_encode($json, true);
    }

    public function store(Request $request)
    {
        // Recoger datos
        $datos = [
            "primer_nombre" => $request->input("primer_nombre"),
            "primer_apellido" => $request->input("primer_apellido"),
            "email" => $request->input("email")
        ];

        // Validar datos
        $validator = Validator::make($datos, [
            "primer_nombre" => 'required|string|max:255',
            "primer_apellido" => 'required|string|max:255',
            "email" => 'required|string|email|max:255|unique:clientes'
        ]);

        // Errores de validacion
        if ($validator->fails()) {
            $json = [
                "status" => 404,
                "detalle" => "Registro con errores."
            ];

            return json_encode($json, true);
        } else {
            // Creando ID y llave secreta
            $id_cliente = Hash::make($datos["primer_nombre"] . $datos["primer_apellido"] . $datos["email"]);
            $llave_secreta_cliente = Hash::make($datos["email"] . $datos["primer_apellido"] . $datos["primer_nombre"]);

            // Crear cliente y asignar datos
            $cliente = new Cliente;
            $cliente->primer_nombre = $datos["primer_nombre"];
            $cliente->primer_apellido = $datos["primer_apellido"];
            $cliente->email = $datos["email"];
            $cliente->id_cliente = str_replace('$', '-', $id_cliente);
            $cliente->llave_secreta = str_replace('$', '-', $llave_secreta_cliente);
            $cliente->save();

            // Respuesta
            $json = [
                "status" => 200,
                "detalle" => "Registro exitoso, por favor guarde sus credenciales.",
                "credenciales" => [
                    "id_cliente" => str_replace('$', '-', $id_cliente),
                    "llave_secreta" => str_replace('$', '-', $llave_secreta_cliente),
                ]
            ];

            return json_encode($json, true);
        }
    }
}

<?php

require_once '../modelo/respuestas.class.php';
require_once '../modelo/usuario.class.php';

//Se crean las nuevas instancias de las clases. Con esto se crean nuevos objetos a partir de las clases mencionadas.
$_respuestas = new respuestas;
$_usuarios = new usuario;

if ($_SERVER['REQUEST_METHOD'] == "POST") { //Condicional para verificar el método pedido.

    // $headers = getallheaders();
    // // if(isset($headers["token"]) && isset($headers["idusuario"])){
    //     //recibimos los datos enviados por el header
    //     $send = [
    //         // "token" => $headers["token"],
    //         "idusuario" =>$headers["idusuario"]
    //     ];
    //     $postBody = json_encode($send);
    // }else{
    //     //recibimos los datos enviados
    //     $postBody = file_get_contents("php://input");
    // }

    //Se crea un array en formato JSON para recibir los datos enviados por el usuario y se guardan en la variable.
    $body =  json_encode(array("idusuario" => $_POST['idusuario']));

    //Enviamos datos al manejador
    $datosArray = $_usuarios->delete($body); //Se guardan las respuesta obtenidas de la función post luego de obtener los datos en la base de datos.
    
    //Delvovemos una respuesta 
    header('Content-Type: application/json'); //Indica que la respuesta sera enviada en formato json.
    
    if (isset($datosArray["result"]["error_id"])) { //Se comprueba si la variable tiene un valor de error.
        $responseCode = $datosArray["result"]["error_id"]; // Se guarda la respuesta de error en la variable.
        http_response_code($responseCode); // Se manda la respuesta de error.
    } else {
        http_response_code(200); //Se manda la respuesta de que el proceso fue exitoso.
    }
    echo json_encode($datosArray); //Se devuelve la respuesta en formato json.

} else {
    // Se manda la respuesta de error si el método no es permitido.
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}
?>
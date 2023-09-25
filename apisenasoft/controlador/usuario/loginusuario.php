<?php 
require_once '../modelo/usuario.class.php';
require_once '../modelo/respuestas.class.php';

//Se crean las nuevas instancias de las clases. Con esto se crean nuevos objetos a partir de las clases mencionadas.
$_usuarios = new usuario;
$_respuestas = new respuestas;

if($_SERVER['REQUEST_METHOD'] == "POST"){//Condicional para verificar el método pedido.

    //Se crea un array en formato JSON para recibir los datos enviados por el usuario y se guardan en la variable.
    $body =  json_encode (array("texto1" => $_POST['texto1'],
    "texto2" => $_POST['texto2']));

    //Enviamos los datos al manejador
    $datosArray = $_usuarios->login($body); //Se guardan las respuesta obtenidas de la función post luego de ingresar los datos en la base de datos.

    //Delvolvemos una respuesta
    header('Content-Type: application/json'); //Indica que la respuesta sera enviada en formato json.

    if(isset($datosArray["result"]["error_id"])){//Se comprueba si la vriable tiene un valor de error.
        $responseCode = $datosArray["result"]["error_id"];// Se guarda la respuesta de error en la variable.
        http_response_code($responseCode);// Se manda la respuesta de error.
    }else{
        http_response_code(200);//Se manda la respuesta de que el proceso fue exitoso.
    }
    echo json_encode($datosArray); //Se devuelve la respuesta en formato json.

}else{
    // Se manda la respuesta de error si el método no es permitido.
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}
?>
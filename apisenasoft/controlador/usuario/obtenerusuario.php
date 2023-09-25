<?php

require_once '../modelo/respuestas.class.php';
require_once '../modelo/usuario.class.php';

//Se crean las nuevas instancias de las clases. Con esto se crean nuevos objetos a partir de las clases mencionadas.
$_respuestas = new respuestas;
$_usuarios = new usuario;

if($_SERVER['REQUEST_METHOD'] == "POST"){//Condicional para verificar el método pedido.
    
    if(isset($_POST["page"])){ //Se verifica si se recibio un dato con el nombre page.
        
        $pagina = $_POST["page"];//Se crea un array en formato JSON para recibir los datos enviados por el usuario y se guardan en la variable.
        $listausuarios = $_usuarios->listaUsuario($pagina); //Se guardan las respuesta obtenidas de la función post luego de ingresar los datos en la base de datos.
        header("Content-Type: application/json");//Indica que la respuesta sera enviada en formato json.

        echo "<table>";
        while ($fila = mysqli_fetch_assoc($listausuarios)) { //Se guarda en una variable la fila del resultado obtenido.

            //Se muestra la tabla con los resultados.
            echo "<tr>";
            echo "<td>" . $fila['idusuario'] . "</td>";
            echo "<td>" . $fila['nombre'] . "</td>";
            echo "<td>" . $fila['correo'] . "</td>";
            echo "<td>" . $fila['contrasena'] . "</td>";
            echo "<td>" . $fila['rol'] . "</td>";
            echo "<td>" . $fila['estado'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo json_encode($_usuarios); //Se devuelve la respuesta en formato json.
        http_response_code(200);//Se manda la respuesta de que el proceso fue exitoso.

    }else if(isset($_POST['idusuario'])){ //Si no se encuentra el dato de page se verifica si se recibio un dato con el nombre page.

        $idusuario = $_POST['idusuario']; //Se crea un array en formato JSON para recibir los datos enviados por el usuario y se guardan en la variable.
        $datosUsuario = $_usuarios->obtenerUsuario($idusuario); //Se guardan las respuesta obtenidas de la función post luego de ingresar los datos en la base de datos.

        header("Content-Type: application/json"); //Indica que la respuesta sera enviada en formato json.
        echo json_encode($datosUsuario); //Se devuelve la respuesta en formato json.
        http_response_code(200); //Se manda la respuesta de que el proceso fue exitoso.
    }  

}else{
    // Se manda la respuesta de error si el método no es permitido.
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}
?>
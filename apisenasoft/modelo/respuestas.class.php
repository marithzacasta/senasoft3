<?php 

class respuestas{
    // Método de seguridad para mostrar el error con un mensaje. Al mismo tiempo es mas explícito para el usuario que tipo de error esta cometiendo.
    
    //Crea la variable response y le asigna un arreglo vacio como valor. Este será ocupado posteriormente con los datos de respuesta obtenidos por los métodos realizados.
    public  $response = [
        'status' => "ok", //El estado se define como ok para que solo cambie en caso de que se presente un error.
        "result" => array()
    ];

    public function error_405(){
        $this->response['status'] = "error"; //Se le asigna el valor "error" a la propiedad de estado.
        //Se configura el array a mostrar en pantalla con el código y mensaje de error.
        $this->response['result'] = array(
            "error_id" => "405",
            "error_msg" => "Metodo no permitido"
        );
        return $this->response; //Se devuelve la respuesta.
    }

    public function error_200($valor = "Datos incorrectos"){ //Se ingresa el valor de result.
        //Se configura el array a mostrar en pantalla con el código y mensaje de error.
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "200",
            "error_msg" => $valor
        );
        return $this->response;
    }

    public function error_400(){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "400",
            "error_msg" => "Datos enviados incompletos o con formato incorrecto"
        );
        return $this->response;
    }

    public function error_500($valor = "Error interno del servidor"){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "500",
            "error_msg" => $valor
        );
        return $this->response;
    }

    public function error_401($valor = "No autorizado"){
        $this->response['status'] = "error";
        $this->response['result'] = array(
            "error_id" => "401",
            "error_msg" => $valor
        );
        return $this->response;
    }
}
?>
<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";


class usuario extends conexion
{

    private $table = "usuario";
    private $idusuario = 0;
    private $nombre = "";
    private $correo = "";
    private $contrasena = "";
    private $rol = "";
    private $estado = "Activo";
    private $estadoDes = "Desactivo";
    //912bc00f049ac8464472020c5cd06759

    //FUNCIONES LOGIN

    public function login($json){
    
        $_respustas = new respuestas;//Se guardan las propiedades y metodos definidos en la clase respuestas.
        $datos = json_decode($json,true); //Decodifica la información.
        if(!isset($datos['texto1']) || !isset($datos["texto2"])){ // Se verifica si las variables estan vacias.
            return $_respustas->error_400();//Si las variables se encuentran vacias devuelve el error 400.
        }else{
            $usuario = $datos["texto1"]; //Se guarda la información de las claves para ser guardadas en las variables.
            $password = $datos["texto2"];
            // $password = parent::encriptar($password);
            $datos = $this->obtenerDatosUsuario($usuario); //Se llama esta función para obtener los datos de la base de datos.
            if($datos){//Si se encuentran los datos se pasa a verificar si la contraseña proporcionada por el usuario es correcta
                    if($password == $datos[0]["contrasena"]){
                          $esta = $datos[0]["estado"]; //Si la contraseña es correcta se guarda el estado en la variable $esta.
                            if($esta == "Activo"){ //Si el estado es activo.
                                //crear el token
                               // $verificar  = $this->insertarToken($datos[0]['UsuarioId']);
                                //if($verificar){
                                        // si se guardo
                                        $result = $_respustas->response;
                                        //$result["result"] = array(
                                          //  "token" => $verificar
                                        //);
                                        header("Location: ../../../camilo/vista/paginaprincipal/index.php");//Si el estado es activo redirecciona al usuario a la pagina principal.
                                        exit();
                                        // return $result;
                                //}//else{
                                        //error al guardar
                                  //      return $_respustas->error_500("Error interno, No hemos podido guardar");
                               // }
                            }else{
                                //el usuario esta inactivo
                                return $_respustas->error_200("El usuario esta inactivo");
                                return "El usuario esta inactivo";

                           }
                    }else{
                        //la contraseña no es igual
                    //    return $_respustas->error_200("El password es invalido");
                    return "La contrasena es incorrecta";

                    }
            }else{
                //no existe el usuario
                // return $_respustas->error_200("El usuario $usuario  no existe $datos ");
                return "El usuario no existe";
            }
        }
    }

    private function obtenerDatosUsuario($correo){ //Se realiza la consulta del correo, contraseña y estado en la base de datos.
        $query = "SELECT correo, contrasena, estado FROM usuario WHERE correo = '$correo'";
        $datos = parent::obtenerDatos($query); // Hace referencia a la función obtenerDatos de la clase padre (conexion.php)
        if(!isset($datos[0]["idusuario"])){//Si se encuentran los datos se devolveran estos mismos.
            return $datos;
        }else{
            return 0;
        }
  
    }

    //FUNCIONES GET

    public function listaUsuario($pagina = 1)
    { //Se indica el valor de la página como uno por defecto
        //Se ingresa el número de filas que serán seleccionadas de la base de datos.
        $inicio  = 0;
        $cantidad = 50;
        if ($pagina > 1) { //Se verifica si el número de paginas es mayor a uno.
            //Se cambian los valores para seleccionar diferentes usuarios al cambio de página.
            $inicio = (($cantidad * ($pagina - 1)) + 1);
            $cantidad = ($cantidad * $pagina);
        }
        $query = "SELECT * FROM " . $this->table . " LIMIT $inicio,$cantidad"; //Se guarda la consulta que se realizará en la base de datos en la variable query.
        $datos = parent::obtenerDatos($query); //Se guardan los datos obtenidos en la consulta.
        return ($datos); //Se devuelven los datos.
    }

    public function obtenerUsuario($idusuario)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE idusuario = $idusuario"; //Se guarda la consulta que se realizará en la base de datos en la variable query.
        return parent::obtenerDatos($query); //Se devuelven los datos obtenidos en la consulta.
    }
    
    //FUNCIONES POST

    public function post($json)
    {
        $_respuestas = new respuestas; //Se crea la instancia de la clase respuestas.
        $datos = json_decode($json, true); //Se decodifica la información en formato json que se pasó como parámetro.

        // if(!isset($datos['token'])){
        //         return $_respuestas->error_401();
        // }else{
        //     $this->token = $datos['token'];
        //     $arrayToken =   $this->buscarToken();
        // if($arrayToken){

        if (!isset($datos['nombre']) || !isset($datos['correo']) || !isset($datos['contrasena'])  || !isset($datos['rol'])) { //Se verifica si los campo necesarios estan vacios.
            return $_respuestas->error_400(); //Se devuelve el error correspondiente.
        } else {
            //Se asigna el valor del campo a la propiedad correspondiente.
            $this->nombre = $datos['nombre'];
            $this->correo = $datos['correo'];
            $this->contrasena = $datos['contrasena'];
            $this->rol = $datos['rol'];
            $resp = $this->insertarUsuario(); // Se llama al método encargado de realizar la consulta en la base de datos.
            if ($resp) { //Se verifica si se realizó la consulta con exito.
                //Se genera la respuesta en array con el id del usuario.
                $respuesta = $_respuestas->response;
                $respuesta["result"] = array(
                    "idusuario" => $resp
                );
                //Se redirecciona al usuario.
                header("Location: ../../../camilo/vista/login/index.php");
                exit();
                return $respuesta; //Se duelve la respuesta.
            } else {
                return $_respuestas->error_500(); //Se devuelve el error correspondiente.
            }
        }
        // }else{
        //     return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
        // }
    }

    public function insertarUsuario()
    {
        //Se guarda la consulta que se realizará en la base de datos en la variable query.
        $query = "INSERT INTO " . $this->table . " (idusuario,nombre,correo,contrasena,rol,estado)
        values
        ('" . $this->idusuario . "','" . $this->nombre . "','" . $this->correo . "','" . $this->contrasena . "','"  . $this->rol . "','" . $this->estado . "')";
        $resp = parent::nonQueryId($query); //Se guardan los datos obtenidos en la consulta.
        //Se verifica si resp recibió algún valor. Si es así, se devuelve este mismo valor.
        if ($resp) {
            return $resp; //Se duelve la respuesta.
        } else {
            return 0;
        }
    }

    //FUNCIONES PUT

    public function put($json)
    {
        $_respuestas = new respuestas; //Se crea la instancia de la clase respuestas.
        $datos = json_decode($json, true); //Se decodifica la información en formato json que se pasó como parámetro.

        // if(!isset($datos['token'])){
        //     return $_respuestas->error_401();
        // }else{
        //     $this->token = $datos['token'];
        //     $arrayToken =   $this->buscarToken();
        //     if($arrayToken){
        if (!isset($datos['idusuario'])) { //Se verifica si el campo idusuario esta vacio.
            return $_respuestas->error_400(); //Se devuelve el error correspondiente.
        } else {
            $this->idusuario = $datos['idusuario']; //Se asigna el valor del campo a la propiedad idusuario.
            //Se verifica si el resto de campos se encuentran llenos. Si es asi, se asigna el valor del campo a la propiedad correspondiente.
            if (isset($datos['nombre'])) {
                $this->nombre = $datos['nombre'];
            }
            if (isset($datos['correo'])) {
                $this->correo = $datos['correo'];
            }
            if (isset($datos['contrasena'])) {
                $this->contrasena = $datos['contrasena'];
            }
            if (isset($datos['rol'])) {
                $this->rol = $datos['rol'];
            }
            if (isset($datos['estado'])) {
                $this->estado = $datos['estado'];
            }

            $resp = $this->modificarUsuario(); // Se llama al método encargado de realizar la consulta en la base de datos.
            if ($resp) { //Se verifica si se realizó la consulta con exito.
                //Se genera la respuesta en array con el id del usuario.
                $respuesta = $_respuestas->response;
                $respuesta["result"] = array(
                    "idusuario" => $this->idusuario
                );
                header("Location: ../../../camilo/vista/plantilla/indexupdate.php");//Si el proceso se cumple se redirecciona al usuario.
                exit();
                return $respuesta; //Se duelve la respuesta.
            } else {
                return $_respuestas->error_500(); //Se devuelve el error correspondiente.
            }
        }

        // }else{
        //     return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
        // }
        // }
    }


    private function modificarUsuario()
    {
        //Se guarda la consulta que se realizará en la base de datos en la variable query.
        $query = "UPDATE " . $this->table . " SET nombre ='" . $this->nombre . "',correo = '" . $this->correo . "', contrasena = '" . $this->contrasena . "', rol = '" .
            $this->rol . "', estado = '" . $this->estado . "' WHERE idusuario = '" . $this->idusuario . "'";
        $resp = parent::nonQuery($query); //Se guardan los datos obtenidos en la consulta.
        //Se verifica si resp recibio la respuesta esperada. Si es asi, se devuelve esta respuesta.
        if ($resp >= 1) {
            return $resp;
        } else {
            return 0;
        }
    }

    //FUNCIONES DESACTIVACIÓN

    public function desactivacionUsuario($json){
        $_respuestas = new respuestas; //Se crean las nuevas instancias de las clases. Con esto se crean nuevos objetos a partir de las clases mencionadas.
        $datos = json_decode($json,true); //Se decodifica el json y se guarda como arreglo en la variable $datos.

        // if(!isset($datos['token'])){
        //     return $_respuestas->error_401();
        // }else{
        //     $this->token = $datos['token'];
        //     $arrayToken =   $this->buscarToken();
        //     if($arrayToken){
                if(!isset($datos['idusuario'])){ //Se verifica si existe el id del usuario en el arreglo de datos. 
                    return $_respuestas->error_400(); //Se duelve el error si este esta vacio.
                }else{
                    $this->idusuario = $datos['idusuario']; //Si existe el id se asigna este valor a la propiedad $this->idusuario.
                    if(isset($datos['estado'])) { $this->estado = $datos['estado']; } // Si existe estado se le asigna este valor a $this->estado.

                    $resp = $this->desactivarUsuario(); //Se llama esta acción para actualizar la informacion del usuario y guardarla en la variable $resp si la modificación fue true or false.
                    if($resp){ //Si $resp es verdadero se envia un array de respuesta con el id del usuario.
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idusuario" => $this->idusuario
                        );
                        header("Location: ../../../camilo/vista/plantilla/indexdelete.php");//Si el proceso se cumple se redirecciona al usuario.
                        exit();
                        return $respuesta;  //Se duelve la respuesta.
                    }else{
                        return $_respuestas->error_500(); //Si resp es falso se devuelve el error.
                    }
                }

            // }else{
            //     return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            // }
        // }
    }

    private function desactivarUsuario(){ //Se toman los valores del estado, el id y la tabla para que se pueda actualizar la base de datos. 
        $query = "UPDATE " . $this->table . " SET estado ='" . $this->estadoDes . "' WHERE idusuario = '" . $this->idusuario . "'"; 
        $resp = parent::nonQuery($query); //Se utiliza esta función para realizar la consulta SQL en la base de datos. 
        if($resp >= 1){ //Si esta respuesta afecta al menos una fila en la tabla se devolvera el número de filas afectadas.
             return $resp;
        }else{
            return 0; // Si no se encuentran filas afectadas se devuelde un 0.
        }
    }

    //FUNCIONES DELETE
    
    public function delete($json)
    {
        $_respuestas = new respuestas; //Se crea la instancia de la clase respuestas.
        $datos = json_decode($json, true); //Se decodifica la información en formato json que se pasó como parámetro.

        // if(!isset($datos['token'])){
        //     return $_respuestas->error_401();
        // }else{
        //     $this->token = $datos['token'];
        //     $arrayToken =   $this->buscarToken();
        //     if($arrayToken){

        if (!isset($datos['idusuario'])) { //Se verifica si el campo idusuario esta vacio.
            return $_respuestas->error_400(); //Se devuelve el error correspondiente.
        } else {
            $this->idusuario = $datos['idusuario']; //Se asigna el valor del campo a la propiedad idusuario.
            $resp = $this->eliminarUsuario(); // Se llama al método encargado de realizar la consulta en la base de datos.
            if ($resp) { //Se verifica si se realizó la consulta con exito.
                //Se genera la respuesta en array con el id del usuario.
                $respuesta = $_respuestas->response;
                $respuesta["result"] = array(
                    "idusuario" => $this->idusuario
                );
                header("Location: ../../../camilo/vista/plantilla/indexdefdelete.php");//Si el proceso se cumple se redirecciona al usuario.
                exit();
                return $respuesta; //Se duelve la respuesta.
            } else {
                return $_respuestas->error_500(); //Se devuelve el error correspondiente.
            }
        }
        // }else{
        //     return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
        // }
        // }
    }

    private function eliminarUsuario()
    {
        //Se guarda la consulta que se realizará en la base de datos en la variable query.
        $query = "DELETE FROM " . $this->table . " WHERE idusuario= '" . $this->idusuario . "'";
        $resp = parent::nonQuery($query); //Se guardan los datos obtenidos en la consulta.
        //Se verifica si resp recibio la respuesta esperada. Si es asi, se devuelve esta respuesta.
        if ($resp >= 1) {
            return $resp;
        } else {
            return 0;
        }
    }

    //FUNCIONES TOKEN

    private function insertarToken($usuarioid){
        $val = true;
        $token = bin2hex(openssl_random_pseudo_bytes(16,$val)); //Selecciona una frase de 16 caracteres a-z 0-9
        $date = date("Y-m-d H:i");
        $estado = "Activo";
        $query = "INSERT INTO usuariostoken (UsuarioId,Token,Estado,Fecha)VALUES('$usuarioid','$token','$estado','$date')";
        $verifica = parent::nonQuery($query);
        if($verifica){
            return $token;
        }else{
            return 0;
        }
    }

    private function buscarToken(){
        $query = "SELECT  TokenId,UsuarioId,Estado from usuariostoken WHERE Token = '" . $this->token . "' AND Estado = 'Activo'";
        $resp = parent::obtenerDatos($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }

    private function actualizarToken($tokenid){
        $date = date("Y-m-d H:i");
        $query = "UPDATE usuariostoken SET Fecha = '$date' WHERE TokenId = '$tokenid' ";
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }else{
            return 0;
        }
    }

}
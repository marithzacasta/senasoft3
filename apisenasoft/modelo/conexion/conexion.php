<?php
// Clase conexion (Programación Orientada a Objetos)
class conexion {

    // Inicialización de variables privadas.
    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    private $conexion;

    //Constructor para inicializar los objetos de la clase a partir de los datos de conexion.
    function __construct(){
        $listadatos = $this->datosConexion();
        foreach ($listadatos as $key => $value) {
            $this->server = $value['server'];
            $this->user = $value['user'];
            $this->password = $value['password'];
            $this->database = $value['database'];
            $this->port = $value['port'];
        }
        //Se realiza la conexión a la base de datos
        $this->conexion = new mysqli($this->server,$this->user,$this->password,$this->database,$this->port);
        if($this->conexion->connect_errno){
            echo "fallo la conexion a la base datos";
            die();
        }

    }

    // Se encarga de obtener los datos de conexión almacenados en el archivo config.
    // Lee el contenido del archivo json para guardarlo en la variable jsondata.
    private function datosConexion(){
        $direccion = dirname(__FILE__);
        $jsondata = file_get_contents($direccion . "/" . "config");
        return json_decode($jsondata, true);
    }


    //Se toman los datos ingresados y los modifica para aceptar los caracteres especiales. 
    //Esta evita problemas de codificación incorrecta al procesar los datos.
    private function convertirUTF8($array){
        array_walk_recursive($array,function(&$item,$key){
            if(!mb_detect_encoding($item,'utf-8',true)){
                $item = utf8_encode($item);
            }
        });
        return $array;
    }

    // MODELO VISTA-CONTROLADOR: Los datos se consultan y antes de ser mostrados por pantalla pasan por este filtro (controlador).
    // Funciona como filtro de seguridad.
    public function obtenerDatos($sqlstr){ // Se ejecuta la consulta SQL para que sea pasada como parametro
        $results = $this->conexion->query($sqlstr);// Se guardan los resultados de la consulta en la variable results.
        $resultArray = array();//Se almacenan los resultados de la consulta.
        foreach ($results as $key) {//Se convierten los resultados en un arreglo.
            $resultArray[] = $key;
        }
        return $this->convertirUTF8($resultArray);// Se convierten los datos utilizando la función anterior de UTF-8.

    }

    // Se realiza la consulta de una tabla completa.
    public function nonQuery($sqlstr){
        $results = $this->conexion->query($sqlstr);
        return $this->conexion->affected_rows;
    }

    // Se realiza la consulta de la fila de una tabla por medio de un id.
    public function nonQueryId($sqlstr){
        $results = $this->conexion->query($sqlstr);
         $filas = $this->conexion->affected_rows;
         if($filas >= 1){
            return $this->conexion->insert_id;
         }else{
             return 0;
         }
    }
     
    //Realiza la encriptación de datos para la protección de los mismos.
    protected function encriptar($string){
        return md5($string);
    }
}
?>
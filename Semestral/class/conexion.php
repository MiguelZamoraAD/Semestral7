<?php    
class Conexion {
    private $conexion;
    private $debug = true; 

    public function __construct() {
        ##### Setting SQL vars #####
        $sql_host = "localhost";
        $sql_name = "company_info";
        $sql_user = "root";
        $sql_pass = "";

        $dsn = "mysql:host=$sql_host;dbname=$sql_name;charset=utf8";
        
        try {
            $this->conexion = new PDO($dsn, $sql_user, $sql_pass);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            /*linea para prueba de conexion a la base de datos
            if ($this->debug) {
                echo "Conexión exitosa a la base de datos<br>";
            }*/
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            exit;
        }
    }

    public function getConexion() {
        return $this->conexion;
    }
}
?>


<?php


class Usuario {
    private $con; // Conexión a la base de datos
    private $id; // ID 
    private $nombre_usuario; //nombre 
    private $email; //email
    private $password; //clave


    // Constructor para inicializar los campos
    public function __construct($id = null, $nombre_usuario = null, $email = null, $password = null) {
        include '../connect.php'; 
        $this->con = $con;
        $this->id = $id;
        $this->nombre_usuario = $nombre_usuario;
        $this->email = $email;
        $this->password = $password;
    }
    public function __destruct() {
        if ($this->con) {
            $this->con = null; // Cierra la conexión
        }
    }
 // Getters y setters para los campos privados
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = (int) $id;
    }

    public function getNombre_usuario() {
        return $this->nombre_usuario;
    }

    public function setNombre_usuario($nombre_usuario) {
        $this->nombre_usuario = $this->sanitizeInput($nombre_usuario);
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $this->sanitizeInput($email);
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $this->sanitizeInput($password);
    }


    // Método para insertar un domicilio
    public function insertar() {
        if ($this->nombre_usuario && $this->email && $this->password) {
            $fecha_actual = date("Y-m-d H:i:s");

            $sql = "INSERT INTO `usuarios` (Username, Email, Password, Fec_Alta) 
                VALUES ('$this->nombre_usuario', '$this->email', '$this->password', '$fecha_actual')";
            $result = mysqli_query($this->con, $sql);

            if ($result) {
                return true;
            } else {
                die(mysqli_error($this->con));
            }
        }
        return false; // No se insertó porque faltan datos
    }

    public function modificar() {
        if ($this->id && $this->password) {
            $sql = "UPDATE `usuarios` SET Password='$this->password' WHERE id_Usuario=$this->id";
            $result = mysqli_query($this->con, $sql);

            if ($result) {
                return true;
            } else {
                die(mysqli_error($this->con));
            }
        }
        return false; // No se modificó porque faltan datos
    }

    public function obtenerPorUsername($sUser) {
        $sql = "SELECT * FROM `usuarios` WHERE Username = ?";
        $stmt = $this->con->prepare($sql);        
        $stmt->bind_param("s", $sUser);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row; // Devuelve los datos 
        } 
        else {
            return null; // No se encontró el domicilio
        }
    }

    public function obtenerPorEmail($sUser) {
        $sql = "SELECT * FROM `usuarios` WHERE Email = ?";
        $stmt = $this->con->prepare($sql);        
        $stmt->bind_param("s", $sUser);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row; // Devuelve los datos 
        } 
        else {
            return null; // No se encontró el domicilio
        }
    }

    public function obtenerPorIdUsuario($iId) {
        $sql = "SELECT * FROM `usuarios` WHERE id_Usuario = ?";
        $stmt = $this->con->prepare($sql);        
        $stmt->bind_param("i", $iId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row; // Devuelve los datos 
        } 
        else {
            return null; // No se encontró el domicilio
        }
    }

	// Método privado para sanitizar entradas
    private function sanitizeInput($input) {
        return mysqli_real_escape_string($this->con, $input);
    }
}

 

?>
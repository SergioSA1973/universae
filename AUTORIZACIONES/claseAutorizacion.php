<?php

class Autorizacion {
    private $con; // Conexión a la base de datos
    private $id; // ID del domicilio
    private $username; // Descripción del usuario autorizado
    private $idUsuario; // ID del usuario asociado
    private $idDomicilio; // ID del domicilio a autorizar

    // Constructor para inicializar los campos
    public function __construct($id = null, $username = null,  $idDomicilio = null) {
        include '../connect.php';
        $this->con = $con;
        $this->id = $id;
        $this->username = $username;
        $this->idDomicilio = $idDomicilio;
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

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $this->sanitizeInput($username);
    }

    public function getIdDomicilio() {
        return $this->idDomicilio;
    }

    public function setIdDomicilio($idDomicilio) {
        $this->idDomicilio = (int) $idDomicilio;
    }

    // Método para insertar un domicilio
    public function insertar() {
        if ($this->idDomicilio && $this->username) {
            $sql = "INSERT INTO `autorizaciones` (id_Domicilio, Username) VALUES ('$this->idDomicilio', '$this->username')";
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
        if ($this->id && $this->idDomicilio && $this->username) {
            $sql = "UPDATE `autorizaciones` SET Username='$this->username', id_Domicilio='$this->idDomicilio' WHERE id_Autorizacion=$this->id";
            $result = mysqli_query($this->con, $sql);

            if ($result) {
                return true;
            } else {
                die(mysqli_error($this->con));
            }
        }
        return false; // No se modificó porque faltan datos
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM `autorizaciones` WHERE id_Autorizacion = ?";
        $stmt = $this->con->prepare($sql);        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row; // Devuelve los datos 
        } 
        else {
            return null; // No se encontró la autorización
        }
    }
    // Método para eliminar una autorización
    public function eliminar($id) {
        $id = (int) $id;
        $sql = "DELETE FROM `autorizaciones` WHERE id_Autorizacion=$id";
        $result = mysqli_query($this->con, $sql);

        if ($result) {
            return true;
        } else {
            die(mysqli_error($this->con));
        }

    }


    public function obtenerDomiciliosPorUsuario($idUsuario) {
        $sql = "SELECT id_Domicilio, Desc_Domicilio FROM `domicilios` WHERE Fec_Anulacion IS NULL AND id_Usuario = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $domicilios = [];
        
        while ($row = $result->fetch_assoc()) {
            $domicilios[] = $row; // Agrega cada fila al array
            }
        return $domicilios;    
    }


    public function obtenerAutorizacionesPorDomicilioUsuario($idDomicilio) {
        $sql = "SELECT id_Autorizacion, autorizaciones.id_Domicilio, Username, domicilios.Desc_Domicilio from `autorizaciones` inner join `domicilios` on autorizaciones.id_Domicilio = domicilios.id_Domicilio where autorizaciones.id_Domicilio = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $idDomicilio);
        $stmt->execute();
        $result = $stmt->get_result();
        $autorizaciones = [];
        
        while ($row = $result->fetch_assoc()) {
            $autorizaciones[] = $row; // Agrega cada fila al array
            }
        return $autorizaciones;    
    }

    public function exiteUsuarioAutorizado($username) {
        $sql = "SELECT COUNT(*) as count FROM `usuarios` WHERE Username = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['count'] > 0; // Devuelve true 
    }


    public function exiteUsuarioAutorizadoEnDomicilio($id, $idDomicilio, $username) {

        $id = (int) $id;
        $idDomicilio = (int) $idDomicilio;
        
        $sql = "SELECT * FROM `autorizaciones` WHERE Username = ? AND id_Domicilio = ?";
        $stmt = $this->con->prepare($sql);
        
        $stmt->bind_param("si", $username, $idDomicilio);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            if ($id != 0) {
                if ($row['id_Autorizacion'] != $id) {
                    return true;
                }
            } else {
                return true;
            }
        }
        
        return false; // No se encontró la autorización


    }


	// Método privado para sanitizar entradas
    private function sanitizeInput($input) {
        return mysqli_real_escape_string($this->con, $input);
    }
}

 

?>
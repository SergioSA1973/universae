<?php


class Domicilio {
    private $con; // Conexión a la base de datos
    private $id; // ID del domicilio
    private $descDomicilio; // Descripción del domicilio
    private $idUsuario; // ID del usuario asociado

    // Constructor para inicializar los campos
    public function __construct($id = null, $descDomicilio = null, $idUsuario = null) {
        include '../connect.php'; 
        $this->con = $con;
        $this->id = $id;
        $this->descDomicilio = $descDomicilio;
        $this->idUsuario = $idUsuario;
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

    public function getDescDomicilio() {
        return $this->descDomicilio;
    }

    public function setDescDomicilio($descDomicilio) {
        $this->descDomicilio = $this->sanitizeInput($descDomicilio);
    }

    public function getIdUsuario() {
        return $this->idUsuario;
    }

    public function setIdUsuario($idUsuario) {
        $this->idUsuario = (int) $idUsuario;
    }


    // Método para insertar un domicilio
    public function insertar() {
        if ($this->descDomicilio && $this->idUsuario) {
            $sql = "INSERT INTO `domicilios` (Desc_Domicilio, id_Usuario) VALUES ('$this->descDomicilio', $this->idUsuario)";
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
        if ($this->id && $this->descDomicilio) {
            $sql = "UPDATE `domicilios` SET Desc_Domicilio='$this->descDomicilio' WHERE id_Domicilio=$this->id";
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
        $sql = "SELECT * FROM `domicilios` WHERE id_Domicilio = ?";
        $stmt = $this->con->prepare($sql);        
        $stmt->bind_param("i", $id);
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
    // Método para eliminar un domicilio
    public function eliminar($id) {
        $id = (int) $id;
        $sql = "DELETE FROM `domicilios` WHERE id_Domicilio=$id";
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


    public function obtenerDomiciliosPorUsuarioMasAutorizado($idUsuario) {
        $sql = "SELECT id_Domicilio, Desc_Domicilio FROM `domicilios` WHERE Fec_Anulacion IS NULL AND id_Usuario = ?";
        $sql .= " union ";
        $sql .= " select aut.id_Domicilio, CONCAT(Desc_Domicilio, ' -- AUTORIZADO') AS Desc_Domicilio from autorizaciones aut"; 
        $sql .= " inner join usuarios usu on aut.Username = usu.Username";
        $sql .= " inner join domicilios dom on aut.id_Domicilio = dom.id_domicilio";
        $sql .= " where usu.Fec_Anulacion is null and dom.Fec_Anulacion is null";
        $sql .= " and usu.id_Usuario = ? ";
        $sql .= " group by  aut.id_Domicilio, Desc_Domicilio";

        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("ii", $idUsuario, $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $domicilios = [];
        
        while ($row = $result->fetch_assoc()) {
            $domicilios[] = $row; // Agrega cada fila al array
            }
        return $domicilios;    
    }

    public function tieneUbicacionesAsociadas($idDomicilio) {
        $sql = "SELECT COUNT(*) as count FROM `ubicaciones` WHERE id_Domicilio = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $idDomicilio);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['count'] > 0; // Devuelve true si tiene ubicaciones asociadas
    }


	// Método privado para sanitizar entradas
    private function sanitizeInput($input) {
        return mysqli_real_escape_string($this->con, $input);
    }
}

 

?>
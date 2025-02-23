<?php

class Ubicacion {
    private $con; // Conexión a la base de datos
    private $id; // ID de la ubicacion
    private $idDomicilio; // ID del domicilio 
    private $descUbicacion; // Descripción de la ubicacion

    // Constructor para inicializar los campos
    public function __construct($id = null,  $idDomicilio = null, $descUbicacion = null,) {
        include '../connect.php';
        $this->con = $con;
        $this->id = $id;
        $this->idDomicilio = $idDomicilio;
        $this->descUbicacion = $descUbicacion;
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

    public function getDescUbicacion() {
        return $this->descUbicacion;
    }

    public function setDescUbicacion($descUbicacion) {
        $this->descUbicacion = $this->sanitizeInput($descUbicacion);
    }

    public function getIdDomicilio() {
        return $this->idDomicilio;
    }

    public function setIdDomicilio($idDomicilio) {
        $this->idDomicilio = (int) $idDomicilio;
    }

    // Método para insertar una ubicacion
    public function insertar() {
        if ($this->idDomicilio && $this->descUbicacion) {
            $sql = "INSERT INTO `Ubicaciones` (id_Domicilio, Desc_Ubicacion) VALUES ('$this->idDomicilio', '$this->descUbicacion')";
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
        if ($this->id && $this->idDomicilio && $this->descUbicacion) {
            $sql = "UPDATE `Ubicaciones` SET Desc_Ubicacion='$this->descUbicacion', id_Domicilio='$this->idDomicilio' WHERE id_Ubicacion=$this->id";
            $result = mysqli_query($this->con, $sql);

            if ($result) {
                return true;
            } else {
                die(mysqli_error($this->con));
            }
        }
        return false; // No se modificó porque faltan datos
    }

    public function obtenerUbicacionesPorIdDomicilio($id) {
        $sql = "SELECT id_Ubicacion, Desc_Ubicacion FROM `ubicaciones` WHERE Fec_Anulacion IS NULL AND id_Domicilio = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ubicaciones = [];
        
        while ($row = $result->fetch_assoc()) {
            $ubicaciones[] = $row; // Agrega cada fila al array
            }
        return $ubicaciones;    

    }

    public function obtenerUbicacionesPorIdLista($id) {
        $sql = "SELECT * FROM `Ubicaciones` WHERE Fec_Anulacion IS NULL and id_Ubicacion = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ubicaciones = [];
        
        while ($row = $result->fetch_assoc()) {
            $ubicaciones[] = $row; // Agrega cada fila al array
            }
        return $ubicaciones;    

    }


    public function obtenerPorId($id) {
        $sql = "SELECT * FROM `Ubicaciones` WHERE Fec_Anulacion IS NULL and id_Ubicacion = ?";
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

    // Método para eliminar una ubicacion
    public function eliminar($id) {
        $id = (int) $id;
        $sql = "UPDATE Ubicaciones SET Fec_Anulacion = NOW() WHERE id_Ubicacion = $id";
        $result = mysqli_query($this->con, $sql);

        if ($result) {
            return true;
        } else {
            die(mysqli_error($this->con));
        }
    }
 


public function tieneContenedoresPorUbicacion($id) {
        $sql = "SELECT COUNT(*) as count FROM `Contenedores` WHERE id_Ubicacion = ? AND Fec_Anulacion IS NULL";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['count'] > 0; // Devuelve true si tiene ubicaciones asociadas
    }

public function cantidadArticulosPorUbicacion($id) {
        $sql = "SELECT COUNT(*) as count FROM `Articulos` WHERE id_Ubicacion = ? AND Fec_Anulacion IS NULL";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $id);
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
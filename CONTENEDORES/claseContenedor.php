<?php


class Contenedor {
    private $con; // Conexión a la base de datos
    private $id; // ID
    private $id_Ubicacion; // ID_Ubicacion
    private $id_Padre; // ID_Padre
    private $descContenedor; // Descripción 

    // Constructor para inicializar los campos
    public function __construct($id = null, $id_Ubicacion = null,  $id_Padre = null,   $descContenedor = null) {
        include '../connect.php'; 

        $this->con = $con;
        $this->id = $id;
        $this->id_Ubicacion = $id_Ubicacion;
        $this->id_Padre = $id_Padre;
        $this->descContenedor = $descContenedor;
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

    public function getId_Ubicacion() {
        return $this->id_Ubicacion;
    }

    public function setId_Ubicacion($id_Ubicacion) {
        $this->id_Ubicacion = (int) $id_Ubicacion;
    }

    public function getId_Padre() {
        return $this->id_Padre;
    }

    public function setId_Padre($id_Padre) {
        $this->id_Padre = (int) $id_Padre;
    }

    public function getDescContenedor() {
        return $this->descContenedor;
    }

    public function setDescContenedor($descContenedor) {
        $this->descContenedor = $this->sanitizeInput($descContenedor);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM `Contenedores` WHERE id_Contenedor = ?";
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

    public function tieneContenedoresHijos($id) {
        $sql = "SELECT COUNT(*) as count FROM `Contenedores` WHERE id_Padre = ? AND Fec_Anulacion IS NULL";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['count'] > 0; // Devuelve true 
    }


    public function cantidadArticulosPorContenedor($id) {
        $sql = "SELECT COUNT(*) as count FROM `Articulos` WHERE id_Contenedor = ? AND Fec_Anulacion IS NULL";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['count'] > 0; // Devuelve true 
    }


    public function ObtenerContenedoresFormulario($id_domicilio) 
    {
        $query = 'SELECT * FROM Contenedores WHERE Fec_Anulacion IS NULL';
        $params = [];
        $types = ''; // Definir tipos para bind_param
        $contenedoresHijos = [];
        $contenedoresHijos = $this->obtenerContenedoresPorDomicilio($id_domicilio);
            
            //print_r($contenedoresHijos);

        if (!empty($contenedoresHijos)) {
            $ids = array_column($contenedoresHijos, 'id_Contenedor');
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $query .= " AND id_Contenedor IN ($placeholders)";
            //$params = array_merge($params, $ids);
            $params = $ids;
            $types = str_repeat('i', count($ids)); // "i" para enteros
            //echo $ids;
            //exit();

        } else {
            $query .= " AND id_Contenedor IN (NULL)";
        }
        
        $stmt = $this->con->prepare($query); 
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        // Devolver los resultados
        return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : null;        
    }


    public function obtenerContenedoresPorIdLista($id) {
        $sql = "SELECT * FROM Contenedores WHERE Fec_Anulacion IS NULL AND id_Contenedor = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $contenedor = [];
        
        while ($row = $result->fetch_assoc()) {
            $contenedor[] = $row; // Agrega cada fila al array
            }
        return $contenedor;    
    }

public function obtenerContenedoresPorIdUbicacionSinPadre($id) {
        $sql = "SELECT * FROM `Contenedores` WHERE Fec_Anulacion IS NULL  and id_padre is null and id_Ubicacion = ?";
        
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $contenedor = [];
        
        while ($row = $result->fetch_assoc()) {
            $contenedor[] = $row; // Agrega cada fila al array
            }
        return $contenedor;    

    }


public function FiltrarContenedores($selected_domicilio, $selected_ubicacion) {
        $query = "SELECT c.*, u.id_Domicilio, u.Desc_Ubicacion, cp.Desc_Contenedor AS Contenedor_Padre 
          FROM Contenedores c 
          LEFT JOIN Ubicaciones u ON c.id_Ubicacion = u.id_Ubicacion 
          LEFT JOIN Contenedores cp ON c.id_Padre = cp.id_Contenedor 
          WHERE c.Fec_Anulacion IS NULL";

        $params = [];
        $types = ''; // Definir tipos para bind_param
        $contenedores = [];        
        
        if ($selected_ubicacion) {
            $contenedores = $this->obtenerContenedoresPorUbicacion($selected_ubicacion);
        }
        else
        {
            if ($selected_domicilio) {
                $contenedores = $this->obtenerContenedoresPorDomicilio($selected_domicilio);
            }    
        }
        
            
        if (!empty($contenedores)) {
            $ids = array_column($contenedores, 'id_Contenedor');
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $query .= " AND c.id_Contenedor IN ($placeholders)";
            //$params = array_merge($params, $ids);
            $params = $ids;
            $types = str_repeat('i', count($ids)); // "i" para enteros
            //echo $ids;
            //exit();

        } else {
            $query .= " AND u.id_Domicilio IN (NULL)";
        }
        

        $stmt = $this->con->prepare($query); 
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        // Devolver los resultados
        return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : null;
}



public function obtenerContenedoresPorUbicacion($id_ubicacion) {
        $sql = "SELECT * FROM contenedores WHERE id_Ubicacion = ? AND Fec_Anulacion IS NULL";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $id_ubicacion);
        $stmt->execute();
        $result = $stmt->get_result();
        $contenedores = [];
        
        while ($row = $result->fetch_assoc()) {
            $contenedores[] = $row; // Agrega cada fila al array

            //echo "antes de llamar";
            // Obtener todos los contenedores hijos de forma recursiva
            $hijos = $this->obtenerContenedoresRecursivos( $row['id_Contenedor']);
            $contenedores = array_merge($contenedores, $hijos);


            }
        return $contenedores;    
}


function obtenerContenedoresPorDomicilio($id_domicilio = null) {
    $contenedores = [];

    if ($id_domicilio !== null) {
        // Encuentra ubicaciones asociadas al domicilio especificado
        $query = "SELECT * FROM ubicaciones WHERE id_Domicilio = ? AND Fec_Anulacion IS NULL";
        if ($stmt = $this->con->prepare($query)) {
            $stmt->bind_param("i", $id_domicilio);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($ubicacion = $result->fetch_assoc()) {
                // Obtener todos los contenedores asociados a la ubicación actual de forma recursiva
                $contenedoresEnUbicacion = $this->obtenerContenedoresPorUbicacion( $ubicacion['id_Ubicacion']);
                $contenedores = array_merge($contenedores, $contenedoresEnUbicacion);
            }
        }
    }

    return $contenedores;
}



    function obtenerContenedoresRecursivos($id_contenedor) {
        $contenedores = [];
        
        // Encuentra el contenedor especificado
        $query = "SELECT * FROM contenedores WHERE id_Contenedor = ? AND Fec_Anulacion IS NULL";
        if ($stmt = $this->con->prepare($query)) {
            $stmt->bind_param("i", $id_contenedor);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
             //   $contenedores[] = $row;
                
                // Busca hijos del contenedor especificado
                $hijos = $this->obtenerHijos($row['id_Contenedor']);
                $contenedores = array_merge($contenedores, $hijos);
            }
            

        }
        
        return $contenedores;
    }

    function obtenerHijos( $id_padre) {
    $hijos = [];
    
    $query = "SELECT * FROM contenedores WHERE id_Padre = ? AND Fec_Anulacion IS NULL";
    if ($stmt = $this->con->prepare($query)) {
        $stmt->bind_param("i", $id_padre);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $hijos[] = $row;
            
            // Buscar hijos de los hijos (recursivamente)
            $nietos = $this->obtenerHijos( $row['id_Contenedor']);
            $hijos = array_merge($hijos, $nietos);
        }
        
   
    }
    
    return $hijos;
}


    public function insertar() {
            $id_Ubicacion = isset($this->id_Ubicacion) && $this->id_Ubicacion !== '' ? $this->id_Ubicacion : "NULL";
            $id_Padre = isset($this->id_Padre) && $this->id_Padre !== '' ? $this->id_Padre : "NULL";

            $sql = "INSERT INTO `Contenedores` (Desc_Contenedor, id_Ubicacion, id_Padre) VALUES ('$this->descContenedor', $id_Ubicacion, $id_Padre)";

            //echo $sql;
            //exit();
            $result = mysqli_query($this->con, $sql);
            if ($result) {
                return true;
            } else {
                die(mysqli_error($this->con));
                return false;
            }
    }

    public function modificar() {
            $id_Ubicacion = isset($this->id_Ubicacion) && $this->id_Ubicacion !== '' ? $this->id_Ubicacion : "NULL";
            $id_Padre = isset($this->id_Padre) && $this->id_Padre !== '' ? $this->id_Padre : "NULL";
            
            $sql = "UPDATE `Contenedores` SET Desc_Contenedor='$this->descContenedor', 
                    id_Ubicacion=$id_Ubicacion,
                    id_Padre=$id_Padre
                    WHERE id_Contenedor=$this->id";

            $result = mysqli_query($this->con, $sql);

            if ($result) {
                return true;
            } else {
                die(mysqli_error($this->con));
                return false;
            }
    }


    public function eliminar($id) {
        $id = (int) $id;
        $sql = "UPDATE Contenedores SET Fec_Anulacion = NOW() WHERE id_Contenedor = $id";
        $result = mysqli_query($this->con, $sql);

        if ($result) {
            return true;
        } else {
            die(mysqli_error($this->con));
        }
    }




    // Método privado para sanitizar entradas
    private function sanitizeInput($input) {
        return mysqli_real_escape_string($this->con, $input);
    }



}


?>
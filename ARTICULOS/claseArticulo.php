<?php


class Articulo {
    private $con; // Conexión a la base de datos
    private $id; // ID
    private $id_Ubicacion; // ID_Ubicacion
    private $id_Contenedor; // ID_Contenedor
    private $descArticulo; // Descripción 
    private $id_Usuario; // ID_Usuario


    // Constructor para inicializar los campos
    public function __construct($id = null, $id_Ubicacion = null,  $id_Contenedor = null,   
            $descArticulo = null, $id_Usuario = null, $rutaDestino = null) {
        include '../connect.php'; 

        $this->con = $con;
        $this->id = $id;
        $this->id_Ubicacion = $id_Ubicacion;
        $this->id_Contenedor = $id_Contenedor;
        $this->descArticulo = $descArticulo;
        $this->id_Usuario = $id_Usuario;
        $this->rutaDestino = $rutaDestino;
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

    public function getId_Contenedor() {
        return $this->id_Contenedor;
    }

    public function setId_Contenedor($id_Contenedor) {
        $this->id_Contenedor = (int) $id_Contenedor;
    }

    public function getDescArticulo() {
        return $this->descArticulo;
    }

    public function setDescArticulo($descArticulo) {
        $this->descArticulo = $this->sanitizeInput($descArticulo);
    }

    public function getId_Usuario() {
        return $this->id_Usuario;
    }

    public function setId_Usuario($id_Usuario) {
        $this->id_Usuario = (int) $id_Usuario;
    }


    public function getRutaDestino() {
        return $this->rutaDestino;
    }

    public function setRutaDestino($rutaDestino) {
        $this->rutaDestino = $this->sanitizeInput($rutaDestino);
    }



    // Método privado para sanitizar entradas
    private function sanitizeInput($input) {
        return mysqli_real_escape_string($this->con, $input);
    }

    //---------------------------------------------------
    // Método para insertar un articulo
    public function insertar() {
        
            $fecha_actual = date("Y-m-d H:i:s");
            $id_Ubicacion = isset($this->id_Ubicacion) && $this->id_Ubicacion !== '' ? $this->id_Ubicacion : "NULL";
            $id_Contenedor = isset($this->id_Contenedor) && $this->id_Contenedor !== '' ? $this->id_Contenedor : "NULL";
            $rutaDestino = isset($this->rutaDestino) && $this->rutaDestino !== '' ? $this->rutaDestino : NULL;


            $sql = "INSERT INTO `Articulos` (id_Contenedor, id_Ubicacion, Desc_Articulo, id_Usuario, Fec_Modificacion, img_Foto) 
                VALUES ($id_Contenedor, $id_Ubicacion, '$this->descArticulo', 
                        $this->id_Usuario, '$fecha_actual','$rutaDestino')";

            $result = mysqli_query($this->con, $sql);

            if ($result) {
                return true;
            } else {
                die(mysqli_error($this->con));
                return false;
            }
    }

    public function modificar() {

            $fecha_actual = date("Y-m-d H:i:s");
            $id_Ubicacion = isset($this->id_Ubicacion) && $this->id_Ubicacion !== '' ? $this->id_Ubicacion : "NULL";
            $id_Contenedor = isset($this->id_Contenedor) && $this->id_Contenedor !== '' ? $this->id_Contenedor : "NULL";
            $rutaDestino = isset($this->rutaDestino) && $this->rutaDestino !== '' ? $this->rutaDestino : NULL;
        
            $sql = "UPDATE `Articulos` SET Desc_Articulo='$this->descArticulo', 
                    id_Ubicacion=$id_Ubicacion,
                    id_Contenedor=$id_Contenedor,
                    Fec_Modificacion = '$fecha_actual',
                    img_Foto='$rutaDestino',
                    id_Usuario=$this->id_Usuario
                    WHERE id_Articulo=$this->id";
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
        $sql = "UPDATE Articulos SET Fec_Anulacion = NOW() WHERE id_Articulo = $id";
        $result = mysqli_query($this->con, $sql);

        if ($result) {
            return true;
        } else {
            die(mysqli_error($this->con));
            return false;
        }
    }

    public function moverArticulo($tipoObjeto, $idObjeto, $tipoDestino, $idDestino) {
        
        $idObjeto = (int) $idObjeto;
        $idDestino = (int) $idDestino;
        
      //  echo "estoy dentro de mover";
        if ($tipoObjeto === 'articulo') {
                // Actualizar la ubicación o contenedor del artículo
                if ($tipoDestino === 'ubicacion') {
                    $sql = "UPDATE Articulos SET id_Ubicacion = $idDestino, id_Contenedor = NULL WHERE id_Articulo = $idObjeto";
                } else {
                    $sql = "UPDATE Articulos SET id_Contenedor = $idDestino, id_Ubicacion = NULL WHERE id_Articulo = $idObjeto";
                }
        } elseif ($tipoObjeto === 'contenedor') {
                // Actualizar la ubicación o contenedor del contenedor
                if ($tipoDestino === 'ubicacion') {
                    $sql = "UPDATE Contenedores SET id_Ubicacion = $idDestino, id_Padre = NULL WHERE id_Contenedor = $idObjeto";
                } else {
                    $sql = "UPDATE Contenedores SET id_Padre = $idDestino, id_Ubicacion = NULL WHERE id_Contenedor = $idObjeto";
                }
        }

        $result = mysqli_query($this->con, $sql);

        if ($result) {
            return true;
        } else {
            die(mysqli_error($this->con));
            return false;
        }
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM `Articulos` WHERE Fec_Anulacion IS NULL and id_Articulo = ?";
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

    //---------------------------------------------------


    public function FiltrarComboArticulos($selected_contenedor) {


        $query = "SELECT * FROM Articulos WHERE Fec_Anulacion IS NULL";
        $params = [];
        $types = ''; // Definir tipos para bind_param
        $contenedoresHijos = [];
        
        $contenedoresHijos = $this->obtenerContenedoresRecursivos2($selected_contenedor);

        // Añadir el valor del parámetro a la variable contenedoresHijos
        $contenedoresHijos[] = ['id_Contenedor' => $selected_contenedor];
        
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


    function obtenerContenedoresRecursivos2($id_contenedor) {
       // echo "estoy en contenedores recursivos 2";
       // echo $id_contenedor;
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
                $hijos = $this->obtenerHijos2($row['id_Contenedor']);
                $contenedores = array_merge($contenedores, $hijos);
            }   
            $stmt->close();
        }
    

        //Añadido para que retorne también el valor del combo
        $contenedores[] = ['id_Contenedor' => $id_contenedor];
        //print_r($contenedores);

        return $contenedores;
    }

    function obtenerHijos2($id_padre) {
        $hijos = [];
        
        $query = "SELECT * FROM contenedores WHERE id_Padre = ? AND Fec_Anulacion IS NULL";
        if ($stmt = $this->con->prepare($query)) {
            $stmt->bind_param("i", $id_padre);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $hijos[] = $row;
                
                // Buscar hijos de los hijos (recursivamente)
                $nietos = $this->obtenerHijos2( $row['id_Contenedor']);
                $hijos = array_merge($hijos, $nietos);
            }
            $stmt->close();
        }
        return $hijos;
    }

public function FiltrarComboArticulosDinamico($selected_domicilio, $selected_ubicacion, $selected_contenedor) 
    {
        $types = ''; // Definir tipos para bind_param
        if ($selected_domicilio && !$selected_ubicacion && !$selected_contenedor) {
            // Si el único combo que está completado es el de domicilio, cargamos el combo de artículos con los del propio domicilio

            //echo "paso 2: ";
            $contenedoresHijos = $this->obtenerContenedoresPorDomicilio($selected_domicilio);

            // Obtener las ubicaciones asociadas al domicilio
            $ubidom = $this->obtenerUnicamenteUbicacionesPorDomicilio($selected_domicilio);

            // Construimos la consulta para seleccionar artículos
            $queryArt = 'SELECT * FROM Articulos WHERE Fec_Anulacion IS NULL';
            $paramsArt = [];
       
            if (!empty($contenedoresHijos)) {
               // echo "paso contenedor hijos";
                // Obtener los ids de los contenedores hijos
                $ids = array_column($contenedoresHijos, 'id_Contenedor');
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $queryArt .= " AND id_Contenedor IN ($placeholders)";
                $paramsArt = array_merge($paramsArt, $ids);
                //$paramsArt = $ids;
                $types .= str_repeat('i', count($ids));

            } else {
                $queryArt .= " AND id_Contenedor IN (NULL)"; // No devuelve nada si no hay contenedores
            }

            // Agregar UNION con la tabla de ubicaciones
            if (!empty($ubidom)) {
               // echo "paso ubidom";

                // Obtener los ids de las ubicaciones
                $ubicacionIds = array_column($ubidom, 'id_Ubicacion');
                $placeholdersUbicaciones = implode(',', array_fill(0, count($ubicacionIds), '?'));
                
                // Concatenamos la consulta de ubicaciones con un UNION
                $queryArt .= " UNION SELECT * FROM Articulos WHERE id_Ubicacion IN ($placeholdersUbicaciones) AND Fec_Anulacion IS NULL";
                
                // Agregar los parámetros de ubicaciones al arreglo de parámetros
                $paramsArt = array_merge($paramsArt, $ubicacionIds);
                //$paramsArt = $ubicacionIds;

                $types .= str_repeat('i', count($ubicacionIds));
           }

            $stmt = $this->con->prepare($queryArt); 
            if (!empty($paramsArt)) {
                $stmt->bind_param($types, ...$paramsArt);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : null;    
        }


        if ($selected_domicilio && $selected_ubicacion && !$selected_contenedor) {
            //echo "paso 3: ";
            $contenedoresHijos = $this->obtenerContenedoresPorUbicacion( $selected_ubicacion);
            $queryArt = 'SELECT * FROM Articulos WHERE Fec_Anulacion IS NULL';
            $paramsArt = [];
        
            if (!empty($contenedoresHijos)) {
                $ids = array_column($contenedoresHijos, 'id_Contenedor');
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $queryArt .= " AND id_Contenedor IN ($placeholders)";
                $paramsArt = array_merge($paramsArt, $ids);
                //$paramsArt = $ids;
                $types .= str_repeat('i', count($ids)); // "i" para enteros

            } else {
                $queryArt .= " AND id_Contenedor IN (NULL)"; // No devuelve nada si no hay contenedores
            }


            // Agregar filtro por ubicaciones si hay una ubicación seleccionada
            if (!empty($selected_ubicacion)) {
                //---------------------------------------------------
                $selected_ubicacion = [['id_Ubicacion' => $selected_ubicacion]];
               // print_r($selected_ubicacion);
                $ids = array_column($selected_ubicacion, 'id_Ubicacion');
               // print_r($ids);
                
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $queryArt .= " OR id_Ubicacion IN ($placeholders)";
                $paramsArt = array_merge($paramsArt, $ids);
                $types .= str_repeat('i', count($ids)); // "i" para enteros
                //---------------------------------------------------
            }

            $stmt = $this->con->prepare($queryArt); 
            if (!empty($paramsArt)) {
                $stmt->bind_param($types, ...$paramsArt);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : null; 
        }
    }

    function obtenerContenedoresPorUbicacion( $id_ubicacion) {
        $contenedores = [];

        // Encuentra contenedores en la ubicación especificada
        $query = "SELECT * FROM contenedores WHERE id_Ubicacion = ? AND Fec_Anulacion IS NULL";
         if ($stmt = $this->con->prepare($query)) {
            $stmt->bind_param("i", $id_ubicacion);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                // Agregar el contenedor actual al array de contenedores
                $contenedores[] = $row;
               // echo "en la fila: ".$row;
                // Obtener todos los contenedores hijos de forma recursiva
                $hijos = $this->obtenerContenedoresRecursivos($row['id_Contenedor']);
                $contenedores = array_merge($contenedores, $hijos);
            }
            $stmt->close();
        }
        return $contenedores;
    }

    // Función para obtener contenedores de forma recursiva
    function obtenerContenedoresRecursivos($id_contenedor) {
        $contenedores = [];

        // Encuentra el contenedor especificado
        $query = "SELECT * FROM contenedores WHERE id_Contenedor = ? AND Fec_Anulacion IS NULL";
        if ($stmt = $this->con->prepare($query)) {
            $stmt->bind_param("i", $id_contenedor);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                // Busca hijos del contenedor especificado
                $hijos = $this->obtenerHijos($row['id_Contenedor']);
                $contenedores = array_merge($contenedores, $hijos);
            }
            $stmt->close();
        }
        return $contenedores;
    }


    function obtenerHijos($id_padre) {
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
            $stmt->close();
        }
        return $hijos;
    }

    function obtenerUnicamenteUbicacionesPorDomicilio( $id_domicilio = null) {
        $contenedores = [];

        if ($id_domicilio !== null) {
            // Encuentra ubicaciones asociadas al domicilio especificado
            $query = "SELECT * FROM ubicaciones WHERE id_Domicilio = ? AND Fec_Anulacion IS NULL";
            if ($stmt = $this->con->prepare($query)) {
                $stmt->bind_param("i", $id_domicilio);
                $stmt->execute();
                $result = $stmt->get_result();

                // Recorrer el resultado y almacenar las filas en $contenedores
                while ($row = $result->fetch_assoc()) {
                    $contenedores[] = $row;
                }
                $stmt->close();
            }
        }
        return $contenedores;
    }

    // Función para obtener unicamente las ubicaciones de un articulo
    function obtenerUnicamenteUbicacionesPorArticulo($id_articulo = null) {
        $contenedores = [];

        if ($id_articulo !== null) {
            // Encuentra ubicaciones asociadas a la ubicacion
            $query = "SELECT id_Articulo, Desc_Articulo, IFNULL(id_ubicacion, 0) AS id_Ubicacion 
                      FROM articulos 
                      WHERE id_Articulo = ? AND Fec_Anulacion IS NULL";

            if ($stmt = $this->con->prepare($query)) {
                $stmt->bind_param("i", $id_articulo);
                $stmt->execute();
                $result = $stmt->get_result();

                // Recorrer el resultado y almacenar las filas en $contenedores
                while ($row = $result->fetch_assoc()) {
                    $contenedores[] = $row;
                }
                $stmt->close();
            }
        }
        return $contenedores;
    }

    // Función para obtener contenedores con filtros opcionales
    function obtenerContenedoresPorDomicilio( $id_domicilio = null) {
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
                $stmt->close();
            }
        }
        return $contenedores;
    }

    //---------------------------------------------------
    function obtenerAntecesoresContenedor($idArticulo) 
    {
        // Primero, obtenemos el id_Contenedor asociado al artículo
        $articulo = [];
        $types = ''; // Definir tipos para bind_param
        
        // Encuentra ubicaciones asociadas al domicilio especificado
        $query = "SELECT id_Contenedor FROM Articulos WHERE id_Articulo = ?";
        if ($stmt = $this->con->prepare($query)) 
        {
            $stmt->bind_param("i", $idArticulo);
            $stmt->execute();
            $result = $stmt->get_result();
            $articulo = $result->fetch_assoc();

            if (!$articulo) {
                return []; // Si el artículo no existe, devolvemos un array vacío
            }
            else
            {
                // Ahora obtenemos la cadena de antecesores recursivamente
                $idContenedor = $articulo['id_Contenedor'];
                $antecesores = [];

                while ($idContenedor) 
                {
                    // Obtenemos la información del contenedor actual
                    $query = "SELECT id_Contenedor, id_Padre, Desc_Contenedor FROM Contenedores WHERE id_Contenedor = ?";
                    if ($stmt = $this->con->prepare($query))
                    {
                        $stmt->bind_param("i", $idContenedor);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $contenedor = $result->fetch_assoc();

                        if (!$contenedor) {
                            break; // Si no encontramos el contenedor, rompemos el ciclo
                        }
                        // Añadimos el contenedor a la lista de antecesores
                        $antecesores[] = $contenedor;
                        // Continuamos con el contenedor padre
                        $idContenedor = $contenedor['id_Padre'];
                    }                        
                }
                return $antecesores;
            } 
        }
    }
   

    public function FiltrarConsultaGeneralArticulosListado($selected_domicilio, $selected_ubicacion, $selected_contenedor, $selected_articulo) 
    {

        $generales = []; // Inicializamos $generales como un array vacío
        $queryArt ='sELECT c.id_Contenedor, a.id_Articulo, d.Desc_Domicilio, u.Desc_Ubicacion, c.desc_Contenedor,
                         cp.Desc_Contenedor AS Contenedor_Padre, a.Desc_Articulo,
                         d.id_Domicilio, u.id_Ubicacion, usu.Username, a.Fec_Modificacion, a.img_Foto                
                         FROM contenedores c
                         LEFT JOIN articulos a ON a.id_contenedor = c.id_contenedor 
                         LEFT JOIN Ubicaciones u ON c.id_Ubicacion = u.id_Ubicacion 
                         LEFT JOIN Contenedores cp ON c.id_Padre = cp.id_Contenedor 
                         LEFT JOIN domicilios d ON u.id_Domicilio = d.id_Domicilio
                 LEFT JOIN usuarios usu ON a.id_Usuario = usu.id_Usuario
                         WHERE a.Fec_Anulacion IS NULL AND a.id_ubicacion IS NULL';


        $ubiParam = [];
        $paramsArt = [];
        $types = ''; // Definir tipos para bind_param

        //Tenemos el combo de domicilio y podemos tener el de articulo
        if ($selected_domicilio && !$selected_ubicacion && !$selected_contenedor ) 
        {

            if (!$selected_articulo)
            {
           // echo "articulos";

                // Si el único combo que está completado es el de domicilio, cargamos el combo de artículos con los del propio domicilio
                $contenedoresParam = $this->obtenerContenedoresPorDomicilio($selected_domicilio);

                // Obtener las ubicaciones asociadas al domicilio
                $ubiParam = $this->obtenerUnicamenteUbicacionesPorDomicilio($selected_domicilio);
            }
            else
            {
            $contenedoresParam = $this->obtenerAntecesoresContenedor($selected_articulo);
            $ubiParam = $this->obtenerUnicamenteUbicacionesPorArticulo($selected_articulo);
            }
        }

        //Tenemos el combo de domicilio y el de ubicacion, además podemos tener el de articulo
        if ($selected_domicilio && $selected_ubicacion && !$selected_contenedor ) 
        {
            //echo "paso3";
            if (!$selected_articulo) 
                {
                // Si el único combo que está completado es el de domicilio y ubicacion, cargamos datos de artículos con los del propio domicilio y ubicacion
                $contenedoresParam = $this->obtenerContenedoresPorUbicacion($selected_ubicacion);

                // Obtener las ubicaciones asociadas al domicilio
                $ubiParam = $this->obtenerUnicamenteUbicacionesPorDomicilio($selected_domicilio);
            }
            else
            {
                echo "paso4";
                $contenedoresParam = $this->obtenerAntecesoresContenedor($selected_articulo);
                $ubiParam = $this->obtenerUnicamenteUbicacionesPorArticulo( $selected_articulo);
            }
        }

        //Tenemos el combo de domicilio, ubicacion y contenedor, además podemos tener el de articulo
        if ($selected_domicilio && $selected_ubicacion && $selected_contenedor ) 
        {
            if (!$selected_articulo) 
                {
               // echo "--1";
                // Si el único combo que está completado es el de domicilio y ubicacion, cargamos datos de artículos con los del propio domicilio y ubicacion
                $contenedoresParam = $this->obtenerContenedoresRecursivos2($selected_contenedor);   
                //print_r($contenedoresParam); 
            }
            else
            {
               // echo "--2";
                $contenedoresParam = $this->obtenerAntecesoresContenedor( $selected_articulo);

                //print_r($contenedoresParam);
            }
        }


        if ($selected_domicilio || $selected_ubicacion || $selected_contenedor || $selected_articulo) 
        {
           // echo "estoy tambien con el combo de articulo seleccionado";
            if (!empty($contenedoresParam)) {
             //       echo "estoy contenedor paso 1";
                    $ids = array_column($contenedoresParam, 'id_Contenedor');
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $queryArt .= " AND c.id_Contenedor IN ($placeholders) ";
                    $paramsArt = array_merge($paramsArt, $ids);
                    $types .= str_repeat('i', count($ids)); // "i" para enteros
                } else {
                    $queryArt .= " AND c.id_Contenedor =0"; // Ajuste para evitar errores en SQL
                }
   
                if (!empty($selected_articulo)) {
              //      echo "estoy articulo paso 2";
                    // Añadir la lógica para selected_articulo en la consulta
                    $queryArt .= " AND a.id_Articulo = ?";
                    $paramsArt[] = $selected_articulo;
                    $types .= 'i'; // "i" para enteros
                }


            // Agregar UNION para ubicaciones si hay
                if (!empty($ubiParam)) {
               // echo "paso por la ubicacion del union";
                    $ubicacionIds = array_column($ubiParam, 'id_Ubicacion');
                    $placeholdersUbicaciones = implode(',', array_fill(0, count($ubicacionIds), '?'));

                    $queryArt .= " UNION 
                              SELECT c.id_Contenedor, a.id_Articulo, d.Desc_Domicilio, u.Desc_Ubicacion, c.desc_Contenedor, 
                              cp.Desc_Contenedor AS Contenedor_Padre, a.Desc_Articulo, 
                              d.id_Domicilio, u.id_Ubicacion , usu.Username, a.Fec_Modificacion, a.img_Foto
                              FROM articulos a 
                              LEFT JOIN Contenedores c ON a.id_contenedor = c.id_contenedor 
                              LEFT JOIN Ubicaciones u ON a.id_Ubicacion = u.id_Ubicacion 
                              LEFT JOIN Contenedores cp ON c.id_Padre = cp.id_Contenedor 
                              LEFT JOIN domicilios d ON u.id_Domicilio = d.id_Domicilio
                      LEFT JOIN usuarios usu ON a.id_Usuario = usu.id_Usuario
                              WHERE u.id_Ubicacion IN ($placeholdersUbicaciones) 
                              AND a.Fec_Anulacion IS NULL AND a.id_ubicacion IS NOT NULL";

                    // Añadimos los IDs de ubicaciones a los parámetros
                    $paramsArt = array_merge($paramsArt, $ubicacionIds);
                    $types .= str_repeat('i', count($ubicacionIds)); // "i" para enteros
                }

            $queryArt .= " ORDER BY id_contenedor ASC"; // Cambia el campo por el que quieras ordenar
        }
        //echo $queryArt;
        
        $stmt = $this->con->prepare($queryArt); 
        if (!empty($paramsArt)) {
            $stmt->bind_param($types, ...$paramsArt);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        // Devolver los resultados
        return $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : null;

    }

    //---------------------------------------------------

    // Función para obtener unicamente los articulos asociados a una ubicación
    function obtenerUnicamenteArticulosPorUbicacion($id_ubicacion = null) {
        $contenedores = [];

        if ($id_ubicacion !== null) {
            // Encuentra articulos asociados a una ubicacion
        $query = "select id_Articulo, Desc_Articulo, img_Foto from articulos where Fec_Anulacion is null and id_ubicacion = ?";
            if ($stmt = $this->con->prepare($query)) {
                $stmt->bind_param("i", $id_ubicacion);
                $stmt->execute();
                $result = $stmt->get_result();
                // Recorrer el resultado y almacenar las filas en $contenedores
                while ($row = $result->fetch_assoc()) {
                    $contenedores[] = $row;
                }            
                $stmt->close();
            }
        }
        return $contenedores;
    }

    // Función para obtener unicamente los articulos asociados a un contenedor
    function obtenerUnicamenteArticulosPorContenedor( $id_contenedor = null) {
        $contenedores = [];

        if ($id_contenedor !== null) {
            // Encuentra articulos asociados a una ubicacion
        $query = "select id_Articulo, Desc_Articulo, img_Foto from articulos where Fec_Anulacion is null and id_contenedor = ?";
            if ($stmt = $this->con->prepare($query)) {
                $stmt->bind_param("i", $id_contenedor);
                $stmt->execute();
                $result = $stmt->get_result();
                // Recorrer el resultado y almacenar las filas en $contenedores
                while ($row = $result->fetch_assoc()) {
                    $contenedores[] = $row;
                }            
                $stmt->close();
            }
        }
        return $contenedores;
    }

    function obtenerContenedoresPorUbicacionTree($id_ubicacion) {
        $contenedores = [];
        $contador =1;
        // Encuentra contenedores en la ubicación especificada
        $query = "SELECT * FROM contenedores WHERE id_Ubicacion = ? AND Fec_Anulacion IS NULL order by id_Contenedor ";
         if ($stmt = $this->con->prepare($query)) {
            $stmt->bind_param("i", $id_ubicacion);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                // Agregar el contenedor actual al array de contenedores
                $row['numero_contador'] = $contador;
                $contenedores[] = $row;
               // echo "en la fila: ".$row;
            //echo $contador;
                // Obtener todos los contenedores hijos de forma recursiva
                $hijos = $this->obtenerContenedoresRecursivosTree($row['id_Contenedor'], $contador);
                $contenedores = array_merge($contenedores, $hijos);
            }
            
        }
       // echo "Ubicación: ".$id_ubicacion;
       // echo "Estoy en contenedores por ubicación:   ";
       // print_r($contenedores);
        return $contenedores;
    }

    // Función para obtener contenedores de forma recursiva
    function obtenerContenedoresRecursivosTree( $id_contenedor, $contador) {
        $contenedores = [];

        // Encuentra el contenedor especificado
        $query = "SELECT * FROM contenedores WHERE id_Contenedor = ? AND Fec_Anulacion IS NULL";
        if ($stmt = $this->con->prepare($query)) {
            $stmt->bind_param("i", $id_contenedor);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
             //   $contenedores[] = $row;
                $row['numero_contador'] = $contador;

                // Busca hijos del contenedor especificado
                $hijos = $this->obtenerHijosTree( $row['id_Contenedor'], $contador);
                $contenedores = array_merge($contenedores, $hijos);
            }
            
        }
        return $contenedores;
    }

    // Función para obtener hijos de un contenedor
    function obtenerHijosTree( $id_padre, $contador) {
        $hijos = [];
        
        $query = "SELECT * FROM contenedores WHERE id_Padre = ? AND Fec_Anulacion IS NULL";
        if ($stmt = $this->con->prepare($query)) {
            $stmt->bind_param("i", $id_padre);
            $stmt->execute();
            $result = $stmt->get_result();
      
            while ($row = $result->fetch_assoc()) {
                $contador++; 
                
                $row['numero_contador'] = $contador;
                $hijos[] = $row;
                
                // Buscar hijos de los hijos (recursivamente)
                $nietos = $this->obtenerHijosTree( $row['id_Contenedor'], $contador);
                $hijos = array_merge($hijos, $nietos);
            }
            
        }
        return $hijos;
    }
    //---------------------------------------------------
}

 

?>
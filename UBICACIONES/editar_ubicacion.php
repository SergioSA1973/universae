<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../DOMICILIOS/claseDomicilio.php'; // Incluye el archivo de la clase Domicilio
include 'claseUbicacion.php'; // Incluye el archivo de la clase Ubicacion

$id = $_GET['id'];
$id_domicilio = $_GET['id_Domicilio'] ?? '';

$ubicacionObj = new Ubicacion();
$ubicacion = $ubicacionObj->obtenerPorId($id);

// Obtener todos los domicilios para el combo
$selected_domicilio = $id_domicilio ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $desc_ubicacion = $_POST['Desc_Ubicacion'];
    $id_domicilio = $_POST['id_Domicilio'];


    $ubicacionObjMod = new Ubicacion( $id, $id_domicilio, $desc_ubicacion); // Pasamos la conexión a la base de datos
    // Intentamos insertar el domicilio
    $modificado = $ubicacionObjMod->modificar();

    // Si la inserción es exitosa, redirigimos a la página de ubicaciones
    if ($modificado) {
        header('Location: ubicaciones.php?id_Domicilio=' . $id_domicilio);
        exit;
    }

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Ubicación</title>
    <!-- Incluye Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/VERSION1/styles.css">
</head>
<body>
    <div class="background"></div>
    <div class="overlay"></div>
    <div class="container mt-5">
        <h1 class="mb-4">Editar Ubicación</h1>
        <form action="editar_ubicacion.php?id=<?= $id ?>&id_Domicilio=<?= $selected_domicilio ?>" method="post">
            <div class="mb-3">
                <label for="id_Domicilio" class="form-label">Domicilio:</label>
                <select id="id_Domicilio" name="id_Domicilio" class="form-select">
                    <option value="">Seleccione un domicilio</option>
                    
                    <?php 
                    try 
                    {
                        session_start();
                        $idUsuario = $_SESSION['sesUsuario'];
                        if (empty($idUsuario) ) {
                            throw new Exception("La sesión de usuario debe contener datos.");
                        }

                        // Crear una instancia de la clase Domicilio
                        $domicilioObj = new Domicilio();

                        // Obtener domicilios del usuario
                        $domicilios = $domicilioObj->obtenerDomiciliosPorUsuario($idUsuario);

                        if (!empty($domicilios)) 
                        {
                            foreach ($domicilios as $domicilio) 
                            {?>
                            <option value="<?= $domicilio['id_Domicilio'] ?>" <?= $domicilio['id_Domicilio'] == $selected_domicilio ? 'selected' : '' ?>>
                                <?= $domicilio['Desc_Domicilio'] ?>
                            </option>

                        <?php 
                            }
                        } 
                    }catch (Exception $e) {
                        $loginMessage = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                    }
                    ?>

                </select>
            </div>

            <div class="mb-3">
                <label for="Desc_Ubicacion" class="form-label">Descripción:</label>
                <input type="text" id="Desc_Ubicacion" name="Desc_Ubicacion" class="form-control" value="<?= $ubicacion['Desc_Ubicacion'] ?>" required>
            </div>

            <button type="submit" class="btn btn-primary" onclick="return confirm('¿Está seguro de que desea realizar la modificación de la ubicación?');">Aceptar</button>
            <button type="button" class="btn btn-secondary" onclick="volverConDomicilio()">Volver</button>


            <script>
                function volverConDomicilio() {
                    var idDomicilio = document.getElementById("id_Domicilio").value;
                    
                    window.location.href = '/VERSION1/UBICACIONES/ubicaciones.php?id_Domicilio=' + encodeURIComponent(idDomicilio);
                     
                }
            </script>


        </form>
    </div>

    <!-- Incluye Bootstrap JS y Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

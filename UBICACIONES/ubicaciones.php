    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include '../DOMICILIOS/claseDomicilio.php'; // Incluye el archivo de la clase Domicilio
    include 'claseUbicacion.php'; // Incluye el archivo de la clase Ubicacion
    $selected_domicilio = $_GET['id_Domicilio'] ?? '';

    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Listado de Ubicaciones</title>
        <!-- Enlace a Bootstrap CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/VERSION1/styles.css">

    </head>
    <body>
        <div class="background"></div>
        <div class="overlay"></div>    

        <div class="navbar" id="navbar">
            <?php include '../enlaces.php'; ?>
        </div>
        <div class="container mt-5">
            <h1 class="mb-4">Listado de Ubicaciones</h1>
            
            <!-- Combo para seleccionar el Domicilio -->
            <form method="get" action="ubicaciones.php" class="form-inline mb-3">
                <label for="id_Domicilio" class="mr-2">Domicilio:</label>
                <select name="id_Domicilio" id="id_Domicilio" class="form-control mr-2" onchange="this.form.submit()">
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

                    } catch (Exception $e) {
                        $loginMessage = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                    }


                        ?>
                </select>
                <a href="#" class="btn btn-primary" onclick="checkAndRedirectAdd()">Añadir Ubicación</a>

            </form>
            
            <!-- Tabla de ubicaciones -->
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    
                        <?php 

                        // Crear una instancia de la clase Ubicacion
                        $ubicacionObj = new Ubicacion();

                        // Obtener domicilios del usuario
                        $ubicaciones = $ubicacionObj->obtenerUbicacionesPorIdDomicilio($selected_domicilio); 

                        if (!empty($ubicaciones)) 
                        {
                            foreach ($ubicaciones as $ubicacion) 
                            {?>
                            <tr>
                                <td><?= $ubicacion['id_Ubicacion'] ?></td>
                                <td><?= $ubicacion['Desc_Ubicacion'] ?></td>
                                <td>
                                    <a href="#" class="btn btn-warning btn-sm" onclick="checkAndRedirectEdit(<?= $ubicacion['id_Ubicacion'] ?>);">Editar</a>
                                    <a href="#" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $ubicacion['id_Ubicacion'] ?>);">Eliminar</a>
                                </td>
                            </tr>
                            <?php 
                            }
                        } 
                        else
                        {?>
                            <tr>
                                <td colspan="3" class="text-center">No hay ubicaciones disponibles.</td>
                            </tr>
                        <?php
                        }
                        ?>

                </tbody>
            </table>
            <br><br>
            <div class="container my-5">
                    <?php
                    if (isset($loginMessage)) {
                        echo $loginMessage;
                    }
                    ?>
            </div>
        </div>

        <!-- Enlace a Bootstrap JS y dependencias -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <script type="text/javascript">
        function checkAndRedirectEdit(id_Ubicacion) {
            var selectedDomicilio = document.getElementById('id_Domicilio').value;
            if (selectedDomicilio === '') {
                alert('Por favor, selecciona un domicilio antes de editar.');
            } else {
                window.location.href = 'editar_ubicacion.php?id=' + id_Ubicacion + '&id_Domicilio=' + selectedDomicilio;
            }
        }

        function confirmDelete(id_Ubicacion) {
            var xhr = new XMLHttpRequest();
            var selectedDomicilio = document.getElementById('id_Domicilio').value;
            xhr.open("GET", "checkUbicaciones2.php?id_Ubicacion=" + id_Ubicacion, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = xhr.responseText;
                    if (response == 'true') {
                        if (confirm("¿Estás seguro de que deseas eliminar la ubicación?")) {
                            window.location.href = 'eliminar_ubicacion.php?id=' + id_Ubicacion + '&id_Domicilio=' + selectedDomicilio;
                        }
                    } else {
                        alert("Esta ubicacion tiene contenedores o articulos asociados.");
                    }
                }
            };
            xhr.send();
        }

        function checkAndRedirectAdd() {
                var selectedDomicilio = document.getElementById('id_Domicilio').value;
                if (selectedDomicilio === '') {
                alert('Por favor, selecciona un domicilio antes de agregar una ubicación.');
                } else {
                window.location.href = 'agregar_ubicacion.php?id_Domicilio=' + selectedDomicilio;
                }
            }


        </script>

    </body>
    </html>

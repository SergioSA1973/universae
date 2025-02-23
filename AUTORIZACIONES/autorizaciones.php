    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include 'claseAutorizacion.php'; // Incluye el archivo de la clase autorizacion
    include '../DOMICILIOS/claseDomicilio.php'; // Incluye el archivo de la clase Domicilio
    $selected_domicilio = $_GET['id_Domicilio'] ?? '';

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Autorizaciones</title>
        <!-- Enlace a Bootstrap CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/VERSION1/styles.css">

        <script type="text/javascript">
            function confirmSubmit() {
                return confirm("¿Estás seguro de que deseas eliminar la autorización seleccionada?");
            }
        </script>


        <script type="text/javascript">
            function confirmDelete(id) {            
                if (confirm("¿Estás seguro de que deseas eliminar la autorización seleccionada?")) {
                    window.location.href = "deleteAutorizacion.php?deleteid=" + id;
                }
            }

        </script>

    </head>
    <body>
        <div class="background"></div>
        <div class="overlay"></div>
        <div class="navbar" id="navbar">
            <?php include '../enlaces.php'; ?>
        </div>
        <form method="get" action="autorizaciones.php" class="form-inline mb-3">
            <div class="container mt-5">
            <h1 class="mb-4">Autorizaciones</h1>
                <div class="form-group">
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
                    <button class="btn btn-primary my-5"><a href="AddAutorizacion.php" class="text-light">Añadir Autorización</a></button>
                </div>
        </form>



        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th scope="col" width="10%">Id</th>
                    <th scope="col">Domicilio</th>
                    <th scope="col">Autorizado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

            <?php
            if (!isset($loginMessage)) 
            {
                try 
                {
                    // Crear una instancia de la clase Autorizacion
                    $autorizacionObj = new Autorizacion();
          
                    // Obtener las autorizaciones asociadas al usuario
                    $autorizaciones = $autorizacionObj->obtenerAutorizacionesPorDomicilioUsuario($selected_domicilio);
                            if (!empty($autorizaciones)) 
                            {
                            ?>
                                <?php foreach ($autorizaciones as $autorizacion): ?>
                                <tr>
                                    <td><?= $autorizacion['id_Autorizacion'] ?></td>
                                    <td><?= $autorizacion['Username'] ?></td>
                                    <td><?= $autorizacion['Desc_Domicilio'] ?></td>

                                    <td>
                                        <!-- Llamar a la función validarDomicilio al hacer clic en el enlace de edición -->
                                        <a href="updateAutorizacion.php?updateid=<?= $autorizacion['id_Autorizacion'] ?>" class="btn btn-warning btn-sm">Editar</a>

                                        <a href="#" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $autorizacion['id_Autorizacion'] ?>)">Eliminar</a>
                                    </td>               
                                </tr>
                                <?php endforeach; ?>
                            <?php } else 
                            {
                            ?>
                                <tr>
                                    <td colspan="5" class="text-center">No hay autorizaciones disponibles.</td>
                                </tr>
                            <?php 
                            }
                } catch (Exception $e) {
                    $loginMessage = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                }
            }
            ?>

            </tbody>
        </table>
        <br><br>
    </div>
        <div class="container my-5">
            <?php
            if (isset($loginMessage)) {
                echo $loginMessage;
            }
            ?>
        </div>

    </body>
    </html>

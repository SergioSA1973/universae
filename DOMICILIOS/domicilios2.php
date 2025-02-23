            <?php
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            include 'claseDomicilio.php'; // Incluye el archivo de la clase Domicilio

            ?>

            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Domicilios</title>
                <!-- Enlace a Bootstrap CSS -->
                <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" type="text/css" href="/VERSION1/styles.css">
                <script type="text/javascript">
                    function confirmDelete(id) {
                        var xhr = new XMLHttpRequest();
                        xhr.open("GET", "checkUbicaciones2.php?id=" + id, true);
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                var response = xhr.responseText;
                                if (response == 'true') {
                                    if (confirm("¿Estás seguro de que deseas eliminar este domicilio?")) {
                                        window.location.href = "deleteDomicilio2.php?deleteid=" + id;
                                    }
                                } else {
                                    alert("Este domicilio tiene ubicaciones asociadas y no se puede eliminar.");
                                }
                            }
                        };
                        xhr.send();
                    }
                </script>
            </head>
            <body>
                <div class="background"></div>
                <div class="overlay"></div>
                <div class="navbar" id="navbar">
                        <?php include '../enlaces.php'; ?>
                </div>
                <div class="container mt-5">    
                    <form>                    
                            <h1 class="mb-4">Domicilios</h1>
                                <div class="form-group">

                                    <button class="btn btn-primary my-5"><a href="AddDomicilio2.php" class="text-light">Añadir Domicilio</a></button>
                                </div>
                    </form>
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th width="10%">Id</th>
                                <th>Domicilio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
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
                                $domicilios = $domicilioObj->obtenerDomiciliosPorUsuario($idUsuario);
                                if (!empty($domicilios)) 
                                {
                                ?>
                                    <?php foreach ($domicilios as $domicilio): ?>
                                    <tr>
                                        <td><?= $domicilio['id_Domicilio'] ?></td>
                                        <td><?= $domicilio['Desc_Domicilio'] ?></td>
                                        
                                        <td>
                                            <!-- Llamar a la función validarDomicilio al hacer clic en el enlace de edición -->
                                            <a href="updateDomicilio2.php?updateid=<?= $domicilio['id_Domicilio'] ?>" class="btn btn-warning btn-sm">Editar</a>

                                            <a href="#" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $domicilio['id_Domicilio'] ?>)">Eliminar</a>
                                        </td>               
                                    </tr>
                                    <?php endforeach; ?>
                                <?php } else 
                                {
                                ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No hay domicilios disponibles.</td>
                                    </tr>
                                <?php 
                                }
                            } catch (Exception $e) {
                                $loginMessage = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
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

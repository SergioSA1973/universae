

    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include '../DOMICILIOS/claseDomicilio.php'; // Incluye el archivo de la clase Domicilio
    include '../UBICACIONES/claseUbicacion.php'; // Incluye el archivo de la clase Ubicacion
    include '../CONTENEDORES/claseContenedor.php'; // Incluye el archivo de la clase Contenedor
    include '../ARTICULOS/claseArticulo.php'; // Incluye el archivo de la clase Articulo

    // Crear una instancia de la clase Domicilio
    $domicilioObj = new Domicilio();
    // Crear una instancia de la clase Ubicación
    $ubicacionObj = new Ubicacion();
    // Crear una instancia de la clase Contenedor
    $contenedorObj = new Contenedor();
    // Crear una instancia de la clase Articulo
    $articuloObj = new Articulo();

    try 
    {
        session_start();
        $idUsuario = $_SESSION['sesUsuario'];
        if (empty($idUsuario) ) {
            throw new Exception("La sesión de usuario debe contener datos.");
        }

        // Obtener todos los domicilios para el combo
        $domicilios = $domicilioObj->obtenerDomiciliosPorUsuarioMasAutorizado($idUsuario);

        $selected_domicilio = $_GET['id_Domicilio'] ?? '';
        // Obtener ubicaciones y sus contenedores/artículos si se ha seleccionado un domicilio
        $ubicaciones = [];
        if ($selected_domicilio) {
            $ubicaciones = $ubicacionObj->obtenerUbicacionesPorIdDomicilio($selected_domicilio);
        }
    } catch (Exception $e) {
        $loginMessage = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Listado de Ubicaciones y Artículos</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
        <link rel="stylesheet" type="text/css" href="/VERSION1/styles.css">
        <style>
            .treeview ul { list-style-type: none; padding-left: 20px; }
            .treeview li { margin: 10px 0; }
            .treeview span { cursor: pointer; }
            .treeview span i { margin-right: 5px; }
            .selected { font-weight: bold; color: blue; }
            .treeview .location-icon { color: #3498db; }
            .treeview .container-icon { color: #e67e22; }
            .treeview .article-icon { color: #2ecc71; }
        </style>
    </head>
    <body>
        <div class="background"></div>
        <div class="overlay"></div>
        <div class="navbar" id="navbar">
            <?php include '../enlaces.php'; ?>
        </div>
        <div class="container mt-5">
            <h1 class="mb-4">Explorador de Ubicaciones y Artículos</h1>

            <!-- Combo para seleccionar el Domicilio -->
            <form method="get" action="" class="form-inline mb-3" enctype="multipart/form-data">
                <label for="id_Domicilio" class="mr-2">Domicilio:</label>
                <select name="id_Domicilio" id="id_Domicilio" class="form-control mr-2" onchange="this.form.submit()">
                    <option value="">Seleccione un domicilio</option>
                    <?php foreach ($domicilios as $domicilio): ?>
                        <option value="<?= $domicilio['id_Domicilio'] ?>" <?= $domicilio['id_Domicilio'] == $selected_domicilio ? 'selected' : '' ?>>
                            <?= $domicilio['Desc_Domicilio'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <div class="row">
                <div class="col-6">
                    <!-- Treeview de ubicaciones, contenedores y artículos -->
                    <div class="treeview">
                        <ul>
                            <?php foreach ($ubicaciones as $ubicacion): ?>
                                <li>
                                    <!-- Icono de Ubicación -->                            
    				<span class="ubicacion" 
        					data-id="<?= $ubicacion['id_Ubicacion'] ?>" 
        					data-type="ubicacion"
        					ondragover="allowDrop(event)" 
        					ondrop="dropArticulo(event, 'ubicacion', <?= $ubicacion['id_Ubicacion'] ?>)">
        					<i class="fas fa-map-marker-alt location-icon"></i> <?= $ubicacion['Desc_Ubicacion'] ?>
    				</span>

    				<!-- Lista de articulos asociados a una ubicación -->
    				<!-- -------------------------------------------- -->
    				<?php if ($ubicacion['id_Ubicacion']): ?>
        				<?php 
        					$artPorUbicacion = $articuloObj->obtenerUnicamenteArticulosPorUbicacion( $ubicacion['id_Ubicacion']);
        					if (!empty($artPorUbicacion)): ?>
            				<ul>
                					<?php foreach ($artPorUbicacion as $artU): ?>
                    				<li>
                        					<span class="articulo" 
    								draggable="true" 
    								data-id="<?= $artU['id_Articulo'] ?>" 
    								data-type="articulo" 
    								data-desc="<?= $artU['Desc_Articulo'] ?>"
    								data-foto="<?= $artU['img_Foto'] ?>"
    								ondragstart="drag(event)"
    								ondragover="allowDrop(event)">
                            						<i class="fas fa-file-alt article-icon"></i> <?= $artU['Desc_Articulo'] ?>
                        					</span>
                    				</li>
                					<?php endforeach; ?>
            				</ul>
        					<?php endif; ?>
    				<?php endif; ?>
    				<!-- -------------------------------------------- -->
    				<!-- Lista de contenedores asociados a una ubicación -->
    				<?php if ($ubicacion['id_Ubicacion']): ?>
        				<?php 
        					$conPorUbicacion = $articuloObj->obtenerContenedoresPorUbicacionTree( $ubicacion['id_Ubicacion']);
        					if (!empty($conPorUbicacion)): ?>
            				<ul>
                					<?php foreach ($conPorUbicacion as $conU): ?>
    							<?php 
    							$spacePx="";
    							for ($x = 1; $x < $conU['numero_contador']; $x++) {
    								$spacePx = $conU['numero_contador'] * 20; 
    							}
    							//echo "Espacios: ".$spacePx;
    							?>
                    					<li>
    								
    								<span class="contenedor" 
    									draggable="true" 
        									data-id="<?= $conU['id_Contenedor'] ?>" 
        									data-type="contenedor"
    									data-desc="<?= $conU['Desc_Contenedor'] ?>"
    									style="margin-left: <?= intval($spacePx); ?>px;"
    									ondragstart="drag(event)"
        									ondragover="allowDrop(event)" 
        									ondrop="dropArticulo(event, 'contenedor', <?= $conU['id_Contenedor'] ?>)">
        									<i class="fas fa-box-open container-icon"></i> <?= $conU['Desc_Contenedor'] ?>
    								</span>


                    					</li>
    							<!-- Ahora pintamos los articulos asociados a un contenedor -->
    							<?php 
    							$artPorContenedor = $articuloObj->obtenerUnicamenteArticulosPorContenedor($conU['id_Contenedor']);
        							if (!empty($artPorContenedor)): ?>
            						<ul>
                							<?php foreach ($artPorContenedor as $artC): ?>
                    						<li>

    									<span class="articulo" 
    										draggable="true" 
          										data-id="<?= $artC['id_Articulo'] ?>" 
          										data-type="articulo" 
          										data-desc="<?= $artC['Desc_Articulo'] ?>"
    										data-foto="<?= $artC['img_Foto'] ?>"
          										style="margin-left: <?= intval($spacePx); ?>px;"
    										ondragstart="drag(event)"
    										ondragover="allowDrop(event)">
    										<i class="fas fa-file-alt article-icon"></i> <?= $artC['Desc_Articulo'] ?>
    									</span>

                    						</li>
                							<?php endforeach; ?>
            						</ul>
        							<?php endif; ?>
    							<!-- ----------------------------------------------------- -->


                					<?php endforeach; ?>
            				</ul>
        					<?php endif; ?>
    				<?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>


    	<div class="col-6">
        	<!-- Detalles del artículo, ubicación o contenedor -->
        	<h4>Detalles</h4>
        		<form id="formArticulo">
            		<input type="hidden" name="id_Articulo" id="id_Articulo">
            		<div class="form-group">
                			<label for="Desc_Articulo">Descripción</label>
                			<input type="text" class="form-control" id="Desc_Articulo" name="Desc_Articulo" readonly>
            		</div>

    			<!-- Campo para mostrar la imagen del artículo -->
    			<div class="form-group">
        				<label for="foto_Articulo">Foto del artículo</label>
        				<img id="foto_Articulo" src="" alt="Imagen del artículo" style="max-width: 100%; height: auto; display: none;">
    			</div>

            		<!-- Aquí se crearán los botones dinámicamente -->
            		<div id="actionButtons"></div>
        		</form>
    	</div>



            </div>
        </div>

        <div class="container my-5">
            <?php
                if (isset($loginMessage)) {
                    echo $loginMessage;
                }
                ?>
        </div>

        <!-- Enlace a Bootstrap JS y dependencias -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <!-- Script para manejar el Treeview y las operaciones con artículos -->
        <script>





    document.addEventListener('DOMContentLoaded', function() {
        // Selección del item del treeview
    //debugger;
        document.querySelectorAll('.treeview span').forEach(function(item) {
            item.addEventListener('mousedown', function() {
                // Desmarcar todos los nodos previamente seleccionados
                document.querySelectorAll('.treeview span').forEach(function(i) {
                    i.classList.remove('selected');
                });
                // Marcar el nodo actual como seleccionado
                item.classList.add('selected');

                const id = item.getAttribute('data-id');
                const type = item.getAttribute('data-type');
                const desc = item.getAttribute('data-desc') || '';
    	        const foto = item.getAttribute('data-foto') || '';
                const selectedDomicilio = document.getElementById('id_Domicilio').value;
                const actionButtons = document.getElementById('actionButtons');
                
                // Limpiar campo de texto y botones
                document.getElementById('id_Articulo').value = '';
                document.getElementById('Desc_Articulo').value = '';
                document.getElementById('foto_Articulo').src = ''; // Limpiar la imagen
                document.getElementById('foto_Articulo').style.display = 'none'; // Ocultar la imagen
                actionButtons.innerHTML = '';

                // Si es un artículo, actualizar el campo de texto y crear botones
                if (type === 'articulo') {
                    document.getElementById('id_Articulo').value = id;
                    document.getElementById('Desc_Articulo').value = desc;

    		// Si hay una foto disponible, mostrarla
                    if (foto) {
                            document.getElementById('foto_Articulo').src = foto;
                            document.getElementById('foto_Articulo').style.display = 'block'; // Mostrar la imagen
                        } else {
                            document.getElementById('foto_Articulo').style.display = 'none'; // Ocultar la imagen si no hay URL
                        }


                    // Crear botones de Modificar y Eliminar
                    actionButtons.innerHTML = `
                        <input type="button" value="Modificar" class="btn btn-primary mr-2" onclick="modificarArticulo(${selectedDomicilio}, ${id})">
                        <input type="button" value="Eliminar" class="btn btn-danger" onclick="eliminarArticulo(${selectedDomicilio}, ${id})">
                    `;
                } else if (type === 'ubicacion' || type === 'contenedor') {
                    // Si es una ubicación o contenedor, crear botón de Añadir
    			 actionButtons.innerHTML = `
                        <input type="button" value="Añadir" class="btn btn-success" onclick="anadirArticulo(${selectedDomicilio}, ${type === 'ubicacion' ? id : 'null'}, ${type === 'contenedor' ? id : 'null'})">
                    `;
                    
                }
            });
        });
    });



    function modificarArticulo(idDomicilio, idArticulo) {
        //(ayuda)alert('Modificar Artículo - ID Artículo: ' + idArticulo + ', ID Domicilio: ' + idDomicilio);
        // Aquí agregar la lógica para redirigir o abrir un modal para modificar el artículo
        window.location.href = 'modificarArticulo.php?id_Domicilio=' + idDomicilio + '&id_Articulo=' + idArticulo;
    }

    function eliminarArticulo(idDomicilio, idArticulo) {
        if (confirm('¿Estás seguro que deseas eliminar este artículo?')) {
            //(ayuda)alert('Eliminar Artículo - ID Artículo: ' + idArticulo + ', ID Domicilio: ' + idDomicilio);
            // Aquí agregar la lógica para eliminar el artículo, probablemente con una petición AJAX
    	window.location.href = 'eliminarArticulo.php?id_Domicilio=' + idDomicilio + '&id_Articulo=' + idArticulo;
        }
    }

    function anadirArticulo(idDomicilio, idUbicacion, idContenedor) {
        // Comprobar si se ha seleccionado un domicilio
    	//(ayuda)alert('añadir - ID Uubicacion: ' + idUbicacion + ', ID Domicilio: ' + idDomicilio + ', ID Contenedor: ' + idContenedor);
        if (!idDomicilio) {
            alert('Faltan datos para añadir el artículo.');
            return;
        }

    window.location.href ='anadirArticulo.php?id_Domicilio=' + idDomicilio + '&id_Ubicacion=' + idUbicacion + '&id_Contenedor=' + idContenedor;
    }


    function allowDrop(event) {
        event.preventDefault();
    }


    function drag(event) {
        event.dataTransfer.setData("text", event.target.getAttribute("data-id"));
        event.dataTransfer.setData("type", event.target.getAttribute("data-type"));
    }

    let selectedArticulos = [];

    document.querySelectorAll('.articulo').forEach(function(item) {
    //debugger;
        item.addEventListener('mousedown', function() {
            const id = item.getAttribute('data-id');
            if (selectedArticulos.includes(id)) {
                selectedArticulos = selectedArticulos.filter(art => art !== id); // Desmarcar si ya está seleccionado
                item.classList.remove('selected');
            } else {
                selectedArticulos.push(id); // Agregar a la lista de seleccionados
                item.classList.add('selected');
            }
        });
    });



    function dropArticulo(event, tipoDestino, idDestino) {
    debugger;
        event.preventDefault();

        let idObjeto = event.dataTransfer.getData("text"); // Obtener ID del objeto arrastrado
        let tipoObjeto = event.dataTransfer.getData("type"); // Obtener tipo del objeto arrastrado (artículo o contenedor)

        if (idObjeto && tipoObjeto) {
            moverObjeto(tipoObjeto, tipoDestino, idObjeto, idDestino);
        }
    }

    function moverObjeto2(tipoObjeto, tipoDestino, idObjeto, idDestino) {
    	debugger;
        let urlComprobacion = `comprobarMoverObjeto.php?tipoObjeto=${tipoObjeto}&idObjeto=${idObjeto}&tipoDestino=${tipoDestino}&idDestino=${idDestino}`;
    console.log("URL de comprobación:", urlComprobacion);
        // Realizar la comprobación previa


    fetch(urlComprobacion, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
        credentials: 'same-origin', // O 'include' si necesitas manejar cookies o sesiones
    })


        .then(response => response.text())
        .then(result => {
    	console.log("Resultado:", result);

            if (result === 'valid') {
                // Si la comprobación es válida, mover el objeto
                let urlMover = `moverObjeto.php?tipoObjeto=${tipoObjeto}&idObjeto=${idObjeto}&tipoDestino=${tipoDestino}&idDestino=${idDestino}`;

                fetch(urlMover, {
                    method: 'GET'
                })
                .then(response => response.text())
                .then(result => {
                    alert(`${tipoObjeto.charAt(0).toUpperCase() + tipoObjeto.slice(1)} movido con exito.`);
                    location.reload(); // Opcional, para actualizar el árbol
                })
                .catch(error => {
                    console.error("Error al mover el objeto:", error);
                });
            } else {
                alert("Error: Operacion no permitida.");
            }
        })
        .catch(error => {
            console.error("Error al comprobar los datos:", error);
        });
    }


    function moverObjeto(tipoObjeto, tipoDestino, idObjeto, idDestino) {
       
        // Formamos la URL para la comprobación
        let urlComprobacion = `comprobarMoverObjeto.php?tipoObjeto=${tipoObjeto}&idObjeto=${idObjeto}&tipoDestino=${tipoDestino}&idDestino=${idDestino}`;
    debugger;
        // Crear una nueva instancia de XMLHttpRequest
        let xhrComprobacion = new XMLHttpRequest();
        
        // Configurar la solicitud GET
        xhrComprobacion.open('GET', urlComprobacion, true);
        
        // Definir la función que se ejecutará cuando se reciba la respuesta
        xhrComprobacion.onload = function() {
            // Verificar si el estado de la respuesta es 200 (OK)
            if (xhrComprobacion.status === 200) {
                let result = xhrComprobacion.responseText.trim();  // Obtener la respuesta y limpiar espacios
                
                console.log("Resultado de la comprobación:", result); // Ver el resultado en la consola
                
                // Si la respuesta es 'valid', procedemos a mover el objeto
                if (result === 'valid') {
                    // Formamos la URL para mover el objeto
                    let urlMover = `moverObjeto.php?tipoObjeto=${tipoObjeto}&idObjeto=${idObjeto}&tipoDestino=${tipoDestino}&idDestino=${idDestino}`;
                    
                    // Crear una nueva instancia de XMLHttpRequest para mover el objeto
                    let xhrMover = new XMLHttpRequest();
                    
                    // Configurar la solicitud GET para mover el objeto
                    xhrMover.open('GET', urlMover, true);
                    
                    // Definir la función que se ejecutará cuando se reciba la respuesta de mover
                    xhrMover.onload = function() {
                        if (xhrMover.status === 200) {
                            alert(`${tipoObjeto.charAt(0).toUpperCase() + tipoObjeto.slice(1)} movido con exito.`);
                            location.reload();  // Recargar la página (opcional)
                        } else {
                            console.error("Error al mover el objeto:", xhrMover.status);
                        }
                    };
                    
                    // Manejo de errores en la solicitud de mover
                    xhrMover.onerror = function() {
                        console.error("Error de red al intentar mover el objeto.");
                    };
                    
                    // Enviar la solicitud para mover el objeto
                    xhrMover.send();
                } else {
                    alert("Error: Operación no permitida.");
                }
            } else {
                console.error("Error en la comprobación de los datos:", xhrComprobacion.status);
            }
        };
        
        // Manejo de errores en la solicitud de comprobación
        xhrComprobacion.onerror = function() {
            console.error("Error de red al intentar comprobar los datos.");
        };
        
        // Enviar la solicitud para comprobar si el objeto puede ser movido
        xhrComprobacion.send();
    }

        </script>
    </body>
    </html>

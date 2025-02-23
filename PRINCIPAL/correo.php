    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Enviar Correo</title>
        <!-- Incluir Bootstrap CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>

    <div class="container mt-5">
        <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        //include 'connect.php';
        include 'claseUsuario.php'; // Incluye el archivo de la clase Usuario

        function send_email_hotmail($to, $subject, $message, $headers = '') {
            $smtpServer = "smtp-mail.outlook.com";
            $port = 587;
            $username = "sergio_saez_a@hotmail.com";
            $password = "SERGIO4407&";  // Recomendado usar una contraseña de aplicación
            $from = "sergio_saez_a@hotmail.com";

            $socket = fsockopen($smtpServer, $port, $errno, $errstr, 30);
            if (!$socket) {
                return "Error: No se puede conectar al servidor SMTP ($errno): $errstr";
            }

            $response = fgets($socket, 512);
            if (substr($response, 0, 3) != '220') {
                return "Error de conexión SMTP: $response";
            }

            fputs($socket, "HELO $smtpServer\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) != '250') {
                return "Error en HELO: $response";
            }

            fputs($socket, "STARTTLS\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) != '220') {
                return "Error en STARTTLS: $response";
            }

            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

            fputs($socket, "EHLO $smtpServer\r\n");
            
            // Read multi-line response from EHLO
            while (substr($response = fgets($socket, 512), 3, 1) != ' ') {
                if (substr($response, 0, 3) != '250') {
                    return "Error en EHLO: $response";
                }
            }

            fputs($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) != '334') {
                return "Error en AUTH LOGIN: $response";
            }

            fputs($socket, base64_encode($username) . "\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) != '334') {
                return "Error en el usuario: $response";
            }

            fputs($socket, base64_encode($password) . "\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) != '235') {
                return "Error en la contraseña: $response";
            }

            fputs($socket, "MAIL FROM: <$from>\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) != '250') {
                return "Error en MAIL FROM: $response";
            }

            fputs($socket, "RCPT TO: <$to>\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) != '250') {
                return "Error en RCPT TO: $response";
            }

            fputs($socket, "DATA\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) != '354') {
                return "Error en DATA: $response";
            }

            // Codificar caracteres especiales en el asunto
            $subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');

            // Ajustar los encabezados para el correo HTML
            $headers .= "From: $from\r\n";
            $headers .= "To: $to\r\n";
            $headers .= "Subject: $subject\r\n";
            $headers .= "Date: " . date('r') . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            fputs($socket, "$headers\r\n$message\r\n.\r\n");
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) != '250') {
                return "Error al enviar el mensaje: $response";
            }

            fputs($socket, "QUIT\r\n");
            fclose($socket);

            return "Correo enviado correctamente";
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    	    $contraseña = "";

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    // Contenido del correo en HTML
                    $to = $email;
                    $subject = "Recuperación de contraseña";

                    /*
    		  $sql="select * from `usuarios` where Email='$email'";
    		  //echo $sql;
        	 	$result=mysqli_query($con, $sql);
        	 	$row=mysqli_fetch_assoc($result);
                    */
            $usuario = new Usuario(); // Pasamos la conexión a la clase
            $row = $usuario->obtenerPorEmail($email);


    		if(isset($row)){
    			$contraseña =$row['Password'];
    			//echo "Contraseña: ".$contraseña;
    		}


                    $message = "
                    <html>
                    <head>
                        <title>Recuperación de contraseña</title>
                    </head>


                    <body>
                        <p>Recordatorio de contraseña. </p>
                    
    		    <p>Su contraseña es: $contraseña </p>
    		</body>
                    </html>";


                    $headers = "From: SOPORTE DE INFORMÁTICA <sergio_saez_a@hotmail.com>\r\n";

                    $result = send_email_hotmail($to, $subject, $message, $headers);
                    echo "<div class='alert alert-success'>$result</div>";
                } catch (Exception $e) {
                    echo "<div class='alert alert-danger'>No se pudo enviar el correo. Error: {$e->getMessage()}</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>El correo electrónico proporcionado no es válido.</div>";
            }
        }
        ?>

       
    </div>

    <!-- Incluir Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>

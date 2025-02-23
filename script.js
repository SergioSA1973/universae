 function validarFormulario() {
            var valido = true;

            // Validar nombre de usuario
            var nombreUsuario = document.getElementById("nombre_usuario");
            if (nombreUsuario.value.trim() === "") {
                nombreUsuario.classList.add("is-invalid");
                valido = false;
            } else {
                nombreUsuario.classList.remove("is-invalid");
            }

            // Validar contraseña
            var password = document.getElementById("password");
            if (password.value.trim() === "") {
                password.classList.add("is-invalid");
                valido = false;
            } else {
                password.classList.remove("is-invalid");
            }

            return valido;
        }

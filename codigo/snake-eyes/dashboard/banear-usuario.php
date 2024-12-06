<?php
    if (isset($_POST['nombre_usuario'])) {
        //Obtenemos el nombre del usuario
        $nombre_usuario = $_POST['nombre_usuario'];

        //Datos necesarios para la conexion a la base de datos
        $servidor = "localhost";
        $usuario_db = "root";
        $contrasena_db = "";
        $base = "snake_eyes";

        //Conectamos a la base de datos
        $conexion = new mysqli($servidor, $usuario_db, $contrasena_db, $base);

        //Verificamos la conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        //Baneamos al usuario según el nombre previamente obtenido
        $sql = "UPDATE usuarios SET baneado = 1 WHERE nombre_usuario = ?";
        $statement = $conexion->prepare($sql);
        //Usamos "s" debido a que es de tipo cadena ("string")
        $statement->bind_param("s", $nombre_usuario);

        if ($statement->execute()) {
            echo "Usuario baneado correctamente.";
        } else {
            echo "Error al banear el usuario.";
        }

        $statement->close();
        $conexion->close();
    } else {
        echo "No se recibió el nombre del usuario.";
    }
?>
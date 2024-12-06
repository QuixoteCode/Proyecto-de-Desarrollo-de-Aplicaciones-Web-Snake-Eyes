<?php
    session_start();

    //Datos necesarios para la conexión a la base de datos
    $servidor = "localhost";
    $usuario_db = "root";
    $contrasena_db = "";
    $base = "snake_eyes";

    $conexion = new mysqli($servidor, $usuario_db, $contrasena_db, $base);
    if ($conexion->connect_error) {
        die("La conexión ha fallado: " . $conexion->connect_error);
    }

    //Obtenemos el nombre de usuario y el plan desde la base de datos para la sesión actual
    $statement = $conexion->prepare("SELECT nombre_usuario, plan FROM usuarios WHERE id = ?");
    $statement->bind_param("s", $_SESSION['user_id']);
    $statement->execute();
    $statement->bind_result($nombre_usuario, $plan_usuario);
    $statement->fetch();
    $statement->close();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $titulo = $_POST['titulo'];
        $contenido = $_POST['contenido_publicacion'];
        $imagen_binaria = null;

        //Manejamos la imagen subida
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            //Leemos el archivo de imagen como un binario
            $imagen_binaria = file_get_contents($_FILES['imagen']['tmp_name']);
        }

        //Insertamos la publicación en la base de datos, incluyendo el nombre de usuario, plan y la imagen en formato binario
        $statement = $conexion->prepare("INSERT INTO publicaciones (titulo, contenido, nombre_autor, plan, imagen) VALUES (?, ?, ?, ?, ?)");
        $statement->bind_param("sssss", $titulo, $contenido, $nombre_usuario, $plan_usuario, $imagen_binaria);

        if ($statement->execute()) {
            //Redirigimos de nuevo a la página para recargar
            header("Location: foro.php");
            exit;
        } else {
            echo "Error al guardar la publicación: " . $conexion->error;
        }

        $statement->close();
    } else {
        echo "Método de solicitud no válido.";
    }

    $conexion->close();
?>
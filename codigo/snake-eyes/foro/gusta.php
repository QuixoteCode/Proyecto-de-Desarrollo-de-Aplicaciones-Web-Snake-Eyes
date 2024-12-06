<?php
    session_start();

    //Verificamos si el usuario ha hecho login para poder enviar el "me gusta"
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["error" => "Debes iniciar sesión para dar un me gusta."]);
        exit();
    }

    //Verificamos el envío del ID de la publicación
    if (!isset($_POST['publicacion_id'])) {
        echo json_encode(["error" => "Publicación no válida."]);
        exit();
    }

    $publicacion_id = $_POST['publicacion_id'];
    $usuario_actual = $_SESSION['user_id'];

    //Datos necesarios para la conexión a la base de datos
    $servidor = "localhost";
    $usuario_db = "root";
    $contrasena_db = "";
    $base = "snake_eyes";

    $conexion = new mysqli($servidor, $usuario_db, $contrasena_db, $base);
    if ($conexion->connect_error) {
        die(json_encode(["error" => "La conexión ha fallado: " . $conexion->connect_error]));
    }

    //Procedemos a verificar si el usuario ya ha dado "me gusta" a la publicación
    $statement = $conexion->prepare("SELECT COUNT(*) FROM gustas WHERE publicacion_id = ? AND usuario_id = ?");
    $statement->bind_param("ii", $publicacion_id, $usuario_actual);
    $statement->execute();
    $statement->bind_result($me_gusta_existe);
    $statement->fetch();
    $statement->close();

    //Si ya existe el "me gusta" lo eliminamos, y si no existe lo añadimos
    if ($me_gusta_existe > 0) {
        $delete_statement = $conexion->prepare("DELETE FROM gustas WHERE publicacion_id = ? AND usuario_id = ?");
        $delete_statement->bind_param("ii", $publicacion_id, $usuario_actual);
        $delete_statement->execute();
        $delete_statement->close();

        $accion = "eliminado";
    } else {
        $insert_statement = $conexion->prepare("INSERT INTO gustas (publicacion_id, usuario_id) VALUES (?, ?)");
        $insert_statement->bind_param("ii", $publicacion_id, $usuario_actual);
        $insert_statement->execute();
        $insert_statement->close();

        $accion = "añadido";
    }

    //Obtenemos el total de "me gusta"
    $statement = $conexion->prepare("SELECT COUNT(*) AS total_gustas FROM gustas WHERE publicacion_id = ?");
    $statement->bind_param("i", $publicacion_id);
    $statement->execute();
    $statement->bind_result($total_gustas);
    $statement->fetch();
    $statement->close();

    $conexion->close();

    //Devolvemos el resultado en formato JSON
    echo json_encode([
        "accion" => $accion, 
        "total_gustas" => $total_gustas
    ]);

    //Salimos para evitar más salida que pueda interferir con la respuesta JSON
    exit;
?>
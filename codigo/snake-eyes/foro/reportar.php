<?php
    //Establecemos la cabecera Content-Type para que el navegador sepa que estamos enviando información en formato JSON
    header('Content-Type: application/json');

    //Datos necesarios para la conexión a la base de datos
    $servidor = "localhost";
    $usuario_db = "root";
    $contrasena_db = "";
    $base = "snake_eyes";

    $conexion = new mysqli($servidor, $usuario_db, $contrasena_db, $base);

    if ($conexion->connect_error) {
        die("La conexión a la base de datos ha fallado: " . $conexion->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['titulo']) && isset($_POST['motivo']) && isset($_POST['nombre_autor'])) {
        $id = $_POST['id'];
        $titulo = $_POST['titulo'];
        $motivo = $_POST['motivo'];
        $nombre_autor = $_POST['nombre_autor'];
        $fecha = date('Y-m-d H:i:s');

        //Introducimos los datos
        $query = "INSERT INTO reportes (publicacion_id, titulo, motivo, nombre_autor, fecha) 
            VALUES (?, ?, ?, ?, ?)";
        
        if ($statement = $conexion->prepare($query)) {
            $statement->bind_param("issss", $id, $titulo, $motivo, $nombre_autor, $fecha);

            if ($statement->execute()) {
                //Si la inserción es exitosa enviamos una respuesta JSON que lo indica
                echo json_encode(["status" => "success", "message" => "Reporte enviado con éxito"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al enviar el reporte"]);
            }
            $statement->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Error en la base de datos"]);
        }
        
    }

    //Salimos para evitar más salida que pueda interferir con la respuesta JSON
    exit;
?>
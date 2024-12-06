<?php
    if (isset($_POST['reporte_id'])) {
        //Obtenemos el ID del reporte
        $reporte_id = $_POST['reporte_id'];

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

        //Eliminamos el reporte de la base de datos
        $sql = "DELETE FROM reportes WHERE id = ?";
        $statement = $conexion->prepare($sql);
        $statement->bind_param("i", $reporte_id);

        if ($statement->execute()) {
            echo "Reporte eliminado correctamente.";
        } else {
            echo "Error al eliminar el reporte.";
        }

        //Cerramos el statement y la conexión
        $statement->close();
        $conexion->close();
    } else {
        echo "No se recibió el ID del reporte.";
    }
?>
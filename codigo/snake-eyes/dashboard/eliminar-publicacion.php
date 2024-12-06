<?php
    if (isset($_POST['publicacion_id'])) {
        //Obtenemos el ID de la publicación
        $publicacion_id = $_POST['publicacion_id'];
    
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
    
        //Iniciamos la transacción
        $conexion->begin_transaction();
    
        try {

            //Eliminamos registros en la tabla de los "me gustas" que hacen referencia a esta publicación
            $sql_gustas = "DELETE FROM gustas WHERE publicacion_id = ?";
            $statement_gustas = $conexion->prepare($sql_gustas);
            $statement_gustas->bind_param("i", $publicacion_id);
            $statement_gustas->execute();
    
            //Eliminamos registros en la tabla de los reportes que hacen referencia a esta publicación
            $sql_reportes = "DELETE FROM reportes WHERE publicacion_id = ?";
            $statement_reportes = $conexion->prepare($sql_reportes);
            $statement_reportes->bind_param("i", $publicacion_id);
            $statement_reportes->execute();
    
            //Eliminamos registros en la tabla de las publicaciones que hacen referencia a esta publicación
            $sql_publicacion = "DELETE FROM publicaciones WHERE id = ?";
            $statement_publicacion = $conexion->prepare($sql_publicacion);
            $statement_publicacion->bind_param("i", $publicacion_id);
            $statement_publicacion->execute();
    
            //Confirmamos la transacción
            $conexion->commit();
    
            echo "Publicación eliminada con éxito.";
    
        } catch (mysqli_sql_exception $e) {
            //Si algo falla, revertimos la transacción
            $conexion->rollback();
            echo "Error al eliminar la publicación: " . $e->getMessage();
        }
    
        //Cerramos todos los prepared statements y la conexión
        $statement_gustas->close();
        $statement_reportes->close();
        $statement_publicacion->close();
        $conexion->close();
    }
?>
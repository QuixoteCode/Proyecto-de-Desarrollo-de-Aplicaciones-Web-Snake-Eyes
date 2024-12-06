<?php
    //Datos necesarios para la conexión a la base de datos
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "snake_eyes";

    $conexion = new mysqli($host, $username, $password, $database);

    if ($conexion->connect_error) {
        die("Connection failed: " . $conexion->connect_error);
    }

    //Comprobamos si el formulario ha sido enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = $_POST['nombre'];
        $cantidad_existencias = $_POST['cantidad_existencias'];
        $precio = $_POST['precio'];
        $seccion = $_POST['seccion'];
        $descripcion = $_POST['descripcion'];

        //Nos hacemos cargo de la subida de la imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
            $imagen_data = file_get_contents($_FILES['imagen']['tmp_name']);
    
            //Preparamos la inserción del producto en la base de datos
            $statement = $conexion->prepare("INSERT INTO productos (nombre, cantidad_existencias, precio, seccion, descripcion, imagen) VALUES (?, ?, ?, ?, ?, ?)");
            //Usamos "s" debido a que son de tipo cadena ("string"), excepto $cantidad_existencias que es de tipo entero ("integer")
            $statement->bind_param("sissss", $nombre, $cantidad_existencias, $precio, $seccion, $descripcion, $imagen_data);

            //Ejecutamos y comprobamos el resultado
            if ($statement->execute()) {
                header('Location: dashboard.php');
            } else {
                echo "Error: " . $statement->error;
            }

            $statement->close();
        } else {
            echo "Error subiendo imagen.";
        }
    }

    $conexion->close();
?>
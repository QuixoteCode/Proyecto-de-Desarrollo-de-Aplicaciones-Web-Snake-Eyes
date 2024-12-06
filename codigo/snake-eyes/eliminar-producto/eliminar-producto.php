<?php
    session_start();

    $servidor = "localhost";
    $usuario_db = "root";
    $contrasena_db = "";
    $base = "snake_eyes";

    //Creamos la conexión a la base de datos
    $conexion = new mysqli($servidor, $usuario_db, $contrasena_db, $base);

    //Verificamos la conexión
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    //Verificamos que el formulario ha sido enviado y que el producto_id está presente
    if (isset($_POST['producto_id'])) {
        $producto_id = $_POST['producto_id'];

        //Eliminamos el producto de la tabla de los favoritos si está presente en esta
        $query_eliminar_favorito = "DELETE FROM favoritos WHERE producto_id = ?";
        $statement = $conexion->prepare($query_eliminar_favorito);
        $statement->bind_param("i", $producto_id);
        $statement->execute();

        //Eliminamos el producto del carrito si está presente en la sesión
        if (isset($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $key => $producto) {
                if ($producto['id'] == $producto_id) {
                    //Eliminamos el producto del carrito
                    unset($_SESSION['carrito'][$key]);
                    break;
                }
            }
        }

        //Finalmente, eliminamos el producto de la tabla de los productos
        $query_eliminar_producto = "DELETE FROM productos WHERE id = ?";
        $statement = $conexion->prepare($query_eliminar_producto);
        $statement->bind_param("i", $producto_id);
        $statement->execute();

        //Redirige al índice
        header('Location: ../index.php');

    } else {
        echo "ID del producto no recibido.";
    }
?>
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

    //Verificamos que el id del producto ha sido pasado por GET
    if (isset($_GET['producto_id'])) {
        $producto_id = $_GET['producto_id'];

        //Preparamos la consulta para obtener los datos del producto
        $statement = $conexion->prepare("SELECT id, nombre, precio FROM productos WHERE id = ?");
        if ($statement === false) {
            echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta: ' . $conexion->error]);
            exit();
        }
        $statement->bind_param("i", $producto_id);
        $statement->execute();
        $result = $statement->get_result();

        //Verificamos que el producto existe obteniendo un número de filas (rows) de respuesta que es mayor de cero
        if ($result->num_rows > 0) {
            $producto = $result->fetch_assoc();

            //Si el carrito no existe lo inicializamos
            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }

            //Añadimos el producto al carrito
            if (isset($_SESSION['carrito'][$producto_id])) {
                //Si el producto ya está en el carrito incrementamos la cantidad en uno
                $_SESSION['carrito'][$producto_id]['cantidad'] += 1;
            } else {
                //Si el producto no está ya en el carrito lo agregamos
                $_SESSION['carrito'][$producto_id] = [
                    'id' => $producto['id'],
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'cantidad' => 1
                ];
            }

            //Respondemos con éxito
            echo json_encode(['success' => true]);
        } else {
            //Si no podemos responder con éxito indicamos que el producto no ha sido encontrado
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        }   

        $statement->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'ID del producto no proporcionado']);
    }

    $conexion->close();
?>
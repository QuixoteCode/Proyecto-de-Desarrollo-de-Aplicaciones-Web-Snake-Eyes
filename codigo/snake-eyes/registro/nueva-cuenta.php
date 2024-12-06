<?php
    session_start();

    //Datos necesarios para la conexion a la base de datos
    $servidor = "localhost";
    $usuario_db = "root";
    $contrasena_db = "";
    $base = "snake_eyes";

    //Conexión a la base de datos
    $conexion = new mysqli($servidor, $usuario_db, $contrasena_db, $base);

    //Verificamos la conexión
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    //Datos obligatorios del usuario
    $nombre_usuario = $_POST['nombre_usuario'];
    $correo_usuario = $_POST['correo_usuario'];
    $contrasena_usuario = $_POST['contrasena_usuario'];
    //Creamos un hash seguro de la contraseña del usuario
    $contrasena_hash = password_hash($contrasena_usuario, PASSWORD_DEFAULT);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $plan = $_POST['plan'];
    $baneado = 0;

    //Datos opcionales del usuario
    $nombre_facturacion = isset($_POST['nombre_facturacion']) ? $_POST['nombre_facturacion'] : null;
    $apellidos_facturacion = isset($_POST['apellidos_facturacion']) ? $_POST['apellidos_facturacion'] : null;
    $provincia_facturacion = isset($_POST['provincia_facturacion']) ? $_POST['provincia_facturacion'] : null;
    $municipio_facturacion = isset($_POST['municipio_facturacion']) ? $_POST['municipio_facturacion'] : null;
    $calle_facturacion = isset($_POST['calle_facturacion']) ? $_POST['calle_facturacion'] : null;
    $numero_facturacion = isset($_POST['numero_facturacion']) ? $_POST['numero_facturacion'] : null;

    //Comprobamos si el nombre de usuario ya ha sido usado
    $query_usuario = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
    $statement_usuario = $conexion->prepare($query_usuario);
    $statement_usuario->bind_param("s", $nombre_usuario);
    $statement_usuario->execute();
    $resultado_usuario = $statement_usuario->get_result();

    //Comprobamos si el correo electrónico ya ha sido usado
    $query_correo = "SELECT * FROM usuarios WHERE correo_usuario = ?";
    $statement_correo = $conexion->prepare($query_correo);
    $statement_correo->bind_param("s", $correo_usuario);
    $statement_correo->execute();
    $resultado_correo = $statement_correo->get_result();

    //Si el nombre de usuario ya existe en la base de datos, mostramos un mensaje de error
    if ($resultado_usuario->num_rows > 0) {
        //Añadimos "window.location.href = 'registro.php';" para que no nos rediriga a nueva-cuenta.php, redirigiéndonos a la propia pestaña de registro, por si el usuario quiere continuar con este
        echo "<script>
            alert('El nombre de usuario ya está registrado. Por favor, elige otro.');
            window.location.href = 'registro.php';
        </script>";
        exit();
    }

    //Si el correo electrónico ya existe en la base de datos, mostramos un mensaje de error
    if ($resultado_correo->num_rows > 0) {
        //Añadimos "window.location.href = 'registro.php';" para que no nos rediriga a nueva-cuenta.php, redirigiéndonos a la propia pestaña de registro, por si el usuario quiere continuar con este
        echo "<script>
            alert('El correo electrónico ya está registrado. Por favor, utiliza otro.');
            window.location.href = 'registro.php'; 
        </script>";
        exit(); 
    }

    //Preparamos la consulta de inserción
    $statement = $conexion->prepare("INSERT INTO usuarios (nombre_usuario, correo_usuario, contrasena_usuario, fecha_nacimiento, nombre_facturacion, apellidos_facturacion, provincia_facturacion, municipio_facturacion, calle_facturacion, numero_facturacion, plan, baneado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    //Usamos "bind_param" para vincular variables a los marcadores de parámetros en la secuencia SQL preparada
    $statement->bind_param("ssssssssssss", $nombre_usuario, $correo_usuario, $contrasena_hash, $fecha_nacimiento, $nombre_facturacion, $apellidos_facturacion, $provincia_facturacion, $municipio_facturacion, $calle_facturacion, $numero_facturacion, $plan, $baneado);
    $statement->execute();

    //Obtenemos el ID del nuevo usuario para iniciar sesión con este
    $nuevo_usuario_id = $statement->insert_id;

    //Cerramos la conexión
    $statement->close();
    $conexion->close();

    //Iniciamos sesión con el ID previamente obtenido
    $_SESSION['user_id'] = $nuevo_usuario_id;

    //Redirigimos el usuario al índice de la página
    header('Location: ..\index.php');
    exit();

?>
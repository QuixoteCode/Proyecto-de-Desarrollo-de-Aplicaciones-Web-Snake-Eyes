<?php
    //Comenzamos la sesión al principio del documento
    session_start();

    //Inicializamos la cuenta del carrito
    $carrito_cantidad = isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0;

    //Manejo del logout
    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Favorito</title>
    <link rel="icon" href="../imagenes/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../recursos/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="favoritos.css">
</head>
<body class="bg-dark text-light">
    <nav class="navbar navbar-expand-lg bg-danger navbar-light fixed-top">
        <!--La "marca" de la barra de navegación-->
        <a class="navbar-brand" href="../index.php">
            <img src="../imagenes/logo.png" alt="Logo" id="logo-barra-navegacion" class="rounded">
        </a>
    
        <!--Necesario para que la barra de navegación se muestre como un botón desplegable cuando el ancho de la pantalla se reduce-->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    
        <section class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav">
                <li class="nav-item p-4">
                    <a class="nav-link" aria-current="page" href="../index.php">Página principal</a>
                </li>
                <li class="nav-item p-4">
                    <a class="nav-link" href="../foro/foro.php">Foro</a>
                </li>
                <li class="nav-item dropdown p-4">
                    <a class="nav-link" id="nav-cuenta" data-target="#" href="http://example.com/" data-toggle="dropdown"
                        role="button" aria-haspopup="true" aria-expanded="false">
                        Cuenta
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="nav-cuenta">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li><a href="../dashboard/dashboard.php" class="nav-link">Dashboard</a></li>
                            <li><a href="?logout" class="nav-link">Cerrar Sesión</a></li>
                        <?php else: ?>
                            <li><a href="../dashboard/dashboard.php" class="nav-link">Iniciar Sesión</a></li>
                            <li><a href="../registro/registro.php" class="nav-link">Registrarse</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item p-4">
                    <a class="nav-link" href="../acerca/acerca.php">Acerca de nosotros</a>
                </li>
                <li class="nav-item p-4">
                    <a class="nav-link" href="../contacto/contacto.php">Contacto</a>
                </li>
                <?php
                    //Creamos una variable "flag" para comprobar si el usuario no es oro
                    $usuario_no_es_oro = false;
                            
                    if (isset($_SESSION['user_id'])) {
                        //Datos necesarios para la conexion a la base de datos
                        $servidor = "localhost";
                        $usuario_db = "root";
                        $contrasena_db = "";
                        $base = "snake_eyes";

                        $conexion_es_oro = new mysqli($servidor, $usuario_db, $contrasena_db, $base);

                        //Verificamos la conexión
                        if ($conexion_es_oro->connect_error) {
                            die("Conexión de comprobación de si el usuario es oro fallida: " . $conexion->connect_error);
                        }

                        //Obtenemos el id del usuario de los datos de sesión
                        $user_id = $_SESSION['user_id'];

                        //Realizamos la consulta para ese id
                        $sql_es_oro = "SELECT plan FROM usuarios WHERE id = ?";
                        $statement_es_oro = $conexion_es_oro->prepare($sql_es_oro);
                        $statement_es_oro->bind_param("i", $user_id);
                        $statement_es_oro->execute();
                        $resultado_es_oro = $statement_es_oro->get_result();

                        if ($resultado_es_oro->num_rows > 0) {
                            $fila_es_oro = $resultado_es_oro->fetch_assoc();
                            $plan_es_oro = $fila_es_oro['plan'];
                
                            //Si el usuario es plata o bronce actualizamos la variable "flag"
                            if ($plan_es_oro == "plata" || $plan_es_oro == "bronce") {
                                $usuario_no_es_oro = true;
                            }
                        }
                        
                        //Liberamos la memoria de los resultados, seguidamente cerramos el statement y la conexión
                        $resultado_es_oro->free();
                        $statement_es_oro->close();
                        $conexion_es_oro->close();
                    }

                    if ($usuario_no_es_oro) {
                ?>
                    <li class="nav-item dropdown p-4">
                        <a class="btn btn-dark nav-link" id="nav-notificacion" data-target="#" href="http://example.com/" data-toggle="dropdown"
                            role="button" aria-haspopup="true" aria-expanded="false">
                            <!--Icono obtenido de https://icons.getbootstrap.com/icons/bell/-->
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(220, 53, 69)"
                                class="bi bi-bell" viewBox="0 0 16 16">
                                <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
                            </svg>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="nav-notificacion">
                            <li><a class="dropdown-item" href="../cambiar-plan/cambiar-plan.php"><img src="../imagenes/notificacion.png" alt="Oferta" height="333"></a></li>
                        </ul>
                    </li>
                <?php
                    }
                ?>
                <li class="nav-item p-4">
                    <a href="../carrito/carrito.php" class="nav-link ml-5">
                        <!--Icono obtenido de https://icons.getbootstrap.com/icons/cart-fill/-->
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-cart-fill" viewBox="0 0 16 16">
                            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                        </svg> (<?php echo $carrito_cantidad; ?>)
                    </a>
                </li>
            </ul>
            
        </section>
    </nav>

    <!--Es necesario envolver al contenido en dos secciones para que las clases no afecten al cálculo del padding-->
    <section id="contenido">
        <section class="p-4">
            <?php
                //Verificamos que el usuario tenga sesión iniciada
                if (!isset($_SESSION['user_id'])) {
                    echo "<p>Por favor, <a href='../dashboard/dashboard.php'>inicia sesión</a> para eliminar este producto de favoritos.</p>";
                }else{

                    //Datos necesarios para la conexion a la base de datos
                    $servidor = "localhost";
                    $usuario_db = "root";
                    $contrasena_db = "";
                    $base = "snake_eyes";
                   
                    $conexion = new mysqli($servidor, $usuario_db, $contrasena_db, $base);

                    if ($conexion->connect_error) {
                        die("Conexión fallida: " . $conexion->connect_error);
                    }

                    //Obtenemos el ID del usuario y del producto
                    $usuario_id = $_SESSION['user_id'];
                    $producto_id = $_POST['producto_id'];

                    //Eliminamos el producto de favoritos
                    $sql = "DELETE FROM favoritos WHERE usuario_id = ? AND producto_id = ?";
                    $statement = $conexion->prepare($sql);
                    //Usamos "i" debido a que son de tipo entero ("integer")
                    $statement->bind_param("ii", $usuario_id, $producto_id);

                    if ($statement->execute()) {
                        echo "<p>Producto eliminado de favoritos con éxito.</p>";
                    } else {
                        echo "<p>Error al eliminar el producto de favoritos.</p>";
                    }

                    $statement->close();
                    $conexion->close();
                }
            ?>
            <br>
            <section class="d-flex justify-content-center align-items-center m-2">
                <button id="botonVerFavoritos" class="btn btn-dark btn-lg" onclick="verFavoritos()">Ver tus productos favoritos</button>
            </section>
        </section>
    </section>

    <footer class="text-white bg-dark text-center p-4 fixed-bottom footer">Snake Eyes, marca no registrada<br>
        Proyecto de índole educativa, uso justo del material con propiedad intelectual según el derecho de cita:
        reproducción de imágenes y textos con fines educativos<br>
        Lucas Fernández, licencia "Attribution-NonCommercial 4.0 International"<br>

        <!--Las clases "d-flex" y "justify-content-center" son necesarias para que el contenido de la siguiente fila aparezca centrado-->
        <section class="row d-flex justify-content-center my-2">
            <a class="col-2" href="../cookies-y-privacidad/cookies-y-privacidad.php#aviso-legal">Aviso Legal</a>
            <span class="col-2">|</span>
            <a class="col-2" href="../cookies-y-privacidad/cookies-y-privacidad.php#mas-info">Política de Cookies</a>
        </section>
        <section class="row d-flex justify-content-center my-2">
            <a class="col-2" href="../cookies-y-privacidad/cookies-y-privacidad.php#politica-privacidad">Política de Privacidad</a>
            <span class="col-2">|</span>
            <a class="col-2" href="../terminos-uso/terminos-uso.php">Términos de Uso</a>
        </section>
    </footer>
    
    <script src="../recursos/jquery/jquery-3.3.1.slim.min.js"></script>
    <script src="../recursos/popper/popper.min.js"></script>
    <script src="../recursos/bootstrap/bootstrap.min.js"></script>

    <script src="favoritos.js"></script>
</body>
</html>
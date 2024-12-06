<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snake Eyes</title>
    <link rel="icon" href="imagenes/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="recursos/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="index.css">
</head>
<?php
    session_start();

    //Permite cerrar sesión verificando si el usuario ha hecho clic en "Cerrar sesión"
    if (isset($_GET['logout'])) { 
        session_destroy();
        //Recargamos la página para que el cierre de sesión se haga efectivo
        header("Location: index.php");
        exit();
    }

    //Inicializamos la cuenta del carrito
    $carrito_cantidad = isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0;

    //Verificar si el usuario es administrador (mediante su ID fija, siendo 1), necesario para mostrar el formulario de eliminar productos
    function isAdmin() {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] === 1;
    }
?>

<body class="bg-secondary">

    <!--La clase "fixed-top" nos permite hacer que la barra de navegación esté pegada al tope de la página sin importar cuanto scrolling haga el usuario-->
    <nav class="navbar navbar-expand-lg bg-danger navbar-light fixed-top">
        <!--La "marca" de la barra de navegación-->
        <a class="navbar-brand" href="#">
            <img src="imagenes/logo.png" alt="Logo" id="logo-barra-navegacion" class="rounded">
        </a>

        <!--Necesario para que la barra de navegación se muestre como un botón desplegable cuando el ancho de la pantalla se reduce-->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <section class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav">
                <!--El span con la clase "sr-only" sólo será mostrado en lectores de pantallas, como puede ser JAWS-->
                <li class="nav-item p-4">
                    <a class="nav-link active" aria-current="page" href="#">Página principal
                        <span class="sr-only">(actual)</span></a>
                </li>
                <li class="nav-item p-4">
                    <a class="nav-link" href="foro/foro.php">Foro</a>
                </li>
                <li class="nav-item dropdown p-4">
                    <a class="nav-link" id="nav-cuenta" data-target="#" href="http://example.com/" data-toggle="dropdown"
                        role="button" aria-haspopup="true" aria-expanded="false">
                        Cuenta
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="nav-cuenta">
                        <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="dashboard/dashboard.php" class="nav-link">Dashboard</a></li>
                        <li><a href="?logout" class="nav-link">Cerrar Sesión</a></li>
                        <?php else: ?>
                        <li><a href="dashboard/dashboard.php" class="nav-link">Iniciar Sesión</a></li>
                        <li><a href="registro/registro.php" class="nav-link">Registrarse</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item p-4">
                    <a class="nav-link" href="acerca/acerca.php">Acerca de nosotros</a>
                </li>
                <li class="nav-item p-4">
                    <a class="nav-link" href="contacto/contacto.php">Contacto</a>
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
                            <li><a class="dropdown-item" href="cambiar-plan/cambiar-plan.php"><img src="imagenes/notificacion.png" alt="Oferta" height="333"></a></li>
                        </ul>
                    </li>
                <?php
                    }
                ?>
                <li class="nav-item p-4">
                    <a href="carrito/carrito.php" class="nav-link ml-5">
                        <!--Icono obtenido de https://icons.getbootstrap.com/icons/cart-fill/-->
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                            class="bi bi-cart-fill" viewBox="0 0 16 16">
                            <path
                                d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                        </svg> (<?php echo $carrito_cantidad; ?>)
                    </a>
                </li>
            </ul>

        </section>
    </nav>

    <!--"&#128293;" es el código de entidad decimal del emoji de la llama de fuego-->
    <section class="text-center text-white h1" id="populares">&#128293; Las ofertas más populares &#128293;</section>

    <?php
        $servidor = "localhost";
        $usuario_db = "root";
        $contrasena_db = "";
        $base = "snake_eyes";

        //Creamos la conexión a la base de datos
        $conexion = new mysqli($servidor, $usuario_db, $contrasena_db, $base);


        //Comprobamos la conexión
        if ($conexion->connect_error) {
            die("Connection failed: " . $conexion->connect_error);
        }

        //Query para obtener las filas de la tabla de los productos
        $sql = "SELECT id, nombre, cantidad_existencias, precio, imagen FROM productos WHERE seccion = 'populares'";
        $resultado = $conexion->query($sql);

        //Comprobamos si hay filas
        if ($resultado->num_rows > 0) {
            //Introducimos las filas en un array
            $productos = $resultado->fetch_all(MYSQLI_ASSOC);

            //Recorremos el array
            for ($i = 0; $i < count($productos); $i++) {
                //Si el índice es divisible por 3 creamos una nueva fila. Dependiendo de la anchura del dispositivo se puede cumplir o no que esto se limite a ser una sola fila, para más información véasen las clases de Bootstrap como "col-md-6" y "col-lg-4"
                if ($i % 3 == 0) {
                    echo '<section class="row w-100">';
                }
                //Si el índice es par o impar mostraremos unos colores u otros (véanse las clases de Bootstrap)
                if ($i % 2 == 0) {
                    echo '<section class="col-md-6 col-lg-4 bg-dark p-4 text-center">';
                    echo '<section class="card bg-danger">';
                } else {
                    echo '<section class="col-md-6 col-lg-4 bg-danger p-4 text-center">';
                    echo '<section class="card bg-dark">';
                }

                $imagen_binario = $productos[$i]['imagen'];
                if (!empty($imagen_binario)) {
                    //Convertimos el binario de la imagen a base64
                    $imagen_base_64 = base64_encode($imagen_binario);

                    //Obtenemos el tipo MIME de la imagen
                    $informacion_imagen = finfo_open(FILEINFO_MIME_TYPE);
                    $tipo_imagen = finfo_buffer($informacion_imagen, $imagen_binario);
                    finfo_close($informacion_imagen);

                    //Mostramos la imagen según su tipo MIME e incluimos un enlace a la página de ver-producto
                    echo "<a href='ver-producto/ver-producto.php?id=" . $productos[$i]['id'] . "'><img src='data:$tipo_imagen;base64," . $imagen_base_64 . "' alt='Imagen de producto' class='card-img-top'></a><br>";
                } else {
                    echo "No hay una imagen disponible.<br>";
                }

                echo '<section class="card-body">'; 
    
                //Dependiendo de si el índice es par o impar se mostrarán los elementos con un color u otro (véanse las clases de Bootstrap)
                if ($i % 2 == 0) {
                    //Aquellos productos que tengan unas existencias iguales a cero no podrán ser añadidos al carrito y se mostrará un "banner" que indica que no hay existencias
                    if ($productos[$i]['cantidad_existencias'] == 0) {
                        echo '<section class="todo-vendido">Todo Vendido</section>';
                    }else{
                        //Icono obtenido de: https://icons.getbootstrap.com/icons/basket-fill
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="rgb(52, 58, 64)" class="bi bi-basket-fill" viewBox="0 0 16 16" onclick="anadirCarrito(' . $productos[$i]['id'] . ')">
                            <path d="M5.071 1.243a.5.5 0 0 1 .858.514L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 6h1.717zM3.5 10.5a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0z"/>
                        </svg><br>';
                    }

                    //Mostramos el nombre del producto con un enlace a la página de ver producto
                    echo '<a href="ver-producto/ver-producto.php?id=' . $productos[$i]['id'] . '" class="h4 card-title nombre-producto text-dark">' . $productos[$i]['nombre'] . '</a>';
    
                    //Solo el admin tendrá la capacidad de borrar productos
                    if (isset($_SESSION['user_id']) && isAdmin()){
                        //Icono obtenido de https://icons.getbootstrap.com/icons/x/
                        echo '<form action="eliminar-producto/eliminar-producto.php" method="post" class="formulario-eliminar">
                            <input type="hidden" name="producto_id" value="' . $productos[$i]['id'] . '">
                            <button type="submit" title="Eliminar producto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(52, 58, 64)" class="bi bi-x" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                            </button>
                        </form>';
                    }

                    //Icono obtenido de https://icons.getbootstrap.com/icons/heart/
                    echo '<form action="favoritos/agregar-favorito.php" method="post" class="formulario-favoritos">
                        <input type="hidden" name="producto_id" value="' . $productos[$i]['id'] . '">
                        <button type="submit" title="Añadir a favoritos">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(52, 58, 64)" class="bi bi-heart" viewBox="0 0 16 16">
                                <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                            </svg>
                        </button>
                    </form>';

                    //Mostramos el precio del producto
                    echo '<p class="h4 card-text text-dark">' . $productos[$i]['precio'] .'</p>';

                }else{
                    //Aquellos productos que tengan unas existencias iguales a cero no podrán ser añadidos al carrito y se mostrará un "banner" que indica que no hay existencias
                    if ($productos[$i]['cantidad_existencias'] == 0) {
                        echo '<section class="todo-vendido">Todo Vendido</section>';
                    }else{
                        //Icono obtenido de: https://icons.getbootstrap.com/icons/basket-fill
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="rgb(220, 53, 69)" class="bi bi-basket-fill" viewBox="0 0 16 16" onclick="anadirCarrito(' . $productos[$i]['id'] . ')">
                            <path d="M5.071 1.243a.5.5 0 0 1 .858.514L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 6h1.717zM3.5 10.5a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0z"/>
                        </svg><br>';
                    }

                    //Mostramos el nombre del producto con un enlace a la página de ver producto
                    echo '<a href="ver-producto/ver-producto.php?id=' . $productos[$i]['id'] . '" class="h4 card-title nombre-producto text-danger">' . $productos[$i]['nombre'] . '</a>';

                    //Solo el admin tendrá la capacidad de borrar productos
                    if (isset($_SESSION['user_id']) && isAdmin()){
                        //Icono obtenido de https://icons.getbootstrap.com/icons/x/
                        echo '<form action="eliminar-producto/eliminar-producto.php" method="post" class="formulario-eliminar">
                            <input type="hidden" name="producto_id" value="' . $productos[$i]['id'] . '">
                            <button type="submit" title="Eliminar producto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(220, 53, 69)" class="bi bi-x" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                            </button>
                        </form>';
                    }

                    //Icono obtenido de https://icons.getbootstrap.com/icons/heart/
                    echo '<form action="favoritos/agregar-favorito.php" method="post" class="formulario-favoritos">
                        <input type="hidden" name="producto_id" value="' . $productos[$i]['id'] . '">
                        <button type="submit" title="Añadir a favoritos">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(220, 53, 69)" class="bi bi-heart" viewBox="0 0 16 16">
                                <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                            </svg>
                        </button>
                    </form>'; 

                    //Mostramos el precio del producto
                    echo '<p class="h4 card-text text-danger">' . $productos[$i]['precio'] . '</p>';
                }
        
                echo '</section></section></section>';

                //Cerramos la fila cuando se llega al tercer elemento en esta
                if ($i % 3 == 2) {
                    echo '</section>';
                }

            }

            //Si el último producto no cerró la fila, la cerramos aquí
            if (count($productos) % 3 != 0) {
                echo '</section>';
            }

        } else {
            echo "No se han encontrado productos.";
        }
    ?>

    <!--"&#9200;" es el código de entidad decimal del emoji del reloj con despertador-->
    <section class="text-center text-white h1 w-100" id="ultimos-lanzamientos">&#9200; Los últimos lanzamientos &#9200;</section>

    <?php
        //Query para obtener las filas de la tabla de los productos
        $sql = "SELECT id, nombre, cantidad_existencias, precio, imagen FROM productos WHERE seccion = 'ultimos_lanzamientos'";
        $resultado = $conexion->query($sql);

        //Comprobamos si hay filas
        if ($resultado->num_rows > 0) {
            //Introducimos las filas en un array
            $productos = $resultado->fetch_all(MYSQLI_ASSOC);

            //Recorremos el array
            for ($i = 0; $i < count($productos); $i++) {
                //Si el índice es divisible por 3 creamos una nueva fila. Dependiendo de la anchura del dispositivo se puede cumplir o no que esto se limite a ser una sola fila, para más información véasen las clases de Bootstrap como "col-md-6" y "col-lg-4"
                if ($i % 3 == 0) {
                    echo '<section class="row w-100">';
                }
                //Si el índice es par o impar mostraremos unos colores u otros (véanse las clases de Bootstrap)
                if ($i % 2 == 0) {
                    echo '<section class="col-md-6 col-lg-4 bg-dark p-4 text-center">';
                    echo '<section class="card bg-danger">';
                } else {
                    echo '<section class="col-md-6 col-lg-4 bg-danger p-4 text-center">';
                    echo '<section class="card bg-dark">';
                }

                $imagen_binario = $productos[$i]['imagen'];
                if (!empty($imagen_binario)) {
                    //Convertimos el binario de la imagen a base64
                    $imagen_base_64 = base64_encode($imagen_binario);

                    //Obtenemos el tipo MIME de la imagen y la mostramos según este
                    $informacion_imagen = finfo_open(FILEINFO_MIME_TYPE);
                    $tipo_imagen = finfo_buffer($informacion_imagen, $imagen_binario);
                    finfo_close($informacion_imagen);

                    //Mostramos la imagen según su tipo MIME e incluimos un enlace a la página de ver-producto
                    echo "<a href='ver-producto/ver-producto.php?id=" . $productos[$i]['id'] . "'><img src='data:$tipo_imagen;base64," . $imagen_base_64 . "' alt='Imagen de producto' class='card-img-top'></a><br>";
                } else {
                    echo "No hay una imagen disponible.<br>";
                }

                echo '<section class="card-body">'; 
    
                //Dependiendo de si el índice es par o impar se mostrarán los elementos con un color u otro (véanse las clases de Bootstrap)
                if ($i % 2 == 0) {
                    //Aquellos productos que tengan unas existencias iguales a cero no podrán ser añadidos al carrito y se mostrará un "banner" que indica que no hay existencias
                    if ($productos[$i]['cantidad_existencias'] == 0) {
                        echo '<section class="todo-vendido">Todo Vendido</section>';
                    }else{
                        //Icono obtenido de: https://icons.getbootstrap.com/icons/basket-fill
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="rgb(52, 58, 64)" class="bi bi-basket-fill" viewBox="0 0 16 16" onclick="anadirCarrito(' . $productos[$i]['id'] . ')">
                            <path d="M5.071 1.243a.5.5 0 0 1 .858.514L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 6h1.717zM3.5 10.5a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0z"/>
                        </svg><br>';
                    }

                    //Mostramos el nombre del producto con un enlace a la página de ver producto
                    echo '<a href="ver-producto/ver-producto.php?id=' . $productos[$i]['id'] . '" class="h4 card-title nombre-producto text-dark">' . $productos[$i]['nombre'] . '</a>';
    
                    //Solo el admin tendrá la capacidad de borrar productos
                    if (isset($_SESSION['user_id']) && isAdmin()){
                        //Icono obtenido de https://icons.getbootstrap.com/icons/x/
                        echo '<form action="eliminar-producto/eliminar-producto.php" method="post" class="formulario-eliminar">
                            <input type="hidden" name="producto_id" value="' . $productos[$i]['id'] . '">
                            <button type="submit" title="Eliminar producto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(52, 58, 64)" class="bi bi-x" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                            </button>
                        </form>';
                    }

                    //Icono obtenido de https://icons.getbootstrap.com/icons/heart/
                    echo '<form action="favoritos/agregar-favorito.php" method="post" class="formulario-favoritos">
                        <input type="hidden" name="producto_id" value="' . $productos[$i]['id'] . '">
                        <button type="submit" title="Añadir a favoritos">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(52, 58, 64)" class="bi bi-heart" viewBox="0 0 16 16">
                                <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                            </svg>
                        </button>
                    </form>';

                    //Mostramos el precio del producto
                    echo '<p class="h4 card-text text-dark">' . $productos[$i]['precio'] .'</p>';

                }else{
                    //Aquellos productos que tengan unas existencias iguales a cero no podrán ser añadidos al carrito y se mostrará un "banner" que indica que no hay existencias
                    if ($productos[$i]['cantidad_existencias'] == 0) {
                        echo '<section class="todo-vendido">Todo Vendido</section>';
                    }else{
                        //Icono obtenido de: https://icons.getbootstrap.com/icons/basket-fill
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="rgb(220, 53, 69)" class="bi bi-basket-fill" viewBox="0 0 16 16" onclick="anadirCarrito(' . $productos[$i]['id'] . ')">
                            <path d="M5.071 1.243a.5.5 0 0 1 .858.514L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 6h1.717zM3.5 10.5a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0z"/>
                        </svg><br>';
                    }

                    //Mostramos el nombre del producto con un enlace a la página de ver producto
                    echo '<a href="ver-producto/ver-producto.php?id=' . $productos[$i]['id'] . '" class="h4 card-title nombre-producto text-danger">' . $productos[$i]['nombre'] . '</a>';

                    //Solo el admin tendrá la capacidad de borrar productos
                    if (isset($_SESSION['user_id']) && isAdmin()){
                        //Icono obtenido de https://icons.getbootstrap.com/icons/x/
                        echo '<form action="eliminar-producto/eliminar-producto.php" method="post" class="formulario-eliminar">
                            <input type="hidden" name="producto_id" value="' . $productos[$i]['id'] . '">
                            <button type="submit" title="Eliminar producto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(220, 53, 69)" class="bi bi-x" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                            </button>
                        </form>';
                    }

                    //Icono obtenido de https://icons.getbootstrap.com/icons/heart/
                    echo '<form action="favoritos/agregar-favorito.php" method="post" class="formulario-favoritos">
                        <input type="hidden" name="producto_id" value="' . $productos[$i]['id'] . '">
                        <button type="submit" title="Añadir a favoritos">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(220, 53, 69)" class="bi bi-heart" viewBox="0 0 16 16">
                                <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                            </svg>
                        </button>
                    </form>'; 

                    //Mostramos el precio del producto
                    echo '<p class="h4 card-text text-danger">' . $productos[$i]['precio'] . '</p>';
                }
        
                echo '</section></section></section>';

                //Cerramos la fila cuando se llega al tercer elemento en esta
                if ($i % 3 == 2) {
                    echo '</section>';
                }

            }

            //Si el último producto no cerró la fila, la cerramos aquí
            if (count($productos) % 3 != 0) {
                echo '</section>';
            }

        } else {
            echo "No se han encontrado productos.";
        }
    ?>

    <!--"&#10084;" (y también "&#65039;") es el código de entidad decimal del emoji del corazón rojo-->
    <section class="text-center text-white h1 w-100" id="recomendados">&#10084; Puede que también te guste... &#10084;</section>

    <?php
        //Query para obtener las filas de la tabla de los productos
        $sql = "SELECT id, nombre, cantidad_existencias, precio, imagen FROM productos WHERE seccion = 'recomendados'";
        $resultado = $conexion->query($sql);

        //Comprobamos si hay filas
        if ($resultado->num_rows > 0) {
            //Introducimos las filas en un array
            $productos = $resultado->fetch_all(MYSQLI_ASSOC);

            //Recorremos el array
            for ($i = 0; $i < count($productos); $i++) {
                //Si el índice es divisible por 3 creamos una nueva fila. Dependiendo de la anchura del dispositivo se puede cumplir o no que esto se limite a ser una sola fila, para más información véasen las clases de Bootstrap como "col-md-6" y "col-lg-4"
                if ($i % 3 == 0) {
                    echo '<section class="row w-100">';
                }
                //Si el índice es par o impar mostraremos unos colores u otros (véanse las clases de Bootstrap)
                if ($i % 2 == 0) {
                    echo '<section class="col-md-6 col-lg-4 bg-dark p-4 text-center">';
                    echo '<section class="card bg-danger">';
                } else {
                    echo '<section class="col-md-6 col-lg-4 bg-danger p-4 text-center">';
                    echo '<section class="card bg-dark">';
                }

                $imagen_binario = $productos[$i]['imagen'];
                if (!empty($imagen_binario)) {
                    //Convertimos el binario de la imagen a base64
                    $imagen_base_64 = base64_encode($imagen_binario);

                    //Obtenemos el tipo MIME de la imagen y la mostramos según este
                    $informacion_imagen = finfo_open(FILEINFO_MIME_TYPE);
                    $tipo_imagen = finfo_buffer($informacion_imagen, $imagen_binario);
                    finfo_close($informacion_imagen);
                    
                    //Mostramos la imagen según su tipo MIME e incluimos un enlace a la página de ver-producto
                    echo "<a href='ver-producto/ver-producto.php?id=" . $productos[$i]['id'] . "'><img src='data:$tipo_imagen;base64," . $imagen_base_64 . "' alt='Imagen de producto' class='card-img-top'></a><br>";
                } else {
                    echo "No hay una imagen disponible.<br>";
                }

                echo '<section class="card-body">'; 
    
                //Dependiendo de si el índice es par o impar se mostrarán los elementos con un color u otro (véanse las clases de Bootstrap)
                if ($i % 2 == 0) {
                    //Aquellos productos que tengan unas existencias iguales a cero no podrán ser añadidos al carrito y se mostrará un "banner" que indica que no hay existencias
                    if ($productos[$i]['cantidad_existencias'] == 0) {
                        echo '<section class="todo-vendido">Todo Vendido</section>';
                    }else{
                        //Icono obtenido de: https://icons.getbootstrap.com/icons/basket-fill
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="rgb(52, 58, 64)" class="bi bi-basket-fill" viewBox="0 0 16 16" onclick="anadirCarrito(' . $productos[$i]['id'] . ')">
                            <path d="M5.071 1.243a.5.5 0 0 1 .858.514L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 6h1.717zM3.5 10.5a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0z"/>
                        </svg><br>';
                    }

                    //Mostramos el nombre del producto con un enlace a la página de ver producto
                    echo '<a href="ver-producto/ver-producto.php?id=' . $productos[$i]['id'] . '" class="h4 card-title nombre-producto text-dark">' . $productos[$i]['nombre'] . '</a>';
    
                    //Solo el admin tendrá la capacidad de borrar productos
                    if (isset($_SESSION['user_id']) && isAdmin()){
                        //Icono obtenido de https://icons.getbootstrap.com/icons/x/
                        echo '<form action="eliminar-producto/eliminar-producto.php" method="post" class="formulario-eliminar">
                            <input type="hidden" name="producto_id" value="' . $productos[$i]['id'] . '">
                            <button type="submit" title="Eliminar producto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(52, 58, 64)" class="bi bi-x" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                            </button>
                        </form>';
                    }

                    //Icono obtenido de https://icons.getbootstrap.com/icons/heart/
                    echo '<form action="favoritos/agregar-favorito.php" method="post" class="formulario-favoritos">
                        <input type="hidden" name="producto_id" value="' . $productos[$i]['id'] . '">
                        <button type="submit" title="Añadir a favoritos">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(52, 58, 64)" class="bi bi-heart" viewBox="0 0 16 16">
                                <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                            </svg>
                        </button>
                    </form>';

                    //Mostramos el precio del producto
                    echo '<p class="h4 card-text text-dark">' . $productos[$i]['precio'] .'</p>';

                }else{
                    //Aquellos productos que tengan unas existencias iguales a cero no podrán ser añadidos al carrito y se mostrará un "banner" que indica que no hay existencias
                    if ($productos[$i]['cantidad_existencias'] == 0) {
                        echo '<section class="todo-vendido">Todo Vendido</section>';
                    }else{
                        //Icono obtenido de: https://icons.getbootstrap.com/icons/basket-fill
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="rgb(220, 53, 69)" class="bi bi-basket-fill" viewBox="0 0 16 16" onclick="anadirCarrito(' . $productos[$i]['id'] . ')">
                            <path d="M5.071 1.243a.5.5 0 0 1 .858.514L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 6h1.717zM3.5 10.5a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0zm2.5 0a.5.5 0 1 0-1 0v3a.5.5 0 0 0 1 0z"/>
                        </svg><br>';
                    }

                    //Mostramos el nombre del producto con un enlace a la página de ver producto
                    echo '<a href="ver-producto/ver-producto.php?id=' . $productos[$i]['id'] . '" class="h4 card-title nombre-producto text-danger">' . $productos[$i]['nombre'] . '</a>';

                    //Solo el admin tendrá la capacidad de borrar productos
                    if (isset($_SESSION['user_id']) && isAdmin()){
                        //Icono obtenido de https://icons.getbootstrap.com/icons/x/
                        echo '<form action="eliminar-producto/eliminar-producto.php" method="post" class="formulario-eliminar">
                            <input type="hidden" name="producto_id" value="' . $productos[$i]['id'] . '">
                            <button type="submit" title="Eliminar producto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(220, 53, 69)" class="bi bi-x" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                            </button>
                        </form>';
                    }

                    //Icono obtenido de https://icons.getbootstrap.com/icons/heart/
                    echo '<form action="favoritos/agregar-favorito.php" method="post" class="formulario-favoritos">
                        <input type="hidden" name="producto_id" value="' . $productos[$i]['id'] . '">
                        <button type="submit" title="Añadir a favoritos">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(220, 53, 69)" class="bi bi-heart" viewBox="0 0 16 16">
                                <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                            </svg>
                        </button>
                    </form>'; 

                    //Mostramos el precio del producto
                    echo '<p class="h4 card-text text-danger">' . $productos[$i]['precio'] . '</p>';
                }
        
                echo '</section></section></section>';

                //Cerramos la fila cuando se llega al tercer elemento en esta
                if ($i % 3 == 2) {
                    echo '</section>';
                }

            }

            //Si el último producto no cerró la fila, la cerramos aquí
            if (count($productos) % 3 != 0) {
                echo '</section>';
            }

        } else {
            echo "No se han encontrado productos.";
        }

        $conexion->close();
    ?>

    <!--Pop up de cookies y política de privacidad-->
    <section class="text-center m-2 p-2 bg-light fixed-bottom" id="barra-cookies-index">
        Tomate un momento para ver nuestra política de cookies y de privacidad:
        <br>
        Nosotros y nuestros socios utilizamos cookies o tecnologías similares para almacenar, acceder y procesar datos
        personales de tu visita a esta app.
        <br>
        Esta información incluye tu nombre y tu dirección geográfica entre otros.
        <br>
        Puedes aceptar, rechazar o configurar el uso de cookies pulsando en los botones correspondientes y siempre
        podrás modificar tu elección en Configuración de privacidad / Cookies, la cual recomendamos encarecidamente que
        leas.
        <br><br>
        <a id="btn-aceptar-cookies" class="link-opacity-75-hover btn-dark p-2 m-5" href="cookies-y-privacidad/cookies-y-privacidad.php#aceptar"
            target="_blank">Aceptar</a>
        <br><br>
        <a class="link-opacity-75-hover btn-dark p-2 m-5" href="cookies-y-privacidad/cookies-y-privacidad.php#mas-info"
            target="_blank">Más info Política de Cookies.</a>
        <br><br>
        <a class="link-opacity-75-hover btn-dark p-2 m-5" href="cookies-y-privacidad/cookies-y-privacidad.php#socios"
            target="_blank">Ver listado de socios (2)</a>
    </section>

    <footer class="text-white bg-dark text-center p-4 w-100">Snake Eyes, marca no registrada<br>
        Proyecto de índole educativa, uso justo del material con propiedad intelectual según el derecho de cita:
        reproducción de imágenes y textos con fines educativos<br>
        Lucas Fernández, licencia "Attribution-NonCommercial 4.0 International"<br>

        <!--Las clases "d-flex" y "justify-content-center" son necesarias para que el contenido de la siguiente fila aparezca centrado-->
        <section class="row d-flex justify-content-center my-2">
            <a class="col-2" href="cookies-y-privacidad/cookies-y-privacidad.php#aviso-legal">Aviso Legal</a>
            <span class="col-2">|</span>
            <a class="col-2" href="cookies-y-privacidad/cookies-y-privacidad.php#mas-info">Política de Cookies</a>
        </section>
        <section class="row d-flex justify-content-center my-2">
            <a class="col-2" href="cookies-y-privacidad/cookies-y-privacidad.php#politica-privacidad">Política de
                Privacidad</a>
            <span class="col-2">|</span>
            <a class="col-2" href="terminos-uso/terminos-uso.php">Términos de Uso</a>
        </section>
    </footer>

    <script src="recursos/jquery/jquery-3.3.1.slim.min.js"></script>
    <script src="recursos/popper/popper.min.js"></script>
    <script src="recursos/bootstrap/bootstrap.min.js"></script>

    <script src="index.js"></script>
</body>
</html>
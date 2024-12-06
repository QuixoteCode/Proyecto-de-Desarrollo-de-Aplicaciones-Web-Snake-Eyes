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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago</title>
    <link rel="icon" href="../imagenes/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../recursos/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="carrito.css"> 
</head>
<body class="bg-dark text-white">
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
                    <a class="nav-link" aria-current="page" href="../index.php">Página principal
                        </a>
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
                    <a href="carrito.php" class="nav-link ml-5">
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
        <section class="col-12 col-sm-6">
            <?php
                if (isset($_SESSION['user_id'])) {
                    //Datos necesarios para la conexion a la base de datos
                    $servidor = "localhost";
                    $usuario_db = "root";
                    $contrasena_db = "";
                    $base = "snake_eyes";

                    //Conexión a la base de datos
                    $conexion = new mysqli($servidor, $usuario_db, $contrasena_db, $base);
        
                    //Verificamos la conexión
                    if ($conexion->connect_error) {
                        die("Error de conexión: " . $conexion->connect_error);
                    }
        
                    //Realizamos la consulta para obtener los datos del usuario
                    $usuario_id = $_SESSION['user_id'];
                    $sql = "SELECT nombre_facturacion, apellidos_facturacion, provincia_facturacion, municipio_facturacion, calle_facturacion, numero_facturacion FROM usuarios WHERE id = ?";
                    $statement = $conexion->prepare($sql);
                    $statement->bind_param("i", $usuario_id);
                    $statement->execute();
                    $resultado = $statement->get_result();
    
                    //Inicializamos las variables
                    $nombre_facturacion = '';
                    $apellidos_facturacion = '';
                    $provincia_facturacion = '';
                    $municipio_facturacion = '';
                    $calle_facturacion = '';
                    $numero_facturacion = '';
    
                    if ($resultado->num_rows > 0) {
                        //En caso de que el usuario tenga datos guardados en la base de datos, los sacamos
                        $fila = $resultado->fetch_assoc();
                        $nombre_facturacion = $fila['nombre_facturacion'];
                        $apellidos_facturacion = $fila['apellidos_facturacion'];
                        $provincia_facturacion = $fila['provincia_facturacion'];
                        $municipio_facturacion = $fila['municipio_facturacion'];
                        $calle_facturacion = $fila['calle_facturacion'];
                        $numero_facturacion = $fila['numero_facturacion'];
                    }
    
                    $statement->close();
                    $conexion->close();
            ?>
    
                    <form method="post" class="m-2 p-2">
                        <label for="nombre_facturacion" class="m-3 p-3">Nombre</label>
                        <input type="text" id="nombre_facturacion" name="nombre_facturacion" class="m-3 p-3 form-control" value="<?php echo htmlspecialchars($nombre_facturacion); ?>"><br>
    
                        <label for="apellidos_facturacion" class="m-3 p-3">Apellido(s)</label>
                        <input type="text" id="apellidos_facturacion" name="apellidos_facturacion" class="m-3 p-3 form-control" value="<?php echo htmlspecialchars($apellidos_facturacion); ?>"><br>
    
                        <label for="provincia_facturacion" class="m-3 p-3">Provincia</label>
                        <select name="provincia_facturacion" id="provincia_facturacion" class="m-3 p-3 form-control">
                            <option value="">--Selecciona una provincia--</option>
                            <option value="alava" <?php if ($provincia_facturacion == 'alava') echo 'selected'; ?>>Álava</option>
                            <option value="albacete" <?php if ($provincia_facturacion == 'albacete') echo 'selected'; ?>>Albacete</option>
                            <option value="alicante" <?php if ($provincia_facturacion == 'alicante') echo 'selected'; ?>>Alicante</option>
                            <option value="almeria" <?php if ($provincia_facturacion == 'almeria') echo 'selected'; ?>>Almería</option>
                            <option value="asturias" <?php if ($provincia_facturacion == 'asturias') echo 'selected'; ?>>Asturias</option>
                            <option value="avila" <?php if ($provincia_facturacion == 'avila') echo 'selected'; ?>>Ávila</option>
                            <option value="badajoz" <?php if ($provincia_facturacion == 'badajoz') echo 'selected'; ?>>Badajoz</option>
                            <option value="baleares" <?php if ($provincia_facturacion == 'baleares') echo 'selected'; ?>>Baleares</option>
                            <option value="barcelona" <?php if ($provincia_facturacion == 'barcelona') echo 'selected'; ?>>Barcelona</option>
                            <option value="burgos" <?php if ($provincia_facturacion == 'burgos') echo 'selected'; ?>>Burgos</option>
                            <option value="caceres" <?php if ($provincia_facturacion == 'caceres') echo 'selected'; ?>>Cáceres</option>
                            <option value="cadiz" <?php if ($provincia_facturacion == 'cadiz') echo 'selected'; ?>>Cádiz</option>
                            <option value="cantabria" <?php if ($provincia_facturacion == 'cantabria') echo 'selected'; ?>>Cantabria</option>
                            <option value="castellon" <?php if ($provincia_facturacion == 'castellon') echo 'selected'; ?>>Castellón</option>
                            <option value="ciudad_real" <?php if ($provincia_facturacion == 'ciudad_real') echo 'selected'; ?>>Ciudad Real</option>
                            <option value="cordoba" <?php if ($provincia_facturacion == 'cordoba') echo 'selected'; ?>>Córdoba</option>
                            <option value="cuenca" <?php if ($provincia_facturacion == 'cuenca') echo 'selected'; ?>>Cuenca</option>
                            <option value="girona" <?php if ($provincia_facturacion == 'girona') echo 'selected'; ?>>Girona</option>
                            <option value="granada" <?php if ($provincia_facturacion == 'granada') echo 'selected'; ?>>Granada</option>
                            <option value="guadalajara" <?php if ($provincia_facturacion == 'guadalajara') echo 'selected'; ?>>Guadalajara</option>
                            <option value="guipuzcoa" <?php if ($provincia_facturacion == 'guipuzcoa') echo 'selected'; ?>>Guipúzcoa</option>
                            <option value="huelva" <?php if ($provincia_facturacion == 'huelva') echo 'selected'; ?>>Huelva</option>
                            <option value="huesca" <?php if ($provincia_facturacion == 'huesca') echo 'selected'; ?>>Huesca</option>
                            <option value="jaen" <?php if ($provincia_facturacion == 'jaen') echo 'selected'; ?>>Jaén</option>
                            <option value="la_coruna" <?php if ($provincia_facturacion == 'la_coruna') echo 'selected'; ?>>La Coruña</option>
                            <option value="la_rioja" <?php if ($provincia_facturacion == 'la_rioja') echo 'selected'; ?>>La Rioja</option>
                            <option value="las_palmas" <?php if ($provincia_facturacion == 'las_palmas') echo 'selected'; ?>>Las Palmas</option>
                            <option value="leon" <?php if ($provincia_facturacion == 'leon') echo 'selected'; ?>>León</option>
                            <option value="lleida" <?php if ($provincia_facturacion == 'lleida') echo 'selected'; ?>>Lleida</option>
                            <option value="lugo" <?php if ($provincia_facturacion == 'lugo') echo 'selected'; ?>>Lugo</option>
                            <option value="madrid" <?php if ($provincia_facturacion == 'madrid') echo 'selected'; ?>>Madrid</option>
                            <option value="malaga" <?php if ($provincia_facturacion == 'malaga') echo 'selected'; ?>>Málaga</option>
                            <option value="murcia" <?php if ($provincia_facturacion == 'murcia') echo 'selected'; ?>>Murcia</option>
                            <option value="navarra" <?php if ($provincia_facturacion == 'navarra') echo 'selected'; ?>>Navarra</option>
                            <option value="ourense" <?php if ($provincia_facturacion == 'ourense') echo 'selected'; ?>>Ourense</option>
                            <option value="palencia" <?php if ($provincia_facturacion == 'palencia') echo 'selected'; ?>>Palencia</option>
                            <option value="pontevedra" <?php if ($provincia_facturacion == 'pontevedra') echo 'selected'; ?>>Pontevedra</option>
                            <option value="salamanca" <?php if ($provincia_facturacion == 'salamanca') echo 'selected'; ?>>Salamanca</option>
                            <option value="santa_cruz_de_tenerife" <?php if ($provincia_facturacion == 'santa_cruz_de_tenerife') echo 'selected'; ?>>Santa Cruz de Tenerife</option>
                            <option value="segovia" <?php if ($provincia_facturacion == 'segovia') echo 'selected'; ?>>Segovia</option>
                            <option value="sevilla" <?php if ($provincia_facturacion == 'sevilla') echo 'selected'; ?>>Sevilla</option>
                            <option value="soria" <?php if ($provincia_facturacion == 'soria') echo 'selected'; ?>>Soria</option>
                            <option value="tarragona" <?php if ($provincia_facturacion == 'tarragona') echo 'selected'; ?>>Tarragona</option>
                            <option value="teruel" <?php if ($provincia_facturacion == 'teruel') echo 'selected'; ?>>Teruel</option>
                            <option value="toledo" <?php if ($provincia_facturacion == 'toledo') echo 'selected'; ?>>Toledo</option>
                            <option value="valencia" <?php if ($provincia_facturacion == 'valencia') echo 'selected'; ?>>Valencia</option>
                            <option value="valladolid" <?php if ($provincia_facturacion == 'valladolid') echo 'selected'; ?>>Valladolid</option>
                            <option value="vizcaya" <?php if ($provincia_facturacion == 'vizcaya') echo 'selected'; ?>>Vizcaya</option>
                            <option value="zamora" <?php if ($provincia_facturacion == 'zamora') echo 'selected'; ?>>Zamora</option>
                            <option value="zaragoza" <?php if ($provincia_facturacion == 'zaragoza') echo 'selected'; ?>>Zaragoza</option>
                        </select><br>
    
                        <label for="municipio_facturacion" class="m-3 p-3">Municipio</label>
                        <input type="text" id="municipio_facturacion" name="municipio_facturacion" class="m-3 p-3 form-control" value="<?php echo htmlspecialchars($municipio_facturacion); ?>"><br>
            
                        <label for="calle_facturacion" class="m-3 p-3">Calle</label>
                        <input type="text" id="calle_facturacion" name="calle_facturacion" class="m-3 p-3 form-control" value="<?php echo htmlspecialchars($calle_facturacion); ?>"><br>

                        <label for="numero_facturacion" class="m-3 p-3">Número</label>
                        <input type="text" id="numero_facturacion" name="numero_facturacion" class="m-3 p-3 form-control" value="<?php echo htmlspecialchars($numero_facturacion); ?>"><br>
            
                        <input type="radio" id="tarjeta" name="pago" value="tarjeta" class="ml-3 pl-3" required>
                        <label for="tarjeta" class="mr-5">Tarjeta de crédito</label>

                        <input type="radio" id="paypal" name="pago" value="paypal">
                        <label for="paypal" class="mr-5">PayPal</label>

                        <input type="radio" id="transferencia" name="pago" value="transferencia">
                        <label for="transferencia">Transferencia bancaria</label><br>

                        <button type="submit" class="btn btn-danger mt-4 ml-3 pl-3">Realizar pago</button>
                    </form>
    
            <?php
                }else{
                    echo '<p class="m-2"><a href="../dashboard/dashboard.php">Inicia sesión</a> o <a href="../registro/registro.php">registrate</a> para poder proceder al pago.</p>';
                }
            ?>
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

    <script src="carrito.js"></script>
</body>
</html>
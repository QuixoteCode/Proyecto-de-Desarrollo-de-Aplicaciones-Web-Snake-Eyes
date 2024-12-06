<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta</title>
    <link rel="icon" href="../imagenes/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../recursos/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="registro.css">                                         
</head>
<body class="bg-dark text-white">
 
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
                    <a class="nav-link active" id="nav-cuenta" data-target="#" href="http://example.com/" data-toggle="dropdown"
                        role="button" aria-haspopup="true" aria-expanded="false">
                        Cuenta<span class="sr-only">(actual)</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="nav-cuenta">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li><a href="../dashboard/dashboard.php" class="nav-link">Dashboard</a></li>
                            <li><a href="?logout" class="nav-link">Cerrar Sesión</a></li>
                        <?php else: ?>
                            <li><a href="../dashboard/dashboard.php" class="nav-link">Iniciar Sesión</a></li>
                            <li><a href="registro.php" class="nav-link">Registrarse</a></li>
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
        <section class="col-12 col-sm-6">
            <p class="h4 m-2 p-2">Rellena el siguiente formulario para completar tu registro:</p>
            <form action="nueva-cuenta.php" method="post" onsubmit="return validarEdad()" class="m-2 p-2">
                <label for="nombre_usuario" class="m-2 p-2">Nombre de Usuario:</label>
                <input id="nombre_usuario" name="nombre_usuario" type="text" class="m-2 p-2 form-control" required><br>

                <label for="correo_usuario" class="m-2 p-2">Email:</label>
                <input id="correo_usuario" name="correo_usuario" type="email" class="m-2 p-2 form-control" required><br>

                <label for="contrasena_usuario" class="m-2 p-2">Contraseña:</label>
                <input id="contrasena_usuario" name="contrasena_usuario" type="password" class="m-2 p-2 form-control" required><br>
 
                <!--Una función en registro.js será llamada para comprobar la mayoría de edad, véase el "onsubmit" en el formulario-->
                <label for="fecha_nacimiento" class="m-2 p-2">Fecha de nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="m-2 p-2 form-control" required>
 
                <p class="h4 m-2 p-2">¿Te gustaría introducir ya tus datos personales para las compras que puedas hacer en el futuro?</p>
                <!--Para aquellos que quieran introducir sus datos de forma anticipada-->
                <input type="checkbox" id="pre_introduccion_datos" name="pre_introduccion_datos" value="datos_pre_introducidos" class="ml-2 pl-2 form-check-input">
                <label for="pre_introduccion_datos" class="ml-3 pl-3">Sí, me gustaría</label>
                <br><br>
 
                <section id="pre-registro-datos-facturacion">
                    <label for="nombre_facturacion" class="m-3 p-3">Nombre</label>
                    <input type="text" id="nombre_facturacion" name="nombre_facturacion" class="m-3 p-3 form-control"><br>
 
                    <label for="apellidos_facturacion" class="m-3 p-3">Apellido(s)</label>
                    <input type="text" id="apellidos_facturacion" name="apellidos_facturacion" class="m-3 p-3 form-control"><br>
 
                    <label for="provincia_facturacion" class="m-3 p-3">Provincia</label>
                    <select name="provincia_facturacion" id="provincia_facturacion" class="m-3 p-3 form-control">
                        <option value="">--Selecciona una provincia--</option>
                        <option value="alava">Álava</option>
                        <option value="albacete">Albacete</option>
                        <option value="alicante">Alicante</option>
                        <option value="almeria">Almería</option>
                        <option value="asturias">Asturias</option>
                        <option value="avila">Ávila</option>
                        <option value="badajoz">Badajoz</option>
                        <option value="baleares">Baleares</option>
                        <option value="barcelona">Barcelona</option>
                        <option value="burgos">Burgos</option>
                        <option value="caceres">Cáceres</option>
                        <option value="cadiz">Cádiz</option>
                        <option value="cantabria">Cantabria</option>
                        <option value="castellon">Castellón</option>
                        <option value="ciudad_real">Ciudad Real</option>
                        <option value="cordoba">Córdoba</option>
                        <option value="cuenca">Cuenca</option>
                        <option value="girona">Girona</option>
                        <option value="granada">Granada</option>
                        <option value="guadalajara">Guadalajara</option>
                        <option value="guipuzcoa">Guipúzcoa</option>
                        <option value="huelva">Huelva</option>
                        <option value="huesca">Huesca</option>
                        <option value="jaen">Jaén</option>
                        <option value="la_coruna">La Coruña</option>
                        <option value="la_rioja">La Rioja</option>
                        <option value="las_palmas">Las Palmas</option>
                        <option value="leon">León</option>
                        <option value="lleida">Lleida</option>
                        <option value="lugo">Lugo</option>
                        <option value="madrid">Madrid</option>
                        <option value="malaga">Málaga</option>
                        <option value="murcia">Murcia</option>
                        <option value="navarra">Navarra</option>
                        <option value="ourense">Ourense</option>
                        <option value="palencia">Palencia</option>
                        <option value="pontevedra">Pontevedra</option>
                        <option value="salamanca">Salamanca</option>
                        <option value="santa_cruz_de_tenerife">Santa Cruz de Tenerife</option>
                        <option value="segovia">Segovia</option>
                        <option value="sevilla">Sevilla</option>
                        <option value="soria">Soria</option>
                        <option value="tarragona">Tarragona</option>
                        <option value="teruel">Teruel</option>
                        <option value="toledo">Toledo</option>
                        <option value="valencia">Valencia</option>
                        <option value="valladolid">Valladolid</option>
                        <option value="vizcaya">Vizcaya</option>
                        <option value="zamora">Zamora</option>
                        <option value="zaragoza">Zaragoza</option>
                    </select><br>
 
                    <label for="municipio_facturacion" class="m-3 p-3">Municipio</label>
                    <input type="text" id="municipio_facturacion" name="municipio_facturacion" class="m-3 p-3 form-control"><br>
 
                    <label for="calle_facturacion" class="m-3 p-3">Calle</label>
                    <input type="text" id="calle_facturacion" name="calle_facturacion" class="m-3 p-3 form-control"><br>

                    <label for="numero_facturacion" class="m-3 p-3">Número</label>
                    <input type="text" id="numero_facturacion" name="numero_facturacion" class="m-3 p-3 form-control"><br>

                </section>
        
                <section class="m-2 p-2">
                    <p>Selecciona un plan</p>
                    <select name="plan" id="plan" class="form-control" required>
                        <option value="">--Selecciona un plan--</option>
                        <option value="bronce">Plan Bronce (0,00€/mes)</option>
                        <option value="plata">Plan Plata(1,99€/mes)</option>
                        <option value="oro">Plan Oro(9,99€/mes)</option>
                    </select>
                </section>
                <br>

                <button type="submit" class="btn btn-danger btn-lg m-5 p-3">Crear cuenta</button>
            </form>
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

    <script src="registro.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos de las cookies y de privacidad</title>
    <link rel="icon" href="../imagenes/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../recursos/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="cookies-y-privacidad.css">
</head>
<body onload="opcionElegida()" class="bg-secondary">

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
    <section id="contenido">
        <section id="aceptar" class="bg-dark p-3">
            <section class="text-center text-white h1">Ha aceptado las cookies</section><br>
            <section class="text-white h2">
                <p>Estamos sinceramente agradecidos por su disposición a aceptar nuestra política de cookies. Su confianza
                    es muy importante para nosotros y nos comprometemos a garantizar la seguridad y privacidad de sus datos.
                    ¡Gracias por apoyarnos en hacer de nuestra plataforma un lugar seguro y amigable para todos!</p>
            </section>
        </section>
        <br><br><br>
    
        <section id="mas-info" class="bg-dark p-3">
            <section class="text-center text-white h1">Más información sobre las cookies que recabamos</section><br>
            <section class="text-white h2">
                <p>Nuestro servicio web recoge cierta información sobre su uso de nuestra plataforma con el propósito de
                    proporcionarle un mejor servicio.</p>
            </section>
        </section>
        <br><br><br>
    
    
        <section id="socios" class="bg-dark p-3">
            <section class="text-center text-white h1">Ver listado de socios (2)</section><br>
            <section class="text-white h2">
                <p>Nuestro servicio web recoge cierta información sobre su uso de nuestra plataforma.</p>
                <!--Permite alternar la visibilidad de la lista de "Socios Colaboradores". La lista se mostrará si estaba oculta y se ocultará si estaba visible.-->
                <button class="boton"
                    onclick="document.getElementById('lista-colaboradores').style.display = document.getElementById('lista-colaboradores').style.display === 'none' ? 'block' : 'none';">
                    Mostrar/Ocultar Socios Colaboradores
                </button>
                <section id="lista-colaboradores">
                    <h2 class="mover-hacia-abajo">Socios Colaboradores:</h2>
                    <section class="container">
                        <section class="form-group">
                            <input type="checkbox" id="slider-checkbox" class="checkbox">
                            <label for="slider-checkbox" class="slider-label">
                                <section class="slider"></section>
                            </label>
                        </section>
                    </section>
                    <ul>
                        <li><strong>Empresa Ejemplo SA:</strong> Empresa encargada en estudios de mercado de logística</li>
                        <li><strong>Empresa Ejemplo SL:</strong> Empresa encargada en estudios de los gustos sobre juegos.
                        </li>
                    </ul>
                </section>
            </section>
        </section>
        <br><br><br>
    
    
    
        <section id="politica-privacidad" class="bg-dark p-3">
            <section class="text-center text-white h1">Datos personales de los que hacemos uso
            </section><br>
            <section class="text-white h2">
                <p>Con el propósito de proporcionarle nuestros servicios de venta y de foro almacenamos cierta información
                    sobre usted, como su dirección, su nombre y su correo de contacto, además de información recabada por
                    esta app como puede ser tu localización geográfica.</p>
                <p>Puede solicitar que sus datos personales sean eliminados de nuestro servidores <a
                        href="../contacto/contacto.php">en el siguiente formulario de contacto</a>.</p>
            </section>
        </section>
        <br><br><br>
    
    
    
        <section id="aviso-legal" class="bg-dark p-3">
            <section class="text-center text-white h1">Aviso legal</section><br>
            <section class="text-white h2">
                <p>Snake Eyes se reserva el derecho de prestar sus servicios, pudiendo negarlos a aquellos quienes se
                    sospecha que realizan un uso indebido de ellos.</p>
            </section>
        </section>
        <br><br><br>
    
        <section class="d-flex justify-content-center align-items-center m-2">
            <button id="boton-volver-indice" class="btn btn-dark btn-lg" onclick="volverAlIndice()">Volver al índice</button>
        </section>
    </section>

    <footer class="text-white bg-dark text-center p-4 fixed-bottom footer">Snake Eyes, marca no registrada<br>
        Proyecto de índole educativa, uso justo del material con propiedad intelectual según el derecho de cita:
        reproducción de imágenes y textos con fines educativos<br>
        Lucas Fernández, licencia "Attribution-NonCommercial 4.0 International"<br>

        <!--Las clases "d-flex" y "justify-content-center" son necesarias para que el contenido de la siguiente fila aparezca centrado-->
        <section class="row d-flex justify-content-center my-2">
            <a class="col-2" href="#aviso-legal">Aviso Legal</a>
            <span class="col-2">|</span>
            <a class="col-2" href="#mas-info">Política de Cookies</a>
        </section>
        <section class="row d-flex justify-content-center my-2">
            <a class="col-2" href="#politica-privacidad">Política de Privacidad</a>
            <span class="col-2">|</span>
            <a class="col-2" href="../terminos-uso/terminos-uso.php">Términos de Uso</a>
        </section>
    </footer>

    <script src="../recursos/jquery/jquery-3.3.1.slim.min.js"></script>
    <script src="../recursos/popper/popper.min.js"></script>
    <script src="../recursos/bootstrap/bootstrap.min.js"></script>

    <script src="cookies-y-privacidad.js"></script>
</body>
</html>
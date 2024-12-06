<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Foro</title>
    <link rel="icon" href="../imagenes/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../recursos/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="foro.css">    
</head>
<body class="bg-dark text-white">
    <?php
        //Iniciamos la sesión. El usuario solamente podrá crear un nuevo post si tiene la sesión iniciada, también lo necesitaremos para el carrito
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
                    <a class="nav-link active" href="foro.php">Foro<span class="sr-only">(actual)</span></a>
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
        <?php if(isset($_SESSION['user_id'])): ?>
            <p class="h1 m-2 p-2">Crear un nuevo post</p>
            <section class="col-12 col-sm-6">
                <form action="subida.php" method="post" enctype="multipart/form-data" class="m-2 p-2">
                    <label for="titulo">Título:</label><br>
                    <input type="text" name="titulo" id="titulo" class="form-control m-2 p-2" required><br><br>
    
                    <label for="contenido_publicacion">Contenido:</label><br>
                    <textarea name="contenido_publicacion" id="contenido_publicacion" rows="4" class="form-control m-2 p-2" required></textarea><br><br>
    
                    <label for="imagen">Selecciona una imagen:</label><br>
                    <input type="file" name="imagen" id="imagen" class="form-control-file m-2 p-2"><br><br>
    
                    <button type="submit" class="btn btn-danger btn-lg m-4">Publicar</button>
                </form>
            </section>
            <br><br><br>
        <?php endif; ?>
    
        <p class="h1 m-3 p-3">Publicaciones</p>
        <?php

            $servidor = "localhost";
            $usuario_db = "root";
            $contrasena_db = "";
            $base = "snake_eyes";

            $conexion = new mysqli($servidor, $usuario_db, $contrasena_db, $base);
            if ($conexion->connect_error) {
                die("La conexión ha fallado: " . $conexion->connect_error);
            }

            //Consultamos todas las publicaciones junto con la información del autor y la suma de todos los "me gusta"
            $query = "SELECT p.id, p.titulo, p.contenido, p.imagen, u.nombre_usuario AS nombre_autor, u.plan, 
                (SELECT COUNT(*) FROM gustas WHERE publicacion_id = p.id) AS gustas 
                FROM publicaciones p
                JOIN usuarios u ON p.nombre_autor = u.nombre_usuario";
            $result = $conexion->query($query);

            if ($result->num_rows > 0) {
                while ($post = $result->fetch_assoc()) {
                    echo "<section class='border border-danger rounded'>";
                    //Icono obtenido de https://icons.getbootstrap.com/icons/hand-thumbs-up/
                    echo "<p class='h3 m-3 p-3'>" . htmlspecialchars($post['titulo']) . "&nbsp;&nbsp;&nbsp;
                        <svg id='gusta-svg-" . htmlspecialchars($post['id']) . "' onclick=\"darMeGusta(" . htmlspecialchars($post['id']) . ")\" xmlns='http://www.w3.org/2000/svg' width='32' height='32' fill='currentColor' class='bi bi-hand-thumbs-up' viewBox='0 0 16 16'>
                            <path d='M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z'/>
                        </svg>
                        <span id='cuenta-de-gustas-" . htmlspecialchars($post['id']) . "'>" . $post['gustas'] . "</span>
                        </p>";
        
                    echo "<p class='m-3 p-3'>" . htmlspecialchars($post['contenido']) . "</p>";

                    //Los usuarios tendrán una serie de imágenes a los lados de su nombre que indicarán que plan tienen contratado, mejorando la visibilidad y la calidad estética de los usuarios con planes superiores
                    if ($post['plan'] == 'oro') {
                        echo "<p class='m-3 p-3'>Publicación creada por <img src='../imagenes/simbolo_plan_oro.png' alt='usuario con plan oro' height='25'> " . htmlspecialchars($post['nombre_autor']) . " <img src='../imagenes/simbolo_plan_oro.png' alt='usuario con plan oro' height='25'></p>";
                    } elseif ($post['plan'] == 'plata') {
                        echo "<p class='m-3 p-3'>Publicación creada por <img src='../imagenes/simbolo_plan_plata.png' alt='usuario con plan plata' height='25'> " . htmlspecialchars($post['nombre_autor']) . " <img src='../imagenes/simbolo_plan_plata.png' alt='usuario con plan plata' height='25'></p>";
                    } else {
                        echo "<p class='m-3 p-3'>Publicación creada por " . htmlspecialchars($post['nombre_autor']) . "</p>";
                    }

                    //Mostramos la imagen si existe
                    if ($post['imagen']) {
                        //Convertimos el binario de la imagen a base64
                        $imagen_base64 = base64_encode($post['imagen']);

                        //Obtenemos el tipo MIME de la imagen y la mostramos según este
                        $informacion_imagen = finfo_open(FILEINFO_MIME_TYPE);
                        $tipo_imagen = finfo_buffer($informacion_imagen, $post['imagen']);
                        finfo_close($informacion_imagen);
                        echo "<img src='data:$tipo_imagen;base64," . $imagen_base64 . "' width='300' alt='Imagen del post' class='ml-3 pl-3'><br><br>";
                    }

                    //Solo los usuarios registrados pueden reportar publicaciones
                    if (isset($_SESSION['user_id'])) {
                        echo "<label class='ml-3 pl-3' for='motivo'>Reportar:</label>
                            <select class='ml-3 mr-3' id='motivo-" . htmlspecialchars($post['id']) . "' required>
                                <option value=''>Selecciona un motivo</option>
                                <option value='conducta_inadecuada'>Conducta inadecuada</option>
                                <option value='contenido_indebido'>Contenido indebido</option>
                                <option value='spam'>Spam</option>
                            </select>";
                        //Pasamos el id, el título y el nombre del autor, el motivo será obtenido luego en la función JavaScript
                        echo "<button class='btn btn-danger mb-1' onclick=\"reportar(" . htmlspecialchars($post['id']) . ", '" . htmlspecialchars($post['titulo']) . "', '" . htmlspecialchars($post['nombre_autor']) . "')\">Reportar</button>
                            <p id='report-resultado-" . htmlspecialchars($post['id']) . "' class='report-exito ml-3 pl-3 mt-2'></p>";
                    }

                    echo "</section>";
                }
            } else {
            echo "<p class='m-3 p-3'>No hay publicaciones disponibles.</p>";
            }

            //Cerramos la conexión a la base de datos
            $conexion->close();

        ?>
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

    <script src="foro.js"></script>
</body>
</html>
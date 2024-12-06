<?php
    session_start();

    //Inicializamos la cuenta del carrito
    $carrito_cantidad = isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0;

    //Manejo del logout
    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    //Datos necesarios para la conexion a la base de datos
    $servidor = "localhost";
    $usuario_db = "root";
    $contrasena_db = "";
    $base = "snake_eyes";

    //Conexión a la base de datos
    $conexion = new mysqli($servidor, $usuario_db, $contrasena_db, $base);
    if ($conexion->connect_error) {
        die("La conexion ha fallado: " . $conexion->connect_error);
    }

    //Verificar si el usuario es administrador (mediante su ID fija, siendo 1)
    function isAdmin() {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] === 1;
    }

    //Manejo del login
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
        $nombre_usuario = $_POST['nombre_usuario'];
        $contrasena_usuario = $_POST['contrasena_usuario'];

        $statement = $conexion->prepare("SELECT id, contrasena_usuario, baneado FROM usuarios WHERE nombre_usuario = ?");
        $statement->bind_param("s", $nombre_usuario);
        $statement->execute();
        $statement->bind_result($user_id, $contrasena_almacenada, $baneado);
        $statement->fetch();
    
        //Verificar la contraseña hasheada
        if ($contrasena_almacenada && password_verify($contrasena_usuario, $contrasena_almacenada)) {
            //Comprobamos si el usuario ha sido baneado
            if ($baneado === 1) {
                echo "<script>alert('Has sido baneado.');</script>";
            }else{
                $_SESSION['user_id'] = $user_id;
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        } else {
            echo "<p class='m-3 p-3'>Credenciales incorrectos</p>";
        }

        $statement->close();
    }

    //Función para cargar los reportes desde la base de datos
    function cargarReportes() {
        //Usamos la conexión definida fuera de esta función
        global $conexion; 
        //Inicializamos el array de reportes
        $reportes = [];

        //Consultamos los reportes desde la base de datos
        $sql = "SELECT r.id, r.publicacion_id, r.titulo, r.motivo, r.nombre_autor, r.fecha, p.titulo AS publicacion_titulo 
            FROM reportes r 
            JOIN publicaciones p ON r.publicacion_id = p.id";
    
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            //Obtenemos los resultados y los almacenamos en un array
            while ($fila = $resultado->fetch_assoc()) {
                if (isset($fila['publicacion_id']) && isset($fila['titulo'])) {
                    $reportes[] = [
                        'id' => $fila['id'],
                        'publicacion_id' => $fila['publicacion_id'],
                        'titulo' => $fila['titulo'], 
                        'motivo' => $fila['motivo'],
                        'nombre_autor' => $fila['nombre_autor'],
                        'fecha' => $fila['fecha'],
                        'publicacion_titulo' => $fila['publicacion_titulo']
                    ];
                } else {
                    error_log("Faltan campos en el reporte: " . json_encode($fila));
                }
            }
        } else {
            error_log("No se encontraron reportes o la consulta falló.");
        }
    
        return $reportes;
    }

    $reportes = cargarReportes();

    if (isset($_SESSION['user_id']) && isAdmin()) {

        //Consulta para obtener todas las publicaciones de la base de datos
        $sql = "SELECT id, titulo, contenido, imagen FROM publicaciones";
        $resultado = $conexion->query($sql);

        //Inicializamos el array de publicaciones
        $publicaciones = [];
        if ($resultado && $resultado->num_rows > 0) {
            //Introducimos cada publicación en el array
            while ($fila = $resultado->fetch_assoc()) {
                $publicaciones[] = $fila;
            }
        }
    
    }

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="icon" href="../imagenes/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../recursos/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard.css">
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
                    <a class="nav-link active" id="nav-cuenta" data-target="#" href="http://example.com/" data-toggle="dropdown"
                        role="button" aria-haspopup="true" aria-expanded="false">
                        Cuenta<span class="sr-only">(actual)</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="nav-cuenta">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                            <li><a href="?logout" class="nav-link">Cerrar Sesión</a></li>
                        <?php else: ?>
                            <li><a href="dashboard.php" class="nav-link">Iniciar Sesión</a></li>
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
            <?php if (!isset($_SESSION['user_id'])): ?>
                <form method="POST" action="" class="form-inline">
                    <label for="nombre_usuario">Nombre de Usuario: </label><input type="text" name="nombre_usuario"
                        class="form-control m-4" required>
                    <label for="contrasena_usuario">Contraseña: </label><input type="password" name="contrasena_usuario"
                        class="form-control m-4" required>
                    <input type="submit" name="login" value="Login" class="btn btn-danger btn-lg ml-4">
                </form>
                <?php else: ?>
                <h1>Bienvenido a tu dashboard</h1>
                <br>
                <a href="?logout" class="btn btn-danger btn-lg ml-4">Cerrar sesión</a>
                <br><br><br>
    
                <?php if (isAdmin()): ?>
                <h1>Controles de Admin</h1>
                <br><br>
                <!--Formulario para actualizar las variables-->
                <h2>Añadir un nuevo producto</h2>
                <form method="POST" action="anadir-producto.php"  enctype="multipart/form-data">
                    <label for="nombre">Nombre:</label><br>
                    <input type="text" id="nombre" name="nombre" required><br><br>

                    <label for="cantidad_existencias">Cantidad en Existencias:</label><br>
                    <input type="number" id="cantidad_existencias" name="cantidad_existencias" min="0" required><br><br>

                    <label for="precio">Precio:</label><br>
                    <input type="text" id="precio" name="precio" required><br><br>

                    <label for="seccion">Categoría:</label><br>
                    <select id="seccion" name="seccion" required>
                        <option value="populares">Populares</option>
                        <option value="ultimos_lanzamientos">Últimos Lanzamientos</option>
                        <option value="recomendados">Recomendados</option>
                    </select><br><br>

                    <label for="descripcion">Descripción:</label><br>
                    <textarea id="descripcion" name="descripcion" required></textarea><br><br>

                    <label for="imagen">Imagen:</label><br>
                    <input type="file" id="imagen" name="imagen" required><br><br>

                    <button type="submit">Añadir Producto</button>
                </form>
                <?php endif; ?>
                <br><br>
    
                <?php if(isAdmin()): ?>
                    <h2>Publicaciones</h2>
                    <?php if(!empty($publicaciones)): ?>
                        <table class="table table-dark">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Contenido</th>
                                    <th>Imagen</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($publicaciones as $publicacion): ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($publicacion['titulo']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($publicacion['contenido']); ?>
                                        </td>
                                        <td>
                                            <?php
                                                if ($publicacion['imagen']) {
                                                    //Convertimos el binario de la imagen a base64
                                                    $imagen_base_64 = base64_encode($publicacion['imagen']);

                                                    //Obtenemos el tipo MIME de la imagen y la mostramos según este
                                                    $informacion_imagen = finfo_open(FILEINFO_MIME_TYPE);
                                                    $tipo_imagen = finfo_buffer($informacion_imagen, $publicacion['imagen']);
                                                    finfo_close($informacion_imagen);
                                                    echo "<img src='data:$tipo_imagen;base64," . $imagen_base_64 . "' width='150' alt='Imagen del post'><br><br>";
                                                } else {
                                                    echo "Sin imagen";
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <button onclick="eliminarPublicacion(<?php echo htmlspecialchars($publicacion['id']); ?>)">
                                                Eliminar Publicación
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No hay publicaciones para mostrar.</p>
                    <?php endif; ?>
                <?php endif; ?>
                <br><br>
    
                <!--Codigo necesario para que el admin pueda ver y eliminar mensajes de contactos desde este dashboard-->
                <?php 
                    if (isAdmin()) {
                        $servidor = "localhost";
                        $usuario_db = "root";
                        $contrasena_db = "";
                        $base = "snake_eyes";
    
                        $conexion = new mysqli($servidor, $usuario_db, $contrasena_db, $base);
    
                        if ($conexion->connect_error) {
                            die("Conexión fallida: " . $conexion->connect_error);
                        }
        
                        //Procesamos la eliminación si se ha solicitado
                        if (isset($_GET['eliminar'])) {
                            $id_eliminar = $_GET['eliminar'];
        
                            //Metemos en el statement la preparación de la eliminación de un contacto
                            $statement = $conexion->prepare("DELETE FROM contactos WHERE id = ?");
                            $statement->bind_param("i", $id_eliminar);
        
                            if ($statement->execute()) {
                                echo "<p>Contacto eliminado con éxito.</p>";
                            } else {
                                echo "<p>Error al eliminar el contacto: " . $statement->error . "</p>";
                            }
        
                            $statement->close();
                        }
                    
                        //Obtenemos todos los contactos de la base de datos
                        $resultado = $conexion->query("SELECT * FROM contactos ORDER BY fecha DESC");
                        echo '<h2>Contactos a soporte</h2>';
                        if ($resultado->num_rows > 0) {
                            echo "<table class='table table-dark'>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Mensaje</th>
                                        <th>Fecha</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>";
                                    //Mostramos los contactos en una tabla
                                    while ($fila = $resultado->fetch_assoc()) {
                                        echo "<tr>
                                            <td>" . htmlspecialchars($fila['id']) . "</td>
                                            <td>" . htmlspecialchars($fila['nombre']) . "</td>
                                            <td>" . htmlspecialchars($fila['email']) . "</td>
                                            <td>" . htmlspecialchars($fila['mensaje']) . "</td>
                                            <td>" . htmlspecialchars($fila['fecha']) . "</td>
                                            <td>
                                                <a href='mailto:" . htmlspecialchars($fila['email']) . "'>
                                                    Responder
                                                </a>

                                                &nbsp;&nbsp;&nbsp;

                                                <a href='?eliminar=" . $fila['id'] . "' onclick=\"return confirmarBorrado();\">
                                                    Eliminar
                                                </a>
                                            </td>
                                        </tr>";
                                    }
                                echo "</tbody>
                            </table>";
                        } else {
                            echo "<p>No hay contactos para mostrar.</p>";
                        }

                        echo "<br><br>";
        
                        $conexion->close();
    
                        echo '<h2>Reportes de Publicaciones</h2>';
                        if (empty($reportes)) {
                            echo '<p>No hay reportes para mostrar.</p>';
                        } else {
                            echo '<table class="table table-dark">
                                <thead>
                                    <tr>
                                        <th>Titulo del Post</th>
                                        <th>Motivo del Reporte</th>
                                        <th>Nombre del Autor</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($reportes as $reporte) {
                                        echo '<tr>
                                            <td>' . htmlspecialchars($reporte['titulo']) . '</td>
                                            <td>';
                                                if(htmlspecialchars($reporte['motivo']) == "conducta_inadecuada"){
                                                    echo "Conducta inadecuada";
                                                }else{
                                                    if(htmlspecialchars($reporte['motivo']) == "contenido_indebido"){
                                                        echo "Contenido indebido";
                                                    }else{
                                                        if(htmlspecialchars($reporte['motivo']) == "spam"){
                                                            echo "Spam";
                                                        }
                                                    }
                                                }
                                            echo '</td>
                                            <td>' . htmlspecialchars($reporte['nombre_autor']) . '</td>
                                            <td>
                                                <button onclick="eliminarPublicacion(' . htmlspecialchars($reporte['publicacion_id']) . ')">
                                                    Eliminar Publicación
                                                </button>

                                                <button onclick="banearUsuario(\'' . htmlspecialchars($reporte['nombre_autor']) . '\')">
                                                    Banear
                                                </button>

                                                <button onclick="eliminarReporte(' . htmlspecialchars($reporte['id']) . ')">
                                                    Eliminar Reporte
                                                </button>
                                            </td>
                                        </tr>';
                                    }
                                echo '</tbody>
                            </table>';

                        }
                        echo '<br><br>';
                    }

                    echo "<p class='m-3 p-3'>Ver tu <a href='../carrito/carrito.php'>carrito</a></p>";
                    echo "<p class='m-3 p-3'>Ver nuestras <a href='../index.php'>ofertas</a></p>";
                    echo "<p class='m-3 p-3'>Ver tus productos <a href='../favoritos/favoritos.php'>favoritos</a></p>";
                    echo "<p class='m-3 p-3'>Cambiar tu <a href='../cambiar-plan/cambiar-plan.php'>plan</a></p>";
                    echo "<p class='m-3 p-3'>Cambiar tu <a href='cambiar-contrasena.php'>contraseña</a></p>";
                
                ?>
            <?php endif; ?>
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

    <script src="dashboard.js"></script>
</body>
</html>
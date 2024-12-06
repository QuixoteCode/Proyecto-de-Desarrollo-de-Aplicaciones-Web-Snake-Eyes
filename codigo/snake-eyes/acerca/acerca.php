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
    <title>Acerca de nosotros</title>
    <link rel="icon" href="../imagenes/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../recursos/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="acerca.css"> 
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
                <!--El span con la clase "sr-only" sólo será mostrado en lectores de pantallas, como puede ser JAWS-->
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
                    <a class="nav-link active" href="../acerca/acerca.php">Acerca de nosotros
                        <span class="sr-only">(actual)</span></a>
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
            <p class="h3">Este sitio web es el resultado de un proyecto de fin de ciclo formativo, desarrollado con el objetivo de aplicar y demostrar los conocimientos adquiridos a lo largo del curso.</p>
            <p class="h3">Más información: <a href="https://ibq.es/cursos/2o-daweb/" class="badge badge-pill badge-danger">https://ibq.es/cursos/2o-daweb/</a></p>
            <section class="d-flex justify-content-center mb-5">
                <img src="../imagenes/logo_ibq.webp" alt="Logo del IBQ" width="200px">
            </section>

    
            <p class="h3">El contenido, incluyendo imágenes, textos y otros recursos digitales, está licenciado bajo una licencia Creative Commons, lo que significa que puedes compartir, reutilizar y adaptar el material siempre que respetes los términos de la licencia, como la atribución del trabajo original y el uso no comercial.</p>
            <p class="h3">Más información <i>(inglés)</i>: <a href="https://creativecommons.org/licenses/by-nc/4.0/deed.en" class="badge badge-pill badge-danger">https://creativecommons.org/licenses/by-nc/4.0/deed.en/</a></p>
            <section class="d-flex justify-content-center mb-5">
                <img src="../imagenes/acerca_licencia.svg" alt="Logo de la licencia" width="200px">
            </section>

            <p class="h3 mb-5">Existe la excepción de las imágenes utilizadas en este sitio para los diferentes productos, obtenidas de fuentes externas y están sujetas a sus propias licencias. Estas imágenes no están cubiertas por la licencia Creative Commons del sitio y su uso está restringido según los términos establecidos por sus respectivos propietarios. Por favor, revisa las licencias de dichas imágenes antes de utilizarlas, nuestro sitio puede hacer uso de ellas debido a su índole educativa.</p>

            <p class="h3">También cabe mencionar la excepción de las fuentes utilizadas para el texto, Poppins y Nerko, las cuales siguen la licencia de Open Font License, que permite su uso sin límites y sin necesidad de citar a los autores, esto último lo hemos hecho igual para facilitar su reconocimiento.</p>
            <p class="h3">Más información <i>(inglés)</i>: <a href="https://openfontlicense.org/" class="badge badge-pill badge-danger">https://openfontlicense.org/</a></p>
            <p class="h3 mb-5">También puede consultarse la licencia en el directorio "fuentes/OFL.txt".</p>

            <!--
                Copyright 2011-2019 The Bootstrap Authors.
                Copyright 2011-2019 Twitter, Inc.
                Copyright 2018 JS Foundation and other contributors
                Copyright 2019 Federico Zivolo

                Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

                The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

                THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
            -->

            <p class="h3">Adicionalmente, el proyecto hace uso del framework Bootstrap, la libreria jQuery y la libreria Popper.js. Bootstrap, jQuery y Popper.js se hallan bajo la licencia MIT que concede permiso gratuito a cualquier persona para usar, copiar, modificar, fusionar, publicar, distribuir, sublicenciar y/o vender copias, así como permitir a otros hacerlo.</p>
            <p class="h3">Más información <i>(inglés)</i>: <a href="https://opensource.org/license/MIT" class="badge badge-pill badge-danger">https://opensource.org/license/MIT</a> o <i>(inglés)</i>: <a href="https://jquery.com/license/" class="badge badge-pill badge-danger">https://jquery.com/license/</a></p>
            <p class="h3 mb-5">También pueden consultarse las primeras líneas de los archivos en los subdirectorios de "recursos" para más detalles sobre la autoría y puede encontrarse en los comentarios de esta página una copia del texto de la licencia.</p>

            <p class="h3 mb-5">El logotipo ha sido creado mediante el generador de imágenes de Bing y ha sido editado de forma manual mediante el programa Photopea, la fuente utilizada en él es Dhurjati, licenciada también mediante Open Font License.</p>
        
            <p class="h3">Este sitio web sigue las recomendaciones de accesibilidad de W3C en su nivel A.</p>
            <p class="h3">Más información <i>(inglés)</i>: <a href="https://www.w3.org/TR/WCAG22/" class="badge badge-pill badge-danger">https://www.w3.org/TR/WCAG22/</a></p>
            <section class="d-flex justify-content-center mb-5">
                <img src="../imagenes/wcag2.2A-blue.png" alt="Logo de accesibilidad" width="200px">
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

    <script src="acerca.js"></script>
</body>
</html>
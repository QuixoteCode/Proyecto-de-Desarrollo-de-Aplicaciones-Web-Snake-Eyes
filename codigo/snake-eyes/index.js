function anadirCarrito(producto_id) {
    //Hacemos una petición para agregar el producto a la sesión del carrito
    fetch(`carrito/anadir-carrito.php?producto_id=${producto_id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Producto añadido al carrito");
            } else {
                alert("Hubo un error al añadir el producto al carrito");
            }
        })
        .catch(error => console.error("Error:", error));
}

//Si se hace click sobre la barra de cookies y privacidad esta desaparecerá hasta recargar la página
document.getElementById('barra-cookies-index').addEventListener('click', () => {
    new bootstrap.Collapse(document.getElementById('barra-cookies-index')).toggle();
});

//Si se aceptan las cookies la barra de estas desaparecerá permanentemente
document.addEventListener("DOMContentLoaded", function () {
    const BARRA_COOKIES = document.getElementById("barra-cookies-index");
    const BOTON_ACEPTAR = document.getElementById("btn-aceptar-cookies");

    //Verificamos si ya se aceptaron las cookies
    if (localStorage.getItem("cookies-aceptadas") === "true") {
        BARRA_COOKIES.classList.add("cookies-ocultas");
    }

    //Manejamos el clic en el botón
    BOTON_ACEPTAR.addEventListener("click", function () {
        BARRA_COOKIES.classList.add("cookies-ocultas");
        localStorage.setItem("cookies-aceptadas", "true");
    });
});
function verFavoritos() {
    window.location.href = "favoritos.php";
}

function anadirCarrito(producto_id) {
    //Hacemos una petición para agregar el producto a la sesión del carrito
    fetch(`../carrito/anadir-carrito.php?producto_id=${producto_id}`)
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

//Se encarga de que el padding-bottom sea dinámico
document.addEventListener("DOMContentLoaded", function() {
    function adjustPadding() {
        const FOOTER = document.querySelector('.footer');
        const CONTENIDO = document.getElementById('contenido');
        const FOOTER_ALTURA = FOOTER.offsetHeight;

        CONTENIDO.style.paddingBottom = FOOTER_ALTURA + 'px';
    }

    adjustPadding();
    window.addEventListener('resize', adjustPadding);
});
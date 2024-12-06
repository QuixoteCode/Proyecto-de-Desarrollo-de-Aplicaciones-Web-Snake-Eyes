function volverAlIndice() {
    window.location.href = "../index.php";
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
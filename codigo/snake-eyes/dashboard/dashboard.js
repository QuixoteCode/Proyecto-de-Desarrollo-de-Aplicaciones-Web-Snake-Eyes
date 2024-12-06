function eliminarPublicacion(publicacion_id) {
    if (confirm("¿Estás seguro de que deseas eliminar esta publicación?")) {
        //Realizamos una llamada AJAX mediante fetch para enviar la solicitud de eliminación de la publicación
        fetch('eliminar-publicacion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `publicacion_id=${publicacion_id}`
        })
        .then(response => response.text())
        .then(data => {
            //Mostramos la respuesta del documento PHP
            alert(data);
            //Recargamos la página para reflejar los cambios
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    }
}

function banearUsuario(nombre_usuario) {
    if (confirm("¿Estás seguro de que deseas banear a este usuario?")) {
        //Realizamos una llamada AJAX mediante fetch para enviar la solicitud de baneo del usuario
        fetch('banear-usuario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `nombre_usuario=${encodeURIComponent(nombre_usuario)}`
        })
        .then(response => response.text())
        .then(data => {
            //Mostramos la respuesta del documento PHP
            alert(data);
            //Recargamos la página para reflejar los cambios
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    }
}

function eliminarReporte(reporte_id) {
    if (confirm("¿Estás seguro de que deseas eliminar este reporte?")) {
        //Realizamos una llamada AJAX mediante fetch para enviar la solicitud de eliminación del reporte
        fetch('eliminar-reporte.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `reporte_id=${reporte_id}`
        })
        .then(response => response.text())
        .then(data => {
            //Mostramos la respuesta del documento PHP
            alert(data);
            //Recargamos la página para reflejar los cambios
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    }
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

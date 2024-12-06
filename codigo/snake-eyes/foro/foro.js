//Función para registrar el "me gusta" o eliminarlo en caso de que ya esté presente vía fetch
async function darMeGusta(publicacion_id) {
    const SVG = document.getElementById('gusta-svg-' + publicacion_id);
    const CONTADOR_DE_ME_GUSTAS = document.getElementById('cuenta-de-gustas-' + publicacion_id);

    //Verificamos si el SVG ya está activado o desactivado
    if (SVG.classList.contains('gustado')) {
        //Si el usuario ya ha dado "me gusta", se eliminará la clase que lo indica visualmente mediante el color rojo
        SVG.classList.remove('gustado');
    } else {
        //Si el usuario no ha dado "me gusta", se añadirá la clase que lo indica visualmente mediante el color rojo
        SVG.classList.add('gustado');
    }

    //Enviamos una solicitud al servidor para añadir o eliminar el "me gusta" en la base de datos
    const RESPUESTA = await fetch('gusta.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ publicacion_id: publicacion_id })
    });

    const RESULTADO = await RESPUESTA.json();

    //Actualizamos el contador de "me gustas"
    CONTADOR_DE_ME_GUSTAS.textContent = RESULTADO.total_gustas;
}

//Función para enviar el reporte vía fetch
async function reportar(id, titulo, nombre_autor) {
    console.log(`Reportando publicación con ID: ${id}`);

    //Obtenemos el motivo del reporte
    const MOTIVO = document.getElementById(`motivo-${id}`).value;

    //Verificamos si se ha seleccionado un motivo
    if (!MOTIVO) {
        alert("Por favor, selecciona un motivo para reportar.");
        return;
    }
    const RESPUESTA = await fetch("reportar.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `id=${encodeURIComponent(id)}&titulo=${encodeURIComponent(titulo)}&motivo=${encodeURIComponent(MOTIVO)}&nombre_autor=${encodeURIComponent(nombre_autor)}`
    });
    
    //Obtenemos la respuesta como texto
    const TEXTO_RESPUESTA = await RESPUESTA.text();  
    
    //Convertirmos a la respuesta en JSON
    try {
        const RESULTADO = JSON.parse(TEXTO_RESPUESTA);
        if (RESULTADO.status === "success") {
            document.getElementById(`report-resultado-${id}`).innerText = RESULTADO.message;
        } else {
            document.getElementById(`report-resultado-${id}`).innerText = RESULTADO.message;
        }
    } catch (e) {
        console.error("Error al parsear JSON:", e);
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
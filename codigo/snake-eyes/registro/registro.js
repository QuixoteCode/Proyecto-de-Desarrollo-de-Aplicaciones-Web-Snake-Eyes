function validarEdad() {
    const FECHA_NACIMIENTO = new Date(document.getElementById("fecha_nacimiento").value);
    const FECHA_ACTUAL = new Date();
    const MES = FECHA_ACTUAL.getMonth() - FECHA_NACIMIENTO.getMonth();

    //Obtenemos la edad restando el año actual al año de nacimiento
    let edad = FECHA_ACTUAL.getFullYear() - FECHA_NACIMIENTO.getFullYear();

    //Si la variable mes es inferior a cero o igual a cero y los días de la fecha de nacimiento son superiores a los días de la fecha actual reducimos en 1 la edad
    if (MES < 0 || (MES === 0 && FECHA_ACTUAL.getDate() < FECHA_NACIMIENTO.getDate())) {
        edad--;
    }

    if(edad < 18) {

        window.alert("Se requiere la mayoría de edad para poder registrarse.");
        window.location.href = "../index.php";
        
        return false;
    }
    return true;
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



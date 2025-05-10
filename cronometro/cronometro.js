let segundos = 0;
let intervalo = null;

function actualizarCronometro() {
    let hrs = Math.floor(segundos / 3600);
    let mins = Math.floor((segundos % 3600) / 60);
    let secs = segundos % 60;

    document.getElementById('cronometro').innerText =
        `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

function iniciarCronometro() {
    if (intervalo) return; // Ya está corriendo
    intervalo = setInterval(() => {
        segundos++;
        actualizarCronometro();
    }, 1000);
}

function pararCronometro() {
    clearInterval(intervalo);
    intervalo = null;
}

// Esperar a que la página cargue
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('btnIniciar').addEventListener('click', iniciarCronometro);
    document.getElementById('btnParar').addEventListener('click', pararCronometro);
});

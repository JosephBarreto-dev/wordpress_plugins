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

    let nombre = document.getElementById('nombre_usuario').value.trim();

    if (!nombre) {
        alert('Por favor, ingresa tu nombre.');
        return;
    }

    let hrs = Math.floor(segundos / 3600);
    let mins = Math.floor((segundos % 3600) / 60);
    let secs = segundos % 60;

    let tiempoFormateado = `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;

    jQuery.post(cp_ajax_obj.ajax_url, {
        action: 'cp_guardar_tiempo',
        tiempo: tiempoFormateado,
        nombre: nombre,
    },  function(response) {
        if (response.success) {
            alert('Tiempo guardado para '+ nombre +': ' + tiempoFormateado);
        } else {
            alert('Error al guardar el tiempo: ' + response.data);
        }
    });
    // Reiniciar el cronómetro
    document.getElementById('nombre_usuario').value = '';
    document.getElementById('cronometro').innerText = '00:00:00';
    segundos = 0;
}
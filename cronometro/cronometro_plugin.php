<?php
/*
Plugin Name: Cronómetro
Description: Un cronómetro que se puede iniciar y parar con botones.
Version: 1.1
Author: Joseph
*/

if (!defined('ABSPATH')) {
    exit; // Seguridad
}

// Registrar y encolar JS
function cp_registrar_scripts() {
    wp_register_script('cp-cronometro', plugins_url('cronometro.js', __FILE__), array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'cp_registrar_scripts');

// Shortcode
function cp_mostrar_cronometro() {
    // Encolar script al usar el shortcode
    wp_enqueue_script('cronometro', plugin_dir_url(__FILE__) . 'cronometro.js', array('jquery'), null, true);

    wp_localize_script('cronometro', 'cp_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));

    ob_start();
    ?>
    <div>
        <label for="nombre_usuario">Nombre:</label>
        <input type="text" id="nombre_usuario" placeholder="Escribe tu nombre" />
    </div>

    <div id="cronometro" style="font-size: 2em; margin: 10px 0;">00:00:00</div>
    <button onclick="iniciarCronometro()">Iniciar</button>
    <button onclick="pararCronometro()">Parar</button>
    <?php
    return ob_get_clean();
}
add_shortcode('cronometro', 'cp_mostrar_cronometro');

register_activation_hook(__FILE__, 'cp_crear_tablas_cronometro');

function cp_crear_tablas_cronometro() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Tabla usuarios
    $tabla_usuarios = $wpdb->prefix . 'usuarios';
    $sql_usuarios = "CREATE TABLE $tabla_usuarios (
        id_usuario int NOT NULL AUTO_INCREMENT,
        nombre varchar(100) NOT NULL,
        PRIMARY KEY (id_usuario)
    ) $charset_collate;";

    // Tabla tiempos
    $tabla_tiempos = $wpdb->prefix . 'tiempos';
    $sql_tiempos = "CREATE TABLE $tabla_tiempos (
        id_tiempo int NOT NULL AUTO_INCREMENT,
        id_usuario int NOT NULL,
        tiempo varchar(8) NOT NULL,
        PRIMARY KEY (id_tiempo),
        FOREIGN KEY (id_usuario) REFERENCES $tabla_usuarios(id_usuario) ON DELETE CASCADE
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_usuarios);
    dbDelta($sql_tiempos);
}

function cp_guardar_tiempo() {
    global $wpdb;
    $tabla_usuarios = $wpdb->prefix . 'usuarios';
    $tabla_tiempos = $wpdb->prefix . 'tiempos';

    $tiempo = sanitize_text_field($_POST['tiempo']);
    $nombre = sanitize_text_field($_POST['nombre']);

    if (!$nombre || !preg_match('/^\d{2}:\d{2}:\d{2}$/', $tiempo)) {
        wp_send_json_error('Datos inválidos');
    }


    // Buscar si el usuario ya existe
    $id_usuario = $wpdb->get_var($wpdb->prepare(
        "SELECT id_usuario FROM $tabla_usuarios WHERE nombre = %s",
        $nombre
    ));

    // Si no existe, lo creamos
    if (!$id_usuario) {
        $wpdb->insert($tabla_usuarios, array('nombre' => $nombre));
        $id_usuario = $wpdb->insert_id;
    }

    // Ahora guardamos el tiempo relacionado al usuario
    $resultado = $wpdb->insert($tabla_tiempos, array(
        'id_usuario' => $id_usuario,
        'tiempo' => $tiempo
    ));

    if ($resultado == false) {
        wp_send_json_error('Error al guardar el tiempo: ' . $wpdb->last_error);
    }

    wp_send_json_success('Tiempo guardado');
}

add_action('wp_ajax_cp_guardar_tiempo', 'cp_guardar_tiempo');
add_action('wp_ajax_nopriv_cp_guardar_tiempo', 'cp_guardar_tiempo');
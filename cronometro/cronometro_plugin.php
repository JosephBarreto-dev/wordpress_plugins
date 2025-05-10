<?php
/*
Plugin Name: Cronómetro Simple
Description: Un cronómetro que puedes iniciar y parar con botones.
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
    wp_enqueue_script('cp-cronometro');

    ob_start();
    ?>
    <div id="cronometro" style="font-size: 2em; margin-bottom: 10px;">00:00:00</div>
    <button id="btnIniciar">Iniciar</button>
    <button id="btnParar">Parar</button>
    <?php
    return ob_get_clean();
}
add_shortcode('cronometro', 'cp_mostrar_cronometro');

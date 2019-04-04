<?php
/*
Plugin Name: Cinco
Description: Plugin utilizado para modularizar código compartilhado entre as diferentes aplicações Wordpress da Cinco
Author: Bruno Degani
Version: 1.0.0
Updated: 12/12/2018
*/

//define constantes
define("ERRO", "ERRO");
define("SUCESSO", "SUCESSO");

//  DIR PATH
define("CINCO_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("CINCO_INC_PATH", CINCO_PLUGIN_PATH . "inc/");

//  URL
define("CINCO_PLUGIN_URL", plugin_dir_url( __FILE__ ));
define("CINCO_CSS_URL", CINCO_PLUGIN_URL . "css/");
define("CINCO_JS_URL", CINCO_PLUGIN_URL . "js/");
define("CINCO_JSON_URL", CINCO_PLUGIN_URL . "json/");
define("CINCO_IMG_URL",  CINCO_PLUGIN_URL . "img/");

//chaves para configurações na tabela WP_OPTIONS
define("CINCO_BD_WP_OPTION", "cinco_bd");
define("CINCO_ALERTA_WP_OPTION", "cinco_alerta");

// inclui módulos
require_once( CINCO_INC_PATH . 'security.php');
require_once( CINCO_INC_PATH . 'form_post.php');
require_once( CINCO_INC_PATH . 'ajax.php');
require_once( CINCO_INC_PATH . 'debug.php');
require_once( CINCO_INC_PATH . 'database.php');

//carrega menu de administração do plugin
require_once( CINCO_PLUGIN_PATH . 'admin/index.php'); 

/**  
 * cinco_footer - substitui o rodapé padrão do tema.  
 * a troca do rodapé é feita via javascript para não ser necessário alterar o template de plugin do tema 
 * (que poderia ser perdido em caso de atualização do tema)
 **/ 
add_action( 'wp_footer', 'cinco_footer' );
function cinco_footer()
{
    $copyright = "&copy;" . date('Y');
    $url = home_url();
    $site = get_bloginfo('name');
    $rodape = "$copyright - <a href=\"$url\">$site</a><br><i>Esse site é melhor visualizado no Google Chrome</i>";
     echo "<script>jQuery('.site-info').html('$rodape')</script>";
}

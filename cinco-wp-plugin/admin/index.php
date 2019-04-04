<?php
/**
 *  Adiciona item ao menu de configurações do painel administrativo do Wordpress
 */
require_once( CINCO_PLUGIN_PATH . 'admin/pages/bd_cfg_fields.php'); 
require_once(CINCO_PLUGIN_PATH . 'admin/pages/alerta_cfg_fields.php');

add_action( 'admin_menu', 'cinco_admin_link' );
function cinco_admin_link(){

    $admin_menu = add_menu_page(
        'Cinco - Configurações',
        'Cinco',
        'manage_options', 
        __FILE__, 
        NULL, 
        CINCO_IMG_URL . 'icone-cinco.png'
    );

    add_submenu_page(
        __FILE__, 
        'Cinco - Alertas', 
        'Alerta', 
        'manage_options', 
        __FILE__, 
        'cinco_admin_alerta',
        0);    
    
    add_submenu_page(
        __FILE__, 
        'Cinco - Conexões de Banco de Dados', 
        'Banco de Dados', 
        'manage_options', 
        __FILE__.'/bd', 
        'cinco_admin_bd',
        1);    
}
/**
 * registra opções de configuração
 */
add_action( 'admin_init', 'cinco_settings_init' );
function cinco_settings_init(){
    register_setting( CINCO_BD_WP_OPTION, CINCO_BD_WP_OPTION );
    add_settings_section(CINCO_BD_WP_OPTION,'Conexões:','cinco_bd_section',CINCO_BD_WP_OPTION);
   
    register_setting( CINCO_ALERTA_WP_OPTION, CINCO_ALERTA_WP_OPTION );
    add_settings_section(CINCO_ALERTA_WP_OPTION,'Mensagem de alerta','cinco_alerta_section',CINCO_ALERTA_WP_OPTION);
}

/**
 * Configurações de Banco de Dados
 */
function cinco_admin_bd(){
    include_once(CINCO_PLUGIN_PATH . 'admin/pages/cinco-admin-bd.php');
}

function cinco_bd_section() {
    add_settings_field(CINCO_BD_WP_OPTION,'','cinco_bd_fields',CINCO_BD_WP_OPTION,CINCO_BD_WP_OPTION);
}

/**
 * Configurações de alertas
 */
function cinco_alerta_section(){    
    add_settings_field('cinco_alerta_enabled','Exibir','cinco_alerta_enabled_field',
        CINCO_ALERTA_WP_OPTION,CINCO_ALERTA_WP_OPTION);
    add_settings_field('cinco_alerta_titulo','Titulo','cinco_alerta_titulo_field',
        CINCO_ALERTA_WP_OPTION,CINCO_ALERTA_WP_OPTION);
    add_settings_field('cinco_alerta_msg1','Mensagem (linha 1)','cinco_alerta_msg1_field',
        CINCO_ALERTA_WP_OPTION,CINCO_ALERTA_WP_OPTION);
    add_settings_field('cinco_alerta_msg2','Mensagem (linha 2)','cinco_alerta_msg2_field',
        CINCO_ALERTA_WP_OPTION,CINCO_ALERTA_WP_OPTION);

    echo 'Configuração de Mensagem de Alerta na Aplicação';
}

function cinco_admin_alerta(){
    include_once(CINCO_PLUGIN_PATH . 'admin/pages/cinco-admin-alerta.php');

    $notification_bar_options = get_option( 'wpfront-notification-bar-options' );
    
    if(isset(get_option( CINCO_ALERTA_WP_OPTION )['exibir']))
        $notification_bar_options['enabled'] = 'on';
    else
        unset($notification_bar_options['enabled']);
    
    $titulo = get_option( CINCO_ALERTA_WP_OPTION )['titulo'];
    $msg1 = get_option( CINCO_ALERTA_WP_OPTION )['msg1'];
    $msg2 = get_option( CINCO_ALERTA_WP_OPTION )['msg2'];

    $html_mensagem = "<span class='titulo_alerta'>$titulo</span><br>\n";
    $html_mensagem.= "<span class='texto_alerta'>$msg1</span><br>\n";
    $html_mensagem.= "<span class='texto_alerta'>$msg2</span>";

    $notification_bar_options['message'] = $html_mensagem;

    update_option( 'wpfront-notification-bar-options', $notification_bar_options);
}

/**
 * Mensagens genérica com resultado da atualização da configuração
 */
function admin_err_msg() {
    settings_errors();   
}
add_action( 'admin_notices', 'admin_err_msg' );
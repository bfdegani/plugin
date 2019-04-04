<?php
/**
 * Definições associadas à controle de acesso (usadas em conjunto com o plugin Advanced Access Manager)  
 * Implementado por Bruno Degani em 13/11/2018
 **/

 // Desabilita apresentação da barra do Wordpress para quem não é admin
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
	  show_admin_bar(false);
	}
}

//  Desabilita confirmação de logout
add_action('check_admin_referer', 'logout_without_confirm', 10, 2);
function logout_without_confirm($action, $result)
{
    if ($action == "log-out" && !isset($_GET['_wpnonce'])) {
        $redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : wp_login_url();
        $location = str_replace('&amp;', '&', wp_logout_url($redirect_to));
        header("Location: $location");
        die;
    }
}

// Desabilita opção de reset de senha pelo site 
add_filter ( 'allow_password_reset', 'disable_password_reset' );
function disable_password_reset() {
	return false;
}

// inibe mensagens específicas para erros de login
add_filter('login_errors', 'login_error_message');
function login_error_message() {
	return '<b>ERRO:</b> Usuário e/ou Senha inválidos';
}

// redirect para página de administração de usuários, função usada pelo gestor de usuários (Role: TAT_GR_SECURITY)
function redirect_security_admin(){
	wp_redirect( get_admin_url() . "users.php" );
	exit();
}
?>
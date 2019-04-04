<?php
/**
 * Handler genérico para tratar form post dentro do Wordpress
 * Criado por Bruno Degani em 12/12/2018
 * 
 * O Conteúdo da variável $_POST é armazenado dentro de 
 * $_SESSION para que não seja perdido durante o encaminhamento
 */
add_action('admin_post_custom_form_handler', 'custom_form_handler');
function custom_form_handler() {
	if(isset( $_POST['custom_form_handler_nonce'] ) && wp_verify_nonce( $_POST['custom_form_handler_nonce'], 'custom_form_handler_nonce' ))
	{
		if(!isset($_SESSION))
			session_start(); 
		$_SESSION['_POST'] = $_POST;
		wp_redirect($_POST['_wp_http_referer']);
	}
}
?>
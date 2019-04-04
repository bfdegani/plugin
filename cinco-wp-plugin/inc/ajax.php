<?php

//require_once(CINCO_INC_PATH . 'database.php');

/**
 * Handler genérico para tratar Ajax requests dentro do Wordpress
 * Criado por Bruno Degani em 12/12/2018
 */
// Handler para usuário logado:  
add_action( 'wp_ajax_custom_ajax_handler', 'custom_ajax_handler' );

// Handler para usuário não logado:  
add_action( 'wp_ajax_nopriv_custom_ajax_handler', 'custom_nopriv_ajax_handler' );

//Adiciona ao Wordpress javascript utilizado para manipular as requisições
add_action( 'wp_enqueue_scripts', 'secure_enqueue_script' );
function secure_enqueue_script() {
  wp_register_script('secure-ajax-access', esc_url(add_query_arg(array('js_global' => 1), site_url())));
  wp_enqueue_script('secure-ajax-access');
}

//Adiciona o nonce e a url das requisições ao Javascript criado acima
add_action( 'template_redirect', 'javascript_variaveis' );
function javascript_variaveis() {
  if ( !isset( $_GET[ 'js_global' ] ) ) return;

  $nonce = wp_create_nonce('custom_ajax_handler_nonce');

  $variaveis_javascript = array(
    'custom_ajax_handler_nonce' => $nonce, //Esta função cria um nonce para a requisição.
    'xhr_url' => admin_url('admin-ajax.php') // Forma para pegar a url para as consultas dinamicamente.
  );

  $new_array = array();
  foreach($variaveis_javascript as $var => $value) 
    $new_array[] = esc_js($var) . " : '" . esc_js($value) . "'";

  header("Content-type: application/x-javascript");
  printf('var %s = {%s};', 'js_global', implode(',', $new_array));
  exit;
}

// Ajax handler
// Esse handler executa uma função existente no arquivo functions.php do plugin informado na requisição Ajax
// O retorno da função executada deve ser um array no formato [$err,$response]
// Em caso de sucesso $err deve ser 0
function custom_ajax_handler()
{
    if( !wp_verify_nonce( $_POST['custom_ajax_handler_nonce'], 'custom_ajax_handler_nonce' ) ) {
      wp_send_json_error('401: Requisição Inválida'); // Caso não seja verificado o nonce enviado, a requisição vai retornar 401
    }

    $plugin = $_POST['plugin'];
    $module = $_POST['module'];
    $function = $_POST['function'];
    $args = $_POST['args'];   
    
    $module_path = WP_PLUGIN_DIR . "/$plugin/modules/$module/functions.php";
    require_once($module_path);

    $ret = $function($args);
    //$mensagem = '{"mensagem": "'. $ret['mensagem'] . '", "erros": "' . $ret['erros'] . '"}';
    $response_json =  json_encode($ret);

    if($ret['codigo'] === SUCESSO)
      wp_send_json_success($response_json);
    else 
      wp_send_json_error($response_json);
}

// ajax handler para usuários não logados
// não implementado. apenas retorna 403 (forbidden)
function custom_nopriv_ajax_handler()
{
  wp_send_json_error('403: Acesso Não Autorizado');
}
?>
<?php
/**
 * funções auxiliares para debugging da aplicação
 * Criado por Bruno Degani em 12/12/2018
 */

// função para gravação de objetos no log
function var_dump2error_log($v){
	ob_start();
	var_dump($v);
	$contents = ob_get_contents();
	ob_end_clean();
	error_log($contents);
}

// lista os filtros associados a um hook específico
function print_filters_for( $hook = '' ) {
    global $wp_filter;
    if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
        return;
	var_dump2error_log( $wp_filter[$hook] );
}
?>
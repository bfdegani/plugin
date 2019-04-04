<?php
        require_once(plugin_dir_path(__FILE__) . 'model/chamados-dao.php');
        $chamados = ChamadosDAO::listaChamados();
        
        include_once(plugin_dir_path(__FILE__) . 'inc/grid.php');
?>

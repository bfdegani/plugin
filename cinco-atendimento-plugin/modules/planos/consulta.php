<?php
    require_once(plugin_dir_path(__FILE__) . 'model.php');
    $planos_disponiveis = PlanosDAO::getPlanos();
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.4/b-html5-1.5.4/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.4/b-html5-1.5.4/datatables.min.js"></script>
<table id='planos' class='table table-striped' style='width:100%'>
    <thead>
        <tr>
            <th>Plano</th>
            <th>MB/Chip</th>
            <th>Franquia Mensal (R$)</th>
            <!--<th>MB Adcional (R$)</th>-->
            <th>Instalação (R$)</th>
            <th>Reposição (R$)</th>
		    <th></th>
        </tr>
    </thead>
  	<tbody>
        <?php foreach($planos_disponiveis as $p){ ?>
            <tr>  
                <td><?php echo $p->nome; ?></td>
                <td><?php echo $p->mb_franquia; ?></td>
                <td><?php echo $p->preco_franquia; ?></td>
                <!--<td><?php echo $p->preco_mb_adicional; ?></td>-->
                <td><?php echo $p->preco_instalacao; ?></td>
                <td><?php echo $p->preco_reposicao; ?></td>
                <td>*preços <?php echo ($p->preco_inclui_impostos?'com':'sem'); ?> impostos</td>
            </tr>
        <?php  } ?>
    </tbody>
    <thead>
        <tr>
            <th>Plano</th>
            <th>MB/Chip</th>
            <th>Franquia Mensal (R$)</th>
            <!--<th>MB Adcional (R$)</th>-->
            <th>Instalação (R$)</th>
            <th>Reposição (R$)</th>
		    <th></th>   
        </tr>
    </thead>
</table>
<script>
    jQuery(document).ready( function($){ 
        $('#planos').DataTable({
            dom: 'itr', 
            language:{url:'//cdn.datatables.net/plug-ins/1.10.19/i18n/Portuguese-Brasil.json'},
            search: {caseInsensitive:true }
        });
    });
</script>

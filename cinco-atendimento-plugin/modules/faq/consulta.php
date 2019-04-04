<?php
    require_once(plugin_dir_path(__FILE__) . 'model.php');
    $faq = FaqDAO::getFAQ();
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.4/b-html5-1.5.4/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.4/b-html5-1.5.4/datatables.min.js"></script>
<table id='faq' class='table table-striped table-hover' style='width:100%'>
    <thead>
        <tr>
            <th>Pergunta</th>
            <th>Resposta</th>
            <th>Publicado em</th>
            <th style="display:none">Palavras-Chave</th>
        </tr>
    </thead>
  	<tbody>
        <?php foreach($faq as $f){ ?>
            <tr>  
                <td><?php echo $f->pergunta; ?></td>
                <td><?php echo $f->resposta; ?></td>
                <td><?php echo $f->data_alteracao; ?></td>
                <td style="display:none"><?php echo $f->palavras_chave; ?></td>
            </tr>
        <?php  } ?>
    </tbody>
    <thead>
        <tr>
            <th>Pergunta</th>
            <th>Resposta</th>
            <th>Publicado em</th>
            <th style="display:none">Palavras-Chave</th>
        </tr>
    </thead>
</table>
<script>
    jQuery(document).ready( function($){ 
        $('#faq').DataTable({
            dom: '<lf<itr>p>', 
            language:{url:'//cdn.datatables.net/plug-ins/1.10.19/i18n/Portuguese-Brasil.json'},
            search: {caseInsensitive:true }
        });
    });
</script>

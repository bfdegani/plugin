<?php
    require_once(plugin_dir_path(__FILE__) . 'functions.php');
?>
<?php 
    if($retorno != NULL && $retorno['codigo'] == 'ERRO') { 
        echo "<div role='alert' class='alert alert-danger'>" . $retorno['mensagem'] . "</div>";
    } 
?>

<form class="needs-validation" novalidate id="form_consulta_chamado" method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
    <!-- envia infomrações pra o handler padrão de processamento de formulários no wordpress -->
    <div class="form-group">
        <input type="hidden" name="action" value="custom_form_handler">
        <input type="hidden" name="operation" value="consulta">
        <?php wp_nonce_field('custom_form_handler_nonce', 'custom_form_handler_nonce'); ?>
    </div>
    <div class="form-row">
        <div class="form-group col-2 mb-2 mt-0">
            <label for="chave">&nbsp;</label>
            <select  class="form-control" id="chave" name="chave">
                <option value="">--- Selecione ---</option>
                <option value="id_cliente" <?php echo ($chave == "id_cliente" ? "selected" : "" )?>>Id Cliente</option>
                <option value="cpf_cnpj" <?php echo ($chave == "cpf_cnpj" ? "selected" : "" )?>>CPF / CNPJ</option>
                <option value="email" <?php echo ($chave == "email" ? "selected" : "" )?>>e-Mail</option>
                <option value="protocolo" <?php echo ($chave == "protocolo" ? "selected" : "" )?>>Protocolo</option>
            </select>
        </div>
        <div class="form-group col-2 mb-2 mt-0">
            <label for="valor">&nbsp;</label>
            <input type="text" class="form-control" id="valor" name="valor" value="<?php echo $valor ?>">
        </div>  
        <div class="form-group col-2 mb-2 mt-0 text-right text-nowrap">
            <label for="data_inicio">Data de Abertura
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;De:</label>
            <div class="input-group date" data-date-format="dd/mm/yyyy">
                <input  type="date" class="form-control" placeholder="dd/mm/yyyy" id="data_inicio" name="data_inicio" value='<?php echo $dt_i ?>'>
            </div>
        </div>
        <div class="form-group col-2 mb-2 mt-0 text-right">
            <label for="data_fim">Até:</label>
            <div class="input-group date" data-date-format="dd/mm/yyyy">
                <input  type="date" class="form-control" placeholder="dd/mm/yyyy" id="data_fim" name="data_fim" value='<?php echo $dt_f ?>'>
            </div>
        </div>
        <div class="form-group col-2 mt-4">
            <div class="form-check align-middle">
                <input class="form-check-input" type="checkbox" value="1" id="pendentes" name="pendentes"  
                    <?php echo ($retorno == NULL || $pendentes == "1" ? "checked" : "" )?>>
                <label class="form-check-label" for="pendentes">
                    <small>exibir pendentes</small>
                </label>
            </div>
            <div class="form-check align-middle">
                <input class="form-check-input" type="checkbox" value="1" id="concluidos" name="concluidos"  
                    <?php echo ($concluidos == "1" ? "checked" : "" )?>>
                <label class="form-check-label" for="concluidos">
                    <small>exibir concluídos</small>
                </label>
            </div>
        </div>
        <div class="form-group col-1 mb-2 mt-4">
            <button id="btn_consultar" class="btn btn-primary" type="submit">Consultar</button>
        </div>
    </div>
</form>

<br>
<hr style="border-top: 1px solid #ffffff">
<?php 
    if($retorno != NULL && $retorno['codigo'] == SUCESSO) {
        $chamados = $retorno['chamados'];
        include_once(plugin_dir_path(__FILE__) . 'inc/grid.php');
    } 
?>
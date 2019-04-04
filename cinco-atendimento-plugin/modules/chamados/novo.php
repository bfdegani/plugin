<?php
    require_once(plugin_dir_path(__FILE__) . 'model/motivos-contato-dao.php');
    require_once(plugin_dir_path(__FILE__) . 'model/protocolo-dao.php');
    require_once(plugin_dir_path(__FILE__) . 'model/canais-contato-dao.php');
    require_once(plugin_dir_path(__FILE__) . '../planos/model.php');
    require_once(plugin_dir_path(__FILE__) . 'functions.php');
?>
<div>
    <?php if($retorno != NULL) { ?>
        <div role="alert" class="alert <?php echo ($retorno['codigo'] == 'ERRO' ? 'alert-danger' : 'alert-info'); ?>"> 
            <?php echo $retorno['mensagem']; ?>
        </div>
    <?php } ?>
    <?php if(!$chamado->iniciado) {?>
    <form class="needs-validation" novalidate id="form_novo_chamado" method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <?php wp_nonce_field('custom_form_handler_nonce', 'custom_form_handler_nonce'); ?>
        <input type="hidden" name="action" value="custom_form_handler">
        <input type="hidden" name="operation" value="novo">
        <div class="form-row mb-0 mt-0 p-0">
            <div class="form-group col-md-2 mb-2 mt-0">
                <select class="form-control" id="chave" name="chave" autofocus required>
                    <option value="id_cliente" <?php echo ($chave == "id_cliente" ? "selected" : "" )?>>Id Cliente</option>
                    <option value="cpf_cnpj" <?php echo ($chave == "cpf_cnpj" ? "selected" : "" )?>>CPF / CNPJ</option>
                </select>
            </div>
            <div class="form-group col-sm-2 mb-2 mt-0">
                <input type="text" class="form-control" id="valor" name="valor" value="<?php echo $valor ?>" 
                    required <?php echo (isset($retorno['erros']) ? 'is-invalid' : 'is-valid') ?>>
                <div class="invalid-feedback">
                    <?php echo 'Por favor informe o<br>id ou cpf/cnpj do cliente'; ?>
                </div>
            </div>
            <div class="form-group col-sm-2 mb-2 mt-0">
                <button class="btn btn-primary" type="submit">Iniciar Atendimento</button>
            </div>
        </div>
    </form>
    <?php }
        else { 
            $planos = PlanosDAO::getPlanos();
            $motivos = MotivosContatoDAO::getMotivosContato();
    ?>
        <div class="row">
            <div class="col-4 text-left">Protocolo: <span class="text-primary"><?php echo $chamado->protocolo ?></span></div>
            <div class="col-4 text-center">Data: <span class="text-primary"><?php echo $chamado->data_criacao ?></span></div>
            <div class="col-4 text-right">CPF / CNPJ: <span class="text-primary"><?php echo $chamado->cpf_cnpj ?></span></div>
        </div>
        <hr style="border-color:#ffffff">
        <form class="needs-validation" novalidate id="form_registra_chamado" method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
            <?php wp_nonce_field('custom_form_handler_nonce', 'custom_form_handler_nonce'); ?>
            <input type="hidden" name="action" value="custom_form_handler">
            <input type="hidden" name="operation" value="registra">
            <input type="hidden" id="protocolo" name="chamado[protocolo]" value="<?php echo $chamado->protocolo ?>">
            <input type="hidden" id="data_criacao" name="chamado[data_criacao]" value="<?php echo $chamado->data_criacao ?>">
            <input type="hidden" id="id_cliente" name="chamado[id_cliente]" value="<?php echo $chamado->id_cliente ?>">
            <input type="hidden" id="cpf_cnpj" name="chamado[cpf_cnpj]" value="<?php echo $chamado->cpf_cnpj ?>">
            <div class="form-group mb-2">
                <label for="empresa">Empresa:</label>
                <input type="text" class="form-control" id="empresa" name="chamado[empresa]" placeholder="Razão Social ou Nome Fantasia"
                value="<?php echo $chamado->empresa ?>" <?php echo ($chamado->id_cliente != NULL ? "readonly" : "") ?>>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6 mb-2">
                    <label for="contato">Nome*:</label>
                    <input type="text" class="form-control <?php echo (isset($retorno['erros']['contato']) ? 'is-invalid' : 'is-valid') ?>" 
                        id="contato" name="chamado[contato]" placeholder="Nome do contato"
                        required autofocus value="<?php echo $chamado->contato ?>">
                    <div class="invalid-feedback">
                        <?php echo (isset($retorno['erros']['contato']) ? $retorno['erros']['contato'] : 'Informe o nome do contato') ?>
                    </div>
                </div>
                <div class="form-group col-md-3 mb-2">
                    <label for="telefone">Telefone*:</label>
                    <input type="tel" class="form-control <?php echo (isset($retorno['erros']['telefone']) ? 'is-invalid' : 'is-valid') ?>"  
                        id="telefone" name="chamado[telefone]" placeholder="Telefone de contato"
                        pattern="(\+?[0-9]{1,4}\s?)?\(?[0-9]{2,3}\)?\s?[0-9]{4,5}-?[0-9]{4}"
                        required value="<?php echo $chamado->telefone ?>">
                    <div class="invalid-feedback">
                    <?php echo (isset($retorno['erros']['telefone']) ? $retorno['erros']['telefone'] : 'Informe o telefone com o código de área') ?>
                    </div>
                </div>
                <div class="form-group col-md-3 mb-2">
                    <label for="email">E-mail:</label>
                    <input type="email" class="form-control <?php echo (isset($retorno['erros']['email']) ? 'is-invalid' : 'is-valid') ?>"  
                        id="email" name="chamado[email]" placeholder="E-mail de contato"
                        value="<?php echo $chamado->email ?>">
                    <div class="invalid-feedback">
                        <?php echo (isset($retorno['erros']['email']) ? $retorno['erros']['email'] : 'Formato de e-mail inválido') ?>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3 mb-2">
                    <label for="canal_contato">Canal de Contato: </label>
                    <select  class="form-control" id="canal_contato" name="chamado[canal_contato]">
                        <?php 
                            $canais_contato = CanaisContatoDAO::getCanaisContato();
                            foreach($canais_contato as $c) { 
                                echo '<option value="' . $c->nome . '" ' . ( $c->nome == $chamado->canal_contato ? ' selected' : '' ) . '>' . $c->descricao . '</option>';
                            } 
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-3 mb-2">
                    <label for="canal_retorno">Canal para Retorno: </label>
                    <select  class="form-control" id="canal_retorno" name="chamado[canal_retorno]">
                        <?php 
                            $canais_retorno = CanaisContatoDAO::getCanaisRetorno();
                            foreach($canais_retorno as $c) { 
                                echo '<option value="' . $c->nome . '" ' . ( $c->nome == $chamado->canal_retorno ? ' selected' : '' ) . '>' . $c->descricao . '</option>';
                            } 
                        ?>
                    </select>
                    <div class="invalid-feedback">
                        Selecione o plano de interesse
                    </div>
                </div>
                <div class="form-group col-md-3 mb-2">
                    <label for="motivo">Motivo do Contato*: </label>
                    <select  class="form-control" id="motivo" name="chamado[motivo]" onchange="javascript:trata_motivo_plano()">
                        <?php foreach($motivos as $m) { 
                            echo '<option value="' . $m->id . '" ' . ( $m->id == $chamado->motivo ? ' selected' : '' ) . '>' . $m->descricao . '</option>';
                        } ?>
                    </select>
                </div>
                <div class="form-group col-md-3 mb-2">
                    <label for="plano">Plano de Interesse: </label>
                    <select  class="form-control" id="plano" name="chamado[plano]">
                        <option value="">--- selecione ---</option>
                        <?php foreach($planos as $p) { 
                            echo '<option value="' . $p->id . '" ' . ( $p->id == $chamado->plano ? ' selected' : '' ) . '>' . $p->nome . '</option>';
                        } ?>
                    </select>
                    <div class="invalid-feedback">
                        Selecione o plano de interesse
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="solicitacao">Solicitação: </label>
                <textarea class="form-control <?php echo (isset($retorno['erros']['solicitacao']) ? 'is-invalid' : 'is-valid') ?>"  
                    rows="4" maxlength="1000" id="solicitacao" name="chamado[solicitacao]" placeholder="Registre aqui a solicitação do cliente e o atendimento realizado"  
                    spellcheck="true" required><?php $chamado->solicitacao ?></textarea>
                <div class="invalid-feedback">
                    <?php echo (isset($retorno['erros']['solicitacao']) ? $retorno['erros']['solicitacao'] : 'Registre a solicitação do cliente e o atendimento realizado') ?>
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Registrar</button>
            <button class="btn btn-primary" type="reset" onclick="location.reload()">Cancelar&nbsp;</button>
        </form>
        <script>
            function trata_motivo_plano(){
                if(jQuery('#motivo option:selected').text().toUpperCase().indexOf('PLANO') >= 0){
                    jQuery('#plano').prop("required", true);
                } else {
                    jQuery('#plano').prop("required", false);
                }
            }
        </script>
    <?php } ?>    

    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
                }, false);
            });
            }, false);
        })();
    </script>    
</div>
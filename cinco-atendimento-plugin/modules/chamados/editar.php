<?php
    require_once(plugin_dir_path(__FILE__) . 'functions.php');
?>
<div>
    <?php 
        if($retorno != NULL && $retorno['codigo'] == 'ERRO')
            echo "<div role='alert' class='alert alert-danger'>" . $retorno['mensagem'] . "</div>";

        $protocolo = preg_replace("/[^0-9]/", "", $_GET['protocolo']);
        if($protocolo != NULL){
            $chamado = ChamadosDAO::consultaChamados('protocolo',$protocolo,NULL,NULL,TRUE)[0];
            if($chamado->data_conclusao != NULL)
                echo "<div role='alert' class='alert alert-info'>Chamado $protocolo encerrado em $chamado->data_conclusao por $chamado->usuario_conclusao</div>"
    ?>
            <div class="row">
                <div class="col-3 text-left">Protocolo: <span class="text-primary"><?php echo $chamado->protocolo ?></span></div>
                <div class="col-4 text-left">Data de Criação: <span class="text-primary"><?php echo $chamado->data_criacao ?></span></div>
                <div class="col-3 text-left">Usuário Criação: <span class="text-primary"><?php echo $chamado->usuario_criacao ?></span></div>
                <div class="col-2 text-right">Dias em Aberto: <span class="text-primary"><?php echo $chamado->dias_em_aberto ?></span></div>
            </div>
            <div class="row">
                <div class="col-3 text-left">&nbsp;</div>
                <div class="col-4 text-left">Data de Conclusão: <span class="text-primary"><?php echo ($chamado->data_conclusao != NULL) ? $chamado->data_conclusao : 'em aberto'?></span></div>
                <div class="col-3 text-left">Usuário Conclusão: <span class="text-primary"><?php echo $chamado->usuario_conclusao ?></span></div>
                <div class="col-2 text-left">&nbsp;</div>
            </div>
            <hr style="border-color:#ffffff">
            <form class="needs-validation" novalidate id="form_chamado" method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
                    <?php wp_nonce_field('custom_form_handler_nonce', 'custom_form_handler_nonce'); ?>
                    <input type="hidden" name="action" value="custom_form_handler">
                    <input type="hidden" id="operation" name="operation" value="edita">
                    <input type="hidden" id="protocolo" name="protocolo" value="<?php echo $chamado->protocolo ?>">
                    <fieldset disabled>
                    <div class="form-row">
                        <div class="form-group col-md-9 mb-2">
                            <label for="empresa">Empresa:</label>
                            <input disabled type="text" class="form-control" id="empresa" value="<?php echo $chamado->empresa ?>">
                        </div>
                        <div class="form-group col-md-3 mb-2">
                            <label for="cpf_cnpj">CPF / CNPJ:</label>
                            <input disabled type="text" class="form-control" id="cpf_cnpj" value="<?php echo $chamado->cpf_cnpj ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6 mb-2">
                            <label for="contato">Nome:</label>
                            <input disabled type="text" class="form-control" id="contato" value="<?php echo $chamado->contato ?>">
                        </div>
                        <div class="form-group col-md-3 mb-2">
                            <label for="telefone">Telefone:</label>
                            <input disabled type="text" class="form-control" id="telefone" value="<?php echo $chamado->telefone ?>">
                        </div>
                        <div class="form-group col-md-3 mb-2">
                            <label for="email">E-mail:</label>
                            <input disabled type="text" class="form-control" id="email" value="<?php echo $chamado->email ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3 mb-2">
                            <label for="canal_contato">Canal de Contato: </label>
                            <input disabled type="text" class="form-control" id="canal_contato" value="<?php echo $chamado->canal_contato ?>">
                        </div>
                        <div class="form-group col-md-3 mb-2">
                            <label for="canal_retorno">Canal para Retorno: </label>
                            <input disabled type="text" class="form-control" id="canal_retorno" value="<?php echo $chamado->canal_retorno ?>">
                        </div>
                        <div class="form-group col-md-3 mb-2">
                            <label for="motivo">Motivo do Contato: </label>
                            <input disabled type="text" class="form-control" id="motivo" value='<?php echo "$chamado->motivo_desc: $chamado->motivo_sub_desc" ?>'>
                        </div>
                        <div class="form-group col-md-3 mb-2">
                            <label for="plano">Plano de Interesse: </label>
                            <input disabled type="text" class="form-control" id="plano" value="<?php echo $chamado->plano ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="solicitacao">Solicitação: </label>
                        <textarea disabled class="form-control" rows="3" id="solicitacao"><?php echo $chamado->solicitacao ?></textarea>
                    </div>
                    <?php if($chamado->data_conclusao != NULL) { ?>
                        <div class="form-group">
                            <label for="resposta">Resposta: </label>
                            <textarea disabled class="form-control" rows="3" id="resposta"><?php echo $chamado->resposta ?></textarea>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="comentarios">Histórico: </label>
                        <textarea disabled class="form-control" rows="3" id="comentarios"><?php 
                                if($chamado->comentarios != NULL){
                                    $comentarios = json_decode($chamado->comentarios)->{'comentarios'};
                                    foreach($comentarios as $c){
                                        echo $c->{'usuario'} . '(' . $c->{'data'} . '): ' . $c->{'texto'} . "\n";
                                    }
                                }
                            ?>
                        </textarea>
                    </div>
                </fieldset>
                <?php if($chamado->data_conclusao == NULL) { ?>
                    <div class="form-group">
                        <label for="comentario">Comentário: </label>
                        <textarea class="form-control" rows="4" id="comentario" name="comentario" 
                            placeholder="Registre aqui seu comentário ou resposta à solicitação do cliente."  
                            spellcheck="true" required></textarea>
                        <div class="invalid-feedback">Registre aqui seu comentário ou resposta à solicitação do cliente.</div>
                    </div>
                    <!-- Modal Confirmação (início) -->
                    <div class="modal" id="modal-confirmacao" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content" id="confirmacao">
                                <div class="modal-header">  
                                    <h5>
                                        <span id="mensagem-conf"></span>
                                        <br><small>Essa ação não pode ser desfeita.</small>
                                    </h5>
                                </div>
                                <div class="modal-body bg-info text-info">
                                    <p class="p-2 m-2 text-justify"><span class="comentario" id='comentario-conf'></span></p>
                                </div>
                                <div class="modal-footer p-2">
                                    <button type="submit" class="btn btn-primary" aria-label="OK">OK</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Cancelar">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Confirmação (fim) -->
                <?php } ?>
            </form>
            <?php
                if($chamado->data_conclusao == NULL) {
            ?>
                    <button class="btn btn-primary btn-acao"
                        data-toggle="modal" data-target="#modal-confirmacao" data-operacao="comentar">Comentar</button>
                    <button class="btn btn-primary btn-acao"
                        data-toggle="modal" data-target="#modal-confirmacao" data-operacao="encerrar">Encerrar</button>
                    <script>
                        jQuery(document).ready(function($){ 
                            $('.btn-acao').on('click', function(event) {
                                form = $('#form_chamado')[0];
                                if (form.checkValidity() === false) {
                                    event.preventDefault();
                                    event.stopPropagation();
                                }
                                form.classList.add('was-validated');
                            });
                            $('#modal-confirmacao').on('show.bs.modal', function (event) {
                                var button = $(event.relatedTarget); 
                                var op = button.data('operacao'); 
                                var protocolo = $('#protocolo').val();
                                if(op == 'encerrar') 
                                    $('#mensagem-conf').html('Confirma ENCERRAMENTO do chamado <span class="text-primary">' + protocolo + '</span> com a resposta abaixo?');
                                else if(op == 'comentar')
                                    $('#mensagem-conf').html('Confirma a inclusão do COMENTÁRIO abaixo ao chamado <span class="text-primary">' + protocolo + '</span>?');
                                
                                $('#operation').val(op);
                                $('#protocolo-conf').text(protocolo);
                                $('#comentario-conf').text($('#comentario').val());
                            });
                        });
                    </script>
            <?php } ?>
<?php } ?>    
</div>
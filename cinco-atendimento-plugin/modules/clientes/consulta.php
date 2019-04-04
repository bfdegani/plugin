<?php
    require_once(plugin_dir_path(__FILE__) . 'functions.php');
    if($retorno == 'ERRO') { 
        echo "<div role='alert' class='alert alert-danger'>$mensagem</div>";
    } 
?>

<!-- form e info basica -->
    <form class="needs-validation" novalidate id="form_consulta_cliente" method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <!-- envia infomrações pra o handler padrão de processamento de formulários no wordpress -->
        <div class="form-group">
            <input type="hidden" name="action" value="custom_form_handler">
            <?php wp_nonce_field('custom_form_handler_nonce', 'custom_form_handler_nonce'); ?>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3 mb-4 mt-0">
                <select  class="form-control " id="chave" name="chave">
                    <option value="id" <?php echo ($chave == "id" ? "selected" : "" )?>>Id Cliente</option>
                    <option value="cpf_cnpj" <?php echo ($chave == "cpf_cnpj" ? "selected" : "" )?>>CPF / CNPJ</option>
                </select>
            </div>
            <div class="form-group col-md-3 mb-4 mt-0">
                <input type="text" class="form-control" id="valor" name="valor" value="<?php echo $valor ?>">
            </div> 
            <div class="form-group col-sm-2 mb-4 mt-0">
                <button class="btn btn-primary" type="submit">Consultar</button>
            </div>
        </div>
    </form>
<!-- info básica -->
    <?php if(isset($cliente)) { ?>
        <div class="card mb-2" style="width:100%">
            <div class="card-title">
                <h5 class="card-title"><strong><?php echo $cliente->nome; ?></strong></h5>
            </div>
            <div class="row mt-3 mb-3" style="text-align:center">
                <div class="col-4"><strong>Id Cliente: </strong><?php echo $cliente->id; ?></div>
                <div class="col-4"><strong>Documento: </strong><?php echo Cliente::documentoFormatado($cliente->cpf_cnpj); ?></div>
                <div class="col-4"><strong>Data Cadastro: </strong><?php echo $cliente->dt_cadastro; ?></div>
            </div>
        </div>
    <?php } ?>
<!-- contratos -->
    <?php if(isset($cliente)) { ?>
        <div class="card mb-2" style="width:100%">
            <div class="card-title">
                <h5 class="card-title"><a id="collapse-contratos" style="text-decoration:none" data-toggle="collapse" href="#contratos" aria-expanded="true" aria-controls="contratos">Contratos</a></h5>
            </div>
            <ul id="contratos" class="list-group list-group-flush"><li class="list-group-item">
                <table class="table table-secondary table-sm" style="text-align:center">
                    <thead>
                        <tr>
                            <th>Data Início</th>
                            <th>Data Fim</th>
                            <th>Dia Vencimento</th>
                            <th>Dia Inicio Ciclo</th>
                            <th>Plano</th>
                            <th>Franquia (MB)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($cliente->contratos as $c){  ?>
                        <tr>
                            <td><strong><?php echo $c->dt_inicio;?></strong></td>
                            <td><strong><?php echo $c->dt_fim;?></strong></td>
                            <td><strong><?php echo $c->dia_vencimento;?></strong></td>
                            <td><strong><?php echo $c->dia_inicio_ciclo;?></strong></td>
                            <td><strong><?php echo $c->nome_plano;?></strong></td>
                            <td><strong><?php echo $c->franquia;?></strong></td>
                        </tr>
                    </tbody>
                    <?php } ?>
                </table>
            </li></ul>
        </div>
    <?php } ?>
<!-- faturas -->
<?php if(isset($cliente) && sizeof($cliente->faturas) > 0) { ?>
    <div class="card mb-2" style="width:100%">
        <div class="card-title">
            <h5 class="card-title"><a id="collapse-faturas" style="text-decoration:none" data-toggle="collapse" href="#faturas" aria-expanded="true" aria-controls="faturas">Faturas</a></h5>
        </div>
        <ul id="faturas" class="list-group list-group-flush"><li class="list-group-item">
        <table class="table table-secondary" style="text-align:center">
            <thead>
                <tr>
                    <th></th>
                    <th>Ano</th>
                    <th>Mês</th>
                    <th>Data Emissão</th>
                    <th>Data Vencimento</th>
                    <th style="text-align:right">Consumo (MB)</th>
                    <th style="text-align:right">Valor cobrado (R$)</th>
                    <th style="text-align:right">Impostos (R$)</th>
                </tr>
            </thead>
            <tbody class="card-body">
            <?php 
                $itens_nf = $cliente->itens_nf;
                foreach($cliente->faturas as $f){  
            ?>
                <tr>
                    <td>
                        <?php if($f->fatura_disponivel) { ?>
                            <button type="button" style="height:24px" class="btn btn-secondary btn-sm py-0 m-0" 
                                onclick="window.location.href='<?php echo site_url('/index.php/detalhe-fatura/') . '?cliente=' . $cliente->id . '&ciclo=' . $f->id_ciclo . '&mes=' . $f->mes . '&ano=' . $f->ano; ?>'">
                                    <img alt="exibir documento de fatura" title="exibir documento de fatura" 
                                        src="<?php echo plugins_url('./img/fatura-16.png', __FILE__)?>">
                            </button>
                        <?php } ?>
                    </td>
                    <td><strong><?php echo $f->ano;?></strong></td>
                    <td><strong><?php echo $f->mes;?></strong></td>
                    <td><strong><?php echo $f->data_emissao;?></strong></td>
                    <td><strong><?php echo $f->data_vencimento;?></strong></td>
                    <td style="text-align:right"><strong><?php echo number_format($f->consumo_dados, 2,",",".");?></strong></td>
                    <td style="text-align:right"><strong><?php echo number_format($f->valor_total, 2,",",".");?></strong></td>
                    <td style="text-align:right"><strong><?php echo number_format($f->valor_impostos, 2,",",".");?></strong></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="5" style="padding-right:0">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>UF</th>
                                    <th>Nota Fiscal</th>
                                    <th>Serviço</th>
                                    <th style="text-align:right">Consumo (MB)</th>
                                    <th style="text-align:right">Valor cobrado (R$)</th>
                                    <th style="text-align:right">Impostos (R$)</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                                foreach($itens_nf as $i){  
                                    if( $i->ano == $f->ano && 
                                        $i->mes == $f->mes && 
                                        $i->id_ciclo == $f->id_ciclo ) {
                                        ?>
                                            <tr>
                                                <td><?php echo $i->uf;?></td>
                                                <td><?php echo $i->numero_nota_fiscal;?></td>
                                                <td><?php echo $i->servico;?></td>
                                                <td style="text-align:right"><?php echo number_format($i->consumo_dados, 2,",",".");;?></td>
                                                <td style="text-align:right"><?php echo number_format($i->valor_total, 2,",",".");;?></td>
                                                <td style="text-align:right"><?php echo number_format($i->valor_impostos, 2,",",".");;?></td>
                                            </tr>
                                        <?php                                     
                                        //unset($f);
                                    }
                                }
                            ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
            <?php } ?>
        </table>
        </li></ul>
    </div>
<?php } ?>
<!-- filiais -->
    <?php if(isset($cliente) && sizeof($cliente->filiais > 1)) { ?>
        <div class="card mb-2" style="width:100%">        
            <div class="card-title">
                <h5 class="card-title"><a id="collapse-filiais" style="text-decoration:none" data-toggle="collapse" href="#filiais" aria-expanded="true" aria-controls="filiais">Filiais</a></h5>
            </div>
            <ul id="filiais" class="list-group list-group-flush">
                <?php 
                    foreach($cliente->filiais as $f){  
                ?>
                        <li class="list-group-item">
                            <div class="card bg-line-info">
                                <div class="card-title">
                                    <div class="card-title align-middle">
                                        <div class="row">
                                            <div class="col-10 text-left px-4 pt-1">
                                                <h5 class="align-middle">
                                                    <a style="text-decoration:none" data-toggle="collapse" href="#filial-<?php echo $f->estado ?>" aria-expanded="true" aria-controls="filial-<?php echo $f->estado ?>">
                                                        <?php echo "Filial " . $f->estado . " (" . Cliente::documentoFormatado($f->cpf_cnpj) . ")"; ?>
                                                    </a>
                                                </h5>
                                            </div>
                                            <div class="col-2 text-right align-middle px-4">
                                                <button style="width:24px;height:24px" class="btn btn-secondary p-0 mt-0 btn-contato"
                                                    data-toggle="modal" data-target="#modal-contato" data-operacao="novo" 
                                                    data-filial="<?php echo $f->estado ?>">
                                                        <img class="mb-1" alt="adicionar contato" title="adicionar contato" 
                                                            src="<?php echo plugins_url('./img/add-user-16.png', __FILE__)?>">
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" id="filial-<?php echo $f->estado ?>">
                                    <p><strong>Endereço: </strong><?php echo $f->enderecoFormatado(); ?></p>
                                    <div class="card-columns" id='contatos-<?php echo $f->estado ?>'>    
                                        <?php
                                            foreach($f->contatos as $c) {
                                        ?>
                                            <div class="card bg-line-info" id='<?php echo "contato-$c->id_contato" ?>'>
                                                <div class="card-title align-middle">
                                                    <div class="row">
                                                        <div class="col-8 text-left px-4 pt-1" id='<?php echo "contato-nome-$c->id_contato" ?>'>
                                                            <?php echo "$c->nome"; ?>   
                                                        </div>
                                                        <div class="col-4 text-right align-middle px-4">
                                                            <button style="width:24px;height:24px" class="btn btn-secondary p-0 mt-0 btn-contato"
                                                                data-toggle="modal" data-target="#modal-contato" data-operacao="editar" 
                                                                data-filial="<?php echo $f->estado ?>"
                                                                data-id-contato="<?php echo $c->id_contato ?>">
                                                                    <img class="mb-1" alt="editar contato" title="editar contato" 
                                                                        src="<?php echo plugins_url('./img/edit-user-16.png', __FILE__)?>">
                                                            </button>
                                                            <button style="width:24px;height:24px" class="btn btn-secondary p-0 mt-0 btn-contato"
                                                                data-toggle="modal" data-target="#modal-contato" data-operacao="excluir" 
                                                                data-filial="<?php echo $f->estado ?>"
                                                                data-id-contato="<?php echo $c->id_contato ?>">
                                                                    <img class="mb-1" alt="excluir contato" title="excluir contato" 
                                                                        src="<?php echo plugins_url('./img/delete-user-16.png', __FILE__)?>">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>        
                                                <div class="card-footer mt-0 mb-0">
                                                    <strong>E-mail: </strong><span id='<?php echo "contato-email-$c->id_contato" ?>'><?php echo "$c->email"; ?></span>
                                                    <br><strong>Telefone Principal: </strong><span id='<?php echo "contato-telefone-$c->id_contato" ?>'><?php echo "$c->telefone"; ?></span>
                                                    <br><strong>Telefone Alternativo: </strong><span id='<?php echo "contato-telefone2-$c->id_contato" ?>'><?php echo "$c->telefone2"; ?></span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
    <!-- chamados em aberto -->
    <?php if(isset($cliente) && isset($chamados) && sizeof($chamados) > 0) { ?>
        <div class="card mb-2" style="width:100%">
            <div class="card-title">
                <h5 class="card-title"><a style="text-decoration:none" data-toggle="collapse" href="#chamados" aria-expanded="true" aria-controls="contratos">Chamados Abertos</a></h5>
            </div>
            <ul id="chamados" class="list-group list-group-flush"><li class="list-group-item">
                <table class="table table-secondary table-sm" style="text-align:center">
                    <thead>
                        <tr>
                            <th>Protocolo</th>
                            <th>Data de Abertura</th>
                            <th>Dias em Aberto</th>
                            <th>Motivo do Contato</th>
                            <th>Solicitação</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($chamados as $c){  ?>
                        <tr>
                            <td>
                                <button type="button" style="height:24px" class="btn btn-secondary btn-sm py-0 m-0" 
                                    onclick="window.location.href='<?php echo site_url('/index.php/detalhe-atendimento/') . '?protocolo=' . $c->protocolo; ?>'">
                                        <?php echo $c->protocolo;?>
                                </button>
                            </td>
                            <td><strong><?php echo $c->data_criacao;?></strong></td>
                            <td><strong><?php echo $c->dias_em_aberto;?></strong></td>
                            <td class="text-left"><?php echo "$c->motivo_desc: $c->motivo_sub_desc" ?></td>
                            <td class="text-left comentario"><?php echo $c->solicitacao;?></td>
                        </tr>
                    </tbody>
                    <?php } ?>
                </table>
            </li></ul>
        </div>
    <?php } ?>

<!-- Modal contato (inicio) -->
<div class="modal" id="modal-contato" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" id="editar-contato">
            <div class="modal-header">  
                <h5>
                    <span id="mensagem-conf"></span>
                </h5>
                <button type="button" id="fecharModal" class="close" data-dismiss="modal" aria-label="Close" data-html2canvas-ignore="true">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-info text-info">
                <form id="contato-form" class="needs-validation" novalidate>
                    <div class="form-row p-1 mx-1 my-0">
                        <input type="hidden" id="contato-operacao">
                        <input type="hidden" id="contato-cliente" value="<?php echo $cliente->id ?>">
                        <input type="hidden" id="contato-estado">
                        <input type="hidden" id="contato-id">
                        <div class="col-6">
                            <input type="text" class="form-control input-contato" id="contato-nome" placeholder="Nome" required>
                            <div id="if-nome" class="invalid-feedback">Informe o nome do contato</div>
                        </div>
                        <div class="col-6">
                            <input type="email" class="form-control input-contato" id="contato-email" placeholder="E-mail" required>
                            <div id="if-email" class="invalid-feedback">Informe um e-mail de contato válido</div>
                        </div>
                    </div>
                    <div class="form-row p-1 mx-1 my-0">
                        <div class="col-6">
                            <input type="tel" class="form-control input-contato" id="contato-telefone" placeholder="Telefone Principal (XX) XXXX-XXXX" pattern="(\+?[0-9]{1,4}\s?)?\(?[0-9]{2,3}\)?\s?[0-9]{4,5}-?[0-9]{4}" required>
                            <div id="if-telefone" class="invalid-feedback">Informe o telefone com o código de área</div>
                        </div>
                        <div class="col-6">
                            <input type="tel" class="form-control input-contato" id="contato-telefone2" placeholder="Telefone Alternativo (XX) XXXX-XXXX" pattern="(\+?[0-9]{1,4}\s?)?\(?[0-9]{2,3}\)?\s?[0-9]{4,5}-?[0-9]{4}">
                            <div id="if-telefone2" class="invalid-feedback">Informe o telefone com o código de área</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer p-2" id="contato-form-buttons">
                <button id="acao-contato" type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="OK">Confirmar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Cancelar">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal contato (fim) -->
<!-- template contato (inicio) -->  
<div id="template_contato" style="display:none">
    <div class="card bg-line-info" id='contato-{{ID_CONTATO}}'>
        <div class="card-title align-middle">
            <div class="row">
                <div class="col-8 text-left px-4 pt-1" id='contato-nome-{{ID_CONTATO}}'>
                    {{NOME}}
                </div>
                <div class="col-4 text-right align-middle px-4">
                    <button style="width:24px;height:24px" class="btn btn-secondary p-0 mt-0 btn-contato"
                        data-toggle="modal" data-target="#modal-contato" data-operacao="editar" 
                        data-filial="{{FILIAL}}"
                        data-id-contato="{{ID_CONTATO}}">
                            <img class="mb-1" alt="editar contato" title="editar contato" 
                                src="<?php echo plugins_url('./img/edit-user-16.png', __FILE__)?>">
                    </button>
                    <button style="width:24px;height:24px" class="btn btn-secondary p-0 mt-0 btn-contato"
                        data-toggle="modal" data-target="#modal-contato" data-operacao="excluir" 
                        data-filial="{{FILIAL}}"
                        data-id-contato="{{ID_CONTATO}}">
                            <img class="mb-1" alt="excluir contato" title="excluir contato" 
                                src="<?php echo plugins_url('./img/delete-user-16.png', __FILE__)?>">
                    </button>
                </div>
            </div>
        </div>        
        <div class="card-footer mt-0 mb-0">
            <strong>E-mail: </strong><span id='contato-email-{{ID_CONTATO}}'>{{EMAIL}}</span>
            <br><strong>Telefone Principal: </strong><span id='contato-telefone-{{ID_CONTATO}}'>{{TELEFONE}}</span>
            <br><strong>Telefone Alternativo: </strong><span id='contato-telefone2-{{ID_CONTATO}}'>{{TELEFONE2}}</span>
        </div>
    </div>                        
</div>
<!-- template contato (fim) -->

<script>
    jQuery(document).ready(function($){ 
        // inicializa exibição dos elementos
        $('.list-group').collapse();
        $('.card-body').collapse();

        $('#modal-contato').on('show.bs.modal', function (event) {
            //limpa validações anteriores
            var form = $('#contato-form')[0];
            form.classList.remove('was-validated');

            $.each($('.input-contato'), function(k,v){
                $('.input-contato')[k].classList.remove('is-invalid');
            });

            //recupera dados e monta formulário
            var button = $(event.relatedTarget); 
            var op = button.data('operacao'); 
            var filial = button.data('filial'); 
            var id = button.data('id-contato'); 
            var nome = $('#contato-nome-' + id).text().trim(); 

            $('#contato-operacao').val(op);
            $('#contato-estado').val(filial);
            $('#contato-id').val('');
            $('#contato-nome').val('');
            $('#contato-email').val('');
            $('#contato-telefone').val('');
            $('#contato-telefone2').val('');
            
            $('#contato-form').show();
            $('#contato-form-buttons').show();

            if(op == 'novo') {
                $('#mensagem-conf').html('Adicionar contato à filial <span class="text-primary">' + filial + '</span>');
            }
            else {
                $('#contato-id').val(id);
                if(op == 'editar') {
                    var email = $('#contato-email-' + id).text().trim(); 
                    var telefone = $('#contato-telefone-' + id).text().trim(); 
                    var telefone2 = $('#contato-telefone2-' + id).text().trim(); 
                    $('#contato-nome').val(nome);
                    $('#contato-email').val(email);
                    $('#contato-telefone').val(telefone);
                    $('#contato-telefone2').val(telefone2);
                    $('#mensagem-conf').html('Alterar contato <span class="text-primary">' + nome + 
                        '</span> da filial <span class="text-primary">' + filial + '</span>');
                }
                else if(op == 'excluir') {
                    $('#mensagem-conf').html('Excluir contato <span class="text-primary">' + nome + 
                        '</span> da filial <span class="text-primary">' + filial + 
                        '</span>?<br><small>Essa ação não pode ser desfeita</small>');
                    $('#contato-form').hide();
                }
            }

        });

        $('#acao-contato').on('click', function(event) {
            //processa evento via ajax
            form = $('#contato-form')[0];
            form.classList.remove('was-validated');
            operacao = $('#contato-operacao').val();

            if(form.checkValidity() || operacao == 'excluir'){
                id_contato = $('#contato-id').val();
                id_cliente = $('#contato-cliente').val();
                estado = $('#contato-estado').val();
                nome = $('#contato-nome').val();
                email = $('#contato-email').val();
                telefone = $('#contato-telefone').val();
                telefone2 = $('#contato-telefone2').val();
    
                var contato = {
                    "id_contato": id_contato,
                    "id_cliente": id_cliente,
                    "estado": estado,
                    "nome": nome,
                    "email": email,
                    "telefone": telefone,
                    "telefone2": telefone2
                };
    
                var dados_envio = {
                    'custom_ajax_handler_nonce': js_global.custom_ajax_handler_nonce,
                    'action' : 'custom_ajax_handler',
                    'plugin': 'cinco-atendimento', 
                    'module': 'clientes',
                    'function': operacao + '_contato_filial', 
                    'args': [contato]
                }
                jQuery.ajax({
                    url: js_global.xhr_url,
                    type: 'POST',
                    data: dados_envio,
                    dataType: 'JSON',
                    success: function(response) {
                        console.log("RESPONSE: ");
                        console.log(response);
                        
                        response_data = JSON.parse(response.data);

                        if (response['success']){
                            $('#contato-form').hide();
                            $('#contato-form-buttons').hide();
                            $('#mensagem-conf').html(response_data.mensagem);
                            
                            if(operacao == 'excluir')
                                $('#contato-' + id_contato).remove();

                            else if(operacao == 'editar'){
                                $('#contato-nome-' + id_contato).text(nome);
                                $('#contato-email-' + id_contato).text(email);
                                $('#contato-telefone-' + id_contato).text(telefone);
                                $('#contato-telefone2-' + id_contato).text(telefone2);
                            }
                            else if(operacao = 'novo'){
                                var template = $('#template_contato').html();
                                id_contato = response_data.id;
                                template = template.replace(/\{\{ID_CONTATO\}\}/g, id_contato); 
                                template = template.replace(/\{\{FILIAL\}\}/g, estado); 
                                template = template.replace(/\{\{NOME\}\}/g, nome); 
                                template = template.replace(/\{\{EMAIL\}\}/g, email); 
                                template = template.replace(/\{\{TELEFONE\}\}/g, telefone); 
                                template = template.replace(/\{\{TELEFONE2\}\}/g, telefone2); 
                                $('#contatos-' + estado).append(template);
                            }
                        }
                        else { 
                            console.log('Erro ao processar requisição');
                            //TODO TRATAR ERRO CONFORME SOLICITAÇÃO
                            $('#mensagem-conf').html(response_data.mensagem);

                            if(operacao == 'excluir'){
                                $('#contato-form').hide();
                                $('#contato-form-buttons').hide();
                            }
                            else {
                                $.each(response_data.erros, function (k,v){
                                    ($('#contato-' + k)[0]).classList.add('is-invalid');
                                    $('#if-' + k).text(v);
                                });
                            }
                        }
                    }
                }); 
            }     
            //previne submit do form
            form.classList.add('was-validated');
            event.preventDefault();
            event.stopPropagation();     
        });
    });
</script>
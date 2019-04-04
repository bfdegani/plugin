<link rel="stylesheet" type="text/css" href="<?php echo CINCO_CSS_URL . 'datatables.min.css' ?>"/>
<script type="text/javascript" src="<?php echo CINCO_JS_URL . 'datatables.min.js' ?>"></script>
<script type="text/javascript" src="<?php echo CINCO_JS_URL . 'bluebird.js'?>"></script>
<script type="text/javascript" src="<?php echo CINCO_JS_URL . 'html2canvas.js'?>"></script>
<script type="text/javascript" src="<?php echo CINCO_JS_URL . 'jsPDF.min.js'?>"></script>

<style>
    .info_complementar{
        display: none;
    }
</style>

<table id='chamados' class='table table-striped table-hover' style="max-width:100%">
    <thead>
        <tr>
            <th class="no_sort"></th>
            <th>Protocolo</th>
            <th>Empresa</th>
            <th>Data Abertura</th>
            <th>Data Conclusão</th>
            <th>Dias em aberto</th>
            <th>Motivo Contato</th>
            <th>Submotivo</th>
            <th>Solicitação</th>
            <th class="info_complementar">Plano Interesse</th>
            <th class="info_complementar">Id Cliente</th>
            <th class="info_complementar">CPF/CNPJ</th>
            <th class="info_complementar">Nome</th>
            <th class="info_complementar">Telefone</th>
            <th class="info_complementar">E-mail</th>
            <th class="info_complementar">Canal Contato</th>
            <th class="info_complementar">Canal Retorno</th>
            <th class="info_complementar">Usuário Abertura</th>
            <th class="info_complementar">Usuário Conclusao</th>
            <th class="info_complementar">Resposta</th>
            <th class="info_complementar">Comentários</th>
        </tr>
    </thead>
    <tbody>  
        <?php 
            foreach($chamados as $c){  
                $solicitacao = str_replace('\t', '&nbsp;&nbsp;&nbsp;&nbsp;',$c->solicitacao);
                $solicitacao = str_replace('\n', '<br>', $solicitacao);  
        ?>
            <tr id="<?php echo $c->protocolo; ?>">
                <td class="protocolo align-top pl-2 pr-0">
                    <button style="width:24px;height:24px" class="btn btn-secondary p-0 mt-0" 
                        data-toggle="modal" data-target="#detalhesModal" data-protocolo="<?php echo $c->protocolo; ?>">
                        <img alt="baixar como pdf" title="baixar como pdf" 
                            src="<?php echo plugins_url('../img/download-16.png', __FILE__)?>">
                    </button>
                </td>
                <td class="align-top pl-2 pr-0">
                    <button style="height:24px" class="btn btn-secondary btn-sm py-0 mt-0" aria-label="tratar chamado"
                        onclick="window.location.href='<?php echo site_url('/index.php/detalhe-atendimento/') . '?protocolo=' . $c->protocolo; ?>'">
                        <?php echo $c->protocolo; ?>
                    </button>
                </td>
                <td class="empresa"><?php echo $c->empresa; ?></td>
                <td class="data_criacao"><?php echo $c->data_criacao; ?></td>
                <td class="data_conclusao"><?php echo $c->data_conclusao; ?></td>
                <td class="dias_em_aberto"><?php echo $c->dias_em_aberto; ?></td>
                <td class="motivo_desc"><?php echo $c->motivo_desc; ?></td>
                <td class="motivo_sub_desc"><?php echo $c->motivo_sub_desc; ?></td>
                <td class="solicitacao comentario"><?php echo $solicitacao; ?></td>
                <td class="plano info_complementar"><?php echo $c->plano; ?></td>
                <td class="id_cliente info_complementar"><?php echo $c->id_cliente; ?></td>
                <td class="cpf_cnpj info_complementar"><?php echo $c->cpf_cnpj; ?></td>
                <td class="contato info_complementar"><?php echo $c->contato; ?></td>
                <td class="telefone info_complementar"><?php echo $c->telefone; ?></td>
                <td class="email info_complementar"><?php echo $c->email; ?></td>
                <td class="canal_contato info_complementar"><?php echo $c->canal_contato; ?></td>
                <td class="canal_retorno info_complementar"><?php echo $c->canal_retorno; ?></td>
                <td class="usuario_criacao info_complementar"><?php echo $c->usuario_criacao; ?></td>
                <td class="usuario_conclusao info_complementar"><?php echo $c->usuario_conclusao; ?></td>
                <td class="resposta info_complementar"><?php echo $c->resposta; ?></td>
                <td class="comentarios info_complementar"><?php echo $c->comentarios; ?></td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th>Protocolo</th>
            <th>Empresa</th>
            <th>Data Abertura</th>
            <th>Data Conclusão</th>
            <th>Dias em aberto</th>
            <th>Motivo Contato</th>
            <th>Submotivo</th>
            <th>Solicitação</th>
            <th class="info_complementar">Plano Interesse</th>
            <th class="info_complementar">Id Cliente</th>
            <th class="info_complementar">CPF/CNPJ</th>
            <th class="info_complementar">Nome</th>
            <th class="info_complementar">Telefone</th>
            <th class="info_complementar">E-mail</th>
            <th class="info_complementar">Canal Contato</th>
            <th class="info_complementar">Canal Retorno</th>
            <th class="info_complementar">Usuário Abertura</th>
            <th class="info_complementar">Usuário Conclusão</th>
            <th class="info_complementar">Resposta</th>
            <th class="info_complementar">Comentários</th>
        </tr>       
    </tfoot>    
</table>
<!-- detalhesModal -->
<div class="modal" id="detalhesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" id="detalhesModalConteudo">
            <div class="modal-header">  
                <table class="p-0 m-0">
                    <tr>
                        <td rowspan="3"><img src="<?php echo CINCO_IMG_URL . 'logo-cinco196x59.png' ?>"></td>
                        <td class="py-0 px-1 m-0">Protocolo:</td>
                        <td><span id="protocoloModal" class="protocolo" ></span></td>
                    </tr>
                    <tr>
                        <td class="py-0 px-1 m-0">Data de Criação:</td>
                        <td><span class="data_criacao"></span></td>
                    </tr>
                    <tr>
                        <td class="py-0 px-1 m-0">Data de Conclusão:</td>
                        <td><span class="data_conclusao"></td>
                    </tr>
                </table>               
                <button type="button" class="btn btn-sm btn-outline-secondary" id="salvarModal" data-html2canvas-ignore="true">
                    <span>Salvar</span>
                </button>
                <button type="button" id="fecharModal" class="close" data-dismiss="modal" aria-label="Close" data-html2canvas-ignore="true">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped mt-2 mb-2" style="width:100%;border:1">
                    <tr class="row mr-2 ml-2 d-inline-block text-left" style="width:98%">
                        <td><strong>Empresa:</strong><br><span class="empresa" ></span></td>
                        <td><strong>CPF/CNPJ:</strong><br><span class="cpf_cnpj" ></span></td>
                        <td><strong>Nome:</strong><br><span class="contato"></span></td>
                        <td><strong>Telefone:</strong><br><span class="telefone"></span></td>
                        <td><strong>E-mail:</strong><br><span class="email"></span></td>
                    </tr>
                    <tr class="row mr-2 ml-2 d-inline-block" style="width:98%">
                        <td><strong>Motivo Contato:</strong><br><span class="motivo_desc" ></span>: <span class="motivo_sub_desc" ></span></td>
                        <td><strong>Plano:</strong><br><span class="plano"></span></td>
                        <td>&nbsp;</td>
                        <td><strong>Canal Contato:</strong><br><span class="canal_contato"></span></td>
                        <td><strong>Canal Retorno:</strong><br><span class="canal_retorno"></span></td>
                    </tr>
                    <tr class="row mr-2 ml-2 d-inline-block" style="width:98%">
                        <td colspan="5"><strong>Solicitação:</strong><br><span class="solicitacao comentario"></span></td>
                    </tr>
                    <tr class="row mr-2 ml-2 d-inline-block" style="width:98%">
                        <td colspan="5"><strong>Resposta:</strong><br><span class="resposta comentario"></span></td>
                    </tr>
                    <tr class="row mr-2 ml-2 d-inline-block" style="width:98%" data-html2canvas-ignore="true">
                        <td colspan="5"><strong>Histórico:</strong><br><span class="comentarios comentario"></span></td>
                    </tr>
                </table>                
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($){ 
        var dontSort = [];
        $('#chamados thead th').each( function () {
            if ($(this).hasClass( 'no_sort' )) {
                dontSort.push( { "bSortable": false } );
            } else {
                dontSort.push( null );
            }
        });
        $('#chamados').DataTable({  
            aoColumns: dontSort,
            dom: '<lf<itr>Bp>',
            language:{url:"<?php echo CINCO_JSON_URL . 'Portuguese-Brasil.json' ?>"},
            search: {caseInsensitive:true}, 
            buttons: ['excel', 'csv'],
            order: [[1, 'desc']],
            <?php 
                if(sizeof($chamados) == 1 ) 
                    echo "dom: '<<t>B>'";
                else if(sizeof($chamados) <= 10 ) 
                    echo "dom: '<f<itr>B>'";
                else 
                    echo "dom: '<lf<itr>Bp>'";
            ?>
        });  

        $('#detalhesModal').on('show.bs.modal', function (event) {
            // Button that triggered the modal
            var button = $(event.relatedTarget); 
            // Extract info from data-* attributes
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var protocolo = button.data('protocolo'); 
            var modal = $(this);
            var row = $('#' + protocolo);
            modal.find('.protocolo').text(protocolo);
            modal.find('.data_criacao').text(row.find('.data_criacao').text());
            modal.find('.data_conclusao').text(row.find('.data_conclusao').text());
            modal.find('.empresa').text(row.find('.empresa').text());
            modal.find('.contato').text(row.find('.contato').text());
            modal.find('.cpf_cnpj').text(row.find('.cpf_cnpj').text());
            modal.find('.email').text(row.find('.email').text());
            modal.find('.telefone').text(row.find('.telefone').text());
            modal.find('.canal_contato').text(row.find('.canal_contato').text());
            modal.find('.canal_retorno').text(row.find('.canal_retorno').text());
            modal.find('.plano').text(row.find('.plano').text());
            modal.find('.motivo_desc').text(row.find('.motivo_desc').text());
            modal.find('.motivo_sub_desc').text(row.find('.motivo_sub_desc').text());
            modal.find('.solicitacao').text(row.find('.solicitacao').text());
            modal.find('.resposta').text(row.find('.resposta').text());
            modal.find('.comentarios').html("");
            try{
                var lista_comentarios = JSON.parse(row.find('.comentarios').text());
                lista_comentarios.comentarios.forEach(function(c){
                    modal.find('.comentarios').html(
                        modal.find('.comentarios').html() + 
                        c.usuario + ' (' + c.data + '): ' + c.texto + '<br>'
                    );
                });
            }
            catch(e){

            }
        });

        $('#salvarModal').on('click', function (event) {
            html2canvas(document.querySelector("#detalhesModalConteudo"),{scale:1}).then(function(canvas){
                var imgData = canvas.toDataURL('image/png');              
                var doc = new jsPDF('p', 'mm');
                doc.addImage(imgData, 'PNG', 6, 6);
                doc.save($('#protocoloModal').text().trim() + '.pdf');
            });
        });

    })
</script>

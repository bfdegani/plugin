<?php
require_once(plugin_dir_path(__FILE__) . 'model.php');
require_once(plugin_dir_path(__FILE__) . '../chamados/model/chamados-dao.php');
$cliente = preg_replace("/[^0-9]/", "", $_GET['cliente']);
$ciclo = preg_replace("/[^0-9]/", "", $_GET['ciclo']);
$mes = preg_replace("/[^0-9]/", "", $_GET['mes']);
$ano = preg_replace("/[^0-9]/", "", $_GET['ano']);

$qa = '_qa';

$meses = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

$fatura = DocumentoFaturaDAO::consultaDocFatura($cliente, $ciclo, $mes, $ano, $qa);

$json_fatura = json_decode($fatura->documento);

function formata_cep($cep){
    return substr($cep,0,5) . '-' . substr($cep,5,3);
}

function formata_cnpj($cnpj){
    return substr($cnpj,0,2) . "." .  substr($cnpj,2,3) . "." . substr($cnpj,5,3) . "/" . substr($cnpj,8,4) . "-" . substr($cnpj,12,2);
}

function formata_cpf($cpf){
    return substr($cpf,0,3) . "." .  substr($cpf,3,3) . "." . substr($cpf,6,3) . "-" . substr($cpf,9,2);
}

function formata_ie($ie){
    if(((int) $ie > 0)) {
        $ie = str_pad($ie,9,"0",STR_PAD_LEFT);
        return substr($ie,0,3) . "." .  substr($ie,3,3) . "." . substr($ie,6,3);
    }
    return 'Isento';
}

function formata_numero($numero, $dec=0){
    return number_format($numero,$dec,',','.');
}

function formata_moeda($valor){
    return "R$ ". number_format($valor,2,',','.');
}

function formata_mb($mb){
    return number_format($mb,2,',','.') . " MB";
}

function formata_nf($nf){
    return str_pad($nf,9,"0",STR_PAD_LEFT);
}

function formata_id_cliente($id){
    return "JS" . str_pad($id,5,"0",STR_PAD_LEFT);
}

function formata_aliquota($aliquota){
    return ($aliquota * 100) . '%';
}

function formata_data($data){
    return preg_replace('/(\d{4})-(\d{1,2})-(\d{1,2})/', '\3/\2/\1', $data);
}

function hash_nf($cpf_cnpj_cliente, $numero_nf, $valor_servico, $base_calculo, $icms, $emissao, $cnpj_emissor) {
    $s = str_pad($cpf_cnpj_cliente,14,"0",STR_PAD_LEFT);
    $s.= str_pad($numero_nf,9,"0",STR_PAD_LEFT);
    $s.= str_pad(str_replace('.', '', $valor_servico),12,"0",STR_PAD_LEFT);
    $s.= str_pad(str_replace('.', '', $base_calculo),12,"0",STR_PAD_LEFT);
    $s.= str_pad(str_replace('.', '', $icms),12,"0",STR_PAD_LEFT);
    $s.= str_replace('-', '', $emissao);
    $s.= str_pad($cnpj_emissor,14,"0",STR_PAD_LEFT);
    return md5($s);
}
?>

<link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ ) . 'css/fatura.css'?>" type="text/css">


<div id="documento-fatura">
    <?php foreach($json_fatura->filiais as $filial) { ?>
        <div class="fatura-container" id="fatura">  
            <div class="row">
                <div class="col-6 fatura-header">
                    <img src="<?php echo CINCO_IMG_URL ?>logo-cinco2.png">
                </div>
                <div class="col-6 fatura-header">
                    <p class='text-right px-2'>
                        <?php 
                            foreach($json_fatura->cabecalho->contatos as $c){
                                echo "<br>$c";
                            }
                        ?>
                    </p>
                </div>
            </div>
            <div class="fatura-header"> 
                <h1><?php echo $json_fatura->cabecalho->descricao ?></h1>
                <h2><?php echo $meses[$mes - 1] . " " . $ano ?></h2>
            </div>
            <hr>
            <div class="row fatura_cliente">
                <div class="col-8">
                    Nome / Razão Social:<br><strong><?php echo $filial->nome ?></strong>
                </div>
                <br>
                <div class="col-4 text-right">
                    CNPJ:<strong> <?php echo formata_cnpj($filial->cnpj) ?></strong>
                </div>
            </div>
            <div class="row fatura_cliente">
                <div class="col-12">
                    Endereço: <strong>
                        <?php 
                            $cep = formata_cep($filial->cep);
                            echo $filial->endereco . ". " . $filial->cidade . " - " . $filial->uf . "<br>CEP: " . $cep;
                        ?></strong>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-4 text-left fatura-resumo">
                    <h3>Vencimento: <strong><?php echo formata_data($json_fatura->resumo->vencimento) ?></strong></h3>
                </div>
                <div class="col-4 text-center fatura-resumo">
                    <h3>Início do Ciclo: <strong><?php echo formata_data($json_fatura->resumo->inicio_ciclo) ?></strong></h3>
                </div>
                <div class="col-4 text-right fatura-resumo">
                    <h3>Fim do Ciclo: <strong><?php echo formata_data($json_fatura->resumo->fim_ciclo) ?></strong></h3>
                </div>
            </div>
            <div class="fatura-plano">
                <?php 
                    foreach($filial->planos as $p) {
                ?>
                    <div class='row'>
                        <div class="col-12 fatura-resumo">
                            <h2>PLANO <?php echo "$p->referencia - $p->nome" ?></h2>
                        </div>
                    </div>
                    <div class='row'>
                        <div class="col-4 text-left fatura-resumo ">
                            <h3>SIMCARDS: <strong><?php echo formata_numero($p->quantidade_msisdn) ?></strong></h3>
                        </div>
                        <div class="col-4 text-center fatura-resumo">
                            <h3>Consumo: <strong><?php echo formata_mb($p->consumo_total) ?></strong></h3>
                        </div>
                        <div class="col-4 text-right fatura-resumo">
                            <h3>Valor: <strong><?php echo formata_moeda($p->valor_total) ?></strong></h3>
                        </div>
                    </div>
                    <br>
                    <table class="table table-striped fatura-detalhe">
                        <thead>
                            <tr class="row">
                                <td class='col-3 text-left'>Dias Considerados</td>
                                <td class='col-3 text-center'>Tarifa Mensal</td>
                                <td class='col-3 text-center'>SIMCARDS</td>
                                <td class='col-3 text-right'>Valor Cobrado</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            foreach($p->franquia as $f) { 
                                $valor_prorata = (float) $f->valor_prorata;
                        ?>
                            <tr class='row py-0 my-0'>
                                <td class='col-3 text-left'><?php echo $f->dias_prorata ?></td>
                                <td class='col-3 text-center'><?php echo formata_moeda($valor_prorata) ?></td>
                                <td class='col-3 text-center'><?php echo formata_numero($f->quantidade) ?></td>
                                <td class='col-3 text-right'><?php echo formata_moeda($valor_prorata * $f->quantidade) ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <hr>
                    <table class="table table-striped fatura-detalhe">
                        <thead>
                            <tr class="row">
                                <td class='col-4 text-left'>Consumo Excedente</td>
                                <td class='col-4 text-center'>Tarifa</td>
                                <td class='col-4 text-right'>Valor Cobrado</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                if(sizeof($p->excedente) > 0) {
                                    foreach($p->excedente as $e) { ?>
                                        <tr class="row">
                                            <td class='col-4 text-left'><?php echo formata_mb($e->consumo)?></td>
                                            <td class='col-4 text-center'><?php echo formata_moeda($p->tarifa_excedente)?></td>
                                            <td class='col-4 text-right'><?php echo formata_moeda($e->valor)?></td>
                                        </tr>
                                <?php 
                                    }
                                } 
                                else 
                            ?>
                                <tr class="row">
                                    <td class='col-4 text-left'>0,00 MB</td>
                                    <td class='col-4 text-center'><?php echo formata_moeda($p->tarifa_excedente)?></td>
                                    <td class='col-4 text-right'>R$ 0,00</td>
                                </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
            <div class="fatura-footer doc-page-break">
                <p class="px-3 mx-3 text-left">
                    <?php 
                        foreach($json_fatura->rodape->mensagens as $m) {
                            echo "<br>$m";
                        }
                        $chamados = ChamadosDAO::consultaChamados('id_cliente', 
                            $json_fatura->id_cliente, 
                            $json_fatura->resumo->inicio_ciclo,
                            $json_fatura->resumo->fim_ciclo,
                            TRUE, TRUE);

                        if(sizeof($chamados) == 0 || $qa != '')
                            echo "<br>Nenhum protocolo registrado nesse ciclo.";
                        else if(sizeof($chamados) == 1)
                            echo "<br>O protocolo " . $chamados[0]->protocolo . " foi registrado nesse ciclo.";
                        else {
                            echo "<br>Protocolos de reclamação registrados nesse ciclo:";
                            foreach($chamados as $ch)
                                echo " " . $ch->protocolo;
                            echo ".";
                        }
                    ?>
                </p>
            </div>
        </div>
        <p>&nbsp;</p><p>&nbsp;</p>
        <div class="fatura-container" id="nota-fiscal">  
            <div class="nf-header row">
                <img  src="<?php echo CINCO_IMG_URL ?>logo-cinco2.png">
            </div>
            <div class="row">
                <div class="col-7 nf-header">
                    <p><strong><?php echo $json_fatura->dados_cinco->razao_social?></strong></p>
                    <p>CNPJ: <strong><?php echo formata_cnpj($json_fatura->dados_cinco->cnpj) ?></strong><p>
                    <p>IE: <strong><?php echo formata_ie($json_fatura->dados_cinco->ie) ?></strong></p>
                    <p>Endereço:<strong><?php echo $json_fatura->dados_cinco->endereco ?></strong></p>
                    <p>CEP: <strong><?php echo formata_cep($json_fatura->dados_cinco->cep)?></strong></p>
                </div>
                <div class="col-5 nf-header text-right">
                    <p class='text-left'><strong>NOTA FISCAL DE TELECOMUNICAÇÕES - VIA ÚNICA</strong></p>
                    <p class='text-left'>
                        <?php 
                            foreach($json_fatura->cabecalho->contatos as $c){
                                echo "$c<br>";
                            }
                        ?>
                    </p>    
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-7 nf-header">
                    <p>Nº do Cliente: <strong><?php echo formata_id_cliente($json_fatura->id_cliente) ?></strong></p>
                    <p>Cliente: <strong><?php echo $filial->nome ?></strong></p>
                    <p>CNPJ: <strong><?php echo formata_cnpj($filial->cnpj) ?> </strong></p>
                    <p>IE: <strong><?php echo formata_ie($filial->ie) ?> </strong></p>
                    <p>Endereço: <strong><?php echo $filial->endereco . ". " . $filial->cidade . " - " . $filial->uf ?></strong></p>
                    <p>CEP: <strong><?php echo formata_cep($filial->cep) ?></strong></p>
                </div>
                <div class="col-5 nf-header text-right">
                    <p class="text-left">NOTA: <strong><?php echo formata_nf($filial->nota_fiscal->nf) ?></strong> - Série <strong><?php echo $filial->nota_fiscal->serie ?> </strong></p>
                    <p class="text-left">MODELO: <strong><?php echo $json_fatura->dados_cinco->modelo_nf ?></strong></p>
                    <p class="text-left">CFOP: <strong><?php echo $json_fatura->dados_cinco->cfop ?></strong></p>
                    <p class="text-left">Natureza: <strong><?php echo $json_fatura->dados_cinco->natureza_servico ?></strong></p>
                    <p class="text-left">Data Emissão: <strong><?php echo formata_data($filial->nota_fiscal->emissao) ?></strong></p>
                    <p class="text-left">Período de medição: <strong><?php echo formata_data($json_fatura->resumo->inicio_ciclo) . " a " . formata_data($json_fatura->resumo->fim_ciclo) ?></p>
                </div>
            </div>
            <hr>
            <div class="nf-detalhe">
                <table class="table nf-itens">
                    <thead>
                        <tr class="row">
                            <td class="col">SEQ</td>
                            <td class="col">CÓDIGO SERVIÇO</td>
                            <td class="col">DESCRIÇÃO SERVIÇO</td>
                            <td class="col">CFOP</td>
                            <td class="col">BASE CÁLCULO</td>
                            <td class="col">ALÍQUOTA</td>
                            <td class="col">VALOR ICMS</td>
                            <td class="col">ISENTO / NÃO TRIBUTADO</td>
                            <td class="col">VALOR SERVIÇO</td>        
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $base_calculo = 0;
                            $icms = 0;
                            $valor = 0;
                            $aliquota = 0;
                            foreach($filial->nota_fiscal->itens as $i => $item){ 
                                $base_calculo += (float) $item->base_calculo;
                                $icms += (float) $item->valor_icms;
                                $valor += (float) $item->valor_servico;
                                $aliquota = $item->aliquota_icms; //igual para todos
                        ?>
                            <tr class="row">
                                <td class="col"><?php echo $i+1 ?></td>
                                <td class="col"><?php echo $item->codigo_servico ?></td>
                                <td class="col"><?php echo $item->descricao_servico ?></td>
                                <td class="col"><?php echo $json_fatura->dados_cinco->cfop ?></td>
                                <td class="col"><?php echo formata_moeda($item->base_calculo) ?></td>
                                <td class="col"><?php echo formata_aliquota($item->aliquota_icms) ?></td>
                                <td class="col"><?php echo formata_moeda($item->valor_icms) ?></td>
                                <td class="col">&nbsp;</td>
                                <td class="col"><?php echo formata_moeda($item->valor_servico) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <thead>
                        <tr class="row">
                            <td class="col">&nbsp;</td>
                            <td class="col">&nbsp;</td>
                            <td class="col">TOTAL:</td>
                            <td class="col">&nbsp;</td>
                            <td class="col"><?php echo formata_moeda($base_calculo) ?></td>
                            <td class="col">&nbsp;</td>
                            <td class="col"><?php echo formata_moeda($icms) ?></td>
                            <td class="col">&nbsp;</td>
                            <td class="col"><?php echo formata_moeda($valor) ?></td>
                        </tr>
                    </thead>
                </table>
                <hr>
                <div class="row nf-resumo-imposto">
                    <h3>CÁLCULO DO IMPOSTO:</h3>
                </div>
                <div class="row nf-resumo-imposto">
                    <div class="col col-2 text-left">TIPO: ICMS</div>
                    <div class="col col-4 text-center">BASE DE CÁLCULO: <?php echo formata_moeda($base_calculo) ?></div>
                    <div class="col col-2 text-center">ALÍQUOTA: <?php echo formata_aliquota($aliquota) ?></div>
                    <div class="col col-4 text-right">VALOR DO IMPOSTO: <?php echo formata_moeda($icms) ?></div>
                </div>
                <br>
                <hr>
                <div class="nf-hash">
                    <p><br>RESERVADO AO FISCO: (HASH CODE)
                        <br><br><?php 
                            echo hash_nf(
                                    $filial->cnpj, 
                                    $filial->nota_fiscal->nf,
                                    $valor,
                                    $base_calculo,
                                    $icms,
                                    $filial->nota_fiscal->emissao,
                                    $json_fatura->dados_cinco->cnpj
                                );
                        ?>
                    </p>
                </div>
                <hr>
                <div class="nf-hash">
                    <p class="text-left pt-2 pb-0 px-2 my-0">OBSERVAÇÕES:</p>
                    <p class="text-left px-4 py-0 my-0">
                        <br>VALOR DE FUST: <?php echo formata_moeda($filial->nota_fiscal->fust) ?>
                        <br>VALOR DE FUNTEL: <?php echo formata_moeda($filial->nota_fiscal->funttel) ?>
                        <br>VALOR DE PIS: <?php echo formata_moeda($filial->nota_fiscal->pis) ?>
                        <br>VALOR DE COFINS: <?php echo formata_moeda($filial->nota_fiscal->cofins) ?>
                    </p>
                    <br>
                    <p class="text-left px-2 py-0 my-1"><small>Contribuições de Fust 1% e Funttel 0,5% Sobre valores de telecomunicações - Não repassados ao Cliente<br>Tributos Federais (Pis 0,65% e Cofins 3%)</small></p>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<br>
<p class="p-0 m-0">
    <button type="button" class="btn btn-sm btn-primary d-print-none" id="salvarFatura">
        <span>Imprimir</span>
    </button>
</p>
<script>
    jQuery(document).ready(function($){ 
        $('.post-edit-link').addClass('d-print-none');
        $('.site-info').addClass('d-print-none');
        $('.entry-title').addClass('d-print-none');

        $('#salvarFatura').on('click', function (event) {
            window.print();   
        });
    })  

</script>

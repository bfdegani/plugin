<?php
    function cinco_bd_fields(){
        $bd_cfg = get_option('cinco_bd');
        foreach( $bd_cfg as $conexao => $cfg ){
            bd_cfg_fields($conexao, $cfg);
            echo "<hr>";
        }
    }

    function bd_cfg_fields($conexao, $cfg = NULL){
?>
    <table style="width:400px">
        <thead> 
            <tr>
                <td style="width:200px"><h3>[ <?php echo $conexao ?> ]</h3></td>
                <td style="text-align:right">
                    <input class="excluir_conexao" type="checkbox" id="excluir_<?php echo $conexao ?>" 
                        onclick="excluir_conexao('<?php echo $conexao ?>')"> 
                    <label for="excluir_<?php echo $conexao ?>">excluir</label></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong><label for='<?php echo $conexao ?>_host'>Hostname: </label></strong></td>
                <td style="text-align:right">
                    <input class='<?php echo $conexao ?>' type='text' id='<?php echo $conexao ?>_host' required
                        name='cinco_bd[<?php echo $conexao ?>][host]' value='<?php echo $cfg["host"] ?>'>
                </td>
            </tr>
            <tr>
                <td><strong><label for='<?php echo $conexao ?>_passwd'>Port: </label></strong></td>
                <td style="text-align:right">
                    <input class='<?php echo $conexao ?>' type='text' id='<?php echo $conexao ?>_port' required 
                        name='cinco_bd[<?php echo $conexao ?>][port]' value='<?php echo $cfg["port"] ?>'>
                </td>
            </tr>
            <tr>
                <td><strong><label for='<?php echo $conexao ?>_dbname'>Database: </label></strong></td>
                <td style="text-align:right">
                    <input class='<?php echo $conexao ?>' type='text' id='<?php echo $conexao ?>_dbname' required 
                        name='cinco_bd[<?php echo $conexao ?>][dbname]' value='<?php echo $cfg["dbname"] ?>'>
                </td>
            </tr>
            <tr>
                <td><strong><label for='<?php echo $conexao ?>_username'>Username: </label></strong></td>
                <td style="text-align:right">
                    <input class='<?php echo $conexao ?>' type='text' id='<?php echo $conexao ?>_username' required 
                        name='cinco_bd[<?php echo $conexao ?>][username]' value='<?php echo $cfg["username"] ?>'>
                </td>
            </tr>
            <tr>
                <td><strong><label for='<?php echo $conexao ?>_passwd'>Password: </label></strong></td>
                <td style="text-align:right">
                    <input class='<?php echo $conexao ?>' type='password' id='<?php echo $conexao ?>_passwd' required 
                    name='cinco_bd[<?php echo $conexao ?>][passwd]' value='<?php echo $cfg["passwd"] ?>'>
                </td>
            </tr>
        </tbody>
    </table>
<?php } 

function bd_cfg_fields_template() {
    bd_cfg_fields('TEMPLATE');
}

function bd_cfg_scripts(){
?>
    <script>
        jQuery(document).ready(function($){ 
            $('#nova_conexao').on('click', function (event) {
                nome_conexao = $('#nome_nova_conexao').val();
                form = $('#form_nova_conexao')[0];
                if(form.checkValidity() === true) {
                    form.classList.add('was-validated');
                    html_campos = $('#template_conexao')[0].innerHTML;
                    html_campos = html_campos.replace(/TEMPLATE/g, nome_conexao); 
                    $('#campos_conexao')[0].innerHTML += html_campos;
                    $('#nome_conexao_erro').hide();
                    $('#nome_nova_conexao').val('');
                }
                else{
                    $('#nome_conexao_erro').show();
                }
            });
        });

        /**
         * Essa função não pode ser declarada como um handler (como feito acima)
         * em função da manipulação dinâmica do DOM
         */
        function excluir_conexao(conexao) {
            var excluir = (jQuery('#excluir_' + conexao).attr('checked') == 'checked');
            jQuery('.' + conexao).attr('disabled', excluir);
            jQuery('.' + conexao).attr('required', !excluir);
        }
    </script>
<?php 
}
?>

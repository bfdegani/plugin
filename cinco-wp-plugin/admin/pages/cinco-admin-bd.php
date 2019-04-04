<div class="wrap">
     <h1>Cinco Atendimento - Conexões de Bancos de Dados</h1>
     <br>
    <form id="form_nova_conexao">
        <table>
            <tr>
                <td>
                    <input id="nome_nova_conexao" type="text" placeholder="Nome da Conexão" required pattern="[^\W]+" title="apenas letras, números e '_' permitidos">
                </td>
                <td>
                    <button id="nova_conexao" class="button button-primary" type='button'>Configurar Nova Conexão</button>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <span id="nome_conexao_erro" style="display:none;color:red"><small><strong>Informe um nome de conexão válido<strong></small></span>
                </td>
            </tr>
        </table>
    </form>
    <form class="form" action='options.php' method='post'>
        <div id="campos_conexao"></div><hr>
        <?php
            settings_fields( CINCO_BD_WP_OPTION );
            do_settings_sections( CINCO_BD_WP_OPTION );
            submit_button();
        ?>
    </form> 
</div>
<div id="template_conexao" style="display:none">
<?php 
    require_once( CINCO_PLUGIN_PATH . 'admin/pages/bd_cfg_fields.php'); 
    bd_cfg_fields_template();
    bd_cfg_scripts();
?>
</div>

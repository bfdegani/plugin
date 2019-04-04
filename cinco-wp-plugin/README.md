# Cinco WP Plugin
## Plugin para o wordpress com implementações específicas para as aplicações da Cinco Telecom.
Desenvolvida em Wordpress / PHP / MySQL
Criado por **Bruno Degani** em 12/12/2018

### Estrutura de diretórios:
- **wp-content/plugins/cinco-wp-plugin**
    - |- css    (arquivos de estilo complementares ao tema)
    - |- inc    (módulos com implementações base específicas)
    - |- js     (scripts complementares ao tema)
    - |- json   (arquicos de dados e/ou configuração)

### Módulos existentes:
- **ajax.php**:
    - implementa lógica de procesamento de requisições ajax dentro da aplicação consderando as especificidades da arquitetura do Wordpress.
    - exemplo de uso:
        - client web:
        ```
        /* parâmetros de processamento */
        var dados_envio = {
            'custom_ajax_handler_nonce': js_global.custom_ajax_handler_nonce, /* não alterar! */
            'action' : 'custom_ajax_handler', /* não alterar! */
            'snippet': 'simcards', /* snippet localizado em wp-content/custom-snippets a ser acionado via ajax */
            'function': 'alterarStatus', / *função do snippet responsável por processar a requisição, localizada no arquivo functions.php do snippet */
            'args': [iccid, novo] //parâmetros para execução
        }

        /* execução e tratamento do retorno */
        jQuery.ajax({
            url: js_global.xhr_url,
            type: 'POST',
            data: dados_envio,
            dataType: 'JSON',
            success: function(response) {
                if (response == '401'  ){
                    console.log('Requisição inválida');
                }
                if(response == '500' || response == '5000' ){
                    console.log('Erro ao processar requisição');
                }
                else { 
                    console.log('OK');
                }
            }
        }); 
        ```
- **debug.php**:
    - funções para auxiliar o debugging da aplicação
- **form_post.php**:
    - implementa lógica de procesamento de form post dentro da aplicação consderando as especificidades da arquitetura do Wordpress.
    - exemplo de uso:
        - front-end:
        ```
        <form class="needs-validation" novalidate id="formulario" method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>"> <!-- action do formulário deve ser sempre para admin-post.php. a implementação adicional considera o redirecionamento de volta para a página do formulário -->
            <!-- parâmetros de uso interno necessários para o processamento do formulário -->
            <input type="hidden" name="action" value="custom_form_handler">
            <?php wp_nonce_field('custom_form_handler_nonce', 'custom_form_handler_nonce'); ?> 

            <div class="form-row">
            <div class="form-group col-md-9 mb-2">
                <label for="nome">Nome*:</label>
                <input type="text" class="form-control" id="nome" name="campos[nome]" placeholder="Nome"         required autofocus>
            </div>
            <div class="form-group col-md-3 mb-2">
                <label for="telefone">Telefone*:</label>
                <input type="tel" class="form-control"  
                    id="telefone" name="campos[telefone]" placeholder="Telefone de contato"
                    required>
            </div>
        </div>
        ```
        - back-end:
        ```
        //recupera dados do formulário que foram armazenados na sessão
        if(isset($_SESSION['_POST'])) {
            if(isset($_SESSION['_POST']['campos'])){
                $campos = $_SESSION['_POST']['campos']);    
                // executa lógica de negócio associada ao formulário
            }
            //limpa dados de entrada da sessão 
            unset($_SESSION['_POST']);
        }
        ```
- **security.php**:
    - Definições associadas a controle de acesso (usadas em conjunto com o plugin Advanced Access Manager)

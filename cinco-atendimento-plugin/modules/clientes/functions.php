<?php
    require_once(plugin_dir_path(__FILE__) . 'model/clientes-dao.php');
    require_once(plugin_dir_path(__FILE__) . '../chamados/model/chamados-dao.php');

    session_start();

    $chave = NULL;
    $valor = NULL;
    $retorno = NULL;
    $mensagem = NULL;
    $cliente = NULL;
    $erros_validacao = [];
    $chamados = NULL;

    //recupera dados do formulário
    if(isset($_SESSION['_POST'])) {
        $chave = $_SESSION['_POST']['chave'];
        $valor = preg_replace("/[^0-9]/", "", $_SESSION['_POST']['valor']); //remove carcateres não numéricos

        $id = ($chave == 'id' ? $valor : NULL);
        $cpf_cnpj = ($chave == 'cpf_cnpj' ? $valor : NULL);
        
        $cliente = ClienteDAO::consultaCliente($id, $cpf_cnpj, TRUE, TRUE, TRUE, TRUE);
                
        if(isset($cliente)) { //recupera chamados em aberto 
            $chamados = ChamadosDAO::consultaChamados('id_cliente',$cliente->id);
        }
        else {
            $retorno = ERRO;
            $mensagem = "Cliente não encontrado: $valor ";
        }
        
        //limpa dados de entrada da sessão 
        unset($_SESSION['_POST']);
    }

    function novo_contato_filial($args){
        $contato = new ContatoFilial($args[0]);
        if(!$contato->valida($erros))
            return [
                'codigo' => ERRO, 
                'mensagem' => 'Os dados informados apresentam erros',
                'erros' => $erros
            ];    

        $ret = ContatosFilialDAO::novoContatoFilial($contato);

        error_log('>>>>>> ContatosFilialDAO::novoContatoFilial($contato)');
        var_dump2error_log($contato);

        return [
            'codigo' => $ret == 0 ? SUCESSO : ERRO, 
            'mensagem' => $ret == 0 ? 'Novo contato adicionado com sucesso' : 'Erro ao adicionar contato',
            'id' => $contato->id_contato
        ];
    }

    function editar_contato_filial($args){
        $contato = new ContatoFilial($args[0]);
        if(!$contato->valida($erros))
            return [
                'codigo' => ERRO, 
                'mensagem' => 'Os dados informados apresentam erros',
                'erros' => $erros
            ];    

        $ret = ContatosFilialDAO::atualizaContatoFilial($contato);
        return [
            'codigo' => $ret == 0 ? SUCESSO : ERRO, 
            'mensagem' => $ret == 0 ? 'Alteração processada com sucesso' : 'Erro ao processar alteração'
        ];
    }

    function excluir_contato_filial($args){
        $ret = ContatosFilialDAO::excluiContatoFilial(new ContatoFilial($args[0]));
        return [
            'codigo' => $ret == 0 ? SUCESSO : ERRO, 
            'mensagem' => $ret == 0 ? 'Exclusão processada com sucesso' : 'Erro ao processar exclusão'
        ];
    }
?>
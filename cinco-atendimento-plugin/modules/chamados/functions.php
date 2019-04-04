<?php
    require_once(plugin_dir_path(__FILE__) . 'model/chamados-dao.php');
    require_once(plugin_dir_path(__FILE__) . '../clientes/model/clientes-dao.php');
    session_start();

    $chave = NULL;
    $valor = NULL;
    $retorno = NULL;
    $chamado = NULL;

    //recupera dados do formulário
    if(isset($_SESSION['_POST'])) {
        $operacao = $_SESSION['_POST']['operation'];
        switch($operacao){
            case 'novo':
                $chave = $_SESSION['_POST']['chave'];
                $valor = $_SESSION['_POST']['valor'];
                $chamado = new Chamado([$chave => $valor]);
                $retorno = novo_chamado($chamado);
                if($retorno['codigo'] == SUCESSO)
                    $chamado->iniciado = TRUE;
            break;
            case 'registra':
                $chamado = new Chamado($_SESSION['_POST']['chamado']);
                $retorno = registra_chamado($chamado);
                $chamado->iniciado = FALSE;
            break;
            case 'consulta':
                $chave = $_SESSION['_POST']['chave'];
                $valor = $_SESSION['_POST']['valor'];
                $dt_i = $_SESSION['_POST']['data_inicio'];
                $dt_f = $_SESSION['_POST']['data_fim'];
                $concluidos = (isset($_SESSION['_POST']['concluidos']) && $_SESSION['_POST']['concluidos'] == '1');
                $pendentes = (isset($_SESSION['_POST']['pendentes']) && $_SESSION['_POST']['pendentes'] == '1');
                $retorno = consulta_chamados($chave, $valor, $dt_i, $dt_f, $concluidos, $pendentes);
            break;
            case 'encerrar':
                $protocolo = $_SESSION['_POST']['protocolo'];
                $comentario = $_SESSION['_POST']['comentario'];
                $retorno = encerrar_chamado($protocolo, $comentario);
            break;
            case 'comentar':
                $protocolo = $_SESSION['_POST']['protocolo'];
                $comentario = $_SESSION['_POST']['comentario'];
                $retorno = comentar_chamado($protocolo, $comentario);
            break;
        }
        //limpa dados de entrada da sessão 
        unset($_SESSION['_POST']);
    }
    
    // novo chamado($chamado)
    function novo_chamado(&$chamado){
        $cliente = NULL;
        
        if($chamado->id_cliente != NULL) {
            $cliente = ClienteDAO::consultaCliente($chamado->id_cliente, NULL, TRUE);   
            if($cliente == NULL)
            return ['codigo' => ERRO, 
                    'mensagem' => "Cliente $chamado->id_cliente não encontrado. Verifique o Id digitado ou informe o CPF/CNPJ para iniciar atendimento",
                    'erros' => ['id_cliente' => "Cliente $chamado->id_cliente não encontrado"]];
        }
        else {
            if($chamado->valida_cpf_cnpj($msg_erro))
                $cliente = ClienteDAO::consultaCliente(NULL, $chamado->cpf_cnpj, TRUE);   
            else 
                return ['codigo' => ERRO, 
                        'mensagem' => $msg_erro, 
                        'erros' => ['cpf_cnpj' => $msg_erro]];
        }

        $chamado->data_criacao = (new DateTime('NOW', new DateTimeZone('America/Sao_Paulo')))->format('d-m-Y H:i:s');
        $chamado->protocolo = ProtocoloDAO::geraProtocolo()->protocolo_id;

        if($cliente == NULL){
            $chamado->cpf_cnpj = formata_cpf_cnpj($chamado->cpf_cnpj);
            
            return ['codigo' => SUCESSO,
                    'mensagem' => "Atendimento iniciado. Cliente $chamado->cpf_cnpj não encontrado."];
        }
        else {
            $contato = $cliente->filiais[0]->contatos[0];
            $chamado->id_cliente = $cliente->id;
            $chamado->empresa = $cliente->nome;
            $chamado->contato = $contato->nome;
            $chamado->email = $contato->email;
            $chamado->telefone = $contato->telefone;
            $chamado->cpf_cnpj = formata_cpf_cnpj($cliente->cpf_cnpj);
            
            return ['codigo' => SUCESSO,
                    'mensagem' => "Atendimento ao cliente $chamado->empresa ($chamado->cpf_cnpj) iniciado"];
        }
    }

    // registra chamado
    function registra_chamado($chamado){
        if($chamado->valida($erros)){
            if(ChamadosDAO::gravaChamado($chamado)) {
                ChamadosDAO::notificaChamadoBilling($chamado);
                return ['codigo' => SUCESSO,
                        'mensagem' => "Atendimento $chamado->protocolo registrado com sucesso"];
            }
            return ['codigo' => ERRO,
                    'mensagem' => "Ocorreu um erro ao registrar o Atendimento",
                    'erros' => $erros];
        }
        return ['codigo' => ERRO,
                'mensagem' => "Os dados informados apresentam erros",
                'erros' => $erros];
    }

    // consulta chamados 
    function consulta_chamados($chave, $valor, $dt_i, $dt_f, $concluidos, $pendentes){
        $c = ChamadosDAO::consultaChamados($chave, $valor, $dt_i, $dt_f, $concluidos, $pendentes);

        if(sizeof($c) == 0) {
            return ['codigo' => ERRO,
                    'mensagem' => "Nenhum registro de Atendimento encontrado"];
        }
        return ['codigo' => SUCESSO,
                'chamados' => $c];
    }

    // encerra chamado associado a um numero de protocolo especifico
    function encerrar_chamado($protocolo, $comentario){
        if(ChamadosDAO::encerraChamado($protocolo, $comentario)){
            return ['codigo' => SUCESSO,
                    'mensagem' => "Chamado $protocolo encerrado com sucesso"];
        }
        return ['codigo' => ERRO,
                'mensagem' => "Ocorreu um erro ao encerrar o chamado $protocolo"];
    }

    // adiciona comentário ao histórico do chamado
    function comentar_chamado($protocolo, $comentario){
        if(ChamadosDAO::comentaChamado($protocolo, $comentario)){
            return ['codigo' => SUCESSO,
                    'mensagem' => "Comentário adicionado ao chamado $protocolo"];
        }
        return ['codigo' => ERRO,
                'mensagem' => "Ocorreu um erro ao adicionar comentário ao chamado $protocolo"];
    }


    function formata_cpf_cnpj($cpf_cnpj){
        return strlen($cpf_cnpj) == 14 ? CnpjValidator::format($cpf_cnpj) : CpfValidator::format($cpf_cnpj);
    }
?>
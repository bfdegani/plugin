<?php
    /**
     * Classes para consulta e inclusão de Chamados de Atendimentos na base do wordpress
     * Bruno Degani - 30/10/2018
     */
    require_once(CINCO_INC_PATH . '/database.php');
    require_once(CINCO_INC_PATH . '/cnpj-validator.php');
    require_once(CINCO_INC_PATH . '/cpf-validator.php');

    class Chamado extends VO{
        protected $protocolo;
        protected $data_criacao;
        protected $data_conclusao;
        protected $dias_em_aberto;
        protected $usuario_criacao;
        protected $usuario_conclusao;
        protected $id_cliente;
        protected $contato;
        protected $empresa;
        protected $cpf_cnpj;
        protected $motivo;
        protected $motivo_desc;
        protected $motivo_sub_desc;
        protected $plano;
        protected $telefone;
        protected $email;
        protected $canal_contato;
        protected $canal_retorno;
        protected $solicitacao;
        protected $resposta;
        protected $comentarios;

        // funções de validação de campos
        // como padrão, o nome da função deve ser valida_XXXX
        // retornam true se validação ok ou false em caso contrario.
        // $msg_erro armazena a mensagem de erro da validação.

        //protocolo (obrigatório e numérico)
        function valida_protocolo(&$msg_erro){
            if(!isset($this->protocolo) || !is_numeric($this->protocolo)){
                $msg_erro = 'Protocolo inválido: ' . $this->protocolo;
                return false;
            }
            return true;
        }
        
        // data (obrigatório)
        function valida_data_criacao(&$msg_erro){
            try{
                //ajusta formato de tela para formato do banco
                $dt = DateTime::createFromFormat('d-m-Y H:i:s', $this->data_criacao);
                $this->data_criacao = $dt->format('Y-m-d H:i:s'); 
            }
            catch(Exception $e){
                $msg_erro = 'Data inválida: ' . $this->data_criacao;
                return false;
            }
            return true;
        }

        //contato (obrigatório, apenas letras e espaço)
        function valida_contato(&$msg_erro){
            if(!isset($this->contato)) {
                $msg_erro = 'Nome de contato não informado';
                return false;
            }
            $this->contato = trim($this->contato);
            if(strlen($this->contato) == 0) {
                $msg_erro = 'Nome de contato não informado';
                return false;
            }
            else {
                $this->contato = trim($this->contato);
                if(!preg_match("/^[\wÀ-ú ]{5,255}$/", $this->contato)) {
                    $msg_erro = 'Nome de contato inválido: ' . $this->contato;    
                    return false;
                }
            }
            return true;
        }

        //empresa (não obrigatorio, sem validação. apenas retira espaços no inicio/fim e valida tamanho)
        function valida_empresa(&$msg_erro){
            if(isset($this->empresa))
                $this->empresa = trim($this->empresa);
                if(strlen($this->empresa > 255)){
                    $msg_erro = 'Empresa deve ter no máximo 255 caracteres';
                    return false;
                }
            return true;
        }

        //telefone (obrigatório, retira mascaras e valida)
        function valida_telefone(&$msg_erro){
            if(!isset($this->telefone)) {
                $msg_erro = 'Informe o telefone com o código de área';
                return false;
            }
            else {
                $telefone = preg_replace("/[^\+0-9]/", "",$this->telefone); //retira mascara e espaçoes
                if(!preg_match("/\+?[0-9]{10,15}/", $telefone)) {
                    $msg_erro = 'Informe o telefone com o código de área: ' . $this->telefone;
                    return false;
                }
            }
            $this->telefone = $telefone;
            return true;
        }

        //email (opcional, se informado verifica se tem o formato de e-mail)
        function valida_email(&$msg_erro){
            if(isset($this->email))
                $this->email = strtolower(trim($this->email));

            if(strlen($this->email) > 0 && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $msg_erro = 'E-mail de contato inválido: ' . $this->email;
                return false;
            }
            return true;
        }

        //solicitacao (obrigatorio) 
        function valida_solicitacao(&$msg_erro){
            if(!isset($this->solicitacao)) {
                $msg_erro = 'Solicitação não informada';
                return false;
            }
            $this->solicitacao = trim($this->solicitacao);
            if(strlen($this->solicitacao) == 0) {
                $msg_erro = 'Solicitação não informada';
                return false;
            }
            if(strlen($this->solicitacao) > 1000) {
                $msg_erro = 'Registro da solicitação deve ter no máximo 1.000 caracteres';
                return false;
            }
            return true;
        }

        //cpf/cnpj (opcional, se informado, deve ser validado)
        //essa função é pública pq é usada isoladamente 
        public function valida_cpf_cnpj(&$msg_erro){
            $this->cpf_cnpj = preg_replace("/[^0-9]/", "", $this->cpf_cnpj); //limpa caracteres especiais
            $tamanho = strlen($this->cpf_cnpj);

            if($tamanho == 0){
                $msg_erro = 'CPF/CNPJ não informado';
                return false;
            }
            else if($tamanho == 11) {
                $cpf = new CpfValidator();
                if(!$cpf->validate($this->cpf_cnpj)) {
                    $msg_erro = 'CPF inválido: ' . $this->cpf_cnpj;
                    return false;
                }
            }
            else if($tamanho == 14) {
                $cnpj = new CnpjValidator();
                if(!$cnpj->validate($this->cpf_cnpj)) {
                    $msg_erro = 'CNPJ inválido: ' . $this->cpf_cnpj;
                    return false;
                }
                else return true;
            }
            else if($tamanho > 0) {
                $msg_erro = 'CPF/CNPJ inválido: ' . $this->cpf_cnpj;
                return false;
            }
            return true;
        }

        //usuario_criacao (obtem usuario logado)
        function valida_usuario_criacao(&$msg_erro){
            global $current_user;
            $this->usuario_criacao = $current_user->user_login;
            return true;
        }

        // id_cliente
        function valida_id_cliente(&$msg_erro){
            if(!is_numeric($this->id_cliente))
                $this->id_cliente = NULL;
            return true;
        }
    }

    class ChamadosDAO {
        /**
         * consulta registros de chamado
         */
        public static function listaChamados($max_linhas = 100){
            return self::consultaChamados();
        }

        public static function consultaChamados($chave = NULL, $valor = NULL, 
                                                $dt_inicio = NULL, $dt_fim = NULL, 
                                                $concluidos = FALSE, $pendentes = TRUE, 
                                                $max_linhas = 100){
            if(!$concluidos && !$pendentes)
                return NULL;

            $valor = strtolower(trim($valor));
            switch ($chave){
                case 'email':
                    $valor = preg_replace("/[^\-\_\@\.a-z0-9]/", "", $valor);
                    break;
                case 'protocolo': 
                case 'cpf_cnpj':
                case 'id_cliente':
                    $valor = preg_replace("/[^0-9]/", "", $valor);
                    break;
                default:
                    $valor = NULL;
            }
            
            // valida datas
            try{
                if(isset($dt_inicio) && strlen($dt_inicio) > 0) {
                    $dt_inicio = preg_replace('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', '\3-\2-\1', $dt_inicio);
                    $dti = DateTime::createFromFormat('Y-m-d', $dt_inicio);
                }
                if(isset($dt_fim) && strlen($dt_fim) > 0) {
                    $dt_fim = preg_replace('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', '\3-\2-\1', $dt_fim);
                    $dtf = DateTime::createFromFormat('Y-m-d', $dt_fim);
                }
                if(isset($dti) && isset($dtf) && $dti > $dtf)
                return NULL;
            }
            catch(Exception $e){
                return NULL;
            }
            
            $chamados = [];
            $params = [];
            $types = '';

            $query = "SELECT ch.protocolo, ch.id_cliente, ch.empresa, ch.contato, ch.telefone, ch.email, ";
            $query .= "    DATE_FORMAT(ch.data_criacao, '%d/%m/%Y %H:%i:%s') AS data_criacao, ";
            $query .= "    DATE_FORMAT(ch.data_conclusao, '%d/%m/%Y %H:%i:%s') AS data_conclusao, ";
            $query .= "    DATEDIFF(IFNULL(ch.data_conclusao, now()), ch.data_criacao) AS dias_em_aberto, ";
            $query .= "    IF(LENGTH(ch.cpf_cnpj) = 11, CONCAT(SUBSTR(ch.cpf_cnpj, 1, 3), '.', SUBSTR(ch.cpf_cnpj, 4, 3), '.', SUBSTR(ch.cpf_cnpj, 7, 3), '-', SUBSTR(ch.cpf_cnpj, 10, 2)), ";
            $query .= "         IF(LENGTH(ch.cpf_cnpj) = 14, CONCAT(SUBSTR(ch.cpf_cnpj, 1, 2), '.', SUBSTR(ch.cpf_cnpj, 3, 3), '.', SUBSTR(ch.cpf_cnpj, 6, 3), '/', SUBSTR(ch.cpf_cnpj, 9, 4), '-', SUBSTR(ch.cpf_cnpj, 13, 2)), ch.cpf_cnpj)) AS cpf_cnpj, ";
            $query .= "    ch.solicitacao, ch.resposta, ch.comentarios,  ch.usuario_criacao, ch.usuario_conclusao, ";
            $query .= "p.nome AS plano, m.id AS motivo, m.categoria AS motivo_desc, m.motivo AS motivo_sub_desc, c.descricao AS canal_contato, r.descricao AS canal_retorno ";
            $query .= "FROM chamados ch ";
            $query .= "INNER JOIN motivos_contatos m ON ch.motivo = m.id ";
            $query .= "LEFT OUTER JOIN planos p ON ch.plano = p.id ";
            $query .= "LEFT OUTER JOIN canais_contatos c ON ch.canal_contato = c.nome ";
            $query .= "LEFT OUTER JOIN canais_contatos r ON ch.canal_retorno = r.nome ";
            $query .= "where 1 = 1 ";
                        
            if(!$concluidos && $pendentes)
                $query .= "and ch.data_conclusao is NULL ";
            else if($concluidos && !$pendentes)
                $query .= "and ch.data_conclusao is not NULL ";

            if(isset($valor)){
                $query .= "and ch.$chave = ? ";
                array_push($params, $valor);
                $types .= 's';
            }

            if(strlen($dt_inicio) > 0) {
                $query .= "and ch.data_criacao >= ? ";
                array_push($params, $dt_inicio);
                $types .= 's';
            }
            
            if(strlen($dt_fim) > 0) {
                $query .=  "and ch.data_criacao <= ? ";
                array_push($params, $dt_fim);
                $types .= 's';
            }

            $query .= "order by ch.protocolo desc limit $max_linhas ";

            return DB::executa_query(CINCO_ATENDIMENTO_DB, $query, new Chamado(), $types, $params);
        }

 
        /**
         * registra chamado na base de dados
         */
        public static function gravaChamado($chamado) {
            $sql_cmd = "insert into chamados(";
            $sql_cmd.= "protocolo, data_criacao, usuario_criacao, ";
            $sql_cmd.= "id_cliente, contato, empresa, cpf_cnpj, telefone, email,";
            $sql_cmd.= "canal_contato, canal_retorno, motivo, plano, solicitacao) ";
            $sql_cmd.= "values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            $params = array(
                $chamado->protocolo, $chamado->data_criacao, $chamado->usuario_criacao,
                $chamado->id_cliente, $chamado->contato, $chamado->empresa, $chamado->cpf_cnpj, $chamado->telefone, $chamado->email,
                $chamado->canal_contato, $chamado->canal_retorno, $chamado->motivo, $chamado->plano, $chamado->solicitacao
            );

            return DB::executa(CINCO_ATENDIMENTO_DB, $sql_cmd, 'ssssssssssssss', $params) === 0;
        }

        /**
         * Notifica o sistema de billing sobre a abertura de um novo chamamdo
         */
        public static function notificaChamadoBilling($chamado){
            $sql_cmd = "insert into billing.protocolo_atendimento(";
            $sql_cmd.= "id_cliente, protocolo, data_criacao) ";
            $sql_cmd.= "values(?,?,?)";
            
            $params = array($chamado->id_cliente, $chamado->protocolo, $chamado->data_criacao);

            return DB::executa(CINCO_BILLING_DB, $sql_cmd, 'sss', $params) === 0;            
        }

        // encerra chamado identificado por um número de protocolo
        public static function encerraChamado($protocolo, $resposta){
            global $current_user;
            
            $usuario_conclusao = $current_user->user_login;
            $data_conclusao = (new DateTime('NOW', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s');
            
            $params = array($resposta, $usuario_conclusao, $data_conclusao, $protocolo);

            $sql_cmd = "update chamados set resposta = ?, usuario_conclusao = ?, data_conclusao = ? ";
            $sql_cmd.= "where protocolo = ? and data_conclusao is NULL"; 

            return DB::executa(CINCO_ATENDIMENTO_DB, $sql_cmd, 'ssss', $params) === 0;
        }

        // adiciona comentário a chamado identificado por um número de protocolo
        public static function comentaChamado($protocolo, $texto){
            global $current_user;
            $data_comentario = (new DateTime('NOW', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s');
            
            $obj_comentario = json_decode('{"data":"","usuario":"","texto":""}');
            $obj_comentario->data = $data_comentario;
            $obj_comentario->usuario = $current_user->user_login;
            $obj_comentario->texto = $texto;

            $query = "select comentarios from chamados where protocolo=?";
            $historico = DB::executa_query(CINCO_ATENDIMENTO_DB, $query, new Chamado(), 's', [$protocolo])[0];

            if($historico->comentarios == NULL){
                $comentarios = '{"comentarios":[' . json_encode($obj_comentario) . ']}';
            }
            else {
                $comentarios = json_decode($historico->comentarios);
                array_unshift($comentarios->{'comentarios'}, $obj_comentario);
                $comentarios = json_encode($comentarios);
            }

            $sql_cmd = 'update chamados set comentarios = ? where protocolo = ? and data_conclusao is NULL';
            $params = array($comentarios, $protocolo);

            return DB::executa(CINCO_ATENDIMENTO_DB, $sql_cmd, 'ss', $params) === 0;
        }
    }


?>
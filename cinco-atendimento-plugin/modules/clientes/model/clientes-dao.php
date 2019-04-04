<?php
    require_once(CINCO_INC_PATH . '/database.php');
    require_once(CINCO_INC_PATH . '/cnpj-validator.php');
    require_once(CINCO_INC_PATH . '/cpf-validator.php');
   
    ///////////////////////// CLIENTE /////////////////////////
    class Cliente extends VO {
        protected $id;
        protected $nome;
        protected $cpf_cnpj;
        protected $dt_cadastro;
        protected $filiais = [];
        protected $contratos = [];
        protected $faturas = [];
        protected $itens_nf = [];

        public function __construct(Array $properties=array()){
            parent::__construct($properties);
        }
        
        public static function documentoFormatado($cpf_cnpj){
            if(strlen($cpf_cnpj) == 14) //cnpj
                return CnpjValidator::format($cpf_cnpj); 

            return CpfValidator::format($cpf_cnpj);
        }
    }

    class ClienteDAO {
        public static function consultaCliente($id = NULL, $cpf_cnpj = NULL, 
                $filiais = FALSE, $contratos = FALSE, $faturas = FALSE, $itens_nf = FALSE) {  
            
            $sql_cmd = "SELECT c.id_cliente as id, c.nome, c.numero_documento as cpf_cnpj, c.data_cadastro as dt_cadastro ";
            $sql_cmd.= "FROM billing.cliente c WHERE ";
            
            if(isset($id)){
                $sql_cmd.= "c.id_cliente = ?";
                $params = [$id];
                $types = 'i';
            }
            else if(isset($cpf_cnpj)){
                $sql_cmd.= "c.numero_documento = ?";
                $params = [$cpf_cnpj];
                $types = 's';
            }
            else return NULL;

            $clientes = DB::executa_query(CINCO_BILLING_DB, $sql_cmd, new Cliente(), $types, $params);;
            
            if(sizeof($clientes) == 0)
                return NULL;

            $cliente = $clientes[0];

            if($filiais)
                $cliente->filiais = FilialDAO::listaFiliaisCliente($cliente->id);
            if($contratos)
                $cliente->contratos = ContratoDAO::listaContratosCliente($cliente->id);    
            if($faturas)
                $cliente->faturas = FaturasDAO::listaFaturasCliente($cliente->id);    
            if($itens_nf)
                $cliente->itens_nf = ItensNotaFiscalDAO::listaItensNFCliente($cliente->id);    
            
            return $cliente;
        }
    }

    ///////////////////////// CONTRATOS /////////////////////////
    class Contrato extends VO {
        protected $id;
        protected $dt_inicio;
        protected $dt_fim;
        protected $dia_vencimento;
        protected $dia_inicio_ciclo;
        protected $nome_plano;
        protected $franquia;

        public function __construct(Array $properties=array()){
            parent::__construct($properties);
        }
    }

    class ContratoDAO {
        public static function listaContratosCliente($id_cliente) {
            $sql_cmd = "SELECT  ";
            $sql_cmd.= "c.id_contrato as id, ";
            $sql_cmd.= "c.data_cadastro as dt_inicio, ";
            $sql_cmd.= "ifnull(c.data_expiracao,'') as dt_fim, ";
            $sql_cmd.= "i.dia_vencimento, ";
            $sql_cmd.= "i.dia_inicio as dia_inicio_ciclo, ";
            $sql_cmd.= "p.nome as nome_plano, ";
            $sql_cmd.= "p.volume as franquia ";
            $sql_cmd.= "FROM billing.contrato c ";
            $sql_cmd.= "left outer join billing.ciclo i on c.id_ciclo = i.id_ciclo ";
            $sql_cmd.= "left outer join billing.plano p on c.id_plano = p.id_plano ";
            $sql_cmd.= "WHERE c.id_cliente = ? order by c.data_cadastro desc";
            
            return DB::executa_query(CINCO_BILLING_DB, $sql_cmd, new Contrato(), 'i', [$id_cliente]);
        }
    }

    ///////////////////////// FILIAIS /////////////////////////
    class Filial extends VO {
        protected $id_cliente;
        protected $estado;
        protected $cpf_cnpj;
        protected $cidade;
        protected $cep;
        protected $endereco;
        protected $numero;
        protected $complemento;
        protected $contatos = [];

        public function __construct(Array $properties=array()){
            parent::__construct($properties);
        }

        public function enderecoFormatado(){
            $e = "$this->endereco, $this->numero";
            $e.= (strlen($this->complemento)) > 0 ? " ($this->complemento) " : "";
            $e.= ". $this->cidade - $this->estado";
            $e.= ", CEP " . substr($this->cep,0,5) . "-" . substr($this->cep,5,3);
            return $e;
        }
    }

    class FilialDAO {
        public static function listaFiliaisCliente($id_cliente) {
            $sql_cmd = "SELECT f.id_cliente, f.NUMERO_DOCUMENTO as cpf_cnpj, f.estado, f.cidade, f.cep, ";
            $sql_cmd.= "f.endereco, ifnull(f.numero,'') as numero, ifnull(f.complemento,'') as complemento ";
            $sql_cmd.= "FROM billing.cliente_nf f ";
            $sql_cmd.= "WHERE f.id_cliente = ?";
           
            $filiais = DB::executa_query(CINCO_BILLING_DB, $sql_cmd, new Filial(), 'i', [$id_cliente]);
            
            foreach($filiais as $f)
                $f->contatos = ContatosFilialDAO::consultaContatosFilial($f->id_cliente, $f->estado);
            
            return $filiais;
        }
    }

    ///////////////////////// CONTATOS FILIAIS /////////////////////////
    class ContatoFilial extends VO {
        protected $id_cliente;
        protected $estado;
        protected $id_contato;
        protected $nome;
        protected $email;
        protected $telefone;
        protected $telefone2;
        protected $status;
        protected $dt_criacao;
        protected $dt_alteracao;
        protected $usuario;
        protected $historico;

        public function __construct(Array $properties=array()){
            parent::__construct($properties);
        }

        //contato (obrigatório, apenas letras e espaço)
        function valida_contato(&$msg_erro){
            if(!isset($this->contato)) {
                $msg_erro = 'Informe o nome do contato';
                return false;
            }

            $this->contato = trim($this->contato);
            if(strlen($this->contato) == 0) {
                $msg_erro = 'Informe o nome do contato';
                return false;
            }

            $this->contato = trim($this->contato);
            if(!preg_match("/^[\wÀ-ú ]{5,255}$/", $this->contato)) {
                $msg_erro = 'Nome de contato inválido: ' . $this->contato;    
                return false;
            }
            return true;
        }

        //telefone (obrigatório, retira mascaras e valida)
        function valida_telefone(&$msg_erro){
            if(!isset($this->telefone) || $this->telefone == '') {
                $msg_erro = 'Informe o telefone com o código de área';
                return false;
            }

            $telefone = preg_replace("/[^\+0-9]/", "",$this->telefone); //retira mascara e espaçoes
            if(!preg_match("/\+?[0-9]{10,15}/", $telefone)) {
                $msg_erro = 'Telefone inválido: ' . $this->telefone;
                return false;
            }
            $this->telefone = $telefone;
            return true;
        }

        //telefone2 (opcional, retira mascaras e valida)
        function valida_telefone2(&$msg_erro){
            error_log("===================> telefone2 = '$this->telefone2'");

            if(isset($this->telefone2) && $this->telefone2 != '') {
                $telefone2 = preg_replace("/[^\+0-9]/", "",$this->telefone2); //retira mascara e espaçoes
                if(!preg_match("/\+?[0-9]{10,15}/", $telefone2)) {
                    $msg_erro = 'Telefone inválido: ' . $this->telefone2;
                    return false;
                }
                $this->telefone2 = $telefone2;
            }
            return true;
        }

        //email (opcional, se informado verifica se tem o formato de e-mail)
        function valida_email(&$msg_erro){
            if(!isset($this->email)){
                $msg_erro = 'Informe o e-mail do contato';
                return false;
            }

            $this->email = strtolower(trim($this->email));
            if(strlen($this->email) > 0 && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $msg_erro = 'E-mail de contato inválido';
                return false;
            }
            return true;
        }
    }

    class ContatosFilialDAO {
        public static function consultaContatosFilial($id_cliente, $estado) {
            $sql_cmd = "SELECT id_contato, id_cliente, estado, ";
            $sql_cmd.= "nome, email, telefone, telefone2, status, dt_criacao, dt_alteracao ";
            $sql_cmd.= "FROM billing.contato c ";
            $sql_cmd.= "WHERE status = 'ATIVO' and id_cliente = ? and estado = ?";
            
            return DB::executa_query(CINCO_BILLING_DB, $sql_cmd, new ContatoFilial(), 'is', [$id_cliente, $estado]);
        }

        public static function geraHistoricoContato($id_contato){
            //obtem dados atuais do contato para registrar historico
            $query = "SELECT nome, email, telefone, telefone2, status, historico ";
            $query.= "FROM billing.contato c ";
            $query.= "WHERE id_contato = ?";
            
            $old = DB::executa_query(CINCO_BILLING_DB, $query, new VO(), 'i', [$id_contato])[0];

            $dt_alteracao = (new DateTime('NOW', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s');
            $old->dt_alteracao = $dt_alteracao;
           
            $historico = $old->historico;
            unset($old->historico);
            
            if($historico == NULL){
                $historico = '{"historico":[' . json_encode($old) . ']}';
            }
            else {
                $historico = json_decode($historico);
                array_unshift($historico->{'historico'}, $old);
                $historico = json_encode($historico);
            }

            return ['data' => $dt_alteracao, 'historico' => $historico];
        }   

        public static function atualizaContatoFilial($contato) {
            //gera documento json com histórico
            $historico = self::geraHistoricoContato($contato->id_contato);
            
            //atualiza contato
            $sql_cmd = "update billing.contato c set ";
            $sql_cmd.= "nome = ?, email = ?, telefone = ?, telefone2 = ?, ";
            $sql_cmd.= "usuario = ?, dt_alteracao = ?, historico = ? ";
            $sql_cmd.= "where id_contato = ?";
            
            global $current_user;
            $params = [
                $contato->nome,
                $contato->email,
                $contato->telefone,
                $contato->telefone2,
                $current_user->user_login,
                $historico['data'],
                $historico['historico'],
                $contato->id_contato
            ];

            return DB::executa(CINCO_BILLING_DB, $sql_cmd, 'sssssssi', $params);
        }

        public static function excluiContatoFilial($contato){
            //atualiza contato
            $sql_cmd = "update billing.contato c set ";
            $sql_cmd.= "status = 'EXCLUIDO', usuario = ?, dt_alteracao = ? ";
            $sql_cmd.= "where id_contato = ?";
            
            global $current_user;
            $params = [
                $current_user->user_login,
                (new DateTime('NOW', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s'),
                $contato->id_contato
            ];

            return DB::executa(CINCO_BILLING_DB, $sql_cmd, 'ssi', $params);
        }

        // inclui contato
        // atualiza o objeto $contato com o id retorna pela inserção
        public static function novoContatoFilial(&$contato){
            $sql_cmd = "insert into billing.contato (id_cliente, estado, nome, email, telefone, telefone2, usuario)";
            $sql_cmd.= "values(?,?,?,?,?,?,?)";
            
            global $current_user;
            $params = [
                $contato->id_cliente, 
                $contato->estado, 
                $contato->nome,
                $contato->email,
                $contato->telefone,
                $contato->telefone2,
                $current_user->user_login
            ];

            $ret = DB::executa(CINCO_BILLING_DB, $sql_cmd, 'issssss', $params, $id);
            $contato->id_contato = $id;

            return $ret;
        }
        
    }

    ///////////////////////// FATURAS /////////////////////////
    class Fatura extends VO {
        protected $ano;
        protected $mes;
        protected $id_ciclo;
        protected $data_emissao;
        protected $data_vencimento;
        protected $consumo_dados;
        protected $valor_total;
        protected $valor_impostos;
        protected $fatura_disponivel;
    }

    class FaturasDAO {
        public static function ListaFaturasCliente($id_cliente){
            $sql_cmd = "select ";
            $sql_cmd.= "nf.ano, nf.mes, nf.id_ciclo, ";
            $sql_cmd.= "max(nf.data_emissao) as data_emissao, max(nf.data_vencimento) as data_vencimento, ";
            $sql_cmd.= "sum(nf.consumo_dados) as consumo_dados, sum(nf.valor_total) as valor_total, ";
            $sql_cmd.= "sum(nf.valor_pis + nf.valor_cofins + nf.valor_icms + nf.valor_funttel + nf.valor_fust) as valor_impostos, count(d.id_ciclo) as fatura_disponivel ";
            $sql_cmd.= "from billing.nota_fiscal nf ";
            $sql_cmd.= "left outer join billing.documento_fatura d ";
            $sql_cmd.= "on nf.id_cliente = d.id_cliente and nf.id_ciclo = d.id_ciclo and nf.mes = d.mes and nf.ano = d.ano ";
            $sql_cmd.= "where nf.id_cliente = ?  ";
            $sql_cmd.= "group by nf.id_ciclo, nf.mes, nf.ano ";
            $sql_cmd.= "order by nf.ano desc, nf.mes desc ";

            return DB::executa_query(CINCO_BILLING_DB, $sql_cmd, new Fatura(), 'i', [$id_cliente]);
        }
    }

    ///////////////////////// ITENS NOTAS FISCAIS /////////////////////////
    class ItemNotaFiscal extends VO {
        protected $uf;
        protected $ano;
        protected $mes;
        protected $id_ciclo;
        protected $numero_nota_fiscal;
        protected $servico;
        protected $data_emissao;
        protected $consumo_dados;
        protected $valor_total;
        protected $valor_impostos;
    }

    class ItensNotaFiscalDAO {
        public static function ListaItensNFCliente($id_cliente){
            $sql_cmd = "select ";
            $sql_cmd.= "    i.uf, i.ano, i.mes, i.id_ciclo, i.numero_nota_fiscal, s.descricao as servico, ";
            $sql_cmd.= "    nf.data_emissao, nf.data_vencimento, ";
            $sql_cmd.= "    i.consumo_dados, i.valor_total, ";
            $sql_cmd.= "    (i.valor_pis + i.valor_cofins + i.valor_icms + i.valor_funttel + i.valor_fust) as valor_impostos ";
            $sql_cmd.= "from billing.nota_fiscal nf ";
            $sql_cmd.= "inner join billing.item_nota_fiscal i on nf.numero_nota_fiscal = i.numero_nota_fiscal and nf.uf = i.uf ";
            $sql_cmd.= "inner join billing.descricao_servico s on i.cod_servico = s.cod_servico ";
            $sql_cmd.= "where nf.id_cliente = ? ";
            $sql_cmd.= "order by i.ano desc, i.mes desc, i.uf asc, s.cod_servico asc";

            return DB::executa_query(CINCO_BILLING_DB, $sql_cmd, new ItemNotaFiscal(), 'i', [$id_cliente]);
        }
    }

?>
<?php
/**
 * Classes auxiliares para acesso à base de dados
 * Criado por Bruno Degani em 05/02/2019
 */

/**
 * Classe para criação de conexão com banco de dados
 */

class DB {
    // retorna uma conexão com a base de dados (mysqli)
    // a partir de um nome de conexão configurada na página de administração do plugin
    public static function conecta($nome_conexao) {
        $db_params = get_option(CINCO_BD_WP_OPTION)[$nome_conexao];

        // Create connection
        $dbCon = new mysqli(
            $db_params["host"], 
            $db_params["username"],
            $db_params["passwd"],
            $db_params["dbname"], 
            $db_params["port"]
        );

        $dbCon->set_charset("utf8");

        // Check connection
        if ($dbCon->connect_error) {
            die("Connection failed: " . $dbCon->connect_error);
        }
        return $dbCon;
    }

    /**
     * executa comando sql via prepared statement
     *      $nome_conexao: nome da conexão configurada no plugin
     *      $sql_cmd: comando a ser executado (parametros devem ser representados por ?)
     *      $param_types (opcional): string onde cada caracter representa o tipo do parametro correspondente no array sendo:
     *          i - integer, d - double, s - string, b - BLOB
     *      $param_array (opcional): array com os parametros a serem usados no comando, na ordem em que aparecem
     *      &$err_msg (opcional): retorna a mensagem em caso de erro
     *      &$err_msg (opcional): retorna id gerado em caso de insert com coluna com auto_increment
     *      return int (código de erro ou 0 em caso de sucesso)
     * exemplo:
     *      executa('atendimento', 'update chamados set comentario = ? where protocolo =?', 'ss', ['teste','2019001976']);
     */
    public static function executa($nome_conexao, $sql_cmd, $param_types = NULL, $param_array = array(), &$id = NULL, &$err_msg = NULL){
        $db = self::conecta($nome_conexao);
        $stmt = $db->prepare($sql_cmd);

        if(sizeof($param_array) > 0)
            $stmt->bind_param($param_types, ...$param_array);
        
        $stmt->execute();

        $err = mysqli_stmt_errno($stmt);
        $err_msg = $stmt->error;
        
        error_log("stmt->insert_id: $stmt->insert_id");
        error_log("db->insert_id: $db->insert_id");

        if( $err != 0)
            error_log("DB::executa ERRO: $err - $err_msg");
        else
            $id = $stmt->insert_id;
        
        $stmt->close();
        $db->close();   

        return $err;
    }

    /**
     * executa query sql via prepared statement
     *      $nome_conexao: nome da conexão configurada no plugin
     *      $query: query sql a ser executada (parametros devem ser representados por ?)
     *      $vo: objeto do tipo a ser retornado no array de resultados
     *      $param_types (opcional): string onde cada caracter representa o tipo do parametro correspondente no array sendo:
     *          i - integer, d - double, s - string, b - BLOB
     *      $param_array (opcional): array com os parametros a serem usados no comando, na ordem em que aparecem
     *      &$err_msg (opcional): armazena a mensagem em caso de erro
     *      return [] (array de objetos)
     * exemplo:
     *      executa_query('atendimento', 'select * from chamados where protocolo = ?', $chamado, 's', ['2019001976']);
     */
    public static function executa_query($nome_conexao, $query, $vo, $param_types = NULL, $param_array = array(), &$err_msg = NULL){
        $result_array = [];
        $db = self::conecta($nome_conexao);
        $stmt = $db->prepare($query);

        if(sizeof($param_array) > 0) 
            $stmt->bind_param($param_types, ...$param_array);

        $stmt->execute();
        
        $err = mysqli_stmt_errno($stmt);
        $err_msg = $stmt->error;
        
        if($err == 0) {
            $rs = $stmt->get_result();

            if($rs->num_rows > 0){
                $vo_class = get_class($vo);
                
                while($row = $rs->fetch_assoc()){
                    $obj = new $vo_class($row);
                    array_push($result_array, $obj);
                }
                $rs->close();
            }
        }
        else {
            error_log("DB::executa_query ERRO: $err - $err_msg");
        }
        
        $stmt->close();
        $db->close();   

        return $result_array;
    }
}


/**
 * Classe que implementa a base de um ValueObject,
 */
class VO implements JsonSerializable {
    //construtor padrão, recebe um array chave=>valor com os atributos da classe
    public function __construct(Array $properties=array()){
        foreach($properties as $key => $value){
            $this->{$key} = $value;
        }
    }

    //setter genérico
    public function __set($property, $value){
        return $this->{$property} = $value;
    }
  
    //getter genérico
    public function __get($property){
        return $this->{$property};
    }

    // função responsável por validação de dados.
    // chama as funções especificas de validação de cada atributo da classe. As funções devem ter o nome "valida_<atributo>".
    // retorna true se ok, false em caso contrario
    // $erros é um array que armazena as mensagens de erro dos campos que não passaram pela validação
    // para atributos que não necessitem de validação/tratamento basta não implementar a função de validação específica.
    public function valida(&$erros){
        $ok = true;
        $erros = [];
        $vars = get_object_vars($this);
        foreach($vars as $key => $value){
            if(method_exists($this, 'valida_' . $key)){
                $msg_erro = '';                
                if(!$this->{'valida_' . $key}($msg_erro)){
                    $erros[$key] = $msg_erro;
                    $ok = false;
                }
            }
        }
        return $ok;
    }

    //método da interface JsonSerializable necessário para conversão dos atributos classe em um documento json
    public function jsonSerialize(){
        return get_object_vars($this);
    }
}
?>
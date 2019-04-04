<?php
    /**
     * Classe para obtenção de número de protocolo gerado na base de dados
     * Bruno Degani - 30/10/2018
     */
    require_once(CINCO_INC_PATH . '/database.php');
    class Protocolo extends VO {
        protected $protocolo_id;
    }

    class ProtocoloDAO {
        public static function geraProtocolo() {
            $sql_cmd = "SELECT protocolo as protocolo_id FROM protocolo";
            return DB::executa_query(CINCO_ATENDIMENTO_DB, $sql_cmd, new Protocolo())[0];
        }
    }
?>
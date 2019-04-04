<?php
    /**
     * Classes para consulta FAQ de Atendimento
     * Bruno Degani - 30/10/2018
     */
    require_once(CINCO_INC_PATH . '/database.php');
    
    class FAQ extends VO {
        protected $pergunta;
        protected $resposta;
        protected $palavras_chave;
        protected $data_alteracao;
        protected $usuario;
    }

    class FaqDAO {
        /**
         * consulta perguntas disponíveis
         */
        public static function getFAQ(){
            $sql_cmd = "select pergunta, resposta, palavras_chave, data_alteracao, usuario from faq ";
            $sql_cmd.= "order by prioridade asc, data_alteracao desc";
            return DB::executa_query(CINCO_ATENDIMENTO_DB, $sql_cmd, new FAQ());
        }
    }
?>

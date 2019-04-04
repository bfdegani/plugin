<?php
    /**
     * Classes para consulta de motivos de contato na base do wordpress
     * Bruno Degani - 30/10/2018
     */

    require_once(CINCO_INC_PATH . '/database.php');

    class CanalContato extends VO{
        protected $nome;
        protected $descricao;

    }

    class CanaisContatoDAO {
        /**
         * consulta motivos de contato
         * to do: refatorar estrutrua da tabela para tratar motivo / submotivo separadamente
         */

        public static function getCanaisContato(){
            return self::getCanais("select nome, descricao as descricao from canais_contatos where tipo in ('ENTRADA','AMBOS') order by ordem_exibicao");
        }

        public static function getCanaisRetorno(){
            return self::getCanais("select nome, descricao as descricao from canais_contatos where tipo in ('SAIDA','AMBOS') order by ordem_exibicao");
        }

        private static function getCanais($sql_cmd){
            return DB::executa_query(CINCO_ATENDIMENTO_DB, $sql_cmd, new CanalContato());
        }
    }
?>
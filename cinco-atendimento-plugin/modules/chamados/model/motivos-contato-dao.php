<?php
    /**
     * Classes para consulta de motivos de contato na base do wordpress
     * Bruno Degani - 30/10/2018
     */
    require_once(CINCO_INC_PATH . '/database.php');

    class MotivoContato extends VO{
        protected $id;
        protected $descricao;
    }

    class MotivosContatoDAO {
        /**
         * consulta motivos de contato
         * to do: refatorar estrutrua da tabela para tratar motivo / submotivo separadamente
         */
        public static function getMotivosContato(){
            $sql_cmd = "select id, ";
            $sql_cmd.= "(case when (motivo is NULL or motivo = '') then categoria else concat(categoria, ': ', motivo) end) as descricao ";
            $sql_cmd.= "from motivos_contatos order by ordem_exibicao";
            return DB::executa_query(CINCO_ATENDIMENTO_DB, $sql_cmd, new MotivoContato());
        }
    }
?>
<?php
    /**
     * Classes para consulta de planos na base do wordpress
     * Bruno Degani - 30/10/2018
     */
    require_once(CINCO_INC_PATH . '/database.php');
    class Plano extends VO {
        protected $id;
        protected $nome;
        protected $mb_franquia;
        protected $preco_franquia;
        protected $preco_mb_adicional;
        protected $preco_instalacao;
        protected $preco_reposicao;
        protected $preco_inclui_impostos;
    }

    class PlanosDAO {
        /**
         * consulta planos disponíveis
         */
        public static function getPlanos(){
            $sql_cmd = "select * from planos where plano_publico = 1 and sysdate() between data_inicio_validade and ifnull(data_fim_validade, sysdate()+1) order by nome asc";
            return DB::executa_query(CINCO_ATENDIMENTO_DB, $sql_cmd, new Plano());
        }
    }
?>
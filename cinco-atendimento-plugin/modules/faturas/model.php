<?php
    /**
     * Classes para consulta de faturas
     * Bruno Degani - 02/04/2019
     */

    require_once(CINCO_INC_PATH . '/database.php');

    class DocumentoFatura extends VO {
        protected $cliente;
        protected $ciclo;
        protected $mes;
        protected $ano;
        protected $documento;
        protected $vencimento;
    }

    class DocumentoFaturaDAO {
        public static function consultaDocFatura($cliente, $ciclo, $mes, $ano, $qa=''){
            $sql_cmd = "select id_cliente as cliente, id_ciclo as ciclo, mes, ano, dt_vencimento as vencimento, documento ";
            $sql_cmd.= "from billing.documento_fatura$qa where id_cliente = ? and id_ciclo = ? and mes = ? and ano = ? ";
            error_log(CINCO_BILLING_DB . $qa);
            return DB::executa_query(CINCO_BILLING_DB . $qa, $sql_cmd, new DocumentoFatura(), 'iiii', [$cliente, $ciclo, $mes, $ano])[0];
        }
    }
?>
<?php

Class Database {

    private $conexao;

    public function __construct()
    {

        $banco = "(DESCRIPTION =
             (ADDRESS = (PROTOCOL = TCP)(HOST = 10.192.00.01)(PORT = 1521))
               (CONNECT_DATA =
                 (SERVER = DEDICATED)
                   (SERVICE_NAME = QUALITY)
                )
           )";

        $usuario = "USUARIO";
        $senha = "SENHA"; 

        //$conexao = oci_connect($usuario,$senha, $banco);
        //$conexao = oci_connect($usuario,$senha, $banco,'WE8ISO8859P1');
        $this->conexao = oci_connect($usuario,$senha, $banco,'AL32UTF8');

        /* Se retornar null/false então buscar erro na conexão */
        if (!$this->conexao) {
            $e = oci_error();
            throw new Exception($e['message']);
        }

    }

    public function __destruct()
    {
        /* Fechando Conexão e liberando espaço em memória */
        if(isset($this->conexao) ){
            oci_close($this->conexao);
        }
    }

    public function execute( $instrucaoSQL ){

        $parsed = ociparse($this->conexao, $instrucaoSQL);

        if (!$parsed) {
           $oerr = oci_error($this->conexao);
           throw new Exception("Falha ao tratar a Query: ".$oerr["message"]);
        }

        ociexecute($parsed);

        $rows = oci_num_rows($parsed);

        if ($rows == 0) return 0;
        return 1; //Foi executado com sucesso
    }

    public function select($instrucaoSQL){

        /* Parse do SQL */
        $parsed = oci_parse($this->conexao, $instrucaoSQL);

        /* Se retornar null/false então buscar erro na conexão */
        if (!$parsed) {
           $oerr = oci_error($this->conexao);
           throw new Exception("Falha ao tratar a Query: ".$oerr["message"]);
        }

        /* Buscando dados no Banco */
        oci_execute($parsed);

        /* Percorrer linhas e gerar array para retorno */
        while (($row = oci_fetch_assoc($parsed)) != false) {

            /* Carregando todas as Chaves(Nome dos Campos) do Retorno */
            $indexs = array_keys($row);

            /* Novo array em branco */
            $newArray = array();

            /* Preencher array com os nomes dos campos como chave e valor
               Modelo: Array( 'CAMPO' => 'VALOR') */
            foreach($indexs as $chaves){
                $newArray[$chaves] = $row[$chaves];
            }

            /* Add array com dados da Linha para Enviar */
            $data[] = $newArray;
        }

        /* Liberar Recurso */
        oci_free_statement($parsed);

        return $data;

    }

    public function DSLQ( $parametros){


        $select = (array_key_exists("select", $parametros) )? $parametros["select"]: null;
        $from = (array_key_exists("from", $parametros) )? $parametros["from"]: null;
        $where = (array_key_exists("where", $parametros) )? $parametros["where"]: null;
        $groupby = (array_key_exists("groupby", $parametros) )? $parametros["groupby"]: null;
        $orderby = (array_key_exists("orderby", $parametros) )? $parametros["orderby"]: null;

        if( !is_array($select) ) return null;
        if( strlen($from) < 2  ) return null;

        $sql = "select ".implode("," ,$select)." ";
        $sql .= "from $from ";

        if(is_array($where) ){

            $sql .= "where 1=1 ";
            foreach($where as $key => $value){
                $sql .=  $this->tratarWhere($key,$value);
            }
        }

        if( is_array($groupby) ) $sql .= " group by ".implode("," ,$groupby)." ";

        if( $orderby !== null ) $sql .= " order by $orderby ";

        //var_dump($sql);
        return $this->select($sql);

    }

    public function tratarWhere($field, $value){

        if( strpos($value, "LIKE") == true ) return " AND $field $value ";
        if( strpos($value, "BETWEEN") == true ) return " AND $field $value ";
        if( preg_match("/SELECT.*FROM/i", $value) ) return " AND $field IN ( $value )";
        if( is_array($value) ) return " AND $field IN( '". implode("','", $value) . "' ) ";

        switch (trim($value)){

            case "IS NULL":
                return " AND $field IS NULL ";
            case "IS NOT NULL":
                return " AND $field IS NOT NULL ";
            default:
                return " AND $field = '$value' ";
        }

    }

    public function runSqlLoader( $controlNameFile ){
        $code = "sqlldr USERID=". $this->usuario . "/" . $this->senha."@QUALITY_10";
        $code .= ", CONTROL=".$controlNameFile;

        try{

            passthru($code, $returnValue);

            if(!$returnValue) {
                return true;
            }else{
                return $returnValue;
            }
        }catch(Exception $e){
            return $e->getMessage();
        }

    }

}

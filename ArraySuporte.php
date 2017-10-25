<?php

    /**
     * @param $keyInfo chave para buscar dentro dos arrays
     * @param $arrayInfo array onde será procurado o valor
     * @return boolean true | false
    */
    public function findKeyArray($keyInfo, $arrayInfo)
    {
        // Verificar se a Chave existe
        if( array_key_exists($keyInfo, $arr) ){ return true; }

        // Caso não exista, Percorrer os SubArray para Procurar
        foreach( $arr as $key => $value ){

            if( is_array($value) ){

                // Enquando o Valor for um array, buscar a chave nos subArrays
                if( findKeyArray($keyInfo, $value ) == true ){
                    return true;
                }
            }
        }
        // Se nenhuma opão acima retornar como True, então voltar como false
        return false;
    }

    /**
     * Validar os dados de configuração passados vs configuração necessária
     * @param $arrBase array com as chaves obrigatórias para verificar
     * @param $arrComparacao array com os dados para comparação
     * @return boolean true or throw com erro encontrado
     *
     */
    public function arrayKeysEqual($arrBase, $arrComparacao)
    {
        if( is_array($arrComparacao) ){

            foreach( $arrBase as $key => $value ){

                if ( array_key_exists($key, $arrComparacao) === false ){
                    throw new Exception( "Erro no arquivo config.json ! Chave << $key >> não encontrada." );
                }

                if( is_array($value) ){
                    $this->validarPadrao($arrBase[$key], $arrComparacao[$key]);
                }

            }
        }
    }

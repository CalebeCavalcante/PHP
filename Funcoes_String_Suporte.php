<?php

/**
 * Retirar Acentos
 */
$string="olá | à | ñ | calçada | CALÇADA";
function tirarAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/",
    "/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/",
    "/(ñ)/","/(Ñ)/","/(Ç)/","/(ç)/"),explode(" ","a A e E i I o O u U n N C c"),$string);
}
echo tirarAcentos($string); // ola | a | n | calcada | CALCADA

/**
 * Regex get only letters , accented or not
 */
$string="olá | à | ñ | calçada | CALÇADA";
preg_match_all('/[a-zÀ-ÿ]+/miu', $string, $matches); 
// Se preferir cada letra em um ponteiro diferente, tirar o "+" da expressão. Ex.:(olá) array{ 0=: a, 1=> l , 2=> à }
var_dump($matches);
/*
Retorno $matches:
array(1) {
  [0]=>
  array(5) {
    [0]=>
    string(4) "olá"
    [1]=>
    string(2) "à"
    [2]=>
    string(2) "ñ"
    [3]=>
    string(8) "calçada"
    [4]=>
    string(8) "CALÇADA"
  }
}

*/

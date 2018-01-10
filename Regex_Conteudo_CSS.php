<?php
$regex = " / (\s+[\w|-]*:\s*[\w|\d|\#]+;) /";

$str_css = " .jr-color-gray-6 {  color: #979998; background-color: #979998;} ";

$retorno = " color: #979998; background-color: #979998; "; 

$regex = "/ \.jr\-color\-gray\-6\s*{(\s+[\w|-]*:\s*[\w|\d|\#]+;)+\s*} /  ";

$retorno = " .jr-color-gray-6 {  color: #979998; background-color: #979998;} ";
?>

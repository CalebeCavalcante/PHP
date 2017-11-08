<?php

  class Curl
  {

    private static $agent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';

    public function get( $url, $cookie=null, $headers_array=null, $return_page=false )
    {

      $gt = $this->set_curl_comum( $url, $cookie, $headers_array, $return_page);

      $result = curl_exec($gt);

      if(!$result){
        var_dump(curl_error($gt));
      }
      curl_close($gt);

      return $result;

    }

    public function post( $url, $parameters, $cookie=null, $headers_array=null, $return_page=false)
    {

      $pt = $this->set_curl_comum( $url, $cookie, $headers_array, $return_page );

      curl_setopt($pt, CURLOPT_POST, 1);
      curl_setopt($pt, CURLOPT_POSTFIELDS, $parameters);
      $result = curl_exec($pt);
      curl_close($pt);
      return $result;
    }

    public function get_cookie_arr($result)
    {
      $parse_str='';

      preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
      $cookies = array();
      foreach ($matches[1] as $item)
      {
        parse_str($item, $parse_str);
        $cookies = array_merge($cookies, $parse_str);
      }
      return $cookies;
    }

    private function set_curl_comum( $url, $cookie=null, $headers_array=null, $return_page=false )
    {

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // FALSE to stop cURL from verifying the peer's certificate.
      curl_setopt($ch, CURLOPT_HEADER, 1); // TRUE retorna o HEADER da requisição. Importante para pegar os SET-COOKIE
      curl_setopt($ch, CURLINFO_HEADER_OUT, 1); // HEADER_OUT = TRUE não escreve o header_response na tela do cmd na execução
      curl_setopt($ch, CURLOPT_VERBOSE, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, $return_page); // True Retornar o conteúdo da página. False apenas retorna "com ou sem sucesso"
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Aceitar redirecionamento caso o servidor direcione para outra pagina
      curl_setopt($ch, CURLOPT_USERAGENT, self::$agent);
      curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
      curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'SSLv3');
      curl_setopt($ch, CURLOPT_TIMEOUT, (60*3) );
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, (60*3) );

      if(is_array($headers_array))
      {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_array);
      }
      if(!is_null($cookie))
      {
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
      }

      return $ch;
    }

  }

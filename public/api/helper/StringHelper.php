<?php
/*
 * v1.2 2020-08-18
 * Sem dependencias.
 * Oferece metodos estaticos que facilitam tratar strings.
 */

class StringHelper
{
  /**
   * Obtem o texto convertido em caixa alta
   * @param string $string
   * @param bool $trim
   * @param string $charset
   * @return string
   */
  public static function toUpperCase($string, $trim = true, $charset = 'UTF-8')
  {
    if ($trim) $string = trim($string);
    return mb_strtoupper($string, $charset);
  }

  /**
   * Obtem o texto convertido em caixa baixa
   * @param string $string
   * @param bool $trim
   * @param string $charset
   * @return string
   */
  public static function toLowerCase($string, $trim = true, $charset = 'UTF-8')
  {
    if ($trim) $string = trim($string);
    return mb_strtolower($string, $charset);
  }

  /**
   * Obtem os numeros que aparecem na string
   * @param string $string
   * @param bool $separadosEmArray
   * @return mixed
   */
  public static function extractNumbers($string, $separadosEmArray = false) {
    $array = array();
    preg_match_all('/[0-9]+/', $string, $array);
    return ($separadosEmArray) ? $array[0] : array_reduce($array[0], function ($carry, $item) {return $carry.$item;});
  }

  /**
   * Confere se o texto inicia com os caracteres indicados
   * @param $fullString string Texto completo
   * @param $startString string Texto inicial
   * @param bool $caseSensitive Comparação sensível à diferença de caixa alta/baixa
   * @return bool
   */
  public static function startsWith ($fullString, $startString, $caseSensitive = true)
  {
    if (!$caseSensitive) {
      $fullString  = self::toLowerCase($fullString, false);
      $startString = self::toLowerCase($startString, false);
    }
    $len = strlen($startString);
    return (substr($fullString, 0, $len) === $startString);
  }

  /**
   * Confere se o texto encerra com os caracteres indicados
   * @param $fullString string Texto completo
   * @param $endString string Texto final
   * @param bool $caseSensitive Comparação insensível à diferença de caixa alta/baixa
   * @return bool
   */
  public static function endsWith($fullString, $endString, $caseSensitive = true)
  {
    if (!$caseSensitive) {
      $fullString  = self::toLowerCase($fullString, false);
      $endString = self::toLowerCase($endString, false);
    }
    $len = strlen($endString);
    if ($len == 0) return true;
    return (substr($fullString, -$len) === $endString);
  }
}

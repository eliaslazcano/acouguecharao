<?php
/*
 * Functions to manipulate arrays like JavaScript
 * Author: Elias Lazcano Castro Neto
 * Version: 2020-09-12
 */

class ArrayHelper
{
  public $array = [];

  /**
   * ArrayHelper constructor.
   * @param array $array
   */
  public function __construct(array $array)
  {
    $this->array = $array;
  }
  public function __call($name, $arguments)
  {
    return self::$name($this->array, $arguments[0]);
  }

  public static function filter($array, $function)
  {
    return array_filter($array, $function);
  }
  public static function map($array, $function)
  {
    return array_map($function, $array);
  }
  public static function find($array, $function)
  {
    $filtered = self::filter($array, $function);
    $filtered = array_values($filtered);
    if (count($filtered)) return $filtered[0];
    else return null;
  }

  /**
   * Converte os valores String de uma matriz em numéricos, ou apenas das colunas citadas.
   * @param array<array> $matrix
   * @param array<string> $columns
   * @return array<array>
   */
  public static function matrixStringToNumber($matrix, $columns = null)
  {
    foreach ($matrix as $line => $lineValue) {
      foreach ($lineValue as $column => $columnValue) {
        if ($columns === null || ($columns !== null && in_array($column, $columns))) {
          if (is_numeric($columnValue)) $matrix[$line][$column] = $columnValue + 0;
        }
      }
    }
    return $matrix;
  }

  /**
   * Converte os valores Booleanos de uma matriz em numéricos, ou apenas das colunas citadas.
   * @param array<array> $matrix
   * @param array<string> $columns
   * @return array<array>
   */
  public static function matrixStringToBool($matrix, $columns = null)
  {
    foreach ($matrix as $line => $lineValue) {
      foreach ($lineValue as $column => $columnValue) {
        if ($columns === null || ($columns !== null && in_array($column, $columns))) {
          $matrix[$line][$column] = boolval($columnValue);
        }
      }
    }
    return $matrix;
  }
}

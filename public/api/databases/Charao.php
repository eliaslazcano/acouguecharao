<?php
require_once __DIR__.'/../helper/HttpHelper.php';
require_once __DIR__.'/../helper/ArrayHelper.php';

class Charao
{
  //==================== Configurações para conexão ====================//
  private static $_host = 'localhost';
  private static $_database = 'charao';
  private static $_usuario = 'root';
  private static $_senha = '';
  private static $_charset = 'utf8';
  private static $_timezone = '-04:00';
  //====================================================================//

  private static function throwError($info) {
    HttpHelper::erroJson(500, 'FALHA NA BASE DE DADOS', 0, $info);
  }

  /**
   * Obtém uma conexão com o banco através de uma instancia do PDO
   * @return PDO - Conexão PDO
   */
  public static function getConexao() {
    try {
      $conexao = new PDO('mysql:host='.self::$_host.';dbname='.self::$_database.(self::$_charset ? ';charset='.self::$_charset : ''), self::$_usuario, self::$_senha);
      if (self::$_timezone) $conexao->exec("SET time_zone='".self::$_timezone."';");
      return $conexao;
    } catch (PDOException $e) {
      self::throwError($e->getMessage());
    }
  }

  /**
   * Executa uma query utilizando PDO. Retornando um Statement pronto para o fetch.
   * @param string $query SQL Query.
   * @param array $bindParams Array[':param' => $valor] para preparar na query.
   * @param PDO|null $conn Conexão PDO, se não houver, uma nova será criada.
   * @return PDOStatement Statement já executado, pronto para o fetch.
   */
  public static function executedStatement($query, array $bindParams = [], PDO $conn = null)
  {
    $statement = self::preparedStatement($query, $bindParams, $conn);
    if (!$statement->execute()) HttpHelper::erroJson(500, 'Falha na base de dados', 1, array(
      'message' => $statement->errorInfo(),
      'bindParams' => $bindParams,
    ));
    return $statement;
  }

  /**
   * Prepara um Statement do PDO, pronto para a execução.
   * @param string $query SQL Query.
   * @param array $bindParams Array[':param' => $valor] para preparar na query.
   * @param PDO|null $conn Conexão PDO, se não houver, uma nova será criada.
   * @return PDOStatement Statement pronto para ser executado.
   */
  public static function preparedStatement($query, array $bindParams = [], PDO $conn = null)
  {
    if (!$conn) $conn = self::getConexao();
    $statement = $conn->prepare($query);
    foreach ($bindParams as $key => $value) {
      $statement->bindValue($key, $value);
    }
    return $statement;
  }

  /**
   * Realiza uma consulta utilizando o PDO de forma simplificada. Com adaptação correta do tipo do dado e proteção bind para SqlInjection.
   * @param string $query A query da consulta.
   * @param PDO|null $conn Conexão PDO, se não houver, uma nova será criada.
   * @param array $bindParams Array[':param' => $valor] para preparar na query.
   * @param array<string> $numberColumns Nome das colunas numéricas. Só serve caso fetchStyle seja FETCH_ASSOC.
   * @param array<string> $booleanColumns Nome das colunas boleanas. Só serve caso fetchStyle seja FETCH_ASSOC.
   * @param int $fetchStyle PDO::FETCH_[style].
   * @return array<array<string>>|null Retorna o array[linha][coluna]. Null em caso de falha.
   */
  public static function fastQuery($query, PDO $conn = null, $bindParams = [], $numberColumns = [], $booleanColumns = [], $fetchStyle = PDO::FETCH_ASSOC)
  {
    $statement = self::preparedStatement($query, $bindParams, $conn);
    if (!$statement->execute()) {
      header('Error-Info', json_encode($statement->errorInfo()));
      return null;
    }
    $rows = $statement->fetchAll($fetchStyle);
    if ($fetchStyle === PDO::FETCH_ASSOC) {
      if (count($numberColumns) > 0) $rows = ArrayHelper::matrixStringToNumber($rows, $numberColumns);
      if (count($booleanColumns) > 0) $rows = ArrayHelper::matrixStringToBool($rows, $booleanColumns);
    }
    return $rows;
  }

  /**
   * Obtem o horário do banco de dados em datetime (Y-m-d H:i:s).
   * @param PDO $conn - Conexão PDO.
   * @return string|false - Datetime. False em caso de erro.
   */
  public static function getCurrentTimestamp($conn = null) {
    if (!$conn) $conn = self::getConexao();
    $statement = $conn->prepare('SELECT current_timestamp AS tempo');
    if (!$statement->execute()) return false;
    $time = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$time) return false;
    return $time['tempo'];
  }
}
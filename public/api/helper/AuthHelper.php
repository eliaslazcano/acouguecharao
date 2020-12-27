<?php
require_once __DIR__.'/../databases/Charao.php';
require_once __DIR__.'/../helper/JwtHelper.php';
require_once __DIR__.'/../helper/HttpHelper.php';
require_once __DIR__.'/../Config.php';

class AuthHelper
{
  /**
   * Registra uma sessão de login. Retorna o JSON WEB TOKEN da sessão.
   * @param int $userId - Nome de usuario.
   * @param PDO|null $conn - Conexão PDO. Se não fornecer, uma nova será criada.
   * @param bool $silent - Não emitir erro HTTP caso falhe.
   * @return string|false - String JSON WEB TOKEN. False em caso de falha (modo silent).
   */
  public static function newSession($userId, $conn = null, $silent = false) {
    if ($conn === null) $conn = Charao::getConexao();
    $statement = Charao::preparedStatement('SELECT id, username, nome, apelido, cargo FROM usuarios WHERE id = :id', [':id' => strval($userId)], $conn);
    if (!$statement->execute()) {
      if (!$silent) HttpHelper::erroJson(500, 'Falha na base de dados', 1, $statement->errorInfo());
      else return false;
    }
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
      if (!$silent) HttpHelper::erroJson(500, 'Usuário não encontrado', 1, $statement->errorInfo());
      else return false;
    }
    $secret = md5(uniqid(rand(), true), false);
    $date = Charao::getCurrentTimestamp($conn);
    $token = new JwtHelper($secret, [
      'date_login' => $date,
      'id' => intval($user['id']),
      'username' => $user['username'],
      'name' => $user['nome'],
      'shortname' => $user['apelido'],
      'occupation' => $user['cargo'],
    ]);

    $statement = Charao::preparedStatement('INSERT INTO sessoes (usuario, segredo, ip, datahora) VALUES (:username, :secret, :ip, :datahora)', [':username' => $user['username'], ':secret' => $secret, ':ip' => HttpHelper::obterIp(), ':datahora' => $date], $conn);
    if (!$statement->execute()) {
      if (!$silent) HttpHelper::erroJson(500, 'Falha na base de dados', 2, $statement->errorInfo());
      else return false;
    }
    return $token->getToken();
  }

  /**
   * Verifica se a sessão ainda pode ser utilizada.
   * @param string|null $token - String JSON WEB TOKEN. Se não fornecer será buscada no header Authorization.
   * @param PDO|null $conn - Conexão PDO. Se não fornecer, uma nova será criada.
   * @param bool $onlyLast - Somente aprovar se a sessão for a mais recente.
   * @param bool $silent - Não emitir erro HTTP caso falhe.
   * @return bool|null - True = autentico, False = não autentico, NULL = erro
   */
  public static function validateSession($token = null, $conn = null, $onlyLast = true, $silent = false) {
    //Obtem os dados necessários para validar
    if (!$token) $token = HttpHelper::obterCabecalho('Authorization');
    if (!$token) {
      if (!$silent) HttpHelper::erroJson(410, "Token não identificado", 1, array('token' => $token));
      else return null;
    }
    $payload = JwtHelper::getData($token);
    if (!property_exists($payload, 'id') || !property_exists($payload, 'date_login')) {
      if (!$silent) HttpHelper::erroJson(410, 'Faltam dados no seu token de acesso', 2);
      else return null;
    }
    if ($conn === null) $conn = Charao::getConexao();

    //Valida o token apresentado
    $statement = Charao::preparedStatement('SELECT segredo FROM sessoes WHERE usuario = :usuario AND datahora = :datahora LIMIT 1', [':usuario' => strval($payload->id), ':datahora' => $payload->date_login], $conn);
    if (!$statement->execute()) {
      if (!$silent) HttpHelper::erroJson(500, 'Falha na base de dados', 3, $statement->errorInfo());
      else return false;
    }
    $session = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$session) {
      if (!$silent) HttpHelper::erroJson(410, 'O sistema não encontrou sua sessão de acesso', 4);
      else return null;
    }
    $successValidate = JwtHelper::tokenValidate($token, $session['segredo']);
    if (!$successValidate) {
      if (!$silent) HttpHelper::erroJson(410, 'Acesso negado', 5);
      else return null;
    }

    //Valida o tempo da sessão
    try {
      $sessionTime = new DateTime($payload->date_login);
      $sessionTimestamp = $sessionTime->getTimestamp();
      $timeLimit = $sessionTimestamp + (Config::$sessionLifeTime * 60);
      $currentTime = Charao::getCurrentTimestamp($conn);
      $currentTimestamp = new DateTime($currentTime);
      $currentTimestamp = $currentTimestamp->getTimestamp();
      $remainTime = $timeLimit - $currentTimestamp;
      if ($remainTime <= 0) {
        if (!$silent) HttpHelper::erroJson(410, 'O tempo da sua sessão expirou', 6, [
          'seconds_past_timestamp' => $remainTime,
          'current_timestamp' => $currentTimestamp,
          'current_time' => $currentTime,
          'session_timestamp' => $sessionTimestamp,
          'session_time' => $sessionTime->format('Y-m-d H:i:s'),
        ]);
        else return null;
      }
    } catch (Exception $e) {
      if (!$silent) HttpHelper::erroJson(410, 'Falha na validação do login', 7, ['msg' => 'O DateTime do PHP falhou', 'exception' => $e]);
      else return null;
    }

    //Valida se é o token mais recente deste usuário
    if ($onlyLast) {
      $statement = Charao::preparedStatement('SELECT segredo FROM sessoes WHERE usuario = :usuario ORDER BY id DESC LIMIT 1', [':usuario' => strval($payload->id)]);
      if (!$statement->execute()) {
        if (!$silent) HttpHelper::erroJson(500, 'Falha na base de dados', 8, $statement->errorInfo());
        else return false;
      }
      $session = $statement->fetch(PDO::FETCH_ASSOC);
      if (!$session) {
        if (!$silent) HttpHelper::erroJson(410, 'O sistema não encontrou sua sessão de acesso', 9);
        else return null;
      }
      $successValidate = JwtHelper::tokenValidate($token, $session['segredo']);
      if (!$successValidate) {
        if (!$silent) HttpHelper::erroJson(410, 'Sua conta possui um login mais recente', 10);
        else return null;
      }
    }
  }
}
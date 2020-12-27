<?php
/**
 * Valida se a sessão de login ainda pode ser utilizada.
 * all = considera apto uma sessõa que não é a mais recente emitida.
 * newtoken = após a validação, uma nova sessão será registrada e seu token será retornado na resposta.
 * POST
 */

require_once __DIR__.'/../helper/HttpHelper.php';
require_once __DIR__.'/../helper/AuthHelper.php';
require_once __DIR__.'/../databases/Charao.php';

HttpHelper::validarPost();

$token = HttpHelper::obterCabecalho('Authorization');
$all = HttpHelper::obterParametro('all');
$newToken = HttpHelper::obterParametro('newtoken');

$conn = Charao::getConexao();
AuthHelper::validateSession($token, $conn, !$all);

if ($newToken) {
  $payload = JwtHelper::getData($token);
  $newToken = AuthHelper::newSession($payload->id, $conn);
  HttpHelper::emitirJson($newToken);
} else {
  HttpHelper::emitirJson($token);
}
<?php
/**
 * Realiza o login do usuário, através do nome de usuário e senha.
 * Retorna o JSON WEB TOKEN da sessão gerada.
 * POST
 */

require_once __DIR__.'/../helper/HttpHelper.php';
require_once __DIR__.'/../helper/AuthHelper.php';
require_once __DIR__.'/../helper/StringHelper.php';
require_once __DIR__.'/../databases/Charao.php';

HttpHelper::validarPost();
$username = HttpHelper::validarParametro('username');
$password = HttpHelper::validarParametro('password');

$username = StringHelper::toUpperCase($username);

$conn = Charao::getConexao();
$statement = Charao::executedStatement('SELECT id, senha, ativo FROM usuarios WHERE username = :username', [':username' => $username], $conn);
$conta = $statement->fetch(PDO::FETCH_ASSOC);
if (!$conta) HttpHelper::erroJson(400, 'Não há conta com este nome de usuário', 1, 'Usuário não encontrado no banco de dados');
//if ($conta['senha'] === null) HttpHelper::erroJson(400, 'Senha inválida', 2, 'Não realizou o primeiro acesso');
if ($conta['senha'] !== md5($password)) HttpHelper::erroJson(400, 'Senha inválida', 3);
if ($conta['ativo'] !== '1') HttpHelper::erroJson(400, 'A conta está desativada', 4);

$token = AuthHelper::newSession($conta['id'], $conn);
HttpHelper::emitirJson($token);
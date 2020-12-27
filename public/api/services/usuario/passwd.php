<?php
/**
 * POST => Altera a senha.
 * PUT  => Cria a senha, para usuários de primeiro acesso.
 * DELETE => Realiza o reset de senha.
 */

require_once __DIR__ . '/../../helper/HttpHelper.php';
require_once __DIR__ . '/../../helper/StringHelper.php';
require_once __DIR__ . '/../../helper/EmailHelper.php';
require_once __DIR__ . '/../../helper/AuthHelper.php';
require_once __DIR__ . '/../../configs/EmailAcougue.php';
require_once __DIR__ . '/../../databases/Charao.php';

HttpHelper::validarMetodos(['POST', 'PUT', 'DELETE']);

$conn = Charao::getConexao();

if (HttpHelper::isPost()) {
  $token = HttpHelper::obterCabecalho('Authorization');
  $payload = JwtHelper::getData($token);
  AuthHelper::validateSession($token, $conn);

  $senhaAntiga  = HttpHelper::validarParametro('old');
  $senhaNova    = HttpHelper::validarParametro('new');
  $semEmail     = HttpHelper::obterParametro('noemail');

  //Validações
  $finder = strpos($senhaNova, ' ');
  if ($finder !== false) HttpHelper::erroJson(400, "A senha não pode conter espaços");
  $statement = Charao::executedStatement('SELECT email, senha, nome FROM usuarios WHERE id = :id', [':id' => strval($payload->id)], $conn);
  $usuario = $statement->fetch(PDO::FETCH_ASSOC);
  if (!$usuario) HttpHelper::erroJson(400, 'Usuário não identificado', 0);
  if (md5($senhaAntiga) !== $usuario['senha']) HttpHelper::erroJson(400, 'A senha atual não confere', 1);
  if ($senhaAntiga === $senhaNova) HttpHelper::erroJson(400, 'Você digitou a mesma senha');

  //Atualiza a senha
  Charao::executedStatement('UPDATE usuarios SET senha = :senha WHERE id = :id', [':senha' => md5($senhaNova), ':id' => strval($payload->id)], $conn);

  //Notifica por email
  if ($semEmail || !$usuario['email']) HttpHelper::emitirHttp(200, true);
  $url_sistema = Config::$hospedagem_url;
  $mensagem = <<<EOT
<p>Olá Sr(a) $payload->nome,</p>
<p>Informamos que sua senha foi alterada para: $senhaNova</p>
<p>Se não foi você quem alterou a senha, <a href="$url_sistema">acesse o sistema</a> o quanto antes e troque-a para sua segurança.</p>
EOT;
  $mail = new EmailHelper(EmailAcougue::EMAIL_ADDR, EmailAcougue::EMAIL_PASS, EmailAcougue::EMAIL_NOME, EmailAcougue::SMTP_HOST);
  $mail->addDestinatario($usuario['email'], $usuario['nome']);
  $mail->enviarMensagem('Sua senha foi alterada', $mensagem, true);
}
elseif (HttpHelper::isDelete()) {
  $username = HttpHelper::validarParametro('username');

  $statement = Charao::executedStatement('SELECT id, nome, email FROM usuarios WHERE username = :username', [':username' => $username], $conn);
  $usuario = $statement->fetch(PDO::FETCH_ASSOC);
  if (!$usuario) HttpHelper::erroJson(400, "Não existe conta com este nome de usuário", 1);
  if (!$usuario['email']) HttpHelper::erroJson(400, 'Não é possível enviar uma nova senha porque o usuário não possui email', 2);

  $novaSenha = rand(100110, 988998);
  Charao::executedStatement('UPDATE usuarios SET senha = :senha WHERE id = :id', [':senha' => md5($novaSenha), ':id' => $usuario['id']], $conn);
  $url_sistema = Config::$hospedagem_url;
  $mensagem = <<<EOT
<p>Olá Sr(a) {$usuario['nome']},</p>
<p>Sua nova senha é: $novaSenha</p>
<p>Sua senha foi alterada porque foi realizado o procedimento de "esqueci minha senha". Se não foi você quem solicitou a nova senha, <a href="$url_sistema">acesse o sistema</a> o quanto antes e troque-a para sua segurança.</p>
EOT;
  $mail = new EmailHelper(EmailAcougue::EMAIL_ADDR, EmailAcougue::EMAIL_PASS, EmailAcougue::EMAIL_NOME, EmailAcougue::SMTP_HOST);
  $mail->addDestinatario($usuario['email'], $usuario['nome']);
  $mail->enviarMensagem('Sua senha foi resetada', $mensagem, true);
  HttpHelper::emitirJson($usuario['email']);
}
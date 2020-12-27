<?php

require_once __DIR__.'/../helper/HttpHelper.php';
require_once __DIR__.'/../helper/AuthHelper.php';
require_once __DIR__.'/../databases/Charao.php';

HttpHelper::validarMetodos(['GET']);
$conn = Charao::getConexao();
AuthHelper::validateSession(null, $conn);

if (HttpHelper::isGet()) {
  $vendas = Charao::fastQuery('SELECT v.id, v.datahora, v.usuario, u.nome usuario_nome, u.apelido usuario_apelido FROM vendas v INNER JOIN usuarios u ON v.usuario = u.id', $conn, [], ['id', 'usuario']);
  HttpHelper::emitirJson($vendas);
}
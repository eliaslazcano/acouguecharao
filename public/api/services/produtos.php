<?php
/**
 * GET => Obtem a listagem de produtos cadastrados.
 */

require_once __DIR__.'/../helper/HttpHelper.php';
require_once __DIR__.'/../helper/AuthHelper.php';
require_once __DIR__.'/../databases/Charao.php';

HttpHelper::validarMetodos(['GET']);
$conn = Charao::getConexao();
AuthHelper::validateSession(null, $conn);

if (HttpHelper::isGet()) {
  $produtos = Charao::fastQuery('SELECT id, nome, codigo, preco, criado_em, alterado_em FROM produtos', $conn, [], ['id', 'preco']);
  HttpHelper::emitirJson($produtos);
}
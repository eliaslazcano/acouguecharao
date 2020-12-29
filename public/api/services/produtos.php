<?php
/**
 * GET => Obtem a listagem de produtos cadastrados.
 * POST => Insere um produto. Atualiza caso informe o ID.
 * DELETE => Apaga um produto (na verdade inativa).
 */

require_once __DIR__.'/../helper/HttpHelper.php';
require_once __DIR__.'/../helper/AuthHelper.php';
require_once __DIR__.'/../databases/Charao.php';

HttpHelper::validarMetodos(['GET', 'POST', 'DELETE']);
$conn = Charao::getConexao();
AuthHelper::validateSession(null, $conn);

if (HttpHelper::isGet()) {
  $produtos = Charao::fastQuery('SELECT id, nome, codigo, preco, criado_em, alterado_em FROM produtos WHERE ativo = 1', $conn, [], ['id', 'preco']);
  HttpHelper::emitirJson($produtos);
}
elseif (HttpHelper::isPost()) {
  $id = HttpHelper::obterParametro('id');
  $nome = HttpHelper::validarParametro('nome');
  $preco = HttpHelper::validarParametro('preco');
  $codigo = HttpHelper::validarParametro('codigo');
  $queryInsert = 'INSERT INTO produtos (nome, codigo, preco) VALUES (:nome, :codigo, :preco)';
  $queryUpdate = 'UPDATE produtos SET nome = :nome, codigo = :codigo, preco = :preco WHERE id = :id';
  $bind = [':nome' => $nome, ':codigo' => $codigo, ':preco' => $preco];
  if ($id) $bind[':id'] = $id;
  Charao::executedStatement($id ? $queryUpdate : $queryInsert, $bind, $conn);
  if ($id) HttpHelper::emitirHttp(200, true);
  else HttpHelper::emitirJson(intval($conn->lastInsertId()));
}
elseif (HttpHelper::isDelete()) {
  $id = HttpHelper::validarParametro('id');
  Charao::executedStatement('UPDATE produtos SET ativo = 0 WHERE id = :id', [':id' => $id], $conn);
}
<?php
/**
 * GET  => Obtem a listagem de vendas.
 * POST => Inserir venda.
 */

require_once __DIR__.'/../helper/HttpHelper.php';
require_once __DIR__.'/../helper/AuthHelper.php';
require_once __DIR__.'/../databases/Charao.php';

HttpHelper::validarMetodos(['GET', 'POST']);
$conn = Charao::getConexao();
$token = HttpHelper::obterCabecalho('Authorization');
AuthHelper::validateSession($token, $conn);

if (HttpHelper::isGet()) {
  $id = HttpHelper::obterParametro('id');
  if ($id) {
    $vendas = Charao::fastQuery('SELECT v.id, v.datahora, v.usuario, SUM(vp.preco * vp.quantidade) valor, v.desconto, (SUM(vp.preco * vp.quantidade) - v.desconto) total, u.nome usuario_nome, u.apelido usuario_apelido FROM vendas v INNER JOIN usuarios u ON v.usuario = u.id LEFT JOIN vendas_produtos vp ON v.id = vp.venda WHERE v.id = :id GROUP BY v.id', $conn, [':id' => $id], ['id', 'usuario', 'desconto', 'valor', 'total']);
    if (count($vendas) !== 1) HttpHelper::erroJson(400, 'Venda não encontrada');
    $venda = $vendas[0];
    $venda['produtos'] = Charao::fastQuery('SELECT id, produto, quantidade, preco FROM vendas_produtos WHERE venda = :venda', $conn, [':venda' => $id], ['id', 'produto', 'quantidade', 'preco']);
    HttpHelper::emitirJson($venda);
  }

  $vendas = Charao::fastQuery('SELECT v.id, v.datahora, v.usuario, SUM(vp.preco * vp.quantidade) valor, v.desconto, (SUM(vp.preco * vp.quantidade) - v.desconto) total, u.nome usuario_nome, u.apelido usuario_apelido FROM vendas v INNER JOIN usuarios u ON v.usuario = u.id LEFT JOIN vendas_produtos vp ON v.id = vp.venda GROUP BY v.id', $conn, [], ['id', 'usuario', 'desconto', 'valor', 'total']);
  HttpHelper::emitirJson($vendas);
}
elseif (HttpHelper::isPost()) {
  $produtos = HttpHelper::validarParametro('produtos'); //[{produto: Number, quantidade: Number, preco: Number}]
  $desconto = HttpHelper::validarParametro('desconto'); //Double
  $payload = JwtHelper::getData($token);
  $autor = $payload->id;

  Charao::executedStatement('INSERT INTO vendas (usuario, desconto) VALUEs (:usuario, :desconto)', [':usuario' => strval($autor), ':desconto' => strval($desconto)], $conn);
  $vendaId = $conn->lastInsertId();

  foreach ($produtos as $produto) {
    Charao::executedStatement('INSERT INTO vendas_produtos (venda, produto, quantidade, preco) VALUES (:venda, :produto, :quantidade, :preco)', [':venda' => $vendaId, ':produto' => strval($produto['produto']), ':quantidade' => strval($produto['quantidade']), ':preco' => strval($produto['preco'])], $conn);
  }

  HttpHelper::emitirHttp(200, true);
}
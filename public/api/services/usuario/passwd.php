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

//TODO
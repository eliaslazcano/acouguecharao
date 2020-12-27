<?php
require_once __DIR__.'/../helper/HttpHelper.php';
HttpHelper::erroJson(404, 'O serviço não foi encontrado', 0);
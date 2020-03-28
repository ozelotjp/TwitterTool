<?php

use Dotenv\Dotenv;
use mpyw\Cowitter\Client;

require __DIR__ . '/vendor/autoload.php';

session_start();

$ot = (string) filter_input(INPUT_GET, 'oauth_token');
$ov = (string) filter_input(INPUT_GET, 'oauth_verifier');

if (isset($_SESSION['client']) === false || $ot === '' || $ov === '') {
  Dotenv::createImmutable(__DIR__)->load();
  $client = new Client([
    getenv('CK'),
    getenv('CS')
  ]);
  $client = $client->oauthForRequestToken('http://localhost:8000/login.php');
  $_SESSION['client'] = $client;
  header('Location: ' . $client->getAuthorizeUrl());
  exit;
} else {
  echo '<pre>';
  var_dump($_SESSION['client']->oauthForAccessToken($ov));
  echo '</pre>';
  $_SESSION = [];
  exit;
}

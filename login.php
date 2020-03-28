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
  [$ck, $cs, $at, $as] = $_SESSION['client']->oauthForAccessToken($ov)->getCredentials();
  $_SESSION = [];

  echo <<< EOM
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>
    <p>
      CK={$ck}<br>
      CS={$cs}<br>
      AT={$at}<br>
      AS={$as}
    </p>
  </body>
  </html>
  EOM;
  exit;
}

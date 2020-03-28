<?php

use Dotenv\Dotenv;
use mpyw\Cowitter\Client;

require __DIR__ . '/vendor/autoload.php';

define('CACHE_PATH', __DIR__ . '/cache.json');

if (file_exists(CACHE_PATH) === false || time() >= filectime(CACHE_PATH) + 60 || (string) filter_input(INPUT_GET, 'reload') === 'true') {
  Dotenv::createImmutable(__DIR__)->load();

  $client = new Client([
    getenv('CK'),
    getenv('CS'),
    getenv('AT'),
    getenv('AS')
  ]);
  $my_screen_name = $client->get('account/settings')->screen_name;
  $groups = [];
  $users = [];

  foreach ($client->get('friends/list', ['screen_name' => $my_screen_name, 'count' => 200, 'skip_status' => true, 'include_user_entities' => true])->users as $user) {
    $users[$user->id]['name'] = $user->name;
    $users[$user->id]['screen_name'] = $user->screen_name;
    $users[$user->id]['groups'][] = '_follow';
  }
  $groups[] = '_follow';

  foreach ($client->get('lists/list', ['screen_name' => $my_screen_name]) as $list) {
    foreach ($client->get('lists/members', ['list_id' => $list->id, 'count' => 5000, 'include_entities' => true])->users as $user) {
      $users[$user->id]['name'] = $user->name;
      $users[$user->id]['screen_name'] = $user->screen_name;
      $users[$user->id]['groups'][] = $list->name;
    }
    $groups[] = $list->name;
  }

  sort($groups);
  file_put_contents(CACHE_PATH, json_encode([$groups, $users]));
  header('Location: /');
  exit;
}

[$groups, $users] = json_decode(file_get_contents(CACHE_PATH), true);
$last_updated = date('Y/m/d h:i:s', filectime(CACHE_PATH));

$table = '';
$table .= '<thead>';
$table .= '<tr>';
$table .= '<th>name</th>';
$table .= '<th>screen_name</th>';
foreach ($groups as $group) {
  $table .= '<th class="center">' . $group . '</th>';
}
$table .= '</tr>';
$table .= '</thead>';
$table .= '<tbody>';
foreach ($users as $user) {
  $table .= '<tr>';
  $table .= '<td><a href="https://twitter.com/' . $user['screen_name'] . '">' . $user['name'] . '</a></td>';
  $table .= '<td>@' . $user['screen_name'] . '</td>';
  foreach ($groups as $group) {
    if (in_array($group, $user['groups'], true)) {
      $table .= '<td class="center bg-green">✔</td>';
    } else {
      $table .= '<td></td>';
    }
  }
  $table .= '</tr>';
}
$table .= '</tbody>';

echo <<< EOM
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TwitterTool</title>
    <link rel="stylesheet" href="/assets/themes/blue/style.css">
    <style>
      .tablesorter tbody > tr:hover > td,
      .tablesorter tbody > tr.odd:hover > td,
      .tablesorter tbody > tr.even:hover > td {
        background-color: lightgray;
      }
      .center {
        text-align: center;
      }
      .bg-green {
        background-color: greenyellow !important;
      }
    </style>
    <script type="text/javascript" src="/assets/js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="/assets/js/jquery.tablesorter.min.js"></script>
    <script>
      $(document).ready(function(){
        $("#table").tablesorter({
          theme: 'blue'
        })
      })
    </script>
  </head>
  <body>
    <main>
      <h1>
        Twitter Tool
      </h1>
      <p>
        Last Updated on {$last_updated}(UTC).
        <a href="/index.php?reload=true">Destroy and renew cache</a>.
      </p>
      <table id="table" class="tablesorter">
        {$table}
      </table>
    </main>
  </body>
</html>
EOM;

<?php
require 'd:/ORO/3DB/vendor/autoload.php';
$env = file_get_contents('d:/ORO/3DB/.env');
preg_match('/database\.default\.hostname\s*=\s*(.+)/', $env, $h);
preg_match('/database\.default\.username\s*=\s*(.+)/', $env, $u);
preg_match('/database\.default\.password\s*=\s*(.+)/', $env, $p);
preg_match('/database\.default\.database\s*=\s*(.+)/', $env, $d);
$pdo = new PDO('mysql:host='.trim($h[1]).';dbname='.trim($d[1]), trim($u[1]), trim($p[1]));
$rows = $pdo->query('SELECT id, nama_reject FROM reject_types ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r) echo $r['id'].'. '.$r['nama_reject']."\n";
echo 'Total: '.count($rows)."\n";

<?php
$db = new mysqli('localhost','root','','oroproduksi');
$r  = $db->query('SHOW COLUMNS FROM produksi_colorants');
while ($row = $r->fetch_assoc()) echo $row['Field'] . "\n";

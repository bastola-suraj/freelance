<?php
session_start();
require_once 'db-conn.php';
if(isset($_REQUEST['term'])){
    $stmt = $pdo->prepare('SELECT name FROM institute WHERE name LIKE :prefix');
    $stmt->execute(array( ':prefix' => $_REQUEST['term']."%"));
}
else{
    $stmt = $pdo->prepare('SELECT name FROM institute');
    $stmt->execute();
}
$retval = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
  $retval[] = $row['name'];
}

echo(json_encode($retval, JSON_PRETTY_PRINT));
?>
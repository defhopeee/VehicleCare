<?php
require_once __DIR__.'/config/db.php';
header('Content-Type: application/json');
$f = (int)($_GET['fault'] ?? 0);
$g = (int)($_GET['garage'] ?? 0);
$sql = "SELECT m.mechanic_id, m.full_name, f.name AS specialty, ga.name AS garage_name
        FROM mechanics m
        LEFT JOIN faults f  ON f.fault_id = m.specialty_fault_id
        LEFT JOIN garages ga ON ga.garage_id = m.garage_id
        WHERE m.is_available = 1";
$params = [];
if($f){ $sql.=" AND m.specialty_fault_id=?"; $params[]=$f; }
if($g){ $sql.=" AND m.garage_id=?"; $params[]=$g; }
$stmt=$pdo->prepare($sql); $stmt->execute($params);
echo json_encode($stmt->fetchAll());
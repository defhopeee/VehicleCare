<?php
require_once __DIR__.'/config/db.php';
header('Content-Type: application/json');
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'start') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    if (!$name) { echo json_encode(['error'=>'Name required']); exit; }
    $pdo->prepare("INSERT INTO chat_sessions (client_name, client_email) VALUES (?,?)")->execute([$name,$email]);
    echo json_encode(['session_id' => $pdo->lastInsertId(), 'name' => $name]);
    exit;
}
if ($action === 'send') {
    $sid = (int)($_POST['session_id'] ?? 0);
    $msg = trim($_POST['message'] ?? '');
    if (!$sid || !$msg) { echo json_encode(['error'=>'Missing data']); exit; }
    $pdo->prepare("INSERT INTO chat_messages (session_id, sender, message) VALUES (?, 'client', ?)")->execute([$sid,$msg]);
    // A client messaging again means the conversation is active, reopen it
    // if an admin had previously closed it, so it doesn't get missed.
    $pdo->prepare("UPDATE chat_sessions SET status='open' WHERE session_id=?")->execute([$sid]);
    echo json_encode(['ok'=>true]);
    exit;
}
if ($action === 'fetch') {
    $sid = (int)($_GET['session_id'] ?? 0);
    $after = (int)($_GET['after'] ?? 0);
    if (!$sid) { echo json_encode([]); exit; }
    $stmt = $pdo->prepare("SELECT message_id, sender, message, created_at FROM chat_messages
                           WHERE session_id=? AND message_id>? ORDER BY message_id ASC");
    $stmt->execute([$sid,$after]);
    echo json_encode($stmt->fetchAll());
    exit;
}
echo json_encode(['error'=>'Invalid action']);
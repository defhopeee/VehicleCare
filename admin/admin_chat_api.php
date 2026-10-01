<?php
// ===== ADMIN CHAT API =====
error_reporting(E_ALL);
ini_set('display_errors', 0);           // don't leak HTML errors into JSON
header('Content-Type: application/json');

$ROOT = dirname(__DIR__);
require_once $ROOT . '/config/db.php';

// Manually start session and check admin — do NOT redirect, return JSON instead
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['error' => 'Not authenticated. Please log in again.']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ---------- SEND ----------
if ($action === 'send') {
    $sid = (int)($_POST['session_id'] ?? 0);
    $msg = trim($_POST['message'] ?? '');
    if (!$sid || $msg === '') {
        echo json_encode(['error' => 'Missing session_id or message']);
        exit;
    }
    try {
        $stmt = $pdo->prepare("INSERT INTO chat_messages (session_id, sender, message, is_read) VALUES (?, 'admin', ?, 1)");
        $stmt->execute([$sid, $msg]);
        echo json_encode(['ok' => true, 'message_id' => $pdo->lastInsertId()]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'DB: ' . $e->getMessage()]);
    }
    exit;
}

// ---------- FETCH ----------
if ($action === 'fetch') {
    $sid   = (int)($_GET['session_id'] ?? 0);
    $after = (int)($_GET['after'] ?? 0);
    if (!$sid) { echo json_encode([]); exit; }

    try {
        $stmt = $pdo->prepare("SELECT message_id, sender, message, created_at
                               FROM chat_messages
                               WHERE session_id = ? AND message_id > ?
                               ORDER BY message_id ASC");
        $stmt->execute([$sid, $after]);
        echo json_encode($stmt->fetchAll());
    } catch (Exception $e) {
        echo json_encode(['error' => 'DB: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['error' => 'Invalid action: ' . htmlspecialchars($action)]);
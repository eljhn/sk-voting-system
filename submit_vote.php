<?php
session_start();
include 'includes/db.php';

// Ensure voter is logged in
if (!isset($_SESSION['voter_id'])) {
    header("Location: voter_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voter_id = $_SESSION['voter_id'];
    $candidate_id = intval($_POST['candidate_id']); // <- only store candidate_id

    // Check if the voter already voted
    $stmtCheck = $conn->prepare("SELECT * FROM votes WHERE voter_id = ?");
    $stmtCheck->bind_param("i", $voter_id);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();

    if ($result->num_rows > 0) {
        header("Location: vote_dashboard.php?error=You have already voted");
        exit();
    }

    // Insert vote
    $stmt = $conn->prepare("INSERT INTO votes (voter_id, candidate_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $voter_id, $candidate_id);
    if ($stmt->execute()) {
        header("Location: vote_dashboard.php?success=Your vote has been recorded");
        exit();
    } else {
        header("Location: vote_dashboard.php?error=Failed to record vote");
        exit();
    }
}
?>

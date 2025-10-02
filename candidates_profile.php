<?php  
session_start();
include 'includes/db.php';
include 'includes/auth.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Get candidate_id from URL
if (!isset($_GET['candidate_id']) || !is_numeric($_GET['candidate_id'])) {
    header("Location: admin_dashboard.php?error=Invalid candidate ID");
    exit();
}

$candidateId = intval($_GET['candidate_id']);

// Try to fetch candidate from new_candidates first
$stmt = $conn->prepare("SELECT * FROM new_candidates WHERE candidate_id = ?");
$stmt->bind_param("i", $candidateId);
$stmt->execute();
$result = $stmt->get_result();
$candidate = $result->fetch_assoc();
$stmt->close();

// If not found in new_candidates, check candidates table
if (!$candidate) {
    $stmt = $conn->prepare("SELECT * FROM candidates WHERE candidate_id = ?");
    $stmt->bind_param("i", $candidateId);
    $stmt->execute();
    $result = $stmt->get_result();
    $candidate = $result->fetch_assoc();
    $stmt->close();
}

if (!$candidate) {
    header("Location: admin_dashboard.php?error=Candidate not found");
    exit();
}

include 'includes/sidebar.php';
?>

<main class="container mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="bg-white shadow-xl rounded-2xl p-6 sm:p-10 max-w-3xl mx-auto">
        <!-- Candidate Icon -->
        <div class="flex flex-col items-center mb-6">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-full w-24 h-24 flex items-center justify-center text-4xl font-bold shadow-md">
                👤
            </div>
            <h2 class="mt-4 text-2xl sm:text-3xl font-semibold text-gray-800 text-center">
                <?= htmlspecialchars($candidate['full_name']) ?>
            </h2>
            <p class="mt-2 text-gray-500 text-center">
                Candidate
            </p>
        </div>

        <!-- Candidate Information -->
        <div class="mt-8">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-700 mb-6 border-b pb-2">Candidate Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-600 text-sm sm:text-base">
                <p><span class="font-medium text-gray-800">Nickname:</span> <?= htmlspecialchars($candidate['nickname']) ?></p>
                <p><span class="font-medium text-gray-800">Birthdate:</span> <?= htmlspecialchars($candidate['birthdate']) ?></p>
                <p><span class="font-medium text-gray-800">Age:</span> <?= htmlspecialchars($candidate['age']) ?></p>
                <p><span class="font-medium text-gray-800">Birthplace:</span> <?= htmlspecialchars($candidate['birthplace']) ?></p>
                <p><span class="font-medium text-gray-800">Address:</span> <?= htmlspecialchars($candidate['address']) ?></p>
                <p><span class="font-medium text-gray-800">Barangay:</span> <?= htmlspecialchars($candidate['barangay']) ?></p>
                <p><span class="font-medium text-gray-800">Municipality:</span> <?= htmlspecialchars($candidate['municipality']) ?></p>
                <p><span class="font-medium text-gray-800">Province:</span> <?= htmlspecialchars($candidate['province']) ?></p>
                <p><span class="font-medium text-gray-800">Phone:</span> <?= htmlspecialchars($candidate['phone']) ?></p>
                <p><span class="font-medium text-gray-800">Email:</span> <?= htmlspecialchars($candidate['email']) ?></p>
                <p><span class="font-medium text-gray-800">Registered Voter:</span> <?= htmlspecialchars($candidate['registered_voter']) ?></p>
                <p><span class="font-medium text-gray-800">Public Office:</span> <?= htmlspecialchars($candidate['public_office']) ?></p>
                <p><span class="font-medium text-gray-800">Position:</span> <?= htmlspecialchars($candidate['position']) ?></p>
                <p><span class="font-medium text-gray-800">Age Requirement:</span> <?= htmlspecialchars($candidate['age_require']) ?></p>
                <p><span class="font-medium text-gray-800">Current School:</span> <?= htmlspecialchars($candidate['current_school']) ?></p>
                <p><span class="font-medium text-gray-800">Year Level:</span> <?= htmlspecialchars($candidate['year_level']) ?></p>
                <p><span class="font-medium text-gray-800">Occupation:</span> <?= htmlspecialchars($candidate['occupation']) ?></p>
                <p><span class="font-medium text-gray-800">Occupation Position:</span> <?= htmlspecialchars($candidate['occupation_position']) ?></p>
                <p class="col-span-1 sm:col-span-2"><span class="font-medium text-gray-800">Registered On:</span> <?= htmlspecialchars($candidate['created_at']) ?></p>
            </div>
        </div>
    </div>
</main>

<?php     
// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'includes/db.php';

// Detect current page
$currentPage = basename($_SERVER['PHP_SELF']);

// Check if voter is logged in
$isVoterLoggedIn = isset($_SESSION['voter_id']);
$voterName = $isVoterLoggedIn ? $_SESSION['voter_name'] : "";

// Check if admin is logged in
$isAdminLoggedIn = isset($_SESSION['admin_logged_in']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SK Voting System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

<!-- Sidebar -->
<?php if ($isVoterLoggedIn || $isAdminLoggedIn): ?>
<aside class="bg-black text-white w-64 min-h-screen p-4 flex flex-col fixed">
    <!-- Top section: Logo + Title -->
    <div>
        <div class="flex items-center space-x-3 mb-8">
            <img src="assets/images/sklogo.png" alt="SK Logo" class="h-10 w-10 object-contain">
            <span class="font-bold text-lg">SK Voting</span>
        </div>

        <!-- If voter is logged in -->
        <?php if ($isVoterLoggedIn): ?>
            <div class="flex flex-col items-center mb-8">
                <div class="bg-white text-blue-600 rounded-full w-12 h-12 flex items-center justify-center text-xl font-bold">
                    👤
                </div>
                <p class="mt-2 font-semibold"><?= htmlspecialchars($voterName) ?></p>
                <p class="text-sm text-gray-400">Voter</p>
            </div>

            <nav class="flex flex-col space-y-3">
                <a href="profile.php" class="hover:bg-gray-800 px-3 py-2 rounded">👤 Profile</a>
                <a href="vote_dashboard.php" class="hover:bg-gray-800 px-3 py-2 rounded">🗳 Vote Dashboard</a>
                <a href="privacy.php" class="hover:bg-gray-800 px-3 py-2 rounded">🔒 Privacy</a>
                <a href="logout.php" class="hover:bg-gray-800 px-3 py-2 rounded">🚪 Logout</a>
            </nav>

        <!-- If admin is logged in -->
        <?php elseif ($isAdminLoggedIn): ?>
            <div class="flex flex-col items-center mb-8">
                <div class="bg-white text-blue-600 rounded-full w-12 h-12 flex items-center justify-center text-xl font-bold">
                    👤
                </div>
                <p class="mt-2 font-semibold">Administrator</p>
            </div>

            <nav class="flex flex-col space-y-3">
                <a href="admin_dashboard.php" class="hover:bg-gray-800 px-3 py-2 rounded">📊 Admin Dashboard</a>
                <a href="vote_dashboard.php" class="hover:bg-gray-800 px-3 py-2 rounded">👥 Candidates</a>
                <a href="logout.php" class="hover:bg-gray-800 px-3 py-2 rounded">🚪 Logout</a>
            </nav>
        <?php endif; ?>
    </div>
</aside>
<?php endif; ?>

<!-- Main content wrapper (shifted right for sidebar if logged in) -->
<main class="<?= ($isVoterLoggedIn || $isAdminLoggedIn) ? 'ml-64' : '' ?> p-6 flex-1">

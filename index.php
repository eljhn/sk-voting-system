<?php  
session_start();
include 'includes/db.php';
include 'includes/auth.php';

$message = "";

// Default admin credentials
$adminUsername = "admin";
$adminPassword = "12345";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username_or_email']);
    $password = trim($_POST['password']);

    if (empty($usernameOrEmail) || empty($password)) {
        $message = "Both fields are required!";
    } else {
        // Check if admin
        if ($usernameOrEmail === $adminUsername && $password === $adminPassword) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $adminUsername;
            header("Location: admin_dashboard.php?success=Welcome Admin!");
            exit();
        }

        // Otherwise, treat as voter login
        $result = loginVoter($usernameOrEmail, $password, $conn);
        if ($result === true) {
            header("Location: vote_dashboard.php?success=Welcome Voter!");
            exit();
        } else {
            $message = $result;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SK Voting System</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

<?php include 'includes/sidebar.php'; ?>

<!-- Floating Notice Banner -->
<?php if (isset($_GET['success'])): ?>
  <div id="noticeBanner" class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow z-50">
      <?= htmlspecialchars($_GET['success']) ?>
  </div>
<?php elseif (!empty($message)): ?>
  <div id="noticeBanner" class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow z-50">
      <?= htmlspecialchars($message) ?>
  </div>
<?php endif; ?>

<main class="flex-grow">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="text-center mt-12 mb-8">
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-2">Sangguniang Kabataan (SK)</h1>
            <p class="text-xl sm:text-2xl text-blue-600 italic">SK Voting System</p>
        </div>

        <!-- Welcome Section -->
        <div class="text-center mb-12">
            <p class="text-lg text-gray-700">Please login below to participate in the elections.</p>
        </div>

        <!-- Forms + Image -->
        <div class="flex flex-col md:flex-row items-center justify-center gap-8 mb-12">

            <!-- Combined Login Card -->
            <div class="bg-white rounded-xl p-8 w-full md:w-1/2">

                <!-- Login Heading -->
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Login</h2>
                </div>

                <!-- Login Form -->
                <form method="POST" class="space-y-4">
                    <input type="text" name="username_or_email" placeholder="Email or Username" required class="w-full border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <input type="password" name="password" placeholder="Password" required class="w-full border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <button type="submit" class="w-full bg-black text-white py-2 rounded hover:bg-gray-800 transition">Login</button>
                </form>

            </div>

            <!-- Image Column -->
            <div class="w-full md:w-1/2">
                <div class="relative">
                    <img src="assets/images/sk-bg.webp" alt="SK Background" class="w-full h-full object-cover rounded-xl shadow-lg">
                </div>
            </div>

        </div>

        <!-- Registration Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
            <div class="bg-gray-700 rounded-xl shadow-lg p-6 flex flex-col justify-between">
                <h3 class="text-xl font-bold text-white mb-2">Voter Registration</h3>
                <p class="text-gray-300 mb-4">Register here if you are eligible to vote in the SK elections.</p>
                <a href="register.php" class="bg-gray-200 text-gray-800 px-3 py-1 rounded self-start hover:bg-gray-300 transition">Register</a>
            </div>
            <div class="bg-gray-800 rounded-xl shadow-lg p-6 flex flex-col justify-between">
                <h3 class="text-xl font-bold text-white mb-2">Candidate Registration</h3>
                <p class="text-gray-300 mb-4">Run for a position in the SK by registering as a candidate.</p>
                <a href="candidates_register.php" class="bg-gray-200 text-gray-800 px-3 py-1 rounded self-start hover:bg-gray-300 transition">Register</a>
            </div>
        </div>

        <!-- Info Cards --> <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-16"> <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition"> <h3 class="text-lg font-bold mb-2 text-gray-900">Secure & Fair</h3> <p class="text-gray-700 text-sm">Votes are cast safely, preventing multiple voting and keeping voter data confidential.</p> </div> <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition"> <h3 class="text-lg font-bold mb-2 text-gray-900">User-Friendly</h3> <p class="text-gray-700 text-sm">Voters and administrators can easily navigate the system efficiently.</p> </div> <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition"> <h3 class="text-lg font-bold mb-2 text-gray-900">Real-Time Results</h3> <p class="text-gray-700 text-sm">Admins can view results instantly with graphical reports for insights.</p> </div> <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition"> <h3 class="text-lg font-bold mb-2 text-gray-900">Project Members</h3> <ul class="text-gray-700 text-sm space-y-1"> <li>Naifa</li> <li>Cherry Ann</li> <li>Jherame</li> <li>Ethell Ann</li> <li>Benedict Ralph</li> </ul> </div> </div> </div>

    </div>
</main>

<script>
// Auto-hide notice banner after 2s
document.addEventListener("DOMContentLoaded", () => {
    const banner = document.getElementById("noticeBanner");
    if (banner) {
        setTimeout(() => {
            banner.classList.add("transition", "duration-500", "opacity-0");
            setTimeout(() => banner.remove(), 500);
        }, 2000);
    }
});
</script>

</body>
</html>

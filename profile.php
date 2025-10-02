<?php  
session_start();
include 'includes/db.php';

// Admin or voter can view profiles
if (isset($_GET['voter_id']) && is_numeric($_GET['voter_id'])) {
    // Admin is viewing voter profile
    $voterId = intval($_GET['voter_id']);
} else {
    // Normal voter profile
    if (!isset($_SESSION['voter_id'])) {
        header("Location: index.php");
        exit();
    }
    $voterId = $_SESSION['voter_id'];
}

// Fetch voter details
$sql = "SELECT * FROM voters WHERE voter_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $voterId);
$stmt->execute();
$result = $stmt->get_result();
$voter = $result->fetch_assoc();

if (!$voter) {
    echo "Voter not found.";
    exit();
}

// Include header (sidebar + layout wrapper)
include 'includes/sidebar.php';
?>

<!-- Profile Container -->
<div class="flex justify-center items-start py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-xl rounded-2xl p-6 sm:p-10 w-full max-w-2xl">
        <!-- Profile Header -->
        <div class="flex flex-col items-center">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-full w-24 h-24 flex items-center justify-center text-4xl font-bold shadow-md">
                👤
            </div>
            <h2 class="mt-4 text-2xl sm:text-3xl font-semibold text-gray-800 text-center">
                <?= htmlspecialchars($voter['fullname']) ?>
            </h2>
            <p class="text-gray-500 text-sm sm:text-base">Voter</p>
        </div>

        <!-- Profile Information -->
        <div class="mt-8">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-700 mb-6 border-b pb-2">Profile Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-600 text-sm sm:text-base">
                <p><span class="font-medium text-gray-800">Email:</span> <?= htmlspecialchars($voter['email']) ?></p>
                <p><span class="font-medium text-gray-800">Birthdate:</span> <?= htmlspecialchars($voter['birthdate']) ?></p>
                <p><span class="font-medium text-gray-800">Age:</span> <?= htmlspecialchars($voter['age']) ?></p>
                <p><span class="font-medium text-gray-800">Birthplace:</span> <?= htmlspecialchars($voter['birthplace']) ?></p>
                <p><span class="font-medium text-gray-800">Contact:</span> <?= htmlspecialchars($voter['contact']) ?></p>
                <p><span class="font-medium text-gray-800">Sex:</span> <?= htmlspecialchars($voter['sex']) ?></p>
                <p><span class="font-medium text-gray-800">Citizenship:</span> <?= htmlspecialchars($voter['citizenship']) ?></p>
                <p><span class="font-medium text-gray-800">Civil Status:</span> <?= htmlspecialchars($voter['civil_status']) ?></p>
                <p><span class="font-medium text-gray-800">Street/Barangay:</span> <?= htmlspecialchars($voter['street_barangay']) ?></p>
                <p><span class="font-medium text-gray-800">Municipality/City:</span> <?= htmlspecialchars($voter['municipality_city']) ?></p>
                <p><span class="font-medium text-gray-800">Province:</span> <?= htmlspecialchars($voter['province']) ?></p>
                <p><span class="font-medium text-gray-800">Registered Voter:</span> <?= htmlspecialchars($voter['registered_voter']) ?></p>
                <p><span class="font-medium text-gray-800">Registration Status:</span> <?= htmlspecialchars($voter['registration_status']) ?></p>
                <p class="col-span-1 sm:col-span-2"><span class="font-medium text-gray-800">Registered On:</span> <?= htmlspecialchars($voter['created_at']) ?></p>
            </div>
        </div>
    </div>
</div>

</main> <!-- closes main tag from header.php -->
</body>
</html>

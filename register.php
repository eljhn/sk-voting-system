<?php  
session_start();
include 'includes/db.php';
include 'includes/auth.php';

$message = "";
$success = "";

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $birthdate = trim($_POST['birthdate']);
    $birthplace = trim($_POST['birthplace']);
    $contact = trim($_POST['contact']);
    $sex = trim($_POST['sex']);
    $citizenship = trim($_POST['citizenship']);
    $civil_status = trim($_POST['civil_status']);
    $street_barangay = trim($_POST['street_barangay']);
    $municipality_city = trim($_POST['municipality_city']);
    $province = trim($_POST['province']);
    $age = trim($_POST['age']);
    $registered_voter = trim($_POST['registered_voter']);
    $registration_status = trim($_POST['registration_status']);

    // Calculate age in backend
    $today = new DateTime();
    $dob = new DateTime($birthdate);
    $calculatedAge = $today->diff($dob)->y;

    if (
        empty($fullname) || empty($email) || empty($password) || empty($birthdate) ||
        empty($birthplace) || empty($contact) || empty($sex) || 
        empty($citizenship) || empty($civil_status) || empty($street_barangay) || 
        empty($municipality_city) || empty($province) || empty($registered_voter) || empty($registration_status)
    ) {
        $message = "All fields are required!";
    } elseif ($dob > $today) {
        $message = "Birthdate cannot be a future date!";
    } elseif ($calculatedAge < 16) {
        $message = "You must be at least 16 years old to register!";
    } else {
        $result = registerVoter(
            $fullname, $email, $password, $birthdate, $birthplace, $contact,
            $sex, $citizenship, $civil_status, $street_barangay, $municipality_city,
            $province, $calculatedAge, $registered_voter, $registration_status, $conn
        );

        if ($result === true) {
            $success = "Registration successful! Please login.";
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
  <title>Register - SK Voting System</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<?php include 'includes/sidebar.php'; ?>

<!-- Floating Notice Banner -->
<?php if (!empty($message) || !empty($success) || isset($_GET['success'])): ?>
    <div id="noticeBanner" 
         class="fixed top-4 right-4 <?= !empty($message) ? 'bg-red-500' : 'bg-green-600' ?> 
                text-white px-4 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-500">
        <?= htmlspecialchars(!empty($message) ? $message : (!empty($success) ? $success : $_GET['success'])) ?>
    </div>
    <script>
        setTimeout(() => {
            const banner = document.getElementById('noticeBanner');
            if (banner) {
                banner.classList.add('opacity-0');
                setTimeout(() => banner.remove(), 500);
            }
        }, 2500);
    </script>
<?php endif; ?>

<!-- Home Link -->
<div class="px-4 sm:px-6 lg:px-8 mt-4">
  <a href="index.php" class="text-blue-600 hover:underline font-semibold">&larr; Home</a>
</div>

<main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white shadow-lg rounded-xl p-8 sm:p-10 max-w-3xl mx-auto space-y-8">

        <!-- Header -->
        <h1 class="text-center text-2xl sm:text-3xl font-bold mb-1 text-gray-800">Republic of the Philippines</h1>
        <h2 class="text-center text-xl sm:text-2xl font-semibold mb-2 text-gray-700">Commission on Elections (COMELEC)</h2>
        <h3 class="text-center text-lg sm:text-xl font-medium mb-8 text-gray-600">Sangguniang Kabataan (SK) Voter Registration Form</h3>

        <form method="POST" class="space-y-8">

            <!-- Personal Information -->
            <section class="border-b pb-4">
                <h4 class="font-semibold text-lg text-gray-800 mb-4">Personal Information</h4>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block mb-1 font-medium">Full Name *</label>
                        <input type="text" name="fullname" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Email *</label>
                        <input type="email" name="email" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Password *</label>
                        <input type="password" name="password" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Birthdate *</label>
                        <input type="date" name="birthdate" id="birthdate" required max="<?= date('Y-m-d') ?>" class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Age *</label>
                        <input type="number" name="age" id="age" readonly class="w-full border px-3 py-2 rounded-md bg-gray-100">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block mb-1 font-medium">Place of Birth *</label>
                        <input type="text" name="birthplace" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                </div>
            </section>

            <!-- Contact Information -->
            <section class="border-b pb-4">
                <h4 class="font-semibold text-lg text-gray-800 mb-4">Contact Information</h4>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block mb-1 font-medium">Contact Number *</label>
                        <input type="text" name="contact" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Sex *</label>
                        <select name="sex" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                            <option value="">Select Sex</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Citizenship *</label>
                        <input type="text" name="citizenship" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Civil Status *</label>
                        <select name="civil_status" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                            <option value="">Select Civil Status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block mb-1 font-medium">Street / Barangay *</label>
                        <input type="text" name="street_barangay" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Municipality / City *</label>
                        <input type="text" name="municipality_city" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium">Province *</label>
                        <input type="text" name="province" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                </div>
            </section>

            <!-- Other Relevant Information -->
            <section>
                <h4 class="font-semibold text-lg text-gray-800 mb-4">Other Relevant Information</h4>
                <p class="font-medium text-gray-700 mb-2">Are you currently a registered voter? *</p>
                <div class="flex gap-6 mb-4">
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="registered_voter" value="Yes" required>
                        <span>Yes</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="registered_voter" value="No">
                        <span>No</span>
                    </label>
                </div>

                <p class="font-medium text-gray-700 mb-2">Registration Status *</p>
                <div class="flex gap-6">
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="registration_status" value="Active" required>
                        <span>Active</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="registration_status" value="Deactivated">
                        <span>Deactivated</span>
                    </label>
                </div>
            </section>

            <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-lg font-semibold text-lg hover:bg-gray-700 transition">
                Register
            </button>
        </form>
    </div>
</main>

<script>
// Auto-calculate age with notice banner
document.getElementById('birthdate').addEventListener('change', function() {
    let birthdate = new Date(this.value);
    let today = new Date();
    let age = today.getFullYear() - birthdate.getFullYear();
    let m = today.getMonth() - birthdate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthdate.getDate())) age--;

    let ageField = document.getElementById('age');
    ageField.value = isNaN(age) ? "" : age;

    if (birthdate > today) {
        showBanner("Birthdate cannot be a future date.");
        this.value = "";
        ageField.value = "";
        return;
    }
    if (age < 16) {
        showBanner("You must be at least 16 years old to register.");
        this.value = "";
        ageField.value = "";
        return;
    }
});

function showBanner(msg) {
    let banner = document.createElement("div");
    banner.id = "noticeBanner";
    banner.className = "fixed top-4 right-4 bg-red-500 text-white px-4 py-3 rounded shadow-lg z-50 transition-opacity duration-500";
    banner.innerText = msg;
    document.body.appendChild(banner);

    setTimeout(() => {
        banner.classList.add("opacity-0");
        setTimeout(() => banner.remove(), 500);
    }, 2500);
}
</script>

</body>
</html>

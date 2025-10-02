<?php  
session_start();
include 'includes/db.php';
include 'includes/auth.php';

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $nickname = trim($_POST['nickname']);
    $birthdate = trim($_POST['birthdate']);
    $age = trim($_POST['age']);
    $birthplace = trim($_POST['birthplace']);
    $address = trim($_POST['address']);
    $barangay = trim($_POST['barangay']);
    $municipality = trim($_POST['municipality']);
    $province = trim($_POST['province']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $registered_voter = trim($_POST['registered_voter']);
    $public_office = trim($_POST['public_office']);
    $position = trim($_POST['position']);
    $age_require = trim($_POST['age_require']);
    $current_school = trim($_POST['current_school']);
    $year_level = trim($_POST['year_level']);
    $occupation = trim($_POST['occupation']);
    $occupation_position = trim($_POST['occupation_position']);

    // Calculate and validate age
    $birthDateObj = new DateTime($birthdate);
    $today = new DateTime();
    $calculatedAge = $today->diff($birthDateObj)->y;

    if ($calculatedAge < 18) {
        $message = "You must be at least 18 years old to register as a candidate.";
    } elseif ($birthDateObj > $today) {
        $message = "Birthdate cannot be in the future.";
    } elseif (empty($full_name) || empty($birthdate) || empty($age) || empty($birthplace) || empty($address) || 
        empty($barangay) || empty($municipality) || empty($province) || empty($phone) || 
        empty($registered_voter) || empty($public_office) || empty($age_require)) {
        $message = "Please fill in all required fields!";
    } else {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO new_candidates
            (full_name, nickname, birthdate, age, birthplace, address, barangay, municipality, province, phone, email,
            registered_voter, public_office, position, age_require, current_school, year_level, occupation, occupation_position)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssisssssssssssssss",
            $full_name, $nickname, $birthdate, $calculatedAge, $birthplace, $address, $barangay, $municipality, $province,
            $phone, $email, $registered_voter, $public_office, $position, $age_require, $current_school, $year_level,
            $occupation, $occupation_position
        );

        if ($stmt->execute()) {
            header("Location: candidates_register.php?success=Registration successful!");
            exit();
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SK Candidate Registration</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<?php include 'includes/sidebar.php'; ?>

<!-- Floating Notice Banner -->
<?php if (!empty($message) || isset($_GET['success'])): ?>
    <div id="noticeBanner" 
         class="fixed top-4 right-4 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-500">
        <?= htmlspecialchars(!empty($message) ? $message : $_GET['success']) ?>
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

<main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow-lg rounded-xl p-6 sm:p-10 max-w-3xl mx-auto">

        <h1 class="text-center text-2xl sm:text-3xl font-bold mb-1 text-gray-800">Republic of the Philippines</h1>
        <h2 class="text-center text-xl sm:text-2xl font-semibold mb-4 text-gray-700">Commission on Elections (COMELEC)</h2>
        <h3 class="text-center text-lg font-medium mb-8 text-gray-600">Sangguniang Kabataan (SK) Candidate Registration Form</h3>

        <form method="POST" class="space-y-8">

            <!-- Personal Information -->
            <section>
                <h4 class="font-semibold text-lg text-gray-800 mb-4 border-b pb-2">Personal Information</h4>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block mb-1 font-medium text-sm">Full Name *</label>
                        <input type="text" name="full_name" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-sm">Nickname</label>
                        <input type="text" name="nickname" class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-sm">Birthdate *</label>
                        <input type="date" name="birthdate" id="birthdate" required max="<?= date('Y-m-d') ?>" class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-sm">Age *</label>
                        <input type="number" name="age" id="age" readonly min="18" class="w-full border px-3 py-2 rounded-md bg-gray-100">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block mb-1 font-medium text-sm">Place of Birth *</label>
                        <input type="text" name="birthplace" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                </div>
            </section>

            <!-- Contact Information -->
            <section>
                <h4 class="font-semibold text-lg text-gray-800 mb-4 border-b pb-2">Contact Information</h4>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="block mb-1 font-medium text-sm">Residential Address *</label>
                        <input type="text" name="address" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-sm">Barangay *</label>
                        <input type="text" name="barangay" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-sm">City / Municipality *</label>
                        <input type="text" name="municipality" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-sm">Province *</label>
                        <input type="text" name="province" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-sm">Phone / Mobile No. *</label>
                        <input type="text" name="phone" required class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-sm">Email</label>
                        <input type="email" name="email" class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                </div>
            </section>

            <!-- Eligibility -->
            <section>
                <h4 class="font-semibold text-lg text-gray-800 mb-4 border-b pb-2">Eligibility</h4>
                <div class="space-y-4">
                    <div>
                        <p class="font-medium text-gray-700">Are you a registered SK voter in this barangay? *</p>
                        <div class="flex gap-6 mt-1">
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="registered_voter" value="Yes" required>
                                <span>Yes</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="registered_voter" value="No">
                                <span>No</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Are you currently holding any public office? *</p>
                        <div class="flex gap-6 mt-1">
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="public_office" value="Yes" required>
                                <span>Yes</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="public_office" value="No">
                                <span>No</span>
                            </label>
                        </div>
                        <div id="positionWrapper" class="hidden mt-2">
                            <label class="block mb-1 font-medium text-sm">If Yes, specify position</label>
                            <input type="text" name="position" id="positionField" class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                        </div>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Do you meet the age requirement (18–24 years old)? *</p>
                        <div class="flex gap-6 mt-1">
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="age_require" value="Yes" required>
                                <span>Yes</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="age_require" value="No">
                                <span>No</span>
                            </label>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Educational / Work Background -->
            <section>
                <h4 class="font-semibold text-lg text-gray-800 mb-4 border-b pb-2">Educational / Work Background (optional)</h4>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block mb-1 font-medium text-sm">Current School</label>
                        <input type="text" name="current_school" class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-sm">Year / Level</label>
                        <input type="text" name="year_level" class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-sm">Occupation</label>
                        <input type="text" name="occupation" class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-sm">Position</label>
                        <input type="text" name="occupation_position" class="w-full border px-3 py-2 rounded-md focus:ring focus:ring-green-300">
                    </div>
                </div>
            </section>

            <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-lg font-semibold text-lg hover:bg-gray-700 transition">
                Submit
            </button>
        </form>
    </div>
</main>

<script>
// Auto-calculate age
document.getElementById('birthdate').addEventListener('change', function() {
    let birthdate = new Date(this.value);
    let today = new Date();
    let age = today.getFullYear() - birthdate.getFullYear();
    let m = today.getMonth() - birthdate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthdate.getDate())) age--;

    if (birthdate > today) {
        showNotice("Birthdate cannot be in the future!", "error");
        this.value = "";
        document.getElementById('age').value = "";
        return;
    }
    if (age < 18) {
        showNotice("You must be at least 18 years old to register as a candidate.", "error");
        this.value = "";
        document.getElementById('age').value = "";
        return;
    }
    document.getElementById('age').value = !isNaN(age) ? age : "";
});

// Toggle position field
const publicOfficeRadios = document.querySelectorAll('input[name="public_office"]');
const positionWrapper = document.getElementById('positionWrapper');
const positionField = document.getElementById('positionField');

publicOfficeRadios.forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.value === "Yes") {
            positionWrapper.classList.remove('hidden');
        } else {
            positionWrapper.classList.add('hidden');
            positionField.value = "";
        }
    });
});

// Floating notice
function showNotice(text, type="success") {
    const banner = document.createElement("div");
    banner.textContent = text;
    banner.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-500 ${
        type === "error" ? "bg-red-500 text-white" : "bg-green-600 text-white"
    }`;
    document.body.appendChild(banner);
    setTimeout(() => {
        banner.classList.add('opacity-0');
        setTimeout(() => banner.remove(), 500);
    }, 2500);
}
</script>

</body>
</html>

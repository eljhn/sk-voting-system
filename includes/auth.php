<?php
// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

// ========================
// Voter Functions
// ========================

// Register Voter
function registerVoter(
    $fullname, $email, $password, $birthdate, $birthplace, $contact,
    $sex, $citizenship, $civil_status, $street_barangay, $municipality_city,
    $province, $age, $registered_voter, $registration_status, $conn
) {
    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM voters WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) return "Email already registered!";

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $insert = $conn->prepare("
        INSERT INTO voters (
            fullname, email, password, birthdate, birthplace, contact, sex,
            citizenship, civil_status, street_barangay, municipality_city, province,
            age, registered_voter, registration_status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $insert->bind_param(
        "sssssssssssssss",
        $fullname, $email, $hashedPassword, $birthdate, $birthplace, $contact,
        $sex, $citizenship, $civil_status, $street_barangay, $municipality_city, $province,
        $age, $registered_voter, $registration_status
    );

    return $insert->execute() ? true : "Registration failed. Try again.";
}

// Login Voter
function loginVoter($email, $password, $conn) {
    $stmt = $conn->prepare("SELECT * FROM voters WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['voter_id'] = $row['voter_id'];
            $_SESSION['voter_name'] = $row['fullname'];
            return true;
        } else {
            return "Incorrect password!";
        }
    } else {
        return "Email not found!";
    }
}

// Check Voter Session
function checkVoterSession() {
    if (!isset($_SESSION['voter_id'])) {
        header("Location: index.php?error=Please login first.");
        exit();
    }
}

// Logout Voter
function logoutVoter() {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// ========================
// Admin Functions
// ========================

// 🔐 Change this to database-based if you want dynamic admins later
function adminCredentials() {
    return ['username' => 'admin', 'password' => '12345'];
}

function loginAdmin($username, $password) {
    $admin = adminCredentials();
    if ($username === $admin['username'] && $password === $admin['password']) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        return true;
    } else {
        return "Incorrect username or password!";
    }
}

function checkAdminSession() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: index.php?error=Please login first.");
        exit();
    }
}

function logoutAdmin() {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// ========================
// SK Candidate Functions
// ========================

// Register Candidate
function registerCandidate(
    $full_name, $nickname, $birthdate, $age, $birthplace, $address, $barangay, $municipality,
    $province, $phone, $email, $registered_voter, $public_office, $position, $age_require,
    $current_school, $year_level, $occupation, $occupation_position, $conn
) {
    // Check if candidate already exists
    $stmt = $conn->prepare("SELECT * FROM new_candidates WHERE full_name = ? AND birthdate = ?");
    $stmt->bind_param("ss", $full_name, $birthdate);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) return "Candidate already registered!";

    $insert = $conn->prepare("
        INSERT INTO new_candidates (
            full_name, nickname, birthdate, age, birthplace, address, barangay, municipality, province,
            phone, email, registered_voter, public_office, position, age_require,
            current_school, year_level, occupation, occupation_position, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $insert->bind_param(
        "sssisssssssssssssss",
        $full_name, $nickname, $birthdate, $age, $birthplace, $address, $barangay, $municipality,
        $province, $phone, $email, $registered_voter, $public_office, $position, $age_require,
        $current_school, $year_level, $occupation, $occupation_position
    );

    return $insert->execute() ? true : "Candidate registration failed. Try again.";
}

// Check Candidate Session
function checkCandidateSession() {
    if (!isset($_SESSION['candidate_id'])) {
        header("Location: candidates_register.php?error=Please login first.");
        exit();
    }
}

// Logout Candidate
function logoutCandidate() {
    session_unset();
    session_destroy();
    header("Location: candidates_register.php");
    exit();
}

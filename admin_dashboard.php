<?php 
session_start();
include 'includes/db.php';
include 'includes/auth.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}

// ========================
// Handle Candidate Approve/Decline
// ========================
if (isset($_POST['approve_candidate'])) {
    $newCandidateId = intval($_POST['new_candidate_id']);

    // Copy all fields from new_candidates to candidates
    $stmtInsert = $conn->prepare("
        INSERT INTO candidates (
            candidate_id, full_name, nickname, birthdate, age, birthplace, address,
            barangay, municipality, province, phone, email, registered_voter,
            public_office, position, age_require, current_school, year_level,
            occupation, occupation_position, created_at, status
        )
        SELECT 
            candidate_id, full_name, nickname, birthdate, age, birthplace, address,
            barangay, municipality, province, phone, email, registered_voter,
            public_office, position, age_require, current_school, year_level,
            occupation, occupation_position, created_at, 'active'
        FROM new_candidates
        WHERE candidate_id = ?
    ");
    $stmtInsert->bind_param("i", $newCandidateId);
    $stmtInsert->execute();
    $stmtInsert->close();

    // Delete from new_candidates after approval
    $stmtDel = $conn->prepare("DELETE FROM new_candidates WHERE candidate_id = ?");
    $stmtDel->bind_param("i", $newCandidateId);
    $stmtDel->execute();
    $stmtDel->close();

    header("Location: admin_dashboard.php?success=Candidate approved successfully#dashboard");
    exit();
}

if (isset($_POST['decline_candidate'])) {
    $newCandidateId = intval($_POST['new_candidate_id']);
    $stmtDel = $conn->prepare("DELETE FROM new_candidates WHERE candidate_id = ?");
    $stmtDel->bind_param("i", $newCandidateId);
    $stmtDel->execute();
    $stmtDel->close();

    header("Location: admin_dashboard.php?success=Candidate declined#dashboard");
    exit();
}

// Soft delete candidate (mark inactive, keep votes)
if (isset($_POST['delete_candidate'])) {
    $candidateId = intval($_POST['candidate_id']);
    $stmt = $conn->prepare("UPDATE candidates SET status='inactive' WHERE candidate_id = ?");
    $stmt->bind_param("i", $candidateId);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_dashboard.php?success=Candidate marked inactive (votes preserved)#dashboard");
    exit();
}

// Delete voter
if (isset($_POST['delete_voter'])) {
    $voterId = intval($_POST['voter_id']);

    $stmtVotes = $conn->prepare("DELETE FROM votes WHERE voter_id = ?");
    $stmtVotes->bind_param("i", $voterId);
    $stmtVotes->execute();
    $stmtVotes->close();

    $stmt = $conn->prepare("DELETE FROM voters WHERE voter_id = ?");
    $stmt->bind_param("i", $voterId);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_dashboard.php?success=Voter deleted successfully#dashboard");
    exit();
}

// =================== FETCH DATA ===================
// Registered voters
$votersQuery = $conn->query("SELECT voter_id, fullname, email, created_at FROM voters ORDER BY created_at DESC");

// Approved candidates
$candidatesQuery = $conn->query("
    SELECT c.candidate_id, c.full_name, COUNT(v.vote_id) AS votes
    FROM candidates c
    LEFT JOIN votes v ON c.candidate_id = v.candidate_id
    WHERE c.status='active'
    GROUP BY c.candidate_id
    ORDER BY votes DESC
");

// Pending candidates (from new_candidates table)
$pendingCandidatesQuery = $conn->query("SELECT candidate_id, full_name FROM new_candidates ORDER BY created_at ASC");
?>

<?php include 'includes/sidebar.php'; ?>

<!-- =================== Notice Banner =================== -->
<?php if (isset($_GET['success'])): ?>
    <div id="noticeBanner" class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow z-50">
        <?= htmlspecialchars($_GET['success']) ?>
    </div>
<?php elseif (isset($_GET['error'])): ?>
    <div id="noticeBanner" class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow z-50">
        <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<div id="dashboard" class="min-h-screen bg-gray-100 py-8 px-4 sm:px-6 lg:px-8">

    <!-- Dashboard Title -->
    <div class="max-w-7xl mx-auto mb-8">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 text-center">Admin Dashboard</h1>
    </div>

    <div class="max-w-7xl mx-auto grid gap-8 md:grid-cols-1 lg:grid-cols-1">

        <!-- Pending Candidates -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800 text-center">Pending Candidates</h2>
            <p class="text-gray-600 text-center mb-6">Approve or decline newly registered candidates.</p>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm sm:text-base">
                    <thead class="bg-yellow-100">
                        <tr>
                            <th class="px-4 py-2 text-left">ID</th>
                            <th class="px-4 py-2 text-left">Candidate Name</th>
                            <th class="px-4 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while($pending = $pendingCandidatesQuery->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2"><?= $pending['candidate_id'] ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($pending['full_name']) ?></td>
                                <td class="px-4 py-2 text-center space-x-2 flex flex-wrap justify-center gap-2">
                                    <a href="candidates_profile.php?candidate_id=<?= $pending['candidate_id'] ?>" class="text-blue-600 hover:underline">View Profile</a>
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="new_candidate_id" value="<?= $pending['candidate_id'] ?>">
                                        <button type="submit" name="approve_candidate" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition">Approve</button>
                                    </form>
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="new_candidate_id" value="<?= $pending['candidate_id'] ?>">
                                        <button type="submit" name="decline_candidate" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Decline</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($pendingCandidatesQuery->num_rows === 0): ?>
                            <tr>
                                <td colspan="3" class="text-center py-3 text-gray-500">No pending candidates</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Approved Candidates & Votes -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800 text-center">Approved Candidates & Votes</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm sm:text-base">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">ID</th>
                            <th class="px-4 py-2 text-left">Candidate Name</th>
                            <th class="px-4 py-2 text-left">Votes</th>
                            <th class="px-4 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while($candidate = $candidatesQuery->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2"><?= $candidate['candidate_id'] ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($candidate['full_name']) ?></td>
                                <td class="px-4 py-2"><?= $candidate['votes'] ?></td>
                                <td class="px-4 py-2 text-center space-x-2 flex flex-wrap justify-center gap-2">
                                    <a href="candidates_profile.php?candidate_id=<?= $candidate['candidate_id'] ?>" class="text-blue-600 hover:underline">View Profile</a>
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="candidate_id" value="<?= $candidate['candidate_id'] ?>">
                                        <button type="submit" name="delete_candidate" class="text-red-600 hover:underline">Mark Inactive</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($candidatesQuery->num_rows === 0): ?>
                            <tr>
                                <td colspan="4" class="text-center py-3 text-gray-500">No approved candidates yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Registered Voters -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800 text-center">Registered Voters</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm sm:text-base">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">ID</th>
                            <th class="px-4 py-2 text-left">Full Name</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Registered At</th>
                            <th class="px-4 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if ($votersQuery->num_rows > 0): ?>
                            <?php while($voter = $votersQuery->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2"><?= $voter['voter_id'] ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($voter['fullname']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($voter['email']) ?></td>
                                    <td class="px-4 py-2"><?= $voter['created_at'] ?></td>
                                    <td class="px-4 py-2 text-center space-x-2 flex flex-wrap justify-center gap-2">
                                        <a href="profile.php?voter_id=<?= $voter['voter_id'] ?>" class="text-blue-600 hover:underline">View Profile</a>
                                        <form method="POST" class="inline-block">
                                            <input type="hidden" name="voter_id" value="<?= $voter['voter_id'] ?>">
                                            <button type="submit" name="delete_voter" class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-3 text-gray-500">No registered voters yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const banner = document.getElementById("noticeBanner");
    if (banner) {
        // Fade out without scrolling
        setTimeout(() => {
            banner.classList.add("transition", "duration-500", "opacity-0");
            setTimeout(() => banner.remove(), 500);
        }, 2000);
    }
});
</script>

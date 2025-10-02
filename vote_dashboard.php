<?php
session_start();
include 'includes/db.php';
include 'includes/auth.php';

// Ensure voter or admin is logged in
$isVoterLoggedIn = isset($_SESSION['voter_id']);
$isAdminLoggedIn = isset($_SESSION['admin_logged_in']);
$alreadyVoted = false;

if ($isVoterLoggedIn) {
    $stmt = $conn->prepare("SELECT * FROM votes WHERE voter_id = ?");
    $stmt->bind_param("i", $_SESSION['voter_id']);
    $stmt->execute();
    $alreadyVoted = $stmt->get_result()->num_rows > 0;
}

// 5 default fictional SK candidates (no credentials)
$fictionalCandidates = [
    ["name" => "Naifa "],
    ["name" => "Cherry Ann "],
    ["name" => "Jherame"],
    ["name" => "Ethell"],
    ["name" => "Benedict"]
];

// Ensure fictional candidates exist in DB
foreach ($fictionalCandidates as $fc) {
    $stmt = $conn->prepare("SELECT candidate_id FROM candidates WHERE LOWER(full_name) = LOWER(?)");
    $stmt->bind_param("s", $fc['name']);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        $stmtInsert = $conn->prepare("INSERT INTO candidates (full_name, status) VALUES (?, 'active')");
        $stmtInsert->bind_param("s", $fc['name']);
        $stmtInsert->execute();
        $stmtInsert->close();
    }
    $stmt->close();
}

// Build candidates list in correct order
$candidatesList = [];

// First: fetch the 5 fictional ones in the same order
foreach ($fictionalCandidates as $fc) {
    $stmt = $conn->prepare("SELECT candidate_id, full_name FROM candidates WHERE LOWER(full_name) = LOWER(?) AND status='active' LIMIT 1");
    $stmt->bind_param("s", $fc['name']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $candidatesList[] = [
            "id" => $row['candidate_id'],
            "name" => $row['full_name']
        ];
    }
    $stmt->close();
}

// Second: fetch all other approved candidates not in fictional list
$fictionalNames = array_map(fn($c) => strtolower($c['name']), $fictionalCandidates);
$dbCandidates = $conn->query("SELECT candidate_id, full_name FROM candidates WHERE status='active' ORDER BY candidate_id ASC");
while ($row = $dbCandidates->fetch_assoc()) {
    if (!in_array(strtolower($row['full_name']), $fictionalNames)) {
        $candidatesList[] = [
            "id" => $row['candidate_id'],
            "name" => $row['full_name']
        ];
    }
}

$message = $_GET['success'] ?? ($_GET['error'] ?? '');
?>

<?php include 'includes/sidebar.php'; ?>

<main class="min-h-screen bg-gray-50">
  <div class="container mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-20">

    <!-- Header / Hero -->
<header class="mb-8 text-center">
  <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">Voting Dashboard</h1>
  
  <?php if ($isVoterLoggedIn): ?>
    <p class="mt-5 text-sm sm:text-base text-gray-600">
      Cast your vote for the SK candidates. Each voter may cast only one vote — make it count.
    </p>
  <?php elseif ($isAdminLoggedIn): ?>
    <p class="mt-5 text-sm sm:text-base text-gray-600">
      You are viewing the dashboard as an admin.
    </p>
  <?php endif; ?>
</header>


    <!-- Notice Banner -->
    <?php if (!empty($message)): ?>
    <div id="noticeBanner" class="fixed top-4 right-4 z-50 bg-green-100 border border-green-200 text-green-700 p-3 rounded-lg shadow-lg font-medium">
        <?= htmlspecialchars($message) ?>
    </div>
    <script>
      setTimeout(() => {
        const banner = document.getElementById('noticeBanner');
        if (banner) {
          banner.classList.add('opacity-0', 'transition', 'duration-500', 'ease-out');
          setTimeout(() => banner.remove(), 500);
        }
      }, 2000); // 2 seconds
    </script>
    <?php endif; ?>

    <!-- Voting grid -->
    <section class="max-w-7xl mx-auto mt-12">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

        <?php $counter = 1; ?>
        <?php foreach ($candidatesList as $candidate): ?>
          <article class="relative bg-white rounded-xl shadow-md p-6 flex flex-col items-center text-center">
            <!-- number badge -->
            <div class="absolute -top-3 left-4">
              <div class="bg-indigo-600 text-white w-9 h-9 flex items-center justify-center rounded-full font-semibold shadow-sm">
                <?= $counter++ ?>
              </div>
            </div>

            <!-- avatar -->
            <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center text-4xl mb-4">
              👤
            </div>

            <!-- name -->
            <h2 class="text-lg font-semibold text-gray-900 mb-2"><?= htmlspecialchars($candidate['name']) ?></h2>

            <!-- small placeholder -->
            <p class="text-sm text-gray-500 mb-4">Candidate for SK</p>

            <!-- actions -->
            <div class="w-full mt-auto">
              <?php if ($isVoterLoggedIn): ?>
                <form method="POST" action="submit_vote.php" class="w-full">
                  <input type="hidden" name="candidate_id" value="<?= $candidate['id'] ?>">
                  <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                      <?= $alreadyVoted ? 'bg-gray-300 text-gray-700 cursor-not-allowed' : 'bg-indigo-600 text-white hover:bg-indigo-700' ?>"
                    <?= $alreadyVoted ? 'disabled' : '' ?> >
                    <?= $alreadyVoted ? 'Already Voted' : 'Vote' ?>
                  </button>
                </form>
              <?php elseif ($isAdminLoggedIn): ?>
                <div class="text-sm text-gray-500 italic">Admin view only</div>
              <?php else: ?>
                <div class="text-sm text-gray-600">Please <a href="index.php" class="text-indigo-600 font-medium underline">login</a> to vote.</div>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>

      </div>

      <!-- optional footnote only for voters -->
      <?php if ($isVoterLoggedIn): ?>
        <div class="mt-8 text-center text-xs text-gray-500">
          Results are updated after voting ends. This is a simulated environment for demonstration.
        </div>
      <?php endif; ?>
    </section>
  </div>
</main>

<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Privacy Policy - SK Voting System</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

  <?php include 'includes/sidebar.php'; ?>

  <!-- Page Content -->
  <main class="flex-grow">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <h1 class="text-3xl sm:text-4xl font-bold mb-10 text-center text-gray-800">
        Privacy Policy
      </h1>

      <!-- In General -->
      <section class="bg-white p-6 sm:p-8 rounded-xl shadow mb-8">
        <h2 class="text-xl font-semibold mb-3 text-gray-900">In General</h2>
        <p class="text-gray-700 leading-relaxed">
          The SK Voting System ensures that all collected data is handled responsibly.
          Personal information will only be used for election purposes and will never
          be shared with third parties without consent.
        </p>
      </section>

      <!-- For Voters -->
      <section class="bg-white p-6 sm:p-8 rounded-xl shadow mb-8">
        <h2 class="text-xl font-semibold mb-3 text-gray-900">For Voters</h2>
        <p class="text-gray-700 leading-relaxed">
          Voter registration requires your full name and a secure login credential.
          Your vote is confidential and cannot be traced back to you. Each voter is
          allowed to vote only once.
        </p>
      </section>

      <!-- About Non-Vote Data -->
      <section class="bg-white p-6 sm:p-8 rounded-xl shadow mb-8">
        <h2 class="text-xl font-semibold mb-3 text-gray-900">About Non-Vote Data</h2>
        <p class="text-gray-700 leading-relaxed">
          Non-voting data such as login attempts, page visits, and system usage may
          be logged for security and monitoring purposes. This information is used
          only to improve system performance and security.
        </p>
      </section>

      <!-- For Administrators -->
      <section class="bg-white p-6 sm:p-8 rounded-xl shadow">
        <h2 class="text-xl font-semibold mb-3 text-gray-900">For Administrators</h2>
        <p class="text-gray-700 leading-relaxed">
          Administrators have access to voter lists and aggregated voting results.
          They cannot see individual votes. Administrator login is protected by
          default credentials, and access is limited to authorized personnel only.
        </p>
      </section>
    </div>
  </main>

</body>
</html>

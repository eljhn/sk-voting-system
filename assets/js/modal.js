document.addEventListener("DOMContentLoaded", () => {
    const profileBtn = document.getElementById("profileBtn");
    const profileModal = document.getElementById("profileModal");
    const closeProfile = document.getElementById("closeProfileModal");

    const loginBtn = document.getElementById("loginBtn");
    const loginModal = document.getElementById("loginModal");
    const closeLogin = document.getElementById("closeLoginModal");

    // Profile modal logic
    if (profileBtn && profileModal) {
        profileBtn.addEventListener("click", () => {
            profileModal.classList.remove("hidden");
        });
    }

    if (closeProfile && profileModal) {
        closeProfile.addEventListener("click", () => {
            profileModal.classList.add("hidden");
        });
    }

    // Close profile modal if clicking outside
    document.addEventListener("click", (e) => {
        if (profileModal && !profileModal.classList.contains("hidden")) {
            const modalBox = profileModal.querySelector("div");
            if (modalBox && !modalBox.contains(e.target) && e.target !== profileBtn) {
                profileModal.classList.add("hidden");
            }
        }
    });

    // Login modal logic
    if (loginBtn && loginModal) {
        loginBtn.addEventListener("click", () => {
            loginModal.classList.remove("hidden");
        });
    }

    if (closeLogin && loginModal) {
        closeLogin.addEventListener("click", () => {
            loginModal.classList.add("hidden");
        });
    }

    // Close login modal if clicking outside
    document.addEventListener("click", (e) => {
        if (loginModal && !loginModal.classList.contains("hidden")) {
            const modalBox = loginModal.querySelector("div");
            if (modalBox && !modalBox.contains(e.target) && e.target !== loginBtn) {
                loginModal.classList.add("hidden");
            }
        }
    });
});

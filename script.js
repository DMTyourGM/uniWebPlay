const userId = <?php echo isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null'; ?>; // Define userId from session

// Theme toggle and persistence logic
const modeToggle = document.getElementById('modeToggle');
const body = document.body;

// Load saved theme from localStorage
const savedTheme = localStorage.getItem('theme');
if (savedTheme) {
    if (savedTheme === 'light') {
        body.classList.add('light-mode');
        modeToggle.setAttribute('aria-label', 'Switch to dark mode');
    } else {
        body.classList.remove('light-mode');
        modeToggle.setAttribute('aria-label', 'Switch to light mode');
    }
}

modeToggle.addEventListener('click', () => {
    if (body.classList.contains('light-mode')) {
        body.classList.remove('light-mode');
        localStorage.setItem('theme', 'dark');
        modeToggle.setAttribute('aria-label', 'Switch to light mode');
    } else {
        body.classList.add('light-mode');
        localStorage.setItem('theme', 'light');
        modeToggle.setAttribute('aria-label', 'Switch to dark mode');
    }
});

// Utility function to sanitize text content to prevent XSS
function sanitizeText(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Show loading spinner for a container element
function showLoading(container) {
    container.innerHTML = '<li class="loading">Loading...</li>';
}

// Show error message for a container element
function showError(container, message) {
    container.innerHTML = `<li class="error">${sanitizeText(message)}</li>`;
}

// Fetch and display user stats with dynamic progress bar calculation
function fetchUserStats() {
    fetch(`gamification_api.php?action=user_stats&user_id=${userId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalPoints').textContent = data.user_stats.total_points;
                document.getElementById('totalBookings').textContent = data.user_stats.total_bookings;
                document.getElementById('totalCompetitions').textContent = data.user_stats.total_competitions;
                document.getElementById('skillLevel').textContent = data.user_stats.skill_level_name || 'N/A';

                // Dynamic progress calculation example
                const minPoints = data.user_stats.min_points || 0;
                const maxPoints = data.user_stats.max_points || 100;
                const currentPoints = data.user_stats.current_points || 0;
                let progressPercent = 0;
                if (maxPoints > minPoints) {
                    progressPercent = ((currentPoints - minPoints) / (maxPoints - minPoints)) * 100;
                    progressPercent = Math.min(Math.max(progressPercent, 0), 100);
                }
                document.getElementById('skillProgress').style.width = progressPercent + '%';
                document.getElementById('skillProgress').setAttribute('aria-valuenow', progressPercent.toFixed(0));
            } else {
                document.getElementById('totalPoints').textContent = 'N/A';
                document.getElementById('totalBookings').textContent = 'N/A';
                document.getElementById('totalCompetitions').textContent = 'N/A';
                document.getElementById('skillLevel').textContent = 'N/A';
                document.getElementById('skillProgress').style.width = '0%';
            }
        })
        .catch(() => {
            document.getElementById('totalPoints').textContent = 'N/A';
            document.getElementById('totalBookings').textContent = 'N/A';
            document.getElementById('totalCompetitions').textContent = 'N/A';
            document.getElementById('skillLevel').textContent = 'N/A';
            document.getElementById('skillProgress').style.width = '0%';
        });
}

// Fetch and display user achievements with sanitization and error handling
function fetchAchievements() {
    const list = document.getElementById('achievementsList');
    showLoading(list);
    fetch(`gamification_api.php?action=user_achievements&user_id=${userId}`)
        .then(res => res.json())
        .then(data => {
            list.innerHTML = '';
            if (data.success && data.achievements.length > 0) {
                data.achievements.forEach(ach => {
                    const li = document.createElement('li');
                    li.className = 'achievement-item';
                    li.textContent = `${ach.name} (${ach.points_awarded} pts) - Achieved on ${new Date(ach.achieved_at).toLocaleDateString()}`;
                    list.appendChild(li);
                });
            } else {
                list.innerHTML = '<li>No achievements yet.</li>';
            }
        })
        .catch(() => {
            showError(list, 'Error loading achievements');
        });
}

// Fetch and display real activity feed with sanitization and error handling
function fetchActivityFeed() {
    const list = document.getElementById('activityFeedList');
    showLoading(list);
    fetch(`gamification_api.php?action=user_activity_feed&user_id=${userId}`)
        .then(res => res.json())
        .then(data => {
            list.innerHTML = '';
            if (data.success && data.activities.length > 0) {
                data.activities.forEach(activity => {
                    const li = document.createElement('li');
                    li.className = 'activity-item';
                    li.textContent = sanitizeText(activity.description);
                    list.appendChild(li);
                });
            } else {
                list.innerHTML = '<li>No recent activity.</li>';
            }
        })
        .catch(() => {
            showError(list, 'Error loading activity feed');
        });
}

// Show achievement popup notification with accessibility
function showAchievementPopup(message) {
    const popup = document.getElementById('achievementPopup');
    popup.textContent = message;
    popup.style.display = 'block';
    popup.setAttribute('role', 'alert');
    setTimeout(() => {
        popup.style.display = 'none';
        popup.removeAttribute('role');
    }, 5000);
}

// Initial fetches with loading spinners and error handling
document.addEventListener('DOMContentLoaded', () => {
    if (userId === null) {
        // User not logged in, show message or redirect
        alert('User not logged in. Please log in to access the dashboard.');
        return;
    }
    fetchUserStats();
    fetchAchievements();
    fetchActivityFeed();
});

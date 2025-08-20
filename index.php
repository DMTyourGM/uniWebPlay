<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$profilePhoto = $isLoggedIn && !empty($_SESSION['profile_photo']) ? $_SESSION['profile_photo'] : 'uploads/profile_photos/default_avatar.png';
?>
<!DOCTYPE html>
<html lang="en">            
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>UniWebPlay - Competitive Recreational Platform</title>
        <link rel="stylesheet" href="style.css" />
        <link rel="stylesheet" href="responsive.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    </head>
    <body>
        <header class="sticky-header">
            <div class="header-content">
                <h1>UniWebPlay</h1>
                <nav class="navbar">
                    <a href="index.php">Home</a>
                    <a href="booking.html">Book Facility Slot</a>
                    <a href="report.html">Report Issue</a>
                    <a href="account.html">Account</a>
                    <a href="login.html">Login</a>
                    <a href="register.html">Register</a>
                    <button id="colorToggle" aria-label="Toggle Color Theme" class="btn btn-small">Change Color</button>
                </nav>
                <?php if ($isLoggedIn): ?>
                <a href="account.html" class="profile-avatar-link">
                    <img src="<?php echo htmlspecialchars($profilePhoto); ?>" alt="User Avatar" class="user-avatar-circle" />
                </a>
                <?php endif; ?>
                <button id="modeToggle" aria-label="Toggle Light/Dark Mode" class="mode-toggle">
                    <span id="toggleCircle" class="toggle-circle"></span>
                </button>
            </div>
        </header>
        <main id="container">
            <section class="hero">
                <div class="hero-text">
                    <h2>COMPETE • ACHIEVE • DOMINATE</h2>
                    <p>The Ultimate Competitive Recreational Platform for University Athletes</p>
                    <div class="button-group">
                        <a href="home.html" class="btn">🚀 Start Competing</a>
                        <a href="leaderboard.html" class="btn">🏆 View Rankings</a>
                        <a href="login.html" class="btn">🔐 Login</a>
                        <a href="register.html" class="btn">📝 Register</a>
                    </div>
                </div>
                <div class="hero-image">
<img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=MnwzNjUyOXwwfDF8c2VhcmNofDF8fHVuaXZlcnNpdHxlbnwwfHx8fDE2ODI0MjY0MjM&ixlib=rb-4.0.3&q=80&w=1080" alt="University Athletics" />
                </div>
            </section>

            <section class="highlights">
                <h2>Why Choose UniWebPlay?</h2>
                <div class="highlight-cards">
                    <div class="highlight-card">
                        <h3>Competitive Tournaments</h3>
                        <p>Join exciting tournaments and compete with the best university athletes.</p>
                    </div>
                    <div class="highlight-card">
                        <h3>Real-time Leaderboards</h3>
                        <p>Track your progress and see how you rank against others in real-time.</p>
                    </div>
                    <div class="highlight-card">
                        <h3>Achievements & Rewards</h3>
                        <p>Earn badges and points as you compete and improve your skills.</p>
                    </div>
                </div>
            </section>

            <section class="tournament-vibe">
                <h2>🔥 Upcoming Tournaments</h2>
                <div class="tournament-cards">
                    <div class="tournament-card">
                        <h3>Spring Championship 2024</h3>
                        <p>Compete in pool, chess, and table tennis to win exciting prizes!</p>
                        <p><strong>Starts:</strong> May 1, 2024</p>
                        <a href="tournaments.html" class="btn">Join Now</a>
                    </div>
                    <div class="tournament-card">
                        <h3>Summer Showdown</h3>
                        <p>Show your skills in carom and board games. Climb the leaderboard!</p>
                        <p><strong>Starts:</strong> July 15, 2024</p>
                        <a href="tournaments.html" class="btn">Join Now</a>
                    </div>
                </div>
            </section>

            <style>
                .tournament-vibe {
                    max-width: 1200px;
                    margin: 2rem auto;
                    padding: 1rem;
                    text-align: center;
                    background-color: var(--card-bg);
                    border-radius: var(--border-radius);
                    box-shadow: var(--shadow-light);
                    color: var(--text-primary);
                }
                .tournament-cards {
                    display: flex;
                    justify-content: center;
                    gap: 2rem;
                    flex-wrap: wrap;
                }
                .tournament-card {
                    background-color: var(--bg-color);
                    border: 2px solid var(--accent-primary);
                    border-radius: var(--border-radius);
                    padding: 1.5rem;
                    flex: 1 1 300px;
                    min-width: 280px;
                    color: var(--accent-primary);
                    transition: background-color var(--transition-speed), color var(--transition-speed);
                }
                .tournament-card:hover {
                    background-color: var(--accent-primary);
                    color: var(--card-bg);
                }
                .tournament-card h3 {
                    margin-top: 0;
                }
                .tournament-card p {
                    margin: 0.5rem 0;
                }
                .tournament-card .btn {
                    margin-top: 1rem;
                }
            </style>

            <style>
                .highlights {
                    max-width: 1200px;
                    margin: 2rem auto;
                    padding: 1rem;
                    text-align: center;
                }
                .highlight-cards {
                    display: flex;
                    justify-content: center;
                    gap: 2rem;
                    flex-wrap: wrap;
                }
                .highlight-card {
                    background-color: var(--card-bg);
                    border-radius: var(--border-radius);
                    box-shadow: var(--shadow-light);
                    padding: 1.5rem;
                    flex: 1 1 250px;
                    min-width: 250px;
                    color: var(--text-primary);
                    transition: box-shadow var(--transition-speed);
                }
                .highlight-card:hover {
                    box-shadow: var(--shadow-dark);
                }
                .highlight-card h3 {
                    margin-top: 0;
                    color: var(--accent-primary);
                }
            </style>

            <!-- Quick Actions Section -->
            <section class="section-header">
                <h2>⚡ Quick Actions</h2>
                <div class="features">
                    <div class="feature">
                        <img src="https://img.icons8.com/ios-filled/100/FFD700/calendar.png" alt="Booking" />
                        <h3>Book Facility</h3>
                        <p>Reserve your preferred facility slots and start competing immediately.</p>
                    </div>
                    <div class="feature">
                        <img src="https://img.icons8.com/ios-filled/100/FF6B35/maintenance.png" alt="Report" />
                        <h3>Report Issues</h3>
                        <p>Help maintain facilities in top condition for everyone's benefit.</p>
                    </div>
                    <div class="feature">
                        <img src="https://img.icons8.com/ios-filled/100/00FF88/leaderboard.png" alt="Leaderboard" />
                        <h3>View Rankings</h3>
                        <p>See where you stand in the competitive leaderboards.</p>
                    </div>
                </div>
            </section>

            <!-- Legacy Forms Section (for backward compatibility) -->
            <section class="recent-activity">
                <h2>🔧 Legacy Tools</h2>
                <div class="activity-grid">
                    <div class="activity-item">
                        <h3>Book a Facility Slot</h3>
                        <form id="bookingForm" class="legacy-form">
                            <label for="user_id">User ID:</label>
                            <input type="number" id="user_id" name="user_id" required />
                            
                            <label for="facility_id">Facility ID:</label>
                            <input type="number" id="facility_id" name="facility_id" required />
                            
                            <label for="slot">Slot (YYYY-MM-DD HH:MM:SS):</label>
                            <input type="text" id="slot" name="slot" required />
                            
                            <button type="submit" class="btn legacy-btn">Book Slot</button>
                        </form>
                    </div>

                    <div class="activity-item gold">
                        <h3>Report Maintenance Issue</h3>
                        <form id="reportForm" class="legacy-form">
                            <label for="facility_id_report">Facility ID:</label>
                            <input type="number" id="facility_id_report" name="facility_id_report" required />
                            
                            <label for="photo_url">Photo URL:</label>
                            <input type="text" id="photo_url" name="photo_url" />
                            
                            <button type="submit" class="btn legacy-btn">Report Issue</button>
                        </form>
                    </div>
                </div>
            </section>

            <style>
                .legacy-form {
                    display: flex;
                    flex-wrap: wrap;
                    align-items: center;
                    gap: 0.5rem 1rem;
                }
                .legacy-form label {
                    flex: 0 0 auto;
                    font-weight: 600;
                    color: var(--text-primary);
                }
                .legacy-form input {
                    flex: 1 1 150px;
                    padding: 0.5rem 0.75rem;
                    border-radius: var(--border-radius);
                    border: 1px solid var(--accent-secondary);
                    background: var(--card-bg);
                    color: var(--text-primary);
                    font-size: 1rem;
                    transition: border-color var(--transition-speed);
                }
                .legacy-form input:focus {
                    outline: none;
                    border-color: var(--accent-primary);
                }
                .legacy-btn {
                    background-color: var(--btn-bg);
                    color: white;
                    padding: 0.75rem 1.5rem;
                    font-weight: 700;
                    font-size: 1.1rem;
                    border: none;
                    border-radius: var(--border-radius);
                    cursor: pointer;
                    box-shadow: 0 0 15px var(--btn-bg);
                    transition: background-color var(--transition-speed), box-shadow var(--transition-speed);
                    flex: 0 0 auto;
                    margin-left: 1rem;
                }
                .legacy-btn:hover {
                    background-color: var(--btn-hover-bg);
                    box-shadow: 0 0 25px var(--btn-hover-bg);
                }
                @media (max-width: 600px) {
                    .legacy-form {
                        flex-direction: column;
                        align-items: stretch;
                    }
                    .legacy-btn {
                        margin-left: 0;
                        width: 100%;
                    }
                }
            </style>

            <div id="responseMessage" class="message"></div>
        </main>
        <footer>
            <p>&copy; 2024 UniWebPlay - The Ultimate Competitive Recreational Platform</p>
            <p class="subtitle">Where Champions Are Made</p>
        </footer>
        <script src="script.js"></script>
    </body>
</html>

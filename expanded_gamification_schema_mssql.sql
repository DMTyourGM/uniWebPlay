-- Expanded Gamification and Facility Management Schema for UniWebPlay (SQL Server syntax)

CREATE TABLE facility_categories (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
    description NVARCHAR(MAX) NULL,
    created_at DATETIME2 DEFAULT GETDATE()
);

CREATE TABLE facilities (
    id INT IDENTITY(1,1) PRIMARY KEY,
    category_id INT NOT NULL,
    name NVARCHAR(100) NOT NULL,
    description NVARCHAR(MAX) NULL,
    location NVARCHAR(255) NULL,
    capacity INT NULL,
    created_at DATETIME2 DEFAULT GETDATE(),
    CONSTRAINT FK_facilities_category FOREIGN KEY (category_id) REFERENCES facility_categories(id) ON DELETE CASCADE
);

CREATE TABLE user_stats (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    total_points INT DEFAULT 0,
    total_bookings INT DEFAULT 0,
    total_competitions INT DEFAULT 0,
    skill_level_id INT NULL,
    last_updated DATETIME2 DEFAULT GETDATE(),
    CONSTRAINT FK_user_stats_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT FK_user_stats_skill_level FOREIGN KEY (skill_level_id) REFERENCES skill_levels(id) ON DELETE SET NULL
);

CREATE TABLE skill_levels (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
    description NVARCHAR(MAX) NULL,
    min_points INT NOT NULL,
    max_points INT NOT NULL,
    created_at DATETIME2 DEFAULT GETDATE()
);

CREATE TABLE leaderboards (
    id INT IDENTITY(1,1) PRIMARY KEY,
    competition_id INT NULL,
    user_id INT NOT NULL,
    rank INT NOT NULL,
    points INT NOT NULL,
    updated_at DATETIME2 DEFAULT GETDATE(),
    CONSTRAINT FK_leaderboards_competition FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    CONSTRAINT FK_leaderboards_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE challenges (
    id INT IDENTITY(1,1) PRIMARY KEY,
    creator_user_id INT NOT NULL,
    name NVARCHAR(100) NOT NULL,
    description NVARCHAR(MAX) NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at DATETIME2 DEFAULT GETDATE(),
    CONSTRAINT FK_challenges_creator FOREIGN KEY (creator_user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE badges (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
    description NVARCHAR(MAX) NULL,
    icon_url NVARCHAR(255) NULL,
    created_at DATETIME2 DEFAULT GETDATE()
);

CREATE TABLE user_badges (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    badge_id INT NOT NULL,
    awarded_at DATETIME2 DEFAULT GETDATE(),
    CONSTRAINT FK_user_badges_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT FK_user_badges_badge FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE
);

CREATE TABLE tournaments (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
    description NVARCHAR(MAX) NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at DATETIME2 DEFAULT GETDATE()
);

CREATE TABLE user_activity_log (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type NVARCHAR(100) NOT NULL,
    activity_description NVARCHAR(MAX) NULL,
    activity_date DATETIME2 DEFAULT GETDATE(),
    CONSTRAINT FK_user_activity_log_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

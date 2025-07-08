-- Gamification tables for UniWebPlay (SQL Server syntax)

CREATE TABLE user_points (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    points INT DEFAULT 0,
    last_updated DATETIME2 DEFAULT GETDATE(),
    CONSTRAINT FK_user_points_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE achievements (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
    description NVARCHAR(MAX),
    points_awarded INT DEFAULT 0,
    created_at DATETIME2 DEFAULT GETDATE()
);

CREATE TABLE user_achievements (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    achievement_id INT NOT NULL,
    achieved_at DATETIME2 DEFAULT GETDATE(),
    CONSTRAINT FK_user_achievements_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT FK_user_achievements_achievements FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE
);

CREATE TABLE competitions (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
    description NVARCHAR(MAX),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at DATETIME2 DEFAULT GETDATE()
);

CREATE TABLE competition_participants (
    id INT IDENTITY(1,1) PRIMARY KEY,
    competition_id INT NOT NULL,
    user_id INT NOT NULL,
    points_earned INT DEFAULT 0,
    CONSTRAINT FK_competition_participants_competitions FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    CONSTRAINT FK_competition_participants_users FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

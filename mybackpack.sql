-- ============================================
-- DATABASE MYBACKPACK - VERSIONE CORRETTA
-- ============================================

DROP DATABASE IF EXISTS mybackpack;
CREATE DATABASE mybackpack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mybackpack;

-- ============================================
-- TABELLE
-- ============================================

CREATE TABLE utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    data_registrazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_accesso TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE libri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titolo VARCHAR(200) NOT NULL,
    autore VARCHAR(100),
    genere VARCHAR(50),
    stato ENUM('da_leggere', 'in_lettura', 'completato') DEFAULT 'da_leggere',
    voto INT CHECK (voto >= 1 AND voto <= 5),
    preferito BOOLEAN DEFAULT FALSE,
    prezzo DECIMAL(10,2) DEFAULT 0.00,
    data_aggiunta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utenti(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_stato (stato),
    INDEX idx_genere (genere)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE multimedia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titolo VARCHAR(200) NOT NULL,
    tipo ENUM('video', 'audio', 'immagine', 'podcast', 'altro') DEFAULT 'altro',
    link VARCHAR(500),
    categoria VARCHAR(50),
    descrizione TEXT,
    preferito BOOLEAN DEFAULT FALSE,
    data_aggiunta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utenti(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE giochi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nome VARCHAR(200) NOT NULL,
    piattaforma VARCHAR(50),
    genere VARCHAR(50),
    stato ENUM('da_giocare', 'in_corso', 'completato') DEFAULT 'da_giocare',
    voto INT CHECK (voto >= 1 AND voto <= 5),
    preferito BOOLEAN DEFAULT FALSE,
    data_aggiunta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utenti(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_stato (stato)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE documenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titolo VARCHAR(200) NOT NULL,
    tipo VARCHAR(50),
    descrizione TEXT,
    file_path VARCHAR(500),
    importante BOOLEAN DEFAULT FALSE,
    data_aggiunta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utenti(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_importante (importante)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE note (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titolo VARCHAR(200),
    contenuto TEXT NOT NULL,
    colore ENUM('yellow', 'green', 'blue', 'pink', 'orange', 'purple') DEFAULT 'yellow',
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utenti(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERIMENTO UTENTI
-- Password: "password" per entrambi
-- ============================================

INSERT INTO utenti (username, email, password) VALUES 
('admin', 'admin@mybackpack.it', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('user', 'user@mybackpack.it', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('demo', 'demo@mybackpack.it', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ============================================
-- DATI DI ESEMPIO (user_id = 1 = admin)
-- ============================================

-- LIBRI
INSERT INTO libri (user_id, titolo, autore, genere, stato, voto, preferito, prezzo) VALUES
(1, 'Il Signore degli Anelli', 'J.R.R. Tolkien', 'Fantasy', 'completato', 5, 1, 25.90),
(1, '1984', 'George Orwell', 'Distopico', 'completato', 5, 1, 15.00),
(1, 'Harry Potter e la Pietra Filosofale', 'J.K. Rowling', 'Fantasy', 'in_lettura', 4, 1, 20.00),
(1, 'Il Nome della Rosa', 'Umberto Eco', 'Giallo', 'da_leggere', NULL, 0, 18.50),
(1, 'Orgoglio e Pregiudizio', 'Jane Austen', 'Romantico', 'da_leggere', NULL, 0, 12.00),
(1, 'Il Piccolo Principe', 'Antoine de Saint-Exupéry', 'Favola', 'completato', 5, 1, 10.00),
(1, 'Fahrenheit 451', 'Ray Bradbury', 'Fantascienza', 'in_lettura', 4, 0, 14.90),
(1, 'La Divina Commedia', 'Dante Alighieri', 'Classico', 'da_leggere', NULL, 0, 22.00);

-- MULTIMEDIA
INSERT INTO multimedia (user_id, titolo, tipo, link, categoria, preferito) VALUES
(1, 'Corso PHP Completo', 'video', 'https://www.youtube.com/watch?v=example1', 'Programmazione', 1),
(1, 'Tutorial JavaScript', 'video', 'https://www.youtube.com/watch?v=example2', 'Programmazione', 1),
(1, 'Playlist Musica Studio', 'audio', 'https://open.spotify.com/playlist/example', 'Musica', 0),
(1, 'Podcast Filosofia', 'podcast', 'https://podcast.example.com', 'Educazione', 1),
(1, 'Documentario Storia', 'video', 'https://www.youtube.com/watch?v=example3', 'Storia', 0),
(1, 'Tutorial CSS', 'video', 'https://www.youtube.com/watch?v=example4', 'Programmazione', 1);

-- GIOCHI
INSERT INTO giochi (user_id, nome, piattaforma, genere, stato, voto, preferito) VALUES
(1, 'The Last of Us Part II', 'PlayStation 5', 'Action-Adventure', 'completato', 5, 1),
(1, 'Elden Ring', 'PC', 'RPG', 'in_corso', 5, 1),
(1, 'The Legend of Zelda: Breath of the Wild', 'Nintendo Switch', 'Action-Adventure', 'completato', 5, 1),
(1, 'Cyberpunk 2077', 'Xbox Series X', 'RPG', 'da_giocare', NULL, 0),
(1, 'God of War Ragnarök', 'PlayStation 5', 'Action', 'in_corso', 4, 1),
(1, 'Red Dead Redemption 2', 'PC', 'Action-Adventure', 'completato', 5, 1),
(1, 'Minecraft', 'PC', 'Sandbox', 'in_corso', 4, 0),
(1, 'Stray', 'PlayStation 5', 'Adventure', 'da_giocare', NULL, 0);

-- DOCUMENTI
INSERT INTO documenti (user_id, titolo, tipo, descrizione, importante) VALUES
(1, 'Appunti Matematica', 'PDF', 'Appunti completi del corso di matematica avanzata', 1),
(1, 'Tesina Storia', 'Word', 'Bozza della tesina sulla Seconda Guerra Mondiale', 1),
(1, 'Curriculum Vitae', 'PDF', 'CV aggiornato 2024', 1),
(1, 'Ricette Preferite', 'Word', 'Raccolta di ricette personali', 0),
(1, 'Lista Compiti', 'Excel', 'Elenco compiti e scadenze', 1),
(1, 'Progetto Informatica', 'ZIP', 'Codice sorgente del progetto finale', 1);

-- NOTE
INSERT INTO note (user_id, titolo, contenuto, colore) VALUES
(1, 'Ricorda!', 'Studiare per il compito di matematica di venerdì!', 'yellow'),
(1, 'Progetto Scuola', 'Completare il progetto di informatica entro la prossima settimana', 'orange'),
(1, 'Shopping', 'Comprare quaderni nuovi per la scuola', 'green'),
(1, 'Idee Tesi', 'Possibili argomenti: AI, Blockchain, IoT', 'blue'),
(1, 'Compleanno Marco', 'Il 15 del mese! Organizzare festa', 'pink'),
(1, 'Film da Vedere', 'Inception, Interstellar, The Matrix', 'purple');

-- ============================================
-- MESSAGGI FINALI
-- ============================================

SELECT '✅ Database creato con successo!' AS STATUS;
SELECT '' AS '';
SELECT '🔐 CREDENZIALI DI ACCESSO:' AS INFO;
SELECT '' AS '';
SELECT 'Username: admin    | Password: password' AS ACCOUNT_1;
SELECT 'Username: user     | Password: password' AS ACCOUNT_2;
SELECT 'Username: demo     | Password: password' AS ACCOUNT_3;
SELECT '' AS '';
SELECT '📊 DATI CARICATI:' AS RIEPILOGO;
SELECT '- 8 Libri di esempio' AS DATO_1;
SELECT '- 6 Contenuti multimediali' AS DATO_2;
SELECT '- 8 Videogiochi' AS DATO_3;
SELECT '- 6 Documenti' AS DATO_4;
SELECT '- 6 Note rapide' AS DATO_5;

/*credenziali: admin e password*/
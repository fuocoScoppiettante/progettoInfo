DROP DATABASE IF EXISTS mybackpack;
CREATE DATABASE mybackpack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mybackpack;

-- UTENTI con tipologia admin/user
CREATE TABLE utenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    tipologia ENUM('admin', 'user') DEFAULT 'user',
    data_registrazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- LIBRI (senza prezzo)
CREATE TABLE libri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titolo VARCHAR(200) NOT NULL,
    autore VARCHAR(100),
    genere VARCHAR(50),
    stato ENUM('da_leggere', 'in_lettura', 'completato') DEFAULT 'da_leggere',
    voto INT CHECK (voto >= 1 AND voto <= 5),
    preferito BOOLEAN DEFAULT FALSE,
    immagine VARCHAR(500),
    data_aggiunta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utenti(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_genere (genere)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- MULTIMEDIA
CREATE TABLE multimedia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titolo VARCHAR(200) NOT NULL,
    tipo ENUM('video', 'audio', 'immagine', 'podcast', 'altro') DEFAULT 'altro',
    link VARCHAR(500),
    categoria VARCHAR(50),
    preferito BOOLEAN DEFAULT FALSE,
    data_aggiunta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utenti(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- GIOCHI
CREATE TABLE giochi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nome VARCHAR(200) NOT NULL,
    piattaforma VARCHAR(50),
    genere VARCHAR(50),
    stato ENUM('da_giocare', 'in_corso', 'completato') DEFAULT 'da_giocare',
    voto INT CHECK (voto >= 1 AND voto <= 5),
    preferito BOOLEAN DEFAULT FALSE,
    immagine VARCHAR(500),
    data_aggiunta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utenti(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_genere (genere)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- NOTE con scadenza
CREATE TABLE note (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titolo VARCHAR(200),
    contenuto TEXT NOT NULL,
    colore ENUM('yellow','green','blue','pink','orange','purple') DEFAULT 'yellow',
    priorita ENUM('bassa','media','alta','urgente') DEFAULT 'media',
    data_scadenza DATETIME,
    completata BOOLEAN DEFAULT FALSE,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utenti(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- OBIETTIVI
CREATE TABLE obiettivi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titolo VARCHAR(200) NOT NULL,
    descrizione TEXT,
    categoria ENUM('studio','lettura','gaming','personale','altro') DEFAULT 'studio',
    priorita ENUM('bassa','media','alta','urgente') DEFAULT 'media',
    completato BOOLEAN DEFAULT FALSE,
    data_scadenza DATE,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_completamento TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES utenti(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- EVENTI CALENDARIO
CREATE TABLE eventi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    titolo VARCHAR(200) NOT NULL,
    descrizione TEXT,
    tipo ENUM('verifica','compito','lezione','evento','promemoria','altro') DEFAULT 'evento',
    data_evento DATE NOT NULL,
    ora_inizio TIME,
    ora_fine TIME,
    materia VARCHAR(100),
    colore VARCHAR(7) DEFAULT '#dc143c',
    completato BOOLEAN DEFAULT FALSE,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utenti(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_data (data_evento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- UTENTI DI DEFAULT
INSERT INTO utenti (username, email, password, tipologia) VALUES
('admin', 'admin@mybackpack.it', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('user', 'user@mybackpack.it', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- DATI ESEMPIO LIBRI
INSERT INTO libri (user_id, titolo, autore, genere, stato, voto, preferito, immagine) VALUES
(1, 'Il Signore degli Anelli', 'J.R.R. Tolkien', 'Fantasy', 'completato', 5, 1, 'https://covers.openlibrary.org/b/id/8406786-L.jpg'),
(1, '1984', 'George Orwell', 'Distopico', 'completato', 5, 1, 'https://covers.openlibrary.org/b/id/7222246-L.jpg'),
(1, 'Harry Potter', 'J.K. Rowling', 'Fantasy', 'in_lettura', 4, 1, 'https://covers.openlibrary.org/b/id/10110415-L.jpg'),
(1, 'Il Nome della Rosa', 'Umberto Eco', 'Giallo', 'da_leggere', NULL, 0, 'https://covers.openlibrary.org/b/id/8234196-L.jpg'),
(1, 'Fahrenheit 451', 'Ray Bradbury', 'Fantascienza', 'in_lettura', 4, 0, 'https://covers.openlibrary.org/b/id/6735171-L.jpg'),
(1, 'La Divina Commedia', 'Dante Alighieri', 'Classico', 'da_leggere', NULL, 0, 'https://covers.openlibrary.org/b/id/10387188-L.jpg');

-- DATI ESEMPIO GIOCHI
INSERT INTO giochi (user_id, nome, piattaforma, genere, stato, voto, preferito, immagine) VALUES
(1, 'The Last of Us Part II', 'PlayStation 5', 'Action', 'completato', 5, 1, 'https://image.api.playstation.com/vulcan/ap/rnd/202010/0222/niMUubpU9y1PnESfmQPVMKOh.png'),
(1, 'Elden Ring', 'PC', 'RPG', 'in_corso', 5, 1, 'https://image.api.playstation.com/vulcan/ap/rnd/202110/2000/phvVT0qZfcRms5qDAk0SI3CM.png'),
(1, 'God of War Ragnarok', 'PlayStation 5', 'Action', 'in_corso', 4, 1, 'https://image.api.playstation.com/vulcan/ap/rnd/202207/1210/4xJ8XB3bi888QTLZYdl7Oi0s.png'),
(1, 'Minecraft', 'PC', 'Sandbox', 'in_corso', 4, 0, 'https://image.api.playstation.com/vulcan/img/cfn/11307uYG0CXzRuA9aryByTHYfRL3FZJVRFLV35iwKsBRAuEGMk.png'),
(1, 'Cyberpunk 2077', 'Xbox', 'RPG', 'da_giocare', NULL, 0, 'https://image.api.playstation.com/vulcan/ap/rnd/202111/3013/cKZ4tKNFj9C00giTzYtH8PF1.png');

-- DATI ESEMPIO NOTE con scadenze
INSERT INTO note (user_id, titolo, contenuto, colore, priorita, data_scadenza) VALUES
(1, 'Verifica Matematica', 'Studiare integrali e derivate cap. 5-8', 'yellow', 'urgente', DATE_ADD(NOW(), INTERVAL 2 DAY)),
(1, 'Progetto Informatica', 'Completare MyBackpack', 'orange', 'alta', DATE_ADD(NOW(), INTERVAL 5 DAY)),
(1, 'Comprare Quaderni', 'Servono 3 quaderni a righe', 'green', 'bassa', DATE_ADD(NOW(), INTERVAL 10 DAY)),
(1, 'Film da Vedere', 'Inception, Interstellar, Matrix', 'blue', 'media', NULL),
(1, 'SCADENZA VICINA', 'Questa nota scade tra poco!', 'pink', 'urgente', DATE_ADD(NOW(), INTERVAL 1 DAY));

-- DATI ESEMPIO OBIETTIVI
INSERT INTO obiettivi (user_id, titolo, descrizione, categoria, priorita, data_scadenza) VALUES
(1, 'Leggere 5 libri', 'Obiettivo mensile di lettura', 'lettura', 'media', DATE_ADD(CURDATE(), INTERVAL 30 DAY)),
(1, 'Studiare per la verifica', 'Capitoli 5-8 matematica', 'studio', 'urgente', DATE_ADD(CURDATE(), INTERVAL 3 DAY)),
(1, 'Completare Elden Ring', 'Battere tutti i boss', 'gaming', 'bassa', NULL);

-- DATI ESEMPIO EVENTI
INSERT INTO eventi (user_id, titolo, tipo, data_evento, ora_inizio, ora_fine, materia, colore) VALUES
(1, 'Verifica Matematica', 'verifica', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '09:00', '10:00', 'Matematica', '#dc143c'),
(1, 'Consegna Progetto', 'compito', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '23:59', NULL, 'Informatica', '#ff4757'),
(1, 'Interrogazione', 'verifica', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '11:00', '12:00', 'Storia', '#1a1a1a');

SELECT '✅ Database creato!' AS STATUS;
SELECT 'Admin => admin / password' AS ACCOUNT_1;
SELECT 'User  => user / password' AS ACCOUNT_2;

/*credenziali: admin e password*/

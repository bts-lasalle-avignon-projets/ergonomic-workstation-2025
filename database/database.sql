CREATE DATABASE IF NOT EXISTS ergonomic_workstation;
USE ergonomic_workstation;

CREATE TABLE IF NOT EXISTS Processus (
    idProcessus INT AUTO_INCREMENT PRIMARY KEY,
    nomProcessus VARCHAR(255) NOT NULL,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS Etapes (
    idEtapes INT AUTO_INCREMENT PRIMARY KEY,
    idProcessus INT NOT NULL,
    idBac INT,
    descriptionEtape TEXT NOT NULL,
    FOREIGN KEY (idProcessus) REFERENCES Processus(idProcessus) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Image (
    idImage INT AUTO_INCREMENT PRIMARY KEY,
    nomFichier VARCHAR(255) NOT NULL,
    typeMIME VARCHAR(50) NOT NULL,
    contenuBlob MEDIUMBLOB NOT NULL,  -- 16 Mo max pour le contenu de l'image
    tailleImage INT UNSIGNED NOT NULL,
    idProcessus INT DEFAULT NULL,
    idEtapes INT DEFAULT NULL,
    typeImage ENUM('processus', 'etape') NOT NULL,
    FOREIGN KEY (idProcessus) REFERENCES Processus(idProcessus) ON DELETE CASCADE,
    FOREIGN KEY (idEtapes) REFERENCES Etapes(idEtapes) ON DELETE CASCADE,
    CHECK ( (idProcessus IS NOT NULL AND idEtapes IS NULL) OR (idProcessus IS NULL AND idEtapes IS NOT NULL) )
);
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
    descriptionEtape TEXT NOT NULL,
    FOREIGN KEY (idProcessus) REFERENCES Processus(idProcessus) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Image (
    idImage INT AUTO_INCREMENT PRIMARY KEY,
    idEtapes INT NOT NULL,
    nomFichier VARCHAR(255) NOT NULL,
    typeMIME VARCHAR(50) NOT NULL,
    contenuBlob MEDIUMBLOB NOT NULL, -- 16Mo
    tailleImage INT UNSIGNED NOT NULL,
    FOREIGN KEY (idEtapes) REFERENCES Etapes(idEtapes) ON DELETE CASCADE
);
CREATE DATABASE IF NOT EXISTS ergonomic_workstation;
USE ergonomic_workstation;

CREATE TABLE IF NOT EXISTS Processus (
    idProcessus INT AUTO_INCREMENT PRIMARY KEY,
    nomProcessus VARCHAR(255) NOT NULL,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    idImage INT
);

CREATE TABLE IF NOT EXISTS Bac (
    idBac INT AUTO_INCREMENT PRIMARY KEY,
    contenance VARCHAR(255) NOT NULL,
    numeroBac INT
);

CREATE TABLE IF NOT EXISTS Etapes (
    idEtapes INT AUTO_INCREMENT PRIMARY KEY,
    idProcessus INT NOT NULL,
    idBac INT NOT NULL,
    nomEtape VARCHAR(255) NOT NULL,
    descriptionEtape TEXT NOT NULL,
    idImage INT,
    FOREIGN KEY (idProcessus) REFERENCES Processus(idProcessus) ON DELETE CASCADE,
    FOREIGN KEY (idBac) REFERENCES Bac(idBac) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Image (
    idImage INT AUTO_INCREMENT PRIMARY KEY,
    nomFichier VARCHAR(255) NOT NULL,
    typeMIME VARCHAR(50) NOT NULL,
    contenuBlob MEDIUMBLOB NOT NULL,  -- 16 Mo max pour le contenu de l'image
    tailleImage INT UNSIGNED NOT NULL
);
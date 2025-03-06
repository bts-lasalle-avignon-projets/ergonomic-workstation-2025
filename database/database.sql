-- Création de la base de données (si elle n'existe pas déjà)
CREATE DATABASE IF NOT EXISTS ergonomic_workstation;
USE ergonomic_workstation;

-- Table Processus
CREATE TABLE IF NOT EXISTS Processus (
    idProcessus INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table Etapes
CREATE TABLE IF NOT EXISTS Etapes (
    idEtape INT AUTO_INCREMENT PRIMARY KEY,
    idProcessus INT NOT NULL,
    texte TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    ordre INT NOT NULL,
    FOREIGN KEY (idProcessus) REFERENCES Processus(idProcessus) ON DELETE CASCADE
);
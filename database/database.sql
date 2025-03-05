-- Création de la base de données (si elle n'existe pas déjà)
CREATE DATABASE IF NOT EXISTS ergonomic_workstation;
USE ergonomic_workstation;

-- Table Processus
CREATE TABLE IF NOT EXISTS Processus (
    idProcessus INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
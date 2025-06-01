CREATE DATABASE IF NOT EXISTS ergonomic_workstation;
USE ergonomic_workstation;

DROP TABLE IF EXISTS Superviseur;
DROP TABLE IF EXISTS Etape;
DROP TABLE IF EXISTS Bac;
SET foreign_key_checks = 0;
DROP TABLE IF EXISTS Processus;
SET foreign_key_checks = 1;
DROP TABLE IF EXISTS Image;
DROP TABLE IF EXISTS Assemblage;
DROP TABLE IF EXISTS EtatProcessus;

CREATE TABLE IF NOT EXISTS Superviseur(
    idSuperviseur INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS Image (
    idImage INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nomFichier VARCHAR(255) NOT NULL,
    typeMIME VARCHAR(50) NOT NULL,
    contenuBlob MEDIUMBLOB NOT NULL,  -- 16 Mo max pour le contenu de l'image
    tailleImage INT UNSIGNED NOT NULL
);

CREATE TABLE IF NOT EXISTS Processus (
    idProcessus INT AUTO_INCREMENT PRIMARY KEY,
    nomProcessus VARCHAR(255) NOT NULL,
    descriptionProcessus TEXT,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    idImage INT UNSIGNED DEFAULT NULL,
    CONSTRAINT FK_ProcessusImage FOREIGN KEY (idImage) REFERENCES Image(idImage) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS Bac (
    numeroBac INT NOT NULL,
    contenance VARCHAR(255) NOT NULL,
    idProcessus INT NOT NULL,
    CONSTRAINT PK_Bac PRIMARY KEY (numeroBac,idProcessus),
    CONSTRAINT FK_ProcessusBac FOREIGN KEY (idProcessus) REFERENCES Processus(idProcessus) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Etape (
    idEtape INT AUTO_INCREMENT PRIMARY KEY,
    idProcessus INT NOT NULL,
    idBac INT NOT NULL,
    nomEtape VARCHAR(255) NOT NULL,
    numeroEtape INT,
    descriptionEtape TEXT NOT NULL,
    idImage INT UNSIGNED DEFAULT NULL,
    CONSTRAINT FK_EtapeProcessus FOREIGN KEY (idProcessus) REFERENCES Processus(idProcessus) ON DELETE CASCADE,
    CONSTRAINT FK_EtapeBac FOREIGN KEY (idBac,idProcessus) REFERENCES Bac(numeroBac,idProcessus) ON DELETE CASCADE,
    CONSTRAINT FK_EtapeImage FOREIGN KEY (idImage) REFERENCES Image(idImage) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS Assemblage (
    idAssemblage INT AUTO_INCREMENT PRIMARY KEY,
    idProcessus INT NOT NULL,
    nombreEchecs INT NOT NULL DEFAULT 0,
    dureeProcessus TIME DEFAULT NULL,
    dateStatistique TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT FK_Assemblage FOREIGN KEY (idProcessus) REFERENCES Processus(idProcessus) ON DELETE CASCADE
);


CREATE TABLE EtatProcessus (
    idProcessus INTEGER PRIMARY KEY,
    idEtapeActuelle INTEGER,
    dateDerniereModification DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Simuler deux processus si non déjà existants
INSERT INTO Processus (nomProcessus, descriptionProcessus) VALUES
('Montage écran', 'Processus de montage d’un écran d’ordinateur'),
('Assemblage clavier', 'Processus d’assemblage d’un clavier mécanique');

-- Insérer des exécutions d’assemblage avec des erreurs (nombreEchecs)
INSERT INTO Assemblage (idProcessus, nombreEchecs, dureeProcessus, dateStatistique) VALUES
(1, 5, '00:40:00', '2025-05-01 08:00:00'),
(1, 0, '00:35:00', '2025-05-01 10:00:00'),
(1, 1, '00:45:00', '2025-05-02 09:00:00'),
(2, 3, '00:30:00', '2025-05-01 11:00:00'),
(2, 1, '00:28:00', '2025-05-02 08:30:00'),
(2, 0, '00:25:00', '2025-05-03 14:00:00');

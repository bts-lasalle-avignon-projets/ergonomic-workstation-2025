CREATE DATABASE IF NOT EXISTS ergonomic_workstation;
USE ergonomic_workstation;


DROP TABLE IF EXISTS Etape;
DROP TABLE IF EXISTS Bac;
DROP TABLE IF EXISTS Processus;
DROP TABLE IF EXISTS Image;

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

CREATE TABLE IF NOT EXISTS StatistiqueProcessus (
    idStatistique INT AUTO_INCREMENT PRIMARY KEY,
    idProcessus INT NOT NULL,
    nombreExecutions INT NOT NULL DEFAULT 0,
    nombreReussites INT NOT NULL DEFAULT 0,
    nombreEchecs INT NOT NULL DEFAULT 0,
    dureeProcessus TIME DEFAULT NULL,
    dateStatistique TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT FK_StatistiqueProcessus FOREIGN KEY (idProcessus) REFERENCES Processus(idProcessus) ON DELETE CASCADE
);


-- Simulation des données pour les statistiques 
INSERT INTO Processus (nomProcessus, descriptionProcessus) VALUES
('Montage écran', 'Processus de montage d’un écran d’ordinateur'),
('Assemblage clavier', 'Processus d’assemblage d’un clavier mécanique');
INSERT INTO StatistiqueProcessus (idProcessus, nombreExecutions, nombreReussites, nombreEchecs, dureeProcessus)
VALUES
(1, 20, 15, 5, '00:45:00'),
(2, 10, 8, 2, '00:25:00');
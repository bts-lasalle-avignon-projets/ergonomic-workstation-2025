CREATE DATABASE IF NOT EXISTS ergonomic_workstation;
USE ergonomic_workstation;

DROP TABLE IF EXISTS Image;
DROP TABLE IF EXISTS Etape;
DROP TABLE IF EXISTS Bac;
DROP TABLE IF EXISTS Processus;

CREATE TABLE IF NOT EXISTS Processus (
    idProcessus INT AUTO_INCREMENT PRIMARY KEY,
    nomProcessus VARCHAR(255) NOT NULL,
    descriptionProcessus TEXT,
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    idImage INT
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
    idImage INT FOREIGN KEY,
    CONSTRAINT FK_EtapeProcessus FOREIGN KEY (idProcessus) REFERENCES Processus(idProcessus) ON DELETE CASCADE,
    CONSTRAINT FK_EtapeBac FOREIGN KEY (idBac,idProcessus) REFERENCES Bac(numeroBac,idProcessus) ON DELETE CASCADE,
    CONSTRAINT FK_EtapeImage FOREIGN KEY (idImage) REFERENCES Image(idImage) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS Image (
    idImage INT AUTO_INCREMENT PRIMARY KEY,
    nomFichier VARCHAR(255) NOT NULL,
    typeMIME VARCHAR(50) NOT NULL,
    contenuBlob MEDIUMBLOB NOT NULL,  -- 16 Mo max pour le contenu de l'image
    tailleImage INT UNSIGNED NOT NULL
);

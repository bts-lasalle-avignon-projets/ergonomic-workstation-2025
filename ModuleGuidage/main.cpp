/**
 * @file main.cpp
 * @brief Programme principal
 * @details Initialise l'application, la communication Bluetooth, et lance la fenêtre principale.
 * @author BOUSQUET-SOLFRINI Valentin
 * @version 1.1
 */

#include "FenetreDemarrage.h"
#include "Communication.h"
#include <QApplication>
#include <QFile>
#include <QDebug>

int main(int argc, char* argv[])
{
    QApplication a(argc, argv);

    // Initialisation de la communication Bluetooth
    Communication* communication = new Communication();
    if (!communication->connecter())
    {
        qCritical() << "Erreur : Impossible de se connecter à l'ESP32 via Bluetooth.";
        delete communication;
        return -1;
    }

    // Création de la fenêtre principale en injectant la communication
    FenetreDemarrage* fenetreDemarrage = new FenetreDemarrage(communication, nullptr);

    // Chargement de la feuille de style (QSS)
    QFile fichier(":/qss/ModuleGuidage.qss");
    if (fichier.open(QFile::ReadOnly))
    {
        QString feuilleStyle = QLatin1String(fichier.readAll());
        a.setStyleSheet(feuilleStyle);
    }

    fenetreDemarrage->show();

    int retour = a.exec();

    // Nettoyage mémoire
    delete fenetreDemarrage;
    delete communication;

    return retour;
}


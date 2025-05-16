/**
 * @file main.cpp
 *
 * @brief Programme principal
 * @author BOUSQUET-SOLFRINI Valentin
 * @version 1.0
 */
#include "FenetreDemarrage.h"
#include "Communication.h"
#include <QApplication>
#include <QFile>

/**
 * @brief Programme principal
 * @details Crée et affiche la fenêtre principale de l'application
 *
 * @fn main
 * @param argc
 * @param argv[]
 * @return int
 *
 */
int main(int argc, char* argv[])
{
    QApplication a(argc, argv);

    Communication communication;
    if (!communication.connecter())  // Pas de paramètre ici
    {
        qCritical() << "Erreur : Impossible de se connecter à l'ESP32 via Bluetooth.";
        return -1;
    }

    FenetreDemarrage fenetreDemarrage(&communication);

    QFile fichier(":/qss/ModuleGuidage.qss");
    if (fichier.open(QFile::ReadOnly))
    {
        QString feuilleStyle = QLatin1String(fichier.readAll());
        a.setStyleSheet(feuilleStyle);
    }

    fenetreDemarrage.show();

    return a.exec();
}

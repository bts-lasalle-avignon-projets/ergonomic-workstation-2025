/**
 * @file main.cpp
 *
 * @brief Programme principal
 * @author BOUSQUET-SOLFRINI Valentin
 * @version 1.0
 */
#include "FenetreDemarrage.h"
#include <QApplication>

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

    FenetreDemarrage fenetreDemarrage;

    QFile fichier(":/qss/ModuleGuidage.qss");
    if(fichier.open(QFile::ReadOnly))
    {
        QString feuilleStyle = QLatin1String(fichier.readAll());
        a.setStyleSheet(feuilleStyle);
    }

    fenetreDemarrage.show();

    return a.exec();
}

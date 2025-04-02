#include "connexionBDD.h"

QSqlDatabase connexionBDD::db = QSqlDatabase(); // Initialisation statique

QSqlDatabase connexionBDD::getDatabase()
{
    if (!db.isValid()) {
        db = QSqlDatabase::addDatabase("QMYSQL");
        db.setHostName("localhost");
        db.setDatabaseName("ergonomic_workstation");
        db.setUserName("ergoWork");
        db.setPassword("motDePasseSecurise");

        if (!db.open()) {
            qDebug() << "Erreur de connexion à la BDD :" << db.lastError().text();
        } else {
            qDebug() << "Connexion à la BDD réussie !";
        }
    }
    return db;
}

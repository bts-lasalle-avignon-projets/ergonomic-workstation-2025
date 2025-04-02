#include "BaseDeDonnees.h"
#include <QSqlError>
#include <QDebug>

QSqlDatabase BaseDeDonnees::db = QSqlDatabase(); // Initialisation statique

QSqlDatabase BaseDeDonnees::getDatabase()
{
    qDebug() << Q_FUNC_INFO;
    if(!db.isValid())
    {
        db = QSqlDatabase::addDatabase(DRIVER_DATABASE);
        db.setHostName(HOSTNAME);
        db.setDatabaseName(DATABASENAME);
        db.setUserName(USERNAME);
        db.setPassword(PASSWORD);

        if(!db.open())
        {
            qCritical() << "Erreur de connexion à la BDD"
                        << db.lastError().text();
        }
    }
    return db;
}

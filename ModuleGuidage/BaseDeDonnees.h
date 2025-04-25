#ifndef BASEDEDONNEES_H
#define BASEDEDONNEES_H

#include <QSqlDatabase>

/**
 * @def HOSTNAME
 * @brief Définit le driver de la base de données MySQL
 */
#define DRIVER_DATABASE "QMYSQL"

/**
 * @def HOSTNAME
 * @brief Définit l'adresse du serveur SGBD
 */
#define HOSTNAME "192.168.55.16"

/**
 * @def DATABASENAME
 * @brief Définit le nom de la base de données par défaut
 */
#define DATABASENAME "ergonomic_workstation"

/**
 * @def USERNAME
 * @brief Définit le nom d'utilisateur par défaut
 */
#define USERNAME "adminErgoWork"

/**
 * @def PASSWORD
 * @brief Définit le mot de passe par défaut
 */
#define PASSWORD "password"

class BaseDeDonnees
{
  public:
    static QSqlDatabase getDatabase();

  private:
    BaseDeDonnees();
    static QSqlDatabase db;
};

#endif // BASEDEDONNEES_H

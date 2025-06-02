#ifndef ETAPE_H
#define ETAPE_H

#include <QString>
#include <QPair>
#include <QVector>

/**
 * @brief Structure contenant les informations complètes d'une étape
 */
struct Etape
{
    int     idEtape;
    int     numeroEtape;
    QString nomEtape;
    QString descriptionEtape;
    int     numeroBac;
    QString contenance;
    // QVector<QPair<int, QString> > bacs; // (numéro, contenance)
};

/**
 * @enum TableEtape
 * @brief Définit les numéros de colonne de la table Etape
 *
 */
enum TableEtape
{
    TE_ID_ETAPE          = 0,
    TE_ID_PROCESSUS      = 1,
    TE_ID_BAC            = 2,
    TE_NOM_ETAPE         = 3,
    TE_NUMERO_ETAPE      = 4,
    TE_DESCRIPTION_ETAPE = 5,
    TE_ID_IMAGE          = 6,
    NbColonnesTableEtape
};

/**
 * @enum TableBac
 * @brief Définit les numéros de colonne de la table Bac
 *
 */
enum TableBac
{
    TB_NUMERO_BAC = 0,
    TB_CONTENANCE = 1,
    NbColonnesTableBac
};

#endif // ETAPE_H

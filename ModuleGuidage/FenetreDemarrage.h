/**
 * @file FenetreDemarrage.h
 *
 * @brief Déclaration de la classe FenetreDemarrage
 * @author BOUSQUET-SOLFRINI Valentin
 * @version 1.0
 */

#ifndef FENETREDEMARRAGE_H
#define FENETREDEMARRAGE_H

#include "ui_FenetreDemarrage.h"
#include <QtWidgets>
#include <QSqlDatabase>

/**
 * @def APPLICATION
 * @brief Le nom de l'application
 */
#define APPLICATION "ErgonomicWorkstation - ModuleGuidage"

/**
 * @def VERSION
 * @brief La version de l'application
 */
#define VERSION "1.0"

/**
 * @def TI_CONTENU_IMAGE
 * @brief Le numéro de colonne contenuBlob de la Table Image
 */
#define TI_CONTENU_IMAGE 0

#define LARGEUR_IMAGE 1080
#define HAUTEUR_IMAGE 500
namespace Ui
{
class FenetreDemarrage;
}

class FenetreEtapes;

/**
 * @class FenetreDemarrage
 * @brief Déclaration de la classe FenetreDemarrage
 * @details Cette classe gère l'interface graphique de l'application
 */
class FenetreDemarrage : public QMainWindow, public Ui::FenetreDemarrage
{
    Q_OBJECT

  public:
    explicit FenetreDemarrage(QWidget* parent = nullptr);
    ~FenetreDemarrage();

  private:
    void chargerListeProcessus();
    void afficherProcessus();
    void chargerImagePourProcessus(int idProcessus);
    void demarrerProcessus();

    QSqlDatabase db;
    int          etapeActuelIndex;
    int          idProcessusActuel;

    FenetreEtapes* fenetreEtapes; // Pointeur vers la fenêtre des étapes

    /**
     * @enum TableProcessus
     * @brief Définit les numéros de colonne de la table Processus
     *
     */
    enum TableProcessus
    {
        TP_ID_PROCESSUS          = 0,
        TP_NOM_PROCESSUS         = 1,
        TP_DESCRIPTION_PROCESSUS = 2,
        TP_DATE_CREATION         = 3,
        TP_ID_IMAGE              = 4,
        NbColonnesTableProcessus
    };
};

#endif // FENETREDEMARRAGE_H

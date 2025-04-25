#ifndef FENETREETAPES_H
#define FENETREETAPES_H

#include "ui_FenetreEtapes.h"
#include <QtWidgets>
#include <QSqlDatabase>
#include <QPair>
#include <QVector>

/**
 * @brief Structure contenant les informations complètes d'une étape
 */
struct InfosEtape
{
    int idEtape;
    int numeroEtape;
    QString nomEtape;
    QString descriptionEtape;
    QVector<QPair<int, QString>> bacs; // (numéro, contenance)
};

namespace Ui
{
class FenetreEtapes;
}

class FenetreEtapes : public QWidget
{
    Q_OBJECT

  public:
    explicit FenetreEtapes(QWidget* parent = nullptr);
    void chargerEtape(int idProcessus);
    void chargerImagePourEtape(int idEtape);

  protected:
    void showEvent(QShowEvent* event) override;

  private slots:
    void chargerEtapeSuivante();
    void fermerFenetre();

  private:
    void afficherInfosEtapeActuelle();

    QVBoxLayout* layout;
    QLabel*      statusLabel;
    QLabel*      imageLabel;
    QPushButton* boutonEtapeSuivante;
    QPushButton* boutonFermerFenetre;

    QVector<InfosEtape> listeDesEtapes;
    int etapeActuelIndex;
    int idProcessusActuel;

    QSqlDatabase db;
};

#endif // FENETREETAPES_H

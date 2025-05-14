#ifndef FENETREETAPES_H
#define FENETREETAPES_H

#include <QtWidgets>
#include <QSqlDatabase>
#include "Etape.h"

#define LARGEUR_IMAGE_ETAPE 1080
#define HAUTEUR_IMAGE_ETAPE 500

#define MARGE_LAYOUT 25

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

  private:
    void initialiserFenetre();
    void afficherEtapeActuelle();

    QLabel*      labelNumeroEtape;
    QLabel*      labelNomEtape;
    QLabel*      labelDescriptionEtape;
    QLabel*      labelEtatRequete;
    QLabel*      imageEtape;
    QLabel* labelBacNumero;
    QLabel* labelBacContenance;
    QPushButton* boutonEtapeSuivante;

    QVector<Etape> listeDesEtapes;
    int            etapeActuelIndex;
    int            idProcessusActuel;

    QSqlDatabase db;
};

#endif // FENETREETAPES_H

#ifndef FENETREETAPES_H
#define FENETREETAPES_H

#include <QWidget>
#include <QLabel>
#include <QPushButton>
#include <QHBoxLayout>
#include <QVBoxLayout>
#include <QGroupBox>
#include <QSqlDatabase>
#include <QShowEvent>
#include <QVector>
#include <QPair>
#include "Etape.h"

#define LARGEUR_UI 1080
#define HAUTEUR_IMAGE_ETAPE 500

class FenetreEtapes : public QWidget
{
    Q_OBJECT

public:
    explicit FenetreEtapes(QWidget* parent = nullptr);
    void chargerEtape(int idProcessus);

protected:
    void showEvent(QShowEvent* event) override;

private slots:
    void chargerEtapeSuivante();

private:
    void initialiserFenetre();
    void afficherEtapeActuelle();
    void chargerImagePourEtape(int idEtape);

    // Méthodes ajoutées pour organiser le code
    void nettoyerLayoutBacs();
    int recupererBacDeLEtape(int idEtape);
    QVector<QPair<int, QString>> recupererBacsProcessus(int idProcessus);
    void afficherBacs(const QVector<QPair<int, QString>>& bacs, int bacDeLEtape);
    void afficherTexteEtape(const Etape& e);

    // données
    QVector<Etape> listeDesEtapes;
    int etapeActuelIndex;
    int idProcessusActuel;
    QSqlDatabase db;

    // UI
    QLabel* labelNumeroEtape;
    QLabel* labelNomEtape;
    QLabel* labelDescriptionEtape;
    QLabel* labelEtatRequete;
    QLabel* imageEtape;
    QPushButton* boutonEtapeSuivante;

    QHBoxLayout* layoutBacs;
    QVector<QGroupBox*> groupesBacs;     // pour nettoyer
};

#endif // FENETREETAPES_H

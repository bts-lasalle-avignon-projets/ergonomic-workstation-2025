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
#include <QMessageBox>
#include "Etape.h"
#include "Communication.h"

#define LARGEUR_UI 1080
#define HAUTEUR_IMAGE_ETAPE 500

class FenetreEtapes : public QWidget
{
    Q_OBJECT

signals:
    void fermerEtapes();

public:
    explicit FenetreEtapes(Communication* comm, QWidget* parent = nullptr);
    void chargerEtape(int idProcessus);

protected:
    void showEvent(QShowEvent* event) override;

private:
    void initialiserFenetre();
    void afficherEtapeActuelle();
    void chargerImagePourEtape(int idEtape);
    void chargerEtapeSuivante();

    // Méthodes ajoutées pour organiser le code
    void nettoyerLayoutBacs();
    int recupererBacDeLEtape(int idEtape);
    QVector<QPair<int, QString>> recupererBacsProcessus(int idProcessus);
    void afficherBacs(const QVector<QPair<int, QString>>& bacs, int bacDeLEtape);
    void afficherTexteEtape(const Etape& e);
    void afficherPopupDemandePiochage();
    void quitterProcessus();
    void sauvegarderEtatProcessus();

    // données
    QVector<Etape> listeDesEtapes;
    int etapeActuelIndex;
    int idProcessusActuel;
    int recupererIndexDerniereEtape(int idProcessus);
    bool attenteConfirmationPiochage = false;


    // Traiter les trames
    void traiterTrameRecue(const QString &trame);

    QSqlDatabase db;

    // UI
    QLabel* labelNumeroEtape;
    QLabel* labelNomEtape;
    QLabel* labelDescriptionEtape;
    QLabel* labelEtatRequete;
    QLabel* imageEtape;
    QPushButton* boutonEtapeSuivante;
    QPushButton* boutonQuitter;

    QHBoxLayout* layoutBacs;
    QVector<QGroupBox*> groupesBacs;     // pour nettoyer
    Communication *communication;

    QMessageBox* popupPiochage = nullptr;
};

#endif // FENETREETAPES_H

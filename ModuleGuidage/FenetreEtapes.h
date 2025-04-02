#ifndef FENETREETAPES_H
#define FENETREETAPES_H

#include <QtWidgets>
#include <QVector>
#include <QSqlDatabase>

class FenetreEtapes : public QWidget
{
    Q_OBJECT

  public:
    explicit FenetreEtapes(QWidget* parent = nullptr);
    void chargerEtape(int idPprocessus);
    void chargerImagePourEtape(int idEtape);

  protected:
    void showEvent(QShowEvent* event);

  private slots:
    void chargerEtapeSuivante();
    void fermerFenetre();

  private:
    QVBoxLayout* layout;
    QLabel*      statusLabel;
    QLabel*      imageLabel;
    QPushButton* boutonEtapeSuivante;
    QPushButton* boutonFermerFenetre;

    QVector<QString> listeDesEtapes;
    QVector<int>     listeIdEtapes;
    int              etapeActuelIndex;
    int              idProcessusActuel;

    QSqlDatabase db;
};

#endif // FENETREETAPES_H

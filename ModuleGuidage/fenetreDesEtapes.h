#ifndef FENETREDESETAPES_H
#define FENETREDESETAPES_H

#include <QWidget>
#include <QVBoxLayout>
#include <QLabel>
#include <QPushButton>
#include <QVector>
#include <QSqlDatabase>
#include <QSqlQuery>

class fenetreDesEtapes : public QWidget
{
    Q_OBJECT

public:
    explicit fenetreDesEtapes(QWidget *parent = nullptr);
    void chargerEtape(int processusId);
    void chargerImagePourEtape(int idEtape);

private slots:
    void etapeSuivante();

private:
    QVBoxLayout *layout;
    QLabel *statusLabel;
    QLabel *imageLabel;
    QPushButton *boutonEtapeSuivante;

    QVector<QString> listeDesEtapes;
    QVector<int> listeIdEtapes;
    int etapeActuelIndex;
    int idProcessusActuel;

    QSqlDatabase db;
};

#endif // FENETREDESETAPES_H

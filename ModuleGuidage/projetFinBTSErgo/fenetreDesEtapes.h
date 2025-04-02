#ifndef FENETREDESETAPES_H
#define FENETREDESETAPES_H

#include <QWidget>
#include <QPushButton>
#include <QLabel>
#include <QVBoxLayout>
#include <QSqlQuery>
#include <QSqlError>
#include <QStringList>
#include <QDebug>

class fenetreDesEtapes : public QWidget
{
    Q_OBJECT  // Cette macro est nécessaire pour l'utilisation des signaux et des slots dans Qt

public:
    explicit fenetreDesEtapes(QWidget *parent = nullptr);
    void chargerEtape(int processusId);

private slots:
    void etapeSuivante();

private:
    QVBoxLayout *layout;
    QPushButton *boutonEtapeSuivante;
    QLabel *statusLabel;

    QStringList listeDesEtapes;
    QSqlDatabase db;
    int etapeActuelIndex;
    int idProcessusActuel;
};

#endif // FENETREDESETAPES_H

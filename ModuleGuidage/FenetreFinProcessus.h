#ifndef FENETREFINPROCESSUS_H
#define FENETREFINPROCESSUS_H
#include "FenetreEtapes.h"
#include "BaseDeDonnees.h"
#include "Communication.h"
#include <QtWidgets>
#include <QSqlDatabase>
#include <QSqlQuery>
#include <QSqlError>
#include <QShowEvent>

// FenetreFinProcessus.h
class FenetreFinProcessus : public QDialog
{
    Q_OBJECT

public:
    FenetreFinProcessus(int id, qint64 duree, QWidget *parent = nullptr);

private:
    int idAssemblage;
    qint64 dureeMs;

    void sauvegarderStatistiques();
    void afficherInformations();

protected:
    void showEvent(QShowEvent* event) override;
};


#endif // FENETREFINPROCESSUS_H

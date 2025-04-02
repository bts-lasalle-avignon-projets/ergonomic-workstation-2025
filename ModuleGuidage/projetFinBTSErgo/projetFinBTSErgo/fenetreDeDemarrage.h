#ifndef FENETREDEDEMARRAGE_H
#define FENETREDEDEMARRAGE_H

#include "fenetreDesEtapes.h"
#include <QWidget>
#include <QComboBox>
#include <QPushButton>
#include <QLabel>
#include <QVBoxLayout>
#include <QSqlDatabase>
#include <QSqlQuery>
#include <QSqlError>
#include <QPixmap>
#include <QByteArray>
#include <QKeyEvent>

class fenetreDeDemarrage : public QWidget
{
    Q_OBJECT

public:
    explicit fenetreDeDemarrage(fenetreDesEtapes *fenetreEtapes, QWidget *parent = nullptr);
    ~fenetreDeDemarrage();

protected:
    void keyPressEvent(QKeyEvent *event) override;

private:
    void demarrerProcessus();
    void chargerProcessus();
    void chargerImagePourProcessus(int processusId);
    void chargerImageSelectionnee();

    QComboBox *processusComboBox;
    QPushButton *startButton;
    QLabel *statusLabel;
    QLabel *imageLabel;
    QSqlDatabase db;
    int etapeActuelIndex;
    int idProcessusActuel;

    fenetreDesEtapes *fenetreEtapes;  // Pointeur vers la fenêtre des étapes
};

#endif // FENETREDEDEMARRAGE_H

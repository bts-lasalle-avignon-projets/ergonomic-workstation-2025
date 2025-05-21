#include "FenetreFinProcessus.h"
#include <QSqlQuery>
#include <QSqlError>
#include <QLabel>
#include <QVBoxLayout>
#include <QPushButton>
#include <QDebug>
#include <QSqlDatabase>
#include <QTableWidget>
#include <QHeaderView>
#include <QShowEvent>

FenetreFinProcessus::FenetreFinProcessus(int id, qint64 duree, QWidget *parent)
    : QDialog(parent), idAssemblage(id), dureeMs(duree)
{
    setWindowTitle("Fin du Processus");
    sauvegarderStatistiques();
    afficherInformations();
}

// On passe en plein écran une fois que la fenêtre est visible
void FenetreFinProcessus::showEvent(QShowEvent* event)
{
    QDialog::showEvent(event);
    this->showFullScreen();
}

void FenetreFinProcessus::sauvegarderStatistiques()
{
    QSqlDatabase db = BaseDeDonnees::getDatabase();
    QSqlQuery q(db);

    q.prepare(R"(
        UPDATE Assemblage
        SET dureeProcessus = SEC_TO_TIME(:duree)
        WHERE idAssemblage = :aid
    )");
    q.bindValue(":aid", idAssemblage);
    q.bindValue(":duree", dureeMs / 1000);

    if (!q.exec()) {
        qWarning() << "Erreur lors de la sauvegarde de la durée:" << q.lastError().text();
    } else {
        qDebug() << "Statistiques enregistrées pour Assemblage ID:" << idAssemblage;
    }
}

void FenetreFinProcessus::afficherInformations()
{
    QSqlDatabase db = BaseDeDonnees::getDatabase();
    QSqlQuery q(db);

    q.prepare("SELECT nombreEchecs, dateStatistique FROM Assemblage WHERE idAssemblage = :aid");
    q.bindValue(":aid", idAssemblage);
    if (!q.exec()) {
        qWarning() << "Erreur SELECT:" << q.lastError().text();
        return;
    }
    if (!q.next()) {
        qWarning() << "Aucun résultat trouvé pour idAssemblage =" << idAssemblage;
        return;
    }

    int echecs = q.value(0).toInt();
    QString dateDebut = q.value(1).toString();
    QString duree = QString::number(dureeMs / 1000) + " s";

    // Création du tableau
    QTableWidget* table = new QTableWidget(1, 3);
    table->setEditTriggers(QAbstractItemView::NoEditTriggers);
    table->setFocusPolicy(Qt::NoFocus);
    table->setSelectionMode(QAbstractItemView::NoSelection);
    table->verticalHeader()->setVisible(false);
    table->horizontalHeader()->setVisible(true);
    table->horizontalHeader()->setSectionResizeMode(QHeaderView::Stretch);

    QStringList headers = {"Nombre d'échecs", "Durée du processus", "Date de début"};
    table->setHorizontalHeaderLabels(headers);

    table->setItem(0, 0, new QTableWidgetItem(QString::number(echecs)));
    table->setItem(0, 1, new QTableWidgetItem(duree));
    table->setItem(0, 2, new QTableWidgetItem(dateDebut));

    for (int col = 0; col < 3; ++col) {
        table->item(0, col)->setTextAlignment(Qt::AlignCenter);
    }

    QPushButton *btnFermer = new QPushButton("Fermer");
    connect(btnFermer, &QPushButton::clicked, this, &QDialog::accept);

    QVBoxLayout *mainLayout = new QVBoxLayout;
    mainLayout->addStretch();
    mainLayout->addWidget(table, 0, Qt::AlignCenter);
    mainLayout->addWidget(btnFermer, 0, Qt::AlignCenter);
    mainLayout->addStretch();

    setLayout(mainLayout);
}

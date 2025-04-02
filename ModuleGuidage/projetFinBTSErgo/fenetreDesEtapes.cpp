#include "fenetreDesEtapes.h"
#include "connexionBDD.h"
#include <QSqlError>
#include <QDebug>

fenetreDesEtapes::fenetreDesEtapes(QWidget *parent)
    : QWidget(parent), etapeActuelIndex(0), idProcessusActuel(-1)
{
    setWindowTitle("Fenêtre de changement d'étapes");
    showFullScreen();

    layout = new QVBoxLayout(this);

    statusLabel = new QLabel("Aucune étape chargée.", this);
    layout->addWidget(statusLabel);

    boutonEtapeSuivante = new QPushButton("Étape suivante", this);
    boutonEtapeSuivante->setEnabled(false);
    layout->addWidget(boutonEtapeSuivante);

    connect(boutonEtapeSuivante, &QPushButton::clicked, this, &fenetreDesEtapes::etapeSuivante);

    db = connexionBDD::getDatabase();
}

void fenetreDesEtapes::etapeSuivante() {
    if (listeDesEtapes.isEmpty()) {
        statusLabel->setText("Aucune étape disponible.");
        return;
    }

    if (etapeActuelIndex < listeDesEtapes.size() - 1) {
        etapeActuelIndex++;
        statusLabel->setText("Étape actuelle:\n" + listeDesEtapes[etapeActuelIndex]);
    } else {
        statusLabel->setText("Processus terminé !");
        boutonEtapeSuivante->setEnabled(false);
    }
}

void fenetreDesEtapes::chargerEtape(int processusId) {
    listeDesEtapes.clear();
    idProcessusActuel = processusId;

    QSqlQuery query;
    query.prepare("SELECT descriptionEtape FROM Etapes WHERE idProcessus = :processus_id ORDER BY idEtapes ASC");
    query.bindValue(":processus_id", idProcessusActuel);

    if (query.exec()) {
        while (query.next()) {
            listeDesEtapes.append(query.value(0).toString());
        }

        if (listeDesEtapes.isEmpty()) {
            statusLabel->setText("Aucune étape trouvée.");
        } else {
            etapeActuelIndex = 0;
            statusLabel->setText("Étape actuelle:\n" + listeDesEtapes[etapeActuelIndex]);
            boutonEtapeSuivante->setEnabled(true);
        }
    } else {
        statusLabel->setText("Erreur de requête SQL.");
        qDebug() << "Erreur de requête SQL : " << query.lastError().text();
    }
}

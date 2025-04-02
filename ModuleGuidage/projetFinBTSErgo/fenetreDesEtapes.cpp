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

    imageLabel = new QLabel(this);
    layout->addWidget(imageLabel);

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
        chargerImagePourEtape(listeIdEtapes[etapeActuelIndex]); // Mettre à jour l'image
    } else {
        statusLabel->setText("Processus terminé !");
        boutonEtapeSuivante->setEnabled(false);
    }
}

void fenetreDesEtapes::chargerEtape(int processusId) {
    listeDesEtapes.clear();
    listeIdEtapes.clear();  // Stocker les ID des étapes
    idProcessusActuel = processusId;

    QSqlQuery query;
    query.prepare("SELECT idEtapes, descriptionEtape FROM Etapes WHERE idProcessus = :processus_id ORDER BY idEtapes ASC");
    query.bindValue(":processus_id", idProcessusActuel);

    if (query.exec()) {
        while (query.next()) {
            int idEtape = query.value(0).toInt();
            QString description = query.value(1).toString();
            listeDesEtapes.append(description);
            listeIdEtapes.append(idEtape); // Stocker les ID des étapes
        }

        if (listeDesEtapes.isEmpty()) {
            statusLabel->setText("Aucune étape trouvée.");
        } else {
            etapeActuelIndex = 0;
            statusLabel->setText("Étape actuelle:\n" + listeDesEtapes[etapeActuelIndex]);
            boutonEtapeSuivante->setEnabled(true);
            chargerImagePourEtape(listeIdEtapes[etapeActuelIndex]); // Charger l'image immédiatement
        }
    } else {
        statusLabel->setText("Erreur de requête SQL.");
        qDebug() << "Erreur SQL : " << query.lastError().text();
    }
}


void fenetreDesEtapes::chargerImagePourEtape(int idEtape)
{
    QSqlQuery query;
    query.prepare("SELECT contenuBlob FROM Image "
                  "JOIN Etapes ON Etapes.idImage = Image.idImage "
                  "WHERE Etapes.idEtapes = :idEtape");
    query.bindValue(":idEtape", idEtape);

    if (query.exec() && query.next()) {
        QByteArray imageData = query.value(0).toByteArray();

        if (!imageData.isEmpty()) {
            QPixmap pixmap;
            if (pixmap.loadFromData(imageData)) {
                imageLabel->setPixmap(pixmap.scaled(300, 200, Qt::KeepAspectRatio, Qt::SmoothTransformation));
            } else {
                qDebug() << "Erreur : Impossible de charger l'image.";
                imageLabel->clear();
            }
        } else {
            qDebug() << "Aucune image trouvée pour cette étape.";
            imageLabel->clear();
        }
    } else {
        qDebug() << "Erreur SQL : " << query.lastError().text();
        imageLabel->clear();
    }
}


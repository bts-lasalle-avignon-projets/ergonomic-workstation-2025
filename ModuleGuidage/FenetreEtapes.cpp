#include "FenetreEtapes.h"
#include "BaseDeDonnees.h"
#include <QSqlQuery>
#include <QSqlError>
#include <QDebug>

FenetreEtapes::FenetreEtapes(QWidget* parent) :
    QWidget(parent), etapeActuelIndex(0), idProcessusActuel(-1)
{
    qDebug() << Q_FUNC_INFO << this;
    setWindowTitle("Fenêtre de changement d'étapes");

    layout = new QVBoxLayout(this);

    statusLabel = new QLabel("Aucune étape chargée.", this);
    layout->addWidget(statusLabel);

    boutonEtapeSuivante = new QPushButton("Étape suivante", this);
    boutonEtapeSuivante->setEnabled(false);
    boutonFermerFenetre = new QPushButton("Fermer", this);
    layout->addWidget(boutonEtapeSuivante);
    layout->addWidget(boutonFermerFenetre);

    imageLabel = new QLabel(this);
    layout->addWidget(imageLabel);

    connect(boutonEtapeSuivante,
            &QPushButton::clicked,
            this,
            &FenetreEtapes::chargerEtapeSuivante);
    connect(boutonFermerFenetre,
            &QPushButton::clicked,
            this,
            &FenetreEtapes::fermerFenetre);
#ifdef RASPBERRY_PI
    setWindowFlags(Qt::FramelessWindowHint |
                   Qt::Dialog); // Ajouter Qt::WindowStaysOnTopHint*/
    showFullScreen();
#else
    setWindowFlags(Qt::Dialog);
    showMaximized();
#endif

    setWindowModality(Qt::WindowModal);
}

/**
 * @brief S'exécute à chaque fois que la fenêtre est affichée
 *
 * @fn FenetreEtapes::showEvent
 *
 */
void FenetreEtapes::showEvent(QShowEvent* event)
{
    qDebug() << Q_FUNC_INFO << this;
}

void FenetreEtapes::chargerEtape(int idProcessus)
{
    qDebug() << Q_FUNC_INFO << "idProcessus" << idProcessus;

    listeDesEtapes.clear();
    listeIdEtapes.clear(); // Stocker les ID des étapes
    idProcessusActuel = idProcessus;

    db = BaseDeDonnees::getDatabase();

    QSqlQuery query;
    query.prepare("SELECT idEtapes, descriptionEtape FROM Etapes WHERE "
                  "idProcessus = :processus_id ORDER BY idEtapes ASC");
    query.bindValue(":processus_id", idProcessusActuel);

    if(query.exec())
    {
        while(query.next())
        {
            int     idEtape     = query.value(0).toInt();
            QString description = query.value(1).toString();
            listeDesEtapes.append(description);
            listeIdEtapes.append(idEtape); // Stocker les ID des étapes
        }

        if(listeDesEtapes.isEmpty())
        {
            statusLabel->setText("Aucune étape trouvée.");
        }
        else
        {
            etapeActuelIndex = 0;
            statusLabel->setText("Étape actuelle:\n" +
                                 listeDesEtapes[etapeActuelIndex]);
            boutonEtapeSuivante->setEnabled(true);
            chargerImagePourEtape(
              listeIdEtapes[etapeActuelIndex]); // Charger l'image immédiatement
        }
    }
    else
    {
        statusLabel->setText("Erreur de requête SQL.");
        qDebug() << "Erreur SQL : " << query.lastError().text();
    }
}

void FenetreEtapes::chargerImagePourEtape(int idEtape)
{
    QSqlQuery query;
    query.prepare("SELECT contenuBlob FROM Image "
                  "JOIN Etapes ON Etapes.idImage = Image.idImage "
                  "WHERE Etapes.idEtapes = :idEtape");
    query.bindValue(":idEtape", idEtape);

    if(query.exec() && query.next())
    {
        QByteArray imageData = query.value(0).toByteArray();

        if(!imageData.isEmpty())
        {
            QPixmap pixmap;
            if(pixmap.loadFromData(imageData))
            {
                imageLabel->setPixmap(pixmap.scaled(300,
                                                    200,
                                                    Qt::KeepAspectRatio,
                                                    Qt::SmoothTransformation));
            }
            else
            {
                qDebug() << "Erreur : Impossible de charger l'image.";
                imageLabel->clear();
            }
        }
        else
        {
            qDebug() << "Aucune image trouvée pour cette étape.";
            imageLabel->clear();
        }
    }
    else
    {
        qDebug() << "Erreur SQL : " << query.lastError().text();
        imageLabel->clear();
    }
}

void FenetreEtapes::chargerEtapeSuivante()
{
    if(listeDesEtapes.isEmpty())
    {
        statusLabel->setText("Aucune étape disponible.");
        return;
    }

    if(etapeActuelIndex < listeDesEtapes.size() - 1)
    {
        etapeActuelIndex++;
        statusLabel->setText("Étape actuelle:\n" +
                             listeDesEtapes[etapeActuelIndex]);
        chargerImagePourEtape(
          listeIdEtapes[etapeActuelIndex]); // Mettre à jour l'image
    }
    else
    {
        statusLabel->setText("Processus terminé !");
        boutonEtapeSuivante->setEnabled(false);
    }
}

void FenetreEtapes::fermerFenetre()
{
    qDebug() << Q_FUNC_INFO << this;
    this->close();
}

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

    connect(boutonEtapeSuivante, &QPushButton::clicked, this, &FenetreEtapes::chargerEtapeSuivante);
    connect(boutonFermerFenetre, &QPushButton::clicked, this, &FenetreEtapes::fermerFenetre);

#ifdef RASPBERRY_PI
    setWindowFlags(Qt::FramelessWindowHint | Qt::Dialog);
    showFullScreen();
#else
    setWindowFlags(Qt::Dialog);
    showMaximized();
#endif

    setWindowModality(Qt::WindowModal);
}

void FenetreEtapes::showEvent(QShowEvent*)
{
    qDebug() << Q_FUNC_INFO << this;
}

void FenetreEtapes::chargerEtape(int idProcessus)
{
    qDebug() << Q_FUNC_INFO << "idProcessus" << idProcessus;

    listeDesEtapes.clear();
    idProcessusActuel = idProcessus;

    db = BaseDeDonnees::getDatabase();

    QSqlQuery query;
    query.prepare("SELECT idEtape, numeroEtape, nomEtape, descriptionEtape "
                  "FROM Etape WHERE idProcessus = :processus_id "
                  "ORDER BY numeroEtape ASC");
    query.bindValue(":processus_id", idProcessusActuel);

    if(query.exec())
    {
        while(query.next())
        {
            InfosEtape etape;
            etape.idEtape = query.value(0).toInt();
            etape.numeroEtape = query.value(1).toInt();
            etape.nomEtape = query.value(2).toString();
            etape.descriptionEtape = query.value(3).toString();
            listeDesEtapes.append(etape);
        }

        if(listeDesEtapes.isEmpty())
        {
            statusLabel->setText("Aucune étape trouvée.");
        }
        else
        {
            etapeActuelIndex = 0;
            afficherInfosEtapeActuelle();
            boutonEtapeSuivante->setEnabled(true);
            chargerImagePourEtape(listeDesEtapes[etapeActuelIndex].idEtape);
        }
    }
    else
    {
        statusLabel->setText("Erreur de requête SQL.");
        qDebug() << "Erreur SQL : " << query.lastError().text();
    }
}

void FenetreEtapes::afficherInfosEtapeActuelle()
{
    if(etapeActuelIndex >= 0 && etapeActuelIndex < listeDesEtapes.size())
    {
        InfosEtape etape = listeDesEtapes[etapeActuelIndex];

        QString infoText;
        infoText += "Étape n°" + QString::number(etape.numeroEtape) + " : " + etape.nomEtape + "\n";
        infoText += "Description : " + etape.descriptionEtape + "\n";

        // Charger les bacs associés à cette étape
        QSqlQuery query;
        query.prepare("SELECT numeroBac, contenance FROM Bac WHERE idEtape = :idEtape");
        query.bindValue(":idEtape", etape.idEtape);

        if(query.exec())
        {
            while(query.next())
            {
                QString numBac = query.value(0).toString();
                QString contenance = query.value(1).toString();
                infoText += "Bac n°" + numBac + " - Contenance : " + contenance + "\n";
            }
        }
        else
        {
            infoText += "Erreur lors du chargement des bacs.\n";
            qDebug() << "Erreur SQL (bacs) :" << query.lastError().text();
        }

        statusLabel->setText(infoText);
    }
}

void FenetreEtapes::chargerImagePourEtape(int idEtape)
{
    QSqlQuery query;
    query.prepare("SELECT contenuBlob FROM Image "
                  "JOIN Etape ON Etape.idImage = Image.idImage "
                  "WHERE Etape.idEtape = :idEtape");
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
        afficherInfosEtapeActuelle();
        chargerImagePourEtape(listeDesEtapes[etapeActuelIndex].idEtape);
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

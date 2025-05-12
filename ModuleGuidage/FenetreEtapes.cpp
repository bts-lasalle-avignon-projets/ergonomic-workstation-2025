#include "FenetreEtapes.h"
#include "BaseDeDonnees.h"
#include <QSqlQuery>
#include <QSqlError>
#include <QDebug>

FenetreEtapes::FenetreEtapes(QWidget* parent) :
    QWidget(parent), etapeActuelIndex(0), idProcessusActuel(-1)
{
    qDebug() << Q_FUNC_INFO << this;
    setObjectName("FenetreEtape");
    // setWindowTitle("Fenêtre de changement d'étapes");

    initialiserFenetre();

    connect(boutonEtapeSuivante,
            &QPushButton::clicked,
            this,
            &FenetreEtapes::chargerEtapeSuivante);
    connect(boutonFermerFenetre,
            &QPushButton::clicked,
            this,
            &FenetreEtapes::fermerFenetre);

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

void FenetreEtapes::initialiserFenetre()
{
    qDebug() << Q_FUNC_INFO;

    QVBoxLayout* verticalLayoutPrincipal     = new QVBoxLayout(this);
    QHBoxLayout* horizontalLayoutEtape       = new QHBoxLayout;
    QHBoxLayout* horizontalLayoutDescription = new QHBoxLayout;
    QVBoxLayout* verticalLayoutImage         = new QVBoxLayout;
    QHBoxLayout* horizontalLayoutBacs        = new QHBoxLayout;
    QHBoxLayout* horizontalLayoutBoutons     = new QHBoxLayout;
    QHBoxLayout* horizontalLayoutEtat        = new QHBoxLayout;

    horizontalLayoutEtape->setContentsMargins(-1,
                                              MARGE_LAYOUT,
                                              -1,
                                              MARGE_LAYOUT);
    horizontalLayoutBoutons->setContentsMargins(MARGE_LAYOUT,
                                                -1,
                                                MARGE_LAYOUT,
                                                -1);

    labelNumeroEtape      = new QLabel(this);
    labelNomEtape         = new QLabel(this);
    labelDescriptionEtape = new QLabel(this);
    labelEtatRequete      = new QLabel(this);
    labelEtatRequete->setObjectName("labelEtat");
    imageEtape = new QLabel(this);

    /**
     * @todo Ajouter les QLabel pour l'affichage des bacs (à l'identique de la
     * table ErgonomicWorkstation)
     */

    boutonEtapeSuivante = new QPushButton("Étape suivante", this);
    boutonEtapeSuivante->setEnabled(false);
    boutonFermerFenetre = new QPushButton("Fermer", this);

    horizontalLayoutEtape->addStretch();
    horizontalLayoutEtape->addWidget(labelNumeroEtape);
    horizontalLayoutEtape->addStretch();
    horizontalLayoutEtape->addWidget(labelNomEtape);
    horizontalLayoutEtape->addStretch();
    horizontalLayoutDescription->addWidget(labelDescriptionEtape);
    verticalLayoutImage->addWidget(imageEtape);
    horizontalLayoutBoutons->addWidget(boutonFermerFenetre);
    horizontalLayoutBoutons->addStretch();
    horizontalLayoutBoutons->addWidget(boutonEtapeSuivante);
    horizontalLayoutEtat->addWidget(labelEtatRequete);

    verticalLayoutPrincipal->addLayout(horizontalLayoutEtape);
    verticalLayoutPrincipal->addLayout(verticalLayoutImage);
    verticalLayoutPrincipal->addLayout(horizontalLayoutDescription);
    verticalLayoutPrincipal->addLayout(horizontalLayoutBacs);
    verticalLayoutPrincipal->addLayout(horizontalLayoutBoutons);
    verticalLayoutPrincipal->addLayout(horizontalLayoutEtat);
}

void FenetreEtapes::chargerEtape(int idProcessus)
{
    qDebug() << Q_FUNC_INFO << "idProcessus" << idProcessus;

    listeDesEtapes.clear();
    idProcessusActuel = idProcessus;

    db = BaseDeDonnees::getDatabase();

    QSqlQuery query;
    query.prepare("SELECT * "
                  "FROM Etape WHERE idProcessus = :processus_id "
                  "ORDER BY numeroEtape ASC");
    query.bindValue(":processus_id", idProcessusActuel);

    if(query.exec())
    {
        while(query.next())
        {
            Etape etape;
            etape.idEtape = query.value(TableEtape::TE_ID_ETAPE).toInt();
            etape.numeroEtape =
              query.value(TableEtape::TE_NUMERO_ETAPE).toInt();
            etape.nomEtape = query.value(TableEtape::TE_NOM_ETAPE).toString();
            etape.descriptionEtape =
              query.value(TableEtape::TE_DESCRIPTION_ETAPE).toString();
            etape.numeroBac = query.value(TableEtape::TE_ID_BAC).toInt();
            listeDesEtapes.append(etape);
        }

        if(listeDesEtapes.isEmpty())
        {
            labelEtatRequete->setText("Aucune étape trouvée !");
        }
        else
        {
            etapeActuelIndex = 0;
            afficherEtapeActuelle();
            boutonEtapeSuivante->setEnabled(true);
        }
    }
    else
    {
        if(!query.lastError().text().isEmpty())
            qDebug() << Q_FUNC_INFO << "Erreur SQL" << query.lastError().text();
    }
}

void FenetreEtapes::afficherEtapeActuelle()
{
    if(etapeActuelIndex >= 0 && etapeActuelIndex < listeDesEtapes.size())
    {
        Etape etape = listeDesEtapes[etapeActuelIndex];

        labelNumeroEtape->setText("Étape n°" +
                                  QString::number(etape.numeroEtape));
        labelNomEtape->setText(etape.nomEtape);
        labelDescriptionEtape->setText(etape.descriptionEtape);
        labelEtatRequete->setText("");
        imageEtape->clear();

        // Charger les bacs associés à cette étape
        QSqlQuery query;
        query.prepare("SELECT numeroBac, contenance FROM Bac WHERE numeroBac = "
                      ":numeroBac AND idProcessus = :idProcessus");
        query.bindValue(":numeroBac", etape.numeroBac);
        query.bindValue(":idProcessus", idProcessusActuel);

        if(query.exec())
        {
            while(query.next())
            {
                /*QString numeroBac =
                  query.value(TableBac::TB_NUMERO_BAC).toString();*/
                etape.contenance =
                  query.value(TableBac::TB_CONTENANCE).toString();
                qDebug() << Q_FUNC_INFO << "numeroBac" << etape.numeroBac
                         << "contenance" << etape.contenance;
                /**
                 * @todo Faire l'affichage du bac (à l'identique de la table
                 * ErgonomicWorkstation)
                 */
            }
        }
        else
        {
            labelEtatRequete->setText("Erreur lors du chargement des bacs !");
            qDebug() << Q_FUNC_INFO << "Erreur SQL" << query.lastError().text();
        }

        chargerImagePourEtape(listeDesEtapes[etapeActuelIndex].idEtape);
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
                imageEtape->setPixmap(pixmap.scaled(LARGEUR_IMAGE_ETAPE,
                                                    HAUTEUR_IMAGE_ETAPE,
                                                    Qt::KeepAspectRatio,
                                                    Qt::SmoothTransformation));
            }
            else
            {
                qDebug() << Q_FUNC_INFO
                         << "Erreur lors du chargement de l'image !";
                imageEtape->clear();
            }
        }
        else
        {
            qDebug() << Q_FUNC_INFO
                     << "Aucune image trouvée pour cette étape !";
            imageEtape->clear();
        }
    }
    else
    {
        if(!query.lastError().text().isEmpty())
            qDebug() << Q_FUNC_INFO << "Erreur SQL" << query.lastError().text();
        imageEtape->clear();
    }
}

void FenetreEtapes::chargerEtapeSuivante()
{
    if(listeDesEtapes.isEmpty())
    {
        labelEtatRequete->setText("Aucune étape disponible !");
        return;
    }

    if(etapeActuelIndex < listeDesEtapes.size() - 1)
    {
        etapeActuelIndex++;
        afficherEtapeActuelle();
        chargerImagePourEtape(listeDesEtapes[etapeActuelIndex].idEtape);
    }
    else
    {
        labelEtatRequete->setText("Processus terminé !");
        boutonEtapeSuivante->setEnabled(false);
    }
}

void FenetreEtapes::fermerFenetre()
{
    qDebug() << Q_FUNC_INFO << this;
    this->close();
}

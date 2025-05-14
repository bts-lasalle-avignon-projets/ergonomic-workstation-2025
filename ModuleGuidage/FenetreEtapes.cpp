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

    initialiserFenetre();

    connect(boutonEtapeSuivante, &QPushButton::clicked, this, &FenetreEtapes::chargerEtapeSuivante);

    showFullScreen();
#ifdef RASPBERRY_PI
    setWindowFlags(Qt::FramelessWindowHint | Qt::Dialog);
#else
    setWindowFlags(Qt::Dialog);
#endif
    setWindowModality(Qt::WindowModal);
}

void FenetreEtapes::showEvent(QShowEvent*)
{
    qDebug() << Q_FUNC_INFO << this;
}

void FenetreEtapes::initialiserFenetre()
{
    QVBoxLayout* layoutPrincipal = new QVBoxLayout(this);

    QHBoxLayout* layoutHaut = new QHBoxLayout;
    labelNumeroEtape = new QLabel("Étape n°", this);
    labelNomEtape = new QLabel("Nom étape", this);
    layoutHaut->addWidget(labelNumeroEtape);
    layoutHaut->addStretch();
    layoutHaut->addWidget(labelNomEtape);

    imageEtape = new QLabel(this);
    imageEtape->setFixedSize(1080, 500);
    imageEtape->setAlignment(Qt::AlignCenter);

    boutonEtapeSuivante = new QPushButton("Étape suivante", this);

    QHBoxLayout* layoutDescription = new QHBoxLayout;
    labelDescriptionEtape = new QLabel("Description étape", this);
    labelDescriptionEtape->setWordWrap(true);
    layoutDescription->addWidget(labelDescriptionEtape);

    QHBoxLayout* layoutEtat = new QHBoxLayout;
    labelEtatRequete = new QLabel("État requête", this);
    QLabel* labelEtatImage = new QLabel("État image", this);
    layoutEtat->addWidget(labelEtatRequete);
    layoutEtat->addStretch();
    layoutEtat->addWidget(labelEtatImage);

    labelBacNumero = new QLabel(this);
    labelBacContenance = new QLabel(this);
    QHBoxLayout* layoutBacInfos = new QHBoxLayout;
    layoutBacInfos->addWidget(labelBacNumero);
    layoutBacInfos->addStretch();
    layoutBacInfos->addWidget(labelBacContenance);

    layoutPrincipal->addLayout(layoutHaut);
    layoutPrincipal->addWidget(imageEtape, 0, Qt::AlignCenter);
    layoutPrincipal->addWidget(boutonEtapeSuivante, 0, Qt::AlignHCenter);
    layoutPrincipal->addLayout(layoutDescription);
    layoutPrincipal->addLayout(layoutBacInfos);
    layoutPrincipal->addLayout(layoutEtat);
}

void FenetreEtapes::chargerEtape(int idProcessus)
{
    qDebug() << Q_FUNC_INFO << "idProcessus" << idProcessus;

    if(idProcessus <= 0)
    {
        qWarning() << Q_FUNC_INFO << "ID de processus invalide :" << idProcessus;
        return;
    }

    listeDesEtapes.clear();
    idProcessusActuel = idProcessus;

    db = BaseDeDonnees::getDatabase();

    QSqlQuery query;
    query.prepare("SELECT * FROM Etape WHERE idProcessus = :processus_id ORDER BY numeroEtape ASC");
    query.bindValue(":processus_id", idProcessusActuel);

    if(query.exec())
    {
        while(query.next())
        {
            Etape etape;
            etape.idEtape = query.value(TableEtape::TE_ID_ETAPE).toInt();
            etape.numeroEtape = query.value(TableEtape::TE_NUMERO_ETAPE).toInt();
            etape.nomEtape = query.value(TableEtape::TE_NOM_ETAPE).toString();
            etape.descriptionEtape = query.value(TableEtape::TE_DESCRIPTION_ETAPE).toString();
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
        QString erreur = query.lastError().text();
        labelEtatRequete->setText("Erreur SQL : " + erreur);
        qDebug() << Q_FUNC_INFO << "Erreur SQL" << erreur;
    }
}

void FenetreEtapes::afficherEtapeActuelle()
{
    if(etapeActuelIndex >= 0 && etapeActuelIndex < listeDesEtapes.size())
    {
        Etape etape = listeDesEtapes[etapeActuelIndex];

        labelNumeroEtape->setText("Étape n°" + QString::number(etape.numeroEtape));
        labelNomEtape->setText(etape.nomEtape);
        labelDescriptionEtape->setText(etape.descriptionEtape);
        labelEtatRequete->setText("");
        imageEtape->clear();

        QSqlQuery query;
        query.prepare("SELECT numeroBac, contenance FROM Bac WHERE numeroBac = :numeroBac AND idProcessus = :idProcessus");
        query.bindValue(":numeroBac", etape.numeroBac);
        query.bindValue(":idProcessus", idProcessusActuel);

        if(query.exec())
        {
            if(query.next())
            {
                etape.contenance = query.value(TableBac::TB_CONTENANCE).toString();
                qDebug() << Q_FUNC_INFO << "numeroBac" << etape.numeroBac << "contenance" << etape.contenance;

                labelBacNumero->setText("Bac n°" + QString::number(etape.numeroBac));
                labelBacContenance->setText("Contenance : " + etape.contenance);
            }
            else
            {
                labelBacNumero->clear();
                labelBacContenance->clear();
            }
        }
        else
        {
            QString erreur = query.lastError().text();
            labelEtatRequete->setText("Erreur SQL : " + erreur);
            qDebug() << Q_FUNC_INFO << "Erreur SQL" << erreur;
        }

        chargerImagePourEtape(etape.idEtape);
    }
}

void FenetreEtapes::chargerImagePourEtape(int idEtape)
{
    QSqlQuery query;
    query.prepare("SELECT contenuBlob FROM Image JOIN Etape ON Etape.idImage = Image.idImage WHERE Etape.idEtape = :idEtape");
    query.bindValue(":idEtape", idEtape);

    if(query.exec() && query.next())
    {
        QByteArray imageData = query.value(0).toByteArray();

        if(!imageData.isEmpty())
        {
            QPixmap pixmap;
            if(pixmap.loadFromData(imageData))
            {
                imageEtape->setPixmap(pixmap.scaled(LARGEUR_IMAGE_ETAPE, HAUTEUR_IMAGE_ETAPE, Qt::KeepAspectRatio, Qt::SmoothTransformation));
            }
            else
            {
                qDebug() << Q_FUNC_INFO << "Erreur lors du chargement de l'image !";
                imageEtape->clear();
            }
        }
        else
        {
            qDebug() << Q_FUNC_INFO << "Aucune image trouvée pour cette étape !";
            imageEtape->clear();
        }
    }
    else
    {
        QString erreur = query.lastError().text();
        labelEtatRequete->setText("Erreur SQL : " + erreur);
        qDebug() << Q_FUNC_INFO << "Erreur SQL" << erreur;
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
    }
    else
    {
        labelEtatRequete->setText("Processus terminé !");
        boutonEtapeSuivante->setEnabled(false);
    }
}

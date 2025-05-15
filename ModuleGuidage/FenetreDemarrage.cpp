/**
 * @file FenetreDemarrage.cpp
 *
 * @brief Définition de la classe FenetreDemarrage
 * @author BOUSQUET-SOLFRINI Valentin
 * @version 1.0
 */

#include "FenetreDemarrage.h"
#include "FenetreEtapes.h"
#include "BaseDeDonnees.h"
#include <QSqlQuery>
#include <QSqlError>
#include <QPixmap>
#include <QByteArray>
#include <QDebug>

/**
 * @brief Constructeur de la classe FenetreDemarrage
 *
 * @fn FenetreDemarrage::FenetreDemarrage
 * @param parent L'adresse de l'objet parent, si nullptr FenetreDemarrage sera
 * la fenêtre principale de l'application
 */
FenetreDemarrage::FenetreDemarrage(QWidget* parent) :
    QMainWindow(parent), etapeActuelIndex(0), idProcessusActuel(-1),
    fenetreEtapes(nullptr)
{
    qDebug() << Q_FUNC_INFO << this;

    setupUi(this);

    setWindowTitle(QString(APPLICATION) + QString(" v") + QString(VERSION));
    showFullScreen();


    imageProcessus->setMinimumSize(LARGEUR_IMAGE, HAUTEUR_IMAGE);
    imageProcessus->setAlignment(Qt::AlignCenter);
    labelDescriptionProcessus->setAlignment(Qt::AlignCenter);
    labelEtatRequete->setAlignment(Qt::AlignCenter);

    connect(boutonDemarrerProcessus,
            &QPushButton::clicked,
            this,
            &FenetreDemarrage::demarrerProcessus);
    connect(comboBoxListeProcessus,
            QOverload<int>::of(&QComboBox::currentIndexChanged),
            this,
            &FenetreDemarrage::afficherProcessus);

    chargerListeProcessus();
}

FenetreDemarrage::~FenetreDemarrage()
{
    db.close();
    qDebug() << Q_FUNC_INFO << this;
}

void FenetreDemarrage::chargerListeProcessus()
{
    qDebug() << Q_FUNC_INFO;

    db = BaseDeDonnees::getDatabase();
    QSqlQuery query(
      "SELECT idProcessus, nomProcessus, descriptionProcessus FROM Processus");

    if(query.exec())
    {
        comboBoxListeProcessus->clear();
        labelEtatRequete->setText("");
        while(query.next())
        {
            int idProcessus =
              query.value(TableProcessus::TP_ID_PROCESSUS).toInt();
            QString nomProcessus =
              query.value(TableProcessus::TP_NOM_PROCESSUS).toString();
            comboBoxListeProcessus->addItem(nomProcessus, idProcessus);
        }
    }
    else
    {
        qDebug() << Q_FUNC_INFO
                 << "Erreur lors de la récupération des processus"
                 << query.lastError().text();
        labelEtatRequete->setText(
          "Erreur lors de la récupération des processus !");
    }
}

void FenetreDemarrage::afficherProcessus()
{
    QVariant processusSelectionne = comboBoxListeProcessus->currentData();
    qDebug() << Q_FUNC_INFO << "processusSelectionne" << processusSelectionne;
    if(processusSelectionne.isValid())
    {
        int idProcessus = processusSelectionne.toInt();
        qDebug() << Q_FUNC_INFO << "idProcessus" << idProcessus;
        QSqlQuery query(
          "SELECT idProcessus, nomProcessus, descriptionProcessus FROM "
          "Processus WHERE idProcessus=" +
          QString::number(idProcessus));

        if(query.exec() && query.next())
        {
            QString descriptionProcessus =
              query.value(TableProcessus::TP_DESCRIPTION_PROCESSUS).toString();
            labelDescriptionProcessus->setText(descriptionProcessus);
        }
        else
        {
            qDebug()
              << Q_FUNC_INFO
              << "Erreur lors de la récupération de la description du processus"
              << query.lastError().text();
            labelEtatRequete->setText("Erreur lors de la récupération de la "
                                      "description du processus !");
        }
        chargerImagePourProcessus(idProcessus);
        labelEtatRequete->setText("");
        boutonDemarrerProcessus->setEnabled(true);
    }
    else
    {
        qDebug() << Q_FUNC_INFO << "Aucun processus sélectionné !";
        labelEtatRequete->setText("Veuillez sélectionner un processus !");
        imageProcessus->clear();
        boutonDemarrerProcessus->setEnabled(false);
    }
}

void FenetreDemarrage::chargerImagePourProcessus(int idPprocessus)
{
    qDebug() << Q_FUNC_INFO << "idPprocessus" << idPprocessus;
    QSqlQuery query;
    query.prepare("SELECT contenuBlob FROM Image "
                  "JOIN Processus ON Processus.idImage = Image.idImage "
                  "WHERE Processus.idProcessus = :idProcessus");
    query.bindValue(":idProcessus", idPprocessus);

    if(query.exec() && query.next())
    {
        QByteArray contenuImage = query.value(TI_CONTENU_IMAGE).toByteArray();
        if(!contenuImage.isEmpty())
        {
            QPixmap pixmap;
            if(pixmap.loadFromData(contenuImage))
            {
                imageProcessus->setPixmap(
                  pixmap.scaled(LARGEUR_IMAGE,
                                HAUTEUR_IMAGE,
                                Qt::KeepAspectRatio,
                                Qt::SmoothTransformation));
            }
            else
            {
                qDebug() << Q_FUNC_INFO
                         << "Erreur lors du chargement de l'image !";
                imageProcessus->clear();
            }
        }
        else
        {
            qDebug() << Q_FUNC_INFO
                     << "Aucune image trouvée pour ce processus !";
            imageProcessus->clear();
        }
    }
    else
    {
        if(!query.lastError().text().isEmpty())
            qDebug() << Q_FUNC_INFO << "Erreur SQL" << query.lastError().text();
        imageProcessus->clear();
    }
}

void FenetreDemarrage::demarrerProcessus()
{
    QVariant processusSelectionne = comboBoxListeProcessus->currentData();
    qDebug() << Q_FUNC_INFO << "processusSelectionne" << processusSelectionne;

    if (!processusSelectionne.isValid()) {
        labelEtatRequete->setText("Veuillez sélectionner un processus !");
        return;
    } else {
        labelEtatRequete->setText("");
    }

    idProcessusActuel = processusSelectionne.toInt();

    // Création ou réutilisation de la fenêtre d'étapes
    if (fenetreEtapes == nullptr) {
        fenetreEtapes = new FenetreEtapes(this);
        fenetreEtapes->setAttribute(Qt::WA_DeleteOnClose);

        connect(fenetreEtapes, &FenetreEtapes::fermerEtapes, this, [this]() {
            this->showFullScreen();
            fenetreEtapes = nullptr;  // La fenêtre sera supprimée à la fermeture
            this->showFullScreen();
        });
    }

    fenetreEtapes->chargerEtape(idProcessusActuel);
    fenetreEtapes->showFullScreen(); // showFullScreen ici
    this->hide();
}



void FenetreDemarrage::revenirAccueil()
{
    this->show();
    fenetreEtapes = nullptr;
}

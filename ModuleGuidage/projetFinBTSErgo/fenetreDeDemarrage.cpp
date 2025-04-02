#include "fenetreDeDemarrage.h"
#include "connexionBDD.h"
#include <QApplication>
#include <QSpacerItem>
#include <QDebug>
#include <QSqlError>

fenetreDeDemarrage::fenetreDeDemarrage(fenetreDesEtapes *fenetreEtapes, QWidget *parent)
    : QWidget(parent), etapeActuelIndex(0), idProcessusActuel(-1), fenetreEtapes(fenetreEtapes)
{
    setWindowTitle("Fenêtre de Démarrage");
    showFullScreen();

    QVBoxLayout *layout = new QVBoxLayout(this);

    processusComboBox = new QComboBox(this);
    layout->addWidget(processusComboBox);

    startButton = new QPushButton("Démarrer le processus", this);
    layout->addWidget(startButton);

    statusLabel = new QLabel("Veuillez sélectionner un processus", this);
    layout->addWidget(statusLabel);

    imageLabel = new QLabel(this);
    layout->addWidget(imageLabel);

    layout->addItem(new QSpacerItem(0, 0, QSizePolicy::Minimum, QSizePolicy::Expanding));

    connect(startButton, &QPushButton::clicked, this, &fenetreDeDemarrage::demarrerProcessus);

    db = connexionBDD::getDatabase();
    chargerProcessus();
}

fenetreDeDemarrage::~fenetreDeDemarrage() {
    db.close();
}

void fenetreDeDemarrage::keyPressEvent(QKeyEvent *event) {
    if (event->key() == Qt::Key_Escape) {
        if (isFullScreen()) {
            showNormal();
        }
    }
}

void fenetreDeDemarrage::chargerProcessus()
{
    QSqlQuery query("SELECT idProcessus, nomProcessus FROM Processus");

    if (query.exec()) {
        processusComboBox->clear();
        while (query.next()) {
            int processusId = query.value(0).toInt();
            QString processusNom = query.value(1).toString();
            processusComboBox->addItem(processusNom, processusId);
        }
    } else {
        qDebug() << "Erreur lors de la récupération des processus :" << query.lastError().text();
        statusLabel->setText("Erreur lors de la récupération des processus.");
    }
}

void fenetreDeDemarrage::demarrerProcessus()
{
    QVariant selectedData = processusComboBox->currentData();
    if (!selectedData.isValid()) {
        statusLabel->setText("Veuillez sélectionner un processus.");
        return;
    }

    idProcessusActuel = selectedData.toInt();
    chargerImagePourProcessus(idProcessusActuel);

    if (fenetreEtapes) {
        fenetreEtapes->chargerEtape(idProcessusActuel);
        fenetreEtapes->show();
        fenetreEtapes->raise();
        fenetreEtapes->activateWindow();
    }
}


void fenetreDeDemarrage::chargerImagePourProcessus(int processusId)
{
    QSqlQuery query;
    query.prepare("SELECT contenuBlob FROM Image WHERE idProcessus = :processus_id LIMIT 1");
    query.bindValue(":processus_id", processusId);

    if (query.exec() && query.next()) {
        QByteArray imageData = query.value(0).toByteArray();

        if (imageData.isEmpty()) {
            qDebug() << "Aucune image trouvée dans la base de données pour le processus avec ID :" << processusId;
            imageLabel->clear();
            statusLabel->setText("Aucune image trouvée pour ce processus.");
            return;
        }

        QPixmap pixmap;
        if (pixmap.loadFromData(imageData)) {
            imageLabel->setPixmap(pixmap.scaled(300, 200, Qt::KeepAspectRatio, Qt::SmoothTransformation));
        } else {
            qDebug() << "Erreur lors du chargement de l'image.";
            imageLabel->clear();
            statusLabel->setText("Erreur lors du chargement de l'image.");
        }
    } else {
        qDebug() << "Erreur lors de la récupération de l'image pour le processus avec ID :" << processusId
                 << "Erreur SQL :" << query.lastError().text();
        imageLabel->clear();
        statusLabel->setText("Erreur lors de la récupération de l'image.");
    }
}

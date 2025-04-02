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
    connect(processusComboBox, QOverload<int>::of(&QComboBox::currentIndexChanged), this, &fenetreDeDemarrage::chargerImageSelectionnee);

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

void fenetreDeDemarrage::chargerImageSelectionnee()
{
    QVariant selectedData = processusComboBox->currentData();
    if (selectedData.isValid()) {
        int processusId = selectedData.toInt();
        chargerImagePourProcessus(processusId);  // Charger l'image après la sélection
    } else {
        qDebug() << "Aucun processus sélectionné.";
        imageLabel->clear();  // Clear the image label if no process is selected
    }
}

void fenetreDeDemarrage::chargerImagePourProcessus(int processusId)
{
    QSqlQuery query;
    query.prepare("SELECT contenuBlob FROM Image "
                  "JOIN Processus ON Processus.idImage = Image.idImage "
                  "WHERE Processus.idProcessus = :idProcessus");
    query.bindValue(":idProcessus", processusId);

    if (query.exec() && query.next()) {
        QByteArray imageData = query.value(0).toByteArray();

        if (!imageData.isEmpty()) {
            QPixmap pixmap;
            if (pixmap.loadFromData(imageData)) {
                imageLabel->setPixmap(pixmap.scaled(300, 200, Qt::KeepAspectRatio, Qt::SmoothTransformation));
            } else {
                qDebug() << "Erreur : Impossible de charger l'image.";
                imageLabel->clear();  // Clear the image label if image cannot be loaded
            }
        } else {
            qDebug() << "Aucune image trouvée pour ce processus.";
            imageLabel->clear();  // Clear the image label if no image is found
        }
    } else {
        qDebug() << "Erreur SQL :" << query.lastError().text();
        imageLabel->clear();  // Clear the image label if there's an SQL error
    }
}

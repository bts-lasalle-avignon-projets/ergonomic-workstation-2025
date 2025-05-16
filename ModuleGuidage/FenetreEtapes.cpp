#include "FenetreEtapes.h"
#include "BaseDeDonnees.h"
#include "Communication.h"
#include <QSqlQuery>
#include <QSqlError>
#include <QGroupBox>
#include <QVBoxLayout>
#include <QDebug>

FenetreEtapes::FenetreEtapes(QWidget* parent)
    : QWidget(parent),
      etapeActuelIndex(0),
      idProcessusActuel(-1)
{
    setObjectName("FenetreEtape");
    initialiserFenetre();

    connect(boutonEtapeSuivante,
            &QPushButton::clicked,
            this,
            &FenetreEtapes::chargerEtapeSuivante);

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
    auto* layoutPrincipal = new QVBoxLayout(this);

    // En-tête : numéro + nom
    auto* layoutHaut = new QHBoxLayout;
    labelNumeroEtape = new QLabel("Étape n°", this);
    labelNomEtape    = new QLabel("Nom étape", this);
    layoutHaut->addWidget(labelNumeroEtape);
    layoutHaut->addStretch();
    layoutHaut->addWidget(labelNomEtape);

    // Image
    imageEtape = new QLabel(this);
    imageEtape->setFixedSize(LARGEUR_UI, HAUTEUR_IMAGE_ETAPE);
    imageEtape->setAlignment(Qt::AlignCenter);

    // Bacs en ligne
    layoutBacs = new QHBoxLayout;

    // Boutons
    boutonEtapeSuivante = new QPushButton("Étape suivante", this);
    boutonEtapeSuivante->setEnabled(false);
    boutonQuitter = new QPushButton("Quitter le processus", this);
    connect(boutonQuitter, &QPushButton::clicked, this, &FenetreEtapes::quitterProcessus);

    // Description
    auto* layoutDescription = new QHBoxLayout;
    labelDescriptionEtape = new QLabel("Description étape", this);
    labelDescriptionEtape->setWordWrap(true);
    layoutDescription->addWidget(labelDescriptionEtape);

    // État
    auto* layoutEtat = new QHBoxLayout;
    labelEtatRequete = new QLabel("État requête", this);
    layoutEtat->addWidget(labelEtatRequete);
    layoutEtat->addStretch();

    // Layout bouton "Quitter" à droite
    auto* layoutQuitter = new QHBoxLayout;
    layoutQuitter->addStretch();
    layoutQuitter->addWidget(boutonQuitter);

    // Assemblage
    layoutPrincipal->addLayout(layoutHaut);
    layoutPrincipal->addWidget(imageEtape, 0, Qt::AlignCenter);
    layoutPrincipal->addLayout(layoutBacs);
    layoutPrincipal->addWidget(boutonEtapeSuivante, 0, Qt::AlignHCenter);
    layoutPrincipal->addLayout(layoutDescription);
    layoutPrincipal->addLayout(layoutEtat);
    layoutPrincipal->addLayout(layoutQuitter);

    setLayout(layoutPrincipal);
}


void FenetreEtapes::chargerEtape(int idProcessus)
{
    qDebug() << Q_FUNC_INFO << "idProcessus =" << idProcessus;
    if (idProcessus <= 0) {
        qWarning() << "ID invalide";
        return;
    }

    listeDesEtapes.clear();
    idProcessusActuel = idProcessus;

    db = BaseDeDonnees::getDatabase();
    if (!db.isOpen() && !db.open()) {
        qCritical() << "Impossible d'ouvrir la BDD :" << db.lastError().text();
        labelEtatRequete->setText("Erreur BDD : " + db.lastError().text());
        return;
    }

    QSqlQuery query(db);
    query.prepare(R"(
        SELECT idEtape, numeroEtape, nomEtape, descriptionEtape
        FROM Etape
        WHERE idProcessus = :pid
        ORDER BY numeroEtape ASC
    )");
    query.bindValue(":pid", idProcessusActuel);

    if (query.exec()) {
        while (query.next()) {
            Etape e;
            e.idEtape          = query.value("idEtape").toInt();
            e.numeroEtape      = query.value("numeroEtape").toInt();
            e.nomEtape         = query.value("nomEtape").toString();
            e.descriptionEtape = query.value("descriptionEtape").toString();
            listeDesEtapes.append(e);
        }

        qDebug() << "Étapes chargées :" << listeDesEtapes.size();

        if (listeDesEtapes.isEmpty()) {
            labelEtatRequete->setText("Aucune étape trouvée !");
        } else {
            etapeActuelIndex = recupererIndexDerniereEtape(idProcessusActuel);
            afficherEtapeActuelle();
            boutonEtapeSuivante->setEnabled(true);
        }
    } else {
        qCritical() << "Erreur SQL chargerEtape:" << query.lastError().text();
        labelEtatRequete->setText("Erreur SQL : " + query.lastError().text());
    }
}

void FenetreEtapes::sauvegarderEtatProcessus()
{
    if (listeDesEtapes.isEmpty() || etapeActuelIndex < 0 || etapeActuelIndex >= listeDesEtapes.size())
        return;

    int idEtapeActuelle = listeDesEtapes[etapeActuelIndex].idEtape;

    QSqlQuery q(db);
    q.prepare(R"(
        INSERT INTO EtatProcessus (idProcessus, idEtapeActuelle)
        VALUES (:pid, :eid)
        ON DUPLICATE KEY UPDATE
            idEtapeActuelle = VALUES(idEtapeActuelle),
            dateDerniereModification = CURRENT_TIMESTAMP
    )");
    q.bindValue(":pid", idProcessusActuel);
    q.bindValue(":eid", idEtapeActuelle);

    if (!q.exec()) {
        qWarning() << "Échec sauvegarde état processus:" << q.lastError().text();
    }
}

int FenetreEtapes::recupererIndexDerniereEtape(int idProcessus)
{
    QSqlQuery q(db);
    q.prepare("SELECT idEtapeActuelle FROM EtatProcessus WHERE idProcessus = :pid");
    q.bindValue(":pid", idProcessus);
    if (q.exec() && q.next()) {
        int idEtapeSauvegardee = q.value(0).toInt();
        for (int i = 0; i < listeDesEtapes.size(); ++i) {
            if (listeDesEtapes[i].idEtape == idEtapeSauvegardee)
                return i;
        }
    }
    return 0;
}

void FenetreEtapes::afficherBacs(const QVector<QPair<int, QString>>& bacs, int bacDeLEtape)
{
    int count = bacs.size();
    if (count == 0) {
        auto* lbl = new QLabel("— aucun bac —", this);
        layoutBacs->addWidget(lbl);
        return;
    }

    int largeur = LARGEUR_UI / count;
    for (const auto& pair : bacs) {
        int num = pair.first;
        QString cont = pair.second;
        auto* gb = new QGroupBox(QString("Bac n°%1").arg(num), this);
        gb->setFixedWidth(largeur);

        auto* vb = new QVBoxLayout(gb);
        auto* labelCont = new QLabel("Contenance : " + cont, gb);
        labelCont->setStyleSheet("font-size: 9pt; color: black;");
        vb->addWidget(labelCont);

        // Surlignage
        if (num == bacDeLEtape) {
            gb->setStyleSheet("QGroupBox { background-color: #c4fcd4; border: 2px solid green; }");
        } else {
            gb->setStyleSheet("QGroupBox { background-color: #fcd4d4; border: 1px solid darkred; }");
        }

        layoutBacs->addWidget(gb);
        groupesBacs.append(gb);
    }
}

void FenetreEtapes::afficherEtapeActuelle()
{
    if (etapeActuelIndex < 0 || etapeActuelIndex >= listeDesEtapes.size())
        return;

    const auto& e = listeDesEtapes[etapeActuelIndex];
    afficherTexteEtape(e);
    chargerImagePourEtape(e.idEtape);

    nettoyerLayoutBacs();

    int bacDeLEtape = recupererBacDeLEtape(e.idEtape);
    QVector<QPair<int, QString>> bacs = recupererBacsProcessus(idProcessusActuel);
    afficherBacs(bacs, bacDeLEtape);
}

void FenetreEtapes::nettoyerLayoutBacs()
{
    QLayoutItem* it;
    while ((it = layoutBacs->takeAt(0)) != nullptr) {
        if (auto* w = it->widget()) w->deleteLater();
        delete it;
    }
    groupesBacs.clear();
}

int FenetreEtapes::recupererBacDeLEtape(int idEtape)
{
    QSqlQuery q(db);
    q.prepare("SELECT idBac FROM Etape WHERE idEtape = :eid");
    q.bindValue(":eid", idEtape);
    if (q.exec() && q.next())
        return q.value(0).toInt();

    qWarning() << "Erreur récupération bac de l'étape:" << q.lastError().text();
    return -1;
}

QVector<QPair<int, QString>> FenetreEtapes::recupererBacsProcessus(int idProcessus)
{
    QVector<QPair<int, QString>> data;
    QSqlQuery q(db);
    q.prepare(R"(
        SELECT numeroBac, contenance
        FROM Bac
        WHERE idProcessus = :pid
        ORDER BY numeroBac ASC
    )");
    q.bindValue(":pid", idProcessus);

    if (q.exec()) {
        while (q.next()) {
            data.append({ q.value("numeroBac").toInt(), q.value("contenance").toString() });
        }
    } else {
        qWarning() << "Erreur SQL Bac:" << q.lastError().text();
        labelEtatRequete->setText("Erreur SQL Bac");
    }

    return data;
}

void FenetreEtapes::afficherTexteEtape(const Etape& e)
{
    labelNumeroEtape->setText("Étape n°" + QString::number(e.numeroEtape));
    labelNomEtape->setText(e.nomEtape);
    labelDescriptionEtape->setText(e.descriptionEtape);
    labelEtatRequete->clear();
    imageEtape->clear();
}

void FenetreEtapes::chargerImagePourEtape(int idEtape)
{
    QSqlQuery q(db);
    q.prepare(R"(
        SELECT I.contenuBlob
        FROM Image I
        JOIN Etape E ON E.idImage = I.idImage
        WHERE E.idEtape = :eid
    )");
    q.bindValue(":eid", idEtape);

    if (q.exec() && q.next()) {
        auto blob = q.value(0).toByteArray();
        if (!blob.isEmpty()) {
            QPixmap pm;
            if (pm.loadFromData(blob)) {
                imageEtape->setPixmap(
                    pm.scaled(LARGEUR_UI, HAUTEUR_IMAGE_ETAPE,
                              Qt::KeepAspectRatio, Qt::SmoothTransformation));
                return;
            }
        }
    }
    // pas d'image ou échec
    imageEtape->clear();
}

void FenetreEtapes::chargerEtapeSuivante()
{
    if (listeDesEtapes.isEmpty()) {
        labelEtatRequete->setText("Aucune étape disponible !");
        return;
    }

    if (etapeActuelIndex + 1 < listeDesEtapes.size()) {
        ++etapeActuelIndex;
        afficherEtapeActuelle();
    } else {
        labelEtatRequete->setText("Processus terminé !");
        boutonEtapeSuivante->setEnabled(false);

        // Réinitialiser l’état du processus à la fin
        QSqlQuery q(db);
        q.prepare("DELETE FROM EtatProcessus WHERE idProcessus = :pid");
        q.bindValue(":pid", idProcessusActuel);
        if (!q.exec()) {
            qWarning() << "Erreur suppression état processus:" << q.lastError().text();
        } else {
            qDebug() << "État du processus réinitialisé.";
        }
    }
}

void FenetreEtapes::quitterProcessus()
{
    if (etapeActuelIndex < listeDesEtapes.size() - 1) {
        sauvegarderEtatProcessus();
    }
    close();
    emit fermerEtapes();
}

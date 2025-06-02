#ifndef COMMUNICATION_H
#define COMMUNICATION_H

#include <QObject>
#include <QSerialPort>
#include <QString>

/**
 * @brief La classe Communication gère la communication série avec l'ESP32 via Bluetooth.
 */
class Communication : public QObject
{
    Q_OBJECT

public:
    explicit Communication(QObject *parent = nullptr);
    ~Communication();

    /**
     * @brief Connecte la communication au port série Bluetooth donné.
     */
    bool connecter();


    /**
     * @brief Envoie une trame au module ESP32.
     */
    void envoyerTrame(const QString &trame);

    /**
     * @brief Envoie des trames
     */
    void envoyerAcquittement();
    void envoyerFinProcessusOuEtape();
    void envoyerDebutProcessus();

signals:
    /**
     * @brief Signal émis lorsqu'une trame complète est reçue.
     */
    void trameRecue(const QString &trame);

private slots:
    void lireTrame();

private:
    QSerialPort *portSerie;
    QString bufferReception;
};

#endif // COMMUNICATION_H

#include "Communication.h"
#include <QDebug>

Communication::Communication(QObject *parent)
    : QObject(parent),
      portSerie(new QSerialPort(this)),
      bufferReception()
{
    connect(portSerie, &QSerialPort::readyRead, this, &Communication::lireTrame);
}

Communication::~Communication()
{
    if (portSerie->isOpen())
        portSerie->close();
}

bool Communication::connecter()
{
    QString nomPort = "/dev/rfcomm1";  // Port Bluetooth RFCOMM forcé ici
    portSerie->setPortName(nomPort);
    if (!portSerie->open(QIODevice::ReadWrite))
        return false;

    portSerie->setBaudRate(QSerialPort::Baud115200);
    portSerie->setParity(QSerialPort::NoParity);
    portSerie->setDataBits(QSerialPort::Data8);
    portSerie->setStopBits(QSerialPort::OneStop);
    portSerie->setFlowControl(QSerialPort::NoFlowControl);

    return true;
}

void Communication::envoyerTrame(const QString &trame)
{
    if (portSerie->isOpen())
    {
        QByteArray data = trame.toUtf8();
        portSerie->write(data);
    }
}

void Communication::envoyerAcquittement()
{
    envoyerTrame("$A%");
}

void Communication::envoyerDebutProcessus()
{
    qDebug() << Q_FUNC_INFO << "Envoi trame début processus : $D%";
    envoyerTrame("$D%");
}


void Communication::envoyerFinProcessusOuEtape()
{
    qDebug() << Q_FUNC_INFO << "Envoi trame fin processus / étape : $F%";
    envoyerTrame("$F%");
}

void Communication::lireTrame()
{
    bufferReception.append(portSerie->readAll());

    int posFin = bufferReception.indexOf('%');
    while (posFin != -1)
    {
        QString trameComplete = bufferReception.left(posFin + 1);
        bufferReception.remove(0, posFin + 1);

        emit trameRecue(trameComplete.left(trameComplete.length() - 1));

        posFin = bufferReception.indexOf('%');
    }
}

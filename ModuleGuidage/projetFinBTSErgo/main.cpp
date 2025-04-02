#include "fenetreDeDemarrage.h"
#include "fenetreDesEtapes.h"
#include <QApplication>

int main(int argc, char *argv[])
{
    QApplication a(argc, argv);

    fenetreDesEtapes fenetreEtapes;
    fenetreDeDemarrage fenetreDemarrage(&fenetreEtapes);

    fenetreDemarrage.show();

    return a.exec();
}

#ifndef CONNEXIONBDD_H
#define CONNEXIONBDD_H

#include <QSqlDatabase>
#include <QSqlError>
#include <QDebug>

class connexionBDD
{

public:
    static QSqlDatabase getDatabase();

private:
    connexionBDD();
    static QSqlDatabase db;
};

#endif // CONNEXIONBDD_H

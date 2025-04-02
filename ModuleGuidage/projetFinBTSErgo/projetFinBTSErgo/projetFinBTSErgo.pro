QT += core gui widgets sql

CONFIG += c++11

DEFINES += QT_DEPRECATED_WARNINGS

SOURCES += \
    connexionBDD.cpp \
    fenetreDeDemarrage.cpp \
    fenetreDesEtapes.cpp \
    main.cpp

HEADERS += \
    connexionBDD.h \
    fenetreDeDemarrage.h \
    fenetreDesEtapes.h \
    main.h

FORMS += \
    fenetreDeDemarrage.ui \

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
    fenetreDeDemarrage.ui

TRANSLATIONS += \
    bacASableQt_fr_FR.ts

qnx: target.path = /tmp/$${TARGET}/bin
else: unix:!android: target.path = /opt/$${TARGET}/bin
!isEmpty(target.path): INSTALLS += target

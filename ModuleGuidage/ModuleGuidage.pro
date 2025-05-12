QT += core gui widgets sql

CONFIG += c++11

DEFINES += QT_DEPRECATED_WARNINGS

SOURCES += \
    BaseDeDonnees.cpp \
    FenetreDemarrage.cpp \
    FenetreEtapes.cpp \
    main.cpp

HEADERS += \
    BaseDeDonnees.h \
    Etape.h \
    FenetreDemarrage.h \
    FenetreEtapes.h

FORMS += \
    FenetreDemarrage.ui

RESOURCES += \
    ModuleGuidage.qrc

# Les defines pour la version release (sans debug)
#CONFIG(release, debug|release):DEFINES+=QT_NO_DEBUG_OUTPUT RPI
# Les defines pour la version debug
CONFIG(debug, debug|release):DEFINES+=DEBUG

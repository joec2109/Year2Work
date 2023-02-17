QT       += core gui sql

greaterThan(QT_MAJOR_VERSION, 4): QT += widgets

CONFIG += c++11

# You can make your code fail to compile if it uses deprecated APIs.
# In order to do so, uncomment the following line.
#DEFINES += QT_DISABLE_DEPRECATED_BEFORE=0x060000    # disables all the APIs deprecated before Qt 6.0.0

SOURCES += \
    MainWindow.cpp \
    adminmainwindow.cpp \
    bankermainwindow.cpp \
    enterbankpin.cpp \
    forgotbankpin.cpp \
    forgotpassword.cpp \
    globals.cpp \
    loginwindow.cpp \
    main.cpp \
    signupwindow.cpp

HEADERS += \
    MainWindow.h \
    adminmainwindow.h \
    bankermainwindow.h \
    enterbankpin.h \
    forgotbankpin.h \
    forgotpassword.h \
    globals.h \
    globals.h \
    loginwindow.h \
    signupwindow.h

FORMS += \
    MainWindow.ui \
    adminmainwindow.ui \
    bankermainwindow.ui \
    enterbankpin.ui \
    forgotbankpin.ui \
    forgotpassword.ui \
    loginwindow.ui \
    signupwindow.ui

TRANSLATIONS += \
    OOSD_en_GB.ts
CONFIG += lrelease
CONFIG += embed_translations

# Default rules for deployment.
qnx: target.path = /tmp/$${TARGET}/bin
else: unix:!android: target.path = /opt/$${TARGET}/bin
!isEmpty(target.path): INSTALLS += target

#include "MainWindow.h"
#include "loginwindow.h"
#include "globals.h"
#include <QApplication>
#include <QLocale>
#include <QTranslator>
#include <QTextStream>
#include <QtSql>
#include <QSqlDatabase>
#include <QStringList>
#include <QSql>
#include <QSqlDatabase>

int main(int argc, char *argv[])
{
    QApplication a(argc, argv);

    QTranslator translator;
    const QStringList uiLanguages = QLocale::system().uiLanguages();
    for (const QString &locale : uiLanguages) {
        const QString baseName = "OOSD_" + QLocale(locale).name();
        if (translator.load(":/i18n/" + baseName)) {
            a.installTranslator(&translator);
            break;
        }
    }

    // Connecting to db
    QSqlDatabase db = QSqlDatabase::addDatabase("QMYSQL");
    db.setHostName("127.0.0.1");
    db.setUserName("root");
    db.setPassword("");
    db.setDatabaseName("oosdproject");

    if (db.open()) {
        QTextStream(stdout) << "\nDatabase is connected succesfully";
    } else {
        QSqlError error = db.lastError();
        QTextStream(stdout) << "\nDatabase is not connected\n"+error.text();
    }

    QSqlQuery query;
    query.prepare("CREATE TABLE IF NOT EXISTS users (id INTEGER AUTO_INCREMENT PRIMARY KEY, username VARCHAR(32), forename VARCHAR(32), surname VARCHAR(32), password VARCHAR(32), securityQuestion VARCHAR(255), securityAnswer VARCHAR(255), pin INTEGER, userType VARCHAR(32), balance DOUBLE NOT NULL)" );

    QSqlQuery query2;
    query2.prepare("SELECT username FROM users WHERE username='admin'");
    query2.exec();
    if (query2.next()) {
        QTextStream(stdout) << "\nAdmin user already exists";
    } else {
        QSqlQuery query3;
        query3.prepare("INSERT INTO users (username, password, balance) VALUES ('admin', 'Admin123', '0')");
        query3.exec();
        QTextStream(stdout) << "\nAdmin user created";
    }

    if (query.exec()) {
        QTextStream(stdout) << "\nUsers table created successfully";
    } else {
        QTextStream(stdout) << "\nUsers table already exists\n" + query.lastError().text();
    }

    QSqlQuery checkUser;
    checkUser.prepare("SELECT username FROM users WHERE username='ASmith'");
    checkUser.exec();
    if (checkUser.next()) {
        QTextStream(stdout) << "\nUser Adam Smith already exists";
    } else {
        QSqlQuery insertUser;
        insertUser.prepare("INSERT INTO users (username, forename, surname, password, securityQuestion, securityAnswer, pin, userType, Balance) VALUES ('ASmith', 'Adam', 'Smith', 'Basketball123', 'What is the name of your favorite pet?', 'Ralf', '1234', 'Customer', '0')");
        insertUser.exec();
        QTextStream(stdout) << "\nUser Adam Smith inserted successfully.";
    }

    // Collecting accounts information
    QSqlQuery collectingAccounts;
    collectingAccounts.prepare("SELECT forename, surname, password, pin, balance, userType FROM users WHERE username != 'admin'");
    collectingAccounts.exec();
    while(collectingAccounts.next()) {
        accountNames.append(collectingAccounts.value(0).toString() + " " + collectingAccounts.value(1).toString());
        accountPasswords.append(collectingAccounts.value(2).toString());
        accountPins.append(collectingAccounts.value(3).toString());
        accountBalances.append(collectingAccounts.value(4).toString());
        accountUserTypes.append(collectingAccounts.value(5).toString());
    }

    // Collecting accounts information of just customers
    QSqlQuery collectingCustomerAccounts;
    collectingCustomerAccounts.prepare("SELECT forename, surname, password, pin, balance, userType FROM users WHERE userType = 'Customer'");
    collectingCustomerAccounts.exec();
    while(collectingCustomerAccounts.next()) {
        customerAccountNames.append(collectingCustomerAccounts.value(0).toString() + " " + collectingCustomerAccounts.value(1).toString());
        customerAccountPasswords.append(collectingCustomerAccounts.value(2).toString());
        customerAccountPins.append(collectingCustomerAccounts.value(3).toString());
        customerAccountBalances.append(collectingCustomerAccounts.value(4).toString());
        customerAccountUserTypes.append(collectingCustomerAccounts.value(5).toString());
    }

    LoginWindow *lw = new LoginWindow;
    lw->show();
    return a.exec();

}

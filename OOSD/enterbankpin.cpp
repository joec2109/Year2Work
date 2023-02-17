#include "enterbankpin.h"
#include "ui_enterbankpin.h"
#include "loginwindow.h"
#include "globals.h"
#include "MainWindow.h"
#include "ui_mainwindow.h"
#include "forgotbankpin.h"
#include <QTextStream>
#include <QtSql>
#include <QSqlDatabase>
#include <QMessageBox>

class account {
public:
    QString selectUser(QString pin, QString uname) {
        QSqlQuery query;
        query.prepare("SELECT * FROM users WHERE pin = :bankPin AND username = :username");
        query.bindValue(":bankPin", pin);
        query.bindValue(":username", uname);

        if(query.exec()){
            if(query.size() > 0) {
                return "Success";
            } else {
                return "";
            }
        } else {
            return "";
        }
    }

    void selectForename(QString pin, QString uname) {
        // Grabbing the forename of the logged in user
        QSqlQuery query2;
        query2.prepare("SELECT forename FROM users WHERE pin = :bankPin AND username = :username");
        query2.bindValue(":bankPin", pin);
        query2.bindValue(":username", uname);

        if (query2.exec()) {
            if (query2.next()) {
                globalForename = query2.value(0).toString();
            }
        }
    }

    void selectBalance(QString pin, QString uname) {
        // Grabbing the account balance of the logged in user
        QSqlQuery query3;
        query3.prepare("SELECT balance FROM users WHERE pin = :globalBankPin AND username = :globalUsername");
        query3.bindValue(":globalBankPin", pin);
        query3.bindValue(":globalUsername", uname);

        if (query3.exec()) {
            if (query3.next()) {
                globalBalance = query3.value(0).toString();
            }
        }
    }
};

// The main function, executes when an object of 'enterbankpin' is created
EnterBankPin::EnterBankPin(QWidget *parent) :
    QMainWindow(parent),
    ui(new Ui::EnterBankPin)
{
    ui->setupUi(this);
}

EnterBankPin::~EnterBankPin()
{
    delete ui;
}

// Check if the bank pin entered is valid and log them in if it is
void EnterBankPin::on_EnterButton_clicked()
{
    account newAccount;

    QString bankPin = ui->BankPinBox->text();
    QString username = globalUsername;

    // Call of the selectUser function that checks if the bank pin is the correct one for their account on the db
    QString suResult = newAccount.selectUser(bankPin, username);
    try {
        if (suResult == "Success") {
            globalBankPin = bankPin;

            // Call of selectForename function that grabs the forename of the logged in user and sets it to a global variable
            newAccount.selectForename(bankPin, username);

            // Call of selectBalance function that grabs the balance of the logged in user and sets it to a global variable
            newAccount.selectBalance(bankPin, username);

            QTextStream(stdout) << "\nCorrect bank PIN";
            throw(0);
        } else {
            throw(1);
        }
    }
    catch (int result) {
        if (result == 0) {
            QMessageBox::StandardButton alert;
            alert = QMessageBox::information(this, "Bank PIN", "Bank PIN correct",
                                        QMessageBox::Ok);
            if (alert == QMessageBox::Ok) {
                qDebug() << "\nOk was clicked";
                this->hide();
                MainWindow *mw= new MainWindow();
                mw->show();
            } else {
                qDebug() << "\nOk was *not* clicked";
            }
        } else {
            QTextStream(stdout) << "Bank PIN is incorrect";
            QMessageBox::StandardButton alert;
            alert = QMessageBox::warning(this, "Bank PIN", "Bank PIN is incorrect",
                                        QMessageBox::Ok);
            if (alert == QMessageBox::Ok) {
                qDebug() << "\nOk was clicked";
            } else {
                qDebug() << "\nOk was *not* clicked";
            }
        }
    }
}

// Take the user back to the login window, and hide the enter bank pin window
void EnterBankPin::on_backButton_clicked()
{
    this->hide();
    LoginWindow *lw = new LoginWindow;
    lw->show();
}

// If forgot bank pin button is clicked, show the forgot bank pin window and hide the enter bank pin window
void EnterBankPin::on_ForgotBankPinButton_clicked()
{
    this->hide();
    ForgotBankPin *fbp = new ForgotBankPin;
    fbp->show();
}


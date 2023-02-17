#include "signupwindow.h"
#include "ui_signupwindow.h"
#include "MainWindow.h"
#include "loginwindow.h"
#include "globals.h"
#include <QTextStream>
#include <QtSql>
#include <QSqlDatabase>
#include <QMessageBox>

using namespace std;

// Classes with functions used within this source file.
class account {

public:

    // Function to append the newly created account details to the account global variables if they are valid
    void account_global_append(QString forename, QString surname, QString pword, QString strBankpin) {
        accountNames.append(forename + " " + surname);
        accountPasswords.append(pword);
        accountPins.append(strBankpin);
        accountBalances.append("0");
        accountUserTypes.append("Customer");
    }

    // Function to insert details in to the db if they are valid
    void create_new_account(QString username, QString forename, QString surname, QString pword, QString secquestion, QString sqanswer, int bankpin) {
        QTextStream(stdout) <<"\nAttempting to insert details in to db...";
        QSqlQuery qry;
        qry.prepare("INSERT INTO users (username, forename, surname, password, securityQuestion, securityAnswer, pin, userType)"
                    "VALUES (:username, :forename, :surname, :password, :securityQuestion, :securityAnswer, :bankpin, 'Customer')");

        qry.bindValue(":username", username);
        qry.bindValue(":forename", forename);
        qry.bindValue(":surname", surname);
        qry.bindValue(":password", pword);
        qry.bindValue(":securityQuestion", secquestion);
        qry.bindValue(":securityAnswer", sqanswer);
        qry.bindValue(":bankpin", bankpin);
        QTextStream(stdout) << "\nChecking if details were inserted...";
        if(qry.exec()) {
            QTextStream(stdout) << "\nInserted details in to db.";
        } else {
            QTextStream(stdout) << "\nFailed to insert details in to db.";
        }
    }
};

// Initialise the SignUpWindow object.
SignUpWindow::SignUpWindow(QWidget *parent) :
    QMainWindow(parent),
    ui(new Ui::SignUpWindow)
{
    ui->setupUi(this);
}

SignUpWindow::~SignUpWindow()
{
    delete ui;
}

// Validate the details the user has inputted to see if they can create an account. If they are valid, create the account.
void SignUpWindow::on_SignUpButton_clicked()
{
    bool forenameValid = false, surnameValid = false, passwordValid = false, cfirmPasswordValid = false, SQValid = false, SQAnswerValid = false, pinValid = false;

    // Validation

    // Forename validation
    if (ui->ForenameBox->text().isEmpty()) {
        QTextStream(stdout) << "\nForename is empty";
        ui->ForenameLabel->setStyleSheet("QLabel { color : red; }");
        forenameValid = false;
    } else {
        ui->ForenameLabel->setStyleSheet("QLabel { color : black; }");
        forenameValid = true;
    }

    // Surname validation
    if (ui->SurnameBox->text().isEmpty()) {
        QTextStream(stdout) << "\nSurname is empty";
        ui->SurnameLabel->setStyleSheet("QLabel { color : red; }");
        surnameValid = false;
    } else {
        ui->SurnameLabel->setStyleSheet("QLabel { color : black; }");
        surnameValid = true;
    }

    // Password validation
    if (ui->PasswordBox->text().isEmpty()) {
        QTextStream(stdout) << "\nPassword is empty";
        ui->PasswordLabel->setStyleSheet("QLabel { color : red; }");
        passwordValid = false;
    } else {
        ui->PasswordLabel->setStyleSheet("QLabel { color : black; }");
        passwordValid = true;
    }


    // Checking password length
    if (ui->PasswordBox->text().length() < 8) {
        QTextStream(stdout) << "\nPassword is not long enough";
        ui->PasswordLabel->setStyleSheet("QLabel { color : red; }");
        passwordValid = false;
    } else {
        ui->PasswordLabel->setStyleSheet("QLabel { color : black; }");
        passwordValid = true;
    }

    QString password = ui->PasswordBox->text();
    int upper = 0, lower = 0, numbers = 0;


    // Checking password characters
    for (int i = 0; i < ui->PasswordBox->text().length(); i++)
    {
        if (password[i].isLetter()){
            if (password[i].isUpper()){
                upper++;
            } else {
                lower++;
            }
        } else if (password[i].isDigit()){
            numbers++;
        }
    }

    if (upper >= 1 && lower >= 1 && numbers >= 1) {
        QTextStream(stdout) << "\nPassword is valid";
        ui->PasswordLabel->setStyleSheet("QLabel { color : black; }");
        passwordValid = true;
    }
    if (upper == 0) {
        QTextStream(stdout) << "\nPassword needs at least 1 upper case char";
        ui->PasswordLabel->setStyleSheet("QLabel { color : red; }");
        passwordValid = false;
    }
    if (lower == 0) {
        QTextStream(stdout) << "\nPassword needs at least 1 lower case char";
        ui->PasswordLabel->setStyleSheet("QLabel { color : red; }");
        passwordValid = false;
    }
    if (numbers == 0) {
        QTextStream(stdout) << "\nPassword needs at least 1 number";
        ui->PasswordLabel->setStyleSheet("QLabel { color : red; }");
        passwordValid = false;
    }


    // Confirm password validation
    if (ui->ConfirmPasswordBox->text().isEmpty()) {
        QTextStream(stdout) << "\nConfirm password is empty";
        ui->ConfirmPasswordLabel->setStyleSheet("QLabel { color : red; }");
    } else {
        ui->ConfirmPasswordLabel->setStyleSheet("QLabel { color : black; }");
    }

    if (ui->ConfirmPasswordBox->text() != ui->PasswordBox->text()){
        QTextStream(stdout) << "\nConfirm password must match password";
        ui->ConfirmPasswordLabel->setStyleSheet("QLabel { color : red; }");
    } else {
        cfirmPasswordValid = true;
        ui->ConfirmPasswordLabel->setStyleSheet("QLabel { color : black; }");
    }

    // Security question validation

    if (ui->SecurityQuestionBox->currentText() != "") {
        ui->SecurityQuestionLabel->setStyleSheet("QLabel { color : black; }");
        SQValid = true;
    } else {
        ui->SecurityQuestionLabel->setStyleSheet("QLabel { color : red; }");
        SQValid = false;
    }

    // Security question answer validation

    if (ui->SQAnswerBox->text().isEmpty()) {
        ui->SecurityQuestionAnswerLabel->setStyleSheet("QLabel { color : red; }");
        SQAnswerValid = false;
    } else {
        ui->SecurityQuestionAnswerLabel->setStyleSheet("QLabel { color : black; }");
        SQAnswerValid = true;
    }

    // Bank PIN validation

    QString pin = ui->BankPINBox->text();

    if (ui->BankPINBox->text().length() != 4){
        QTextStream(stdout) << "\nBank PIN must be at least 4 characters";
        ui->BankPINLabel->setStyleSheet("QLabel { color : red; }");
        pinValid = false;
    } else {
        ui->BankPINLabel->setStyleSheet("QLabel { color : black; }");
        pinValid = true;
    }

    int letters = 0;

    // Ensuring bank PIN only includes numbers
    if (ui->BankPINBox->text().length() == 4) {
        for (int i = 0; i < 4; i++) {
            if (pin[i].isLetter()){
                letters++;
            }
        }

        if (letters > 0) {
            QTextStream(stdout) << "\nBank PIN must only contain numbers";
            ui->BankPINLabel->setStyleSheet("QLabel { color : red; }");
            pinValid = false;
        } else {
            ui->BankPINLabel->setStyleSheet("QLabel { color : black; }");
            pinValid = true;
        }
    }

    // Confirm bank PIN validation

    if (ui->ConfirmBankPINBox->text() != ui->BankPINBox->text()) {
        QTextStream(stdout) << "\nConfirm bank PIN must be the same as the bank PIN";
        ui->ConfirmBankPINLabel->setStyleSheet("QLabel { color : red; }");
    } else {
        ui->ConfirmBankPINLabel->setStyleSheet("QLabel { color : black; }");
    }

    // If data entered is valid, insert in to the database.
    try {
        if ((forenameValid == true) && (surnameValid == true) && (passwordValid == true) && (SQValid == true) && (SQAnswerValid == true) && (pinValid == true) && (cfirmPasswordValid == true)) {
            QString forename = ui->ForenameBox->text();
            QString surname = ui->SurnameBox->text();
            QString pword = ui->PasswordBox->text();
            QString secquestion = ui->SecurityQuestionBox->currentText();
            QString sqanswer = ui->SQAnswerBox->text();
            QString strBankpin = ui->BankPINBox->text();
            int bankpin =  strBankpin.toInt();

            QTextStream(stdout) << "\nYour username is: " + (forename[0]+surname);

            QTextStream(stdout) << "\nDetails valid.";

            // Create an object of the newAccount class so the functions within it can be executed.
            account newAccount;

            // Append the new account details on to the account global variables
            newAccount.account_global_append(forename, surname, pword, strBankpin);

            // Insert the new account on to the db
            newAccount.create_new_account(forename[0]+surname, forename, surname, pword, secquestion, sqanswer, bankpin);
            QTextStream(stdout) << "\nUser inserted into DB";

            QMessageBox::StandardButton alert;
            alert = QMessageBox::information(this, "Sign Up", "Signed up successfully\nYour username is : " + (forename[0]+surname),
                                        QMessageBox::Ok);
            if (alert == QMessageBox::Ok) {
                qDebug() << "\nOk was clicked";
                this->hide();
                LoginWindow *lw= new LoginWindow();
                lw->show();
            } else {
                qDebug() << "\nOk was *not* clicked";
            }
        } else {
            throw(1);
        }
    }
    // If data entered is invalid, show a 'failed to sign up' message.
    catch (int result) {
        QTextStream(stdout) << "\nUser inserted into DB";

        QMessageBox::StandardButton alert;
        alert = QMessageBox::information(this, "Sign Up", "Failed to sign up",
                                    QMessageBox::Ok);
        if (alert == QMessageBox::Ok) {
            qDebug() << "\nOk was clicked";
        } else {
            qDebug() << "\nOk was *not* clicked";
        }
    }
}

// Return user to the login window if the back button is clicked.
void SignUpWindow::on_backButton_clicked()
{
    this->hide();
    LoginWindow *lw = new LoginWindow;
    lw->show();
}


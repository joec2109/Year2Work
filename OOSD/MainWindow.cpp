#include "MainWindow.h"
#include "ui_MainWindow.h"
#include "enterbankpin.h"
#include "loginwindow.h"
#include "ui_loginwindow.h"
#include "globals.h"
#include <QSqlQuery>
#include <QMessageBox>
#include <QSqlRecord>
#include <QTableWidget>
#include <QTableWidgetItem>
#include <Qt>

// class that includes various functions that handle the data of the logged in user's account
class account {
private:
    QString return_balance() {
        QSqlQuery query;
        query.prepare("SELECT balance FROM users WHERE pin = :globalBankPin AND username = :globalUsername");
        query.bindValue(":globalBankPin", globalBankPin);
        query.bindValue(":globalUsername", globalUsername);
        query.exec();
        if(query.next()) {
            return query.value(0).toString();
        } else{
            return "";
        }
    }

    QString return_balance(QString bpin, QString uname) {
        QSqlQuery query;
        query.prepare("SELECT balance FROM users WHERE pin = :globalBankPin AND username = :globalUsername");
        query.bindValue(":globalBankPin", bpin);
        query.bindValue(":globalUsername", uname);
        query.exec();
        if(query.next()) {
            return query.value(0).toString();
        } else{
            return "";
        }
    }

public:

    // Collects the balance of the logged in user and inserts it in to the global balance variable
    QString set_global_balance() {
        QSqlQuery query;
        query.prepare("SELECT balance FROM users WHERE pin = :globalBankPin AND username = :globalUsername");
        query.bindValue(":globalBankPin", globalBankPin);
        query.bindValue(":globalUsername", globalUsername);
        query.exec();
        if (query.next()) {
            globalBalance = return_balance();
            double d_globalBalance = globalBalance.toDouble();
            double value = round( d_globalBalance * 100.0 ) / 100.0;
            QString balanceValue = QString::number(value);
            return balanceValue;
        } else {
            return "";
        }
    }

    // Collects the balance of the logged in user and inserts it in to the global balance variable
    void set_global_balance(QString bpin, QString uname) {
        QString balance = return_balance(bpin, uname);
        double d_globalBalance = balance.toDouble();
        double value = round( d_globalBalance * 100.0 ) / 100.0;
        QString balanceValue = QString::number(value);

        globalBalance = balanceValue;
    }

    // Collects the names of the other customers on the db and inserts them in to two global arrays (forenames + surnames)
    void set_global_target_names() {
        QSqlQuery query2;

        query2.prepare("SELECT forename, surname FROM users WHERE forename != :globalForename AND username != :globalUsername AND userType = 'Customer'");
        query2.bindValue(":globalForename", globalForename);
        query2.bindValue(":globalUsername", globalUsername);
        query2.exec();
        while (query2.next()) {
            int forenameID = query2.record().indexOf("forename");
            int surnameID = query2.record().indexOf("surname");
            globalTargetForenames.append(query2.value(forenameID).toString());
            globalTargetSurnames.append(query2.value(surnameID).toString());
        }
    }

    // Collects the remaining data of the logged in user from the db and inserts them in to global variables
    void set_globals() {
        QSqlQuery query3;

        query3.prepare("SELECT username, surname, password, securityQuestion, securityAnswer, userType FROM users WHERE forename = :globalForename AND pin = :globalBankPin");
        query3.bindValue(":globalForename", globalForename);
        query3.bindValue(":globalBankPin", globalBankPin);
        query3.exec();

        while (query3.next()) {
            int usernameID = query3.record().indexOf("username");
            int surnameID = query3.record().indexOf("surname");
            int passwordID = query3.record().indexOf("password");
            int securityQuestionID = query3.record().indexOf("securityQuestion");
            int securityAnswerID = query3.record().indexOf("securityAnswer");
            int userTypeID = query3.record().indexOf("userType");

            globalUsername = query3.value(usernameID).toString();
            globalSurname = query3.value(surnameID).toString();
            globalPassword = query3.value(passwordID).toString();
            globalSecurityQ = query3.value(securityQuestionID).toString();
            globalSecurityA = query3.value(securityAnswerID).toString();
            globalUserType = query3.value(userTypeID).toString();
        }
    }
};

// subclass that includes the functions for deposits
class depositActions: public account {
public:
    // Returns "Success" if the deposit has been successfully completed
    QString deposit(QString depositAmount) {
        QSqlQuery query;
        query.prepare("UPDATE users SET balance = balance + :amountToDeposit WHERE username = :globalUsername AND pin = :globalBankPin");
        query.bindValue(":amountToDeposit", depositAmount);
        query.bindValue(":globalUsername", globalUsername);
        query.bindValue(":globalBankPin", globalBankPin);

        globalTransactionAmounts.append(depositAmount);
        globalTransactionActions.append(globalUsername + " deposit");
        globalTransactionBalance.append(QString::number(globalBalance.toDouble() + depositAmount.toDouble()));
        globalBalance = QString::number(globalBalance.toDouble() + depositAmount.toDouble());

        if (query.exec()) {
            return "Success";
        } else {
            return "";
        }
    }

};

// subclass that includes the functions for withdrawals
class withdrawActions: public account {
public:
    QString withdraw(QString withdrawAmount) {
        QSqlQuery query;
        query.prepare("UPDATE users SET balance = balance - :amountToWithdraw WHERE username = :globalUsername AND pin = :globalBankPin");
        query.bindValue(":amountToWithdraw", withdrawAmount);
        query.bindValue(":globalUsername", globalUsername);
        query.bindValue(":globalBankPin", globalBankPin);

        globalTransactionAmounts.append(withdrawAmount);
        globalTransactionActions.append(globalUsername + " withdraw");
        globalTransactionBalance.append(QString::number(globalBalance.toDouble() - withdrawAmount.toDouble()));

        globalBalance = QString::number(globalBalance.toDouble() - withdrawAmount.toDouble());

        if(query.exec()) {
            return "Success";
        } else {
            return "";
        }
    }
};

// subclass that includes the functions of transfers
class transferActions: public account {
private:

    QString sending_user_function(QString transferAmount) {
        // Create query to remove the amount being transferred from the sending users account balance
        QSqlQuery query;
        query.prepare("UPDATE users SET balance = balance - :amountToTransfer WHERE username = :globalUsername AND pin = :globalBankPin");
        query.bindValue(":amountToTransfer", transferAmount);
        query.bindValue(":globalUsername", globalUsername);
        query.bindValue(":globalBankPin", globalBankPin);

        if (query.exec()) {
            return "Success";
        } else {
            return "";
        }
    }

    QString receiving_user_function(QString transferAmount, QString forename, QString surname) {
        // Create query to add the amount being transferred to the receiving users account balance.
        QSqlQuery query2;
        query2.prepare("UPDATE users SET balance = balance + :amountToTransfer WHERE forename = :targetForename AND surname = :targetSurname");
        query2.bindValue(":amountToTransfer", transferAmount);
        query2.bindValue(":targetForename", forename);
        query2.bindValue(":targetSurname", surname);
        if (query2.exec()) {
            return "Success";
        } else {
            return "";
        }
    }

public:
    QString transfer(QString transferAmount, QString receiverForename, QString receiverSurname) {

        QString sufResult = sending_user_function(transferAmount);

        QString rufResult = receiving_user_function(transferAmount, receiverForename, receiverSurname);

        if ((sufResult == "Success") && (rufResult == "Success")) {
            return "Success";
        } else {
            return "";
        }
    }
};

MainWindow::MainWindow(QWidget *parent)
    : QMainWindow(parent)
    , ui(new Ui::MainWindow)
{
    ui->setupUi(this);

    account Account;

    ui->WelcomeLabel->setText("Welcome, " + globalForename + ".");

    QString balanceValue = Account.set_global_balance();

    ui->accountBalanceText->setText("Account balance: £" + balanceValue);

    // Collect the names of other customers and append them to the globalTargetForenames and globalTargetSurnames variables
    Account.set_global_target_names();

    // Add each target name to the transfer combo box
    for (int i = 0; i < globalTargetForenames.size(); i++) {
        ui->transferCombo->addItem((globalTargetForenames[i]) + " " + (globalTargetSurnames[i]));
    }

    ui->stackedWidget->setCurrentIndex(0);

    // Collecting account info of logged in user and inserting it in to global variables for future use

    Account.set_globals();

}

MainWindow::~MainWindow()
{
    delete ui;
}

void MainWindow::on_homeButton_clicked()
{
    account Account;

    // Grabbing the account balance of the logged in user
    QString balanceValue = Account.set_global_balance();

    ui->accountBalanceText->setText("Account balance: £" + balanceValue);

    // Showing the relevant form elements
    ui->stackedWidget->setCurrentIndex(0);

}

// When the user clicks on the deposit subpage button, show them the correct elements for the page.
void MainWindow::on_depositButton_clicked()
{
    ui->stackedWidget->setCurrentIndex(1);
}

// Deposit the amount inputted by the user in to their account balance.
void MainWindow::on_depositButton_2_clicked()
{
    int numOfNumbers = 0;
    int numOfDecimalPoints = 0;
    int numOfSpaces = 0;
    QString depositAmountInput = ui->depositBox->text();

    depositActions da;

    // Loop through each character inputted by the user to check if they are either a number or a decimal point or a space.
    for (int i = 0; i < depositAmountInput.length(); i++) {
        if (depositAmountInput[i].isNumber()) {
            numOfNumbers++;
        }
        if (depositAmountInput[i] == '.') {
            numOfDecimalPoints++;
        }
        if (depositAmountInput[i] == ' ') {
            numOfSpaces++;
        }
    }

    try {
        if ((numOfSpaces == 0) && (numOfNumbers > 0)) {
            // If the deposit amount inputted by the user includes just numbers and maximum 1 bullet point then complete the rest of the code.
            if ((numOfNumbers == depositAmountInput.length() && numOfDecimalPoints ==  0) || (numOfNumbers == depositAmountInput.length()-1 && numOfDecimalPoints == 1)){

                QString daResult = da.deposit(depositAmountInput);

                if (daResult == "Success") {
                    QTextStream(stdout) << "\nDeposit succesful";
                    QMessageBox::StandardButton alert;
                    alert = QMessageBox::information(this, "Deposit", "Deposit successful",
                                                QMessageBox::Ok);
                    if (alert == QMessageBox::Ok) {
                        qDebug() << "\nOk was clicked";
                    } else {
                        qDebug() << "\nOk was *not* clicked";
                    }
                }
            } else {
                throw(1);
            }
        } else {
            throw(1);
        }
    }
    catch (int result) {
        QMessageBox::StandardButton alert;
        alert = QMessageBox::information(this, "Deposit", "Deposit unsuccessful. You can only deposit number values.",
                                    QMessageBox::Ok);
        if (alert == QMessageBox::Ok) {
            qDebug() << "\nOk was clicked";
        } else {
            qDebug() << "\nOk was *not* clicked";
        }
    }
}

// If withdraw subpage button clicked, show the withdraw subpage.
void MainWindow::on_withdrawButton_clicked()
{
    ui->stackedWidget->setCurrentIndex(2);
}

// If value inputted by user is valid, allow them to withdraw that amount.
void MainWindow::on_withdrawButton2_clicked()
{
    withdrawActions wa;

    double amountToWithdraw = ui->withdrawBox->text().toDouble();
    QString withdrawAmountInput = ui->withdrawBox->text();
    int numOfNumbers = 0;
    int numOfDecimalPoints = 0;
    int numOfSpaces = 0;

    // Loop through each character inputted by the user to check if they are either a number or a decimal point or a space.
    for (int i = 0; i < withdrawAmountInput.length(); i++) {
        if (withdrawAmountInput[i].isNumber()) {
            numOfNumbers++;
        }
        if (withdrawAmountInput[i] == '.') {
            numOfDecimalPoints++;
        }
        if (withdrawAmountInput[i] == ' ') {
            numOfSpaces++;
        }
    }

    // Grabbing the account balance of the logged in user
    wa.set_global_balance(globalBankPin, globalUsername);

    try {
        // If the user has enough funds in their balance to withdraw the amount they input, complete it.
        if ((globalBalance.toDouble() - amountToWithdraw >= 0) && (withdrawAmountInput != "") && (numOfSpaces == 0)) {
            // If the withdraw amount inputted by the user includes just numbers and maximum 1 bullet point then complete the rest of the code.
            if ((numOfNumbers == withdrawAmountInput.length() && numOfDecimalPoints ==  0) || (numOfNumbers == withdrawAmountInput.length()-1 && numOfDecimalPoints == 1)){

                // Calls the withdraw function within the withdraw actions class and stores the result in a QString variable.
                QString waResult = wa.withdraw(withdrawAmountInput);

                // If the withdraw is a success, display a message to the user telling them it was successful
                if (waResult == "Success") {
                    QTextStream(stdout) << "\nWithdraw succesful";
                    QMessageBox::StandardButton alert;
                    alert = QMessageBox::information(this, "Withdraw", "Withdraw successful",
                                                QMessageBox::Ok);
                    if (alert == QMessageBox::Ok) {
                        qDebug() << "\nOk was clicked";
                    } else {
                        qDebug() << "\nOk was *not* clicked";
                    }
                }
            } else {
                throw(1);
            }
        } else {
            throw(2);
        }
    }
    catch (int result) {
        if (result == 1) {
            QMessageBox::StandardButton alert;
            alert = QMessageBox::information(this, "Withdraw", "Withdraw unsuccessful. You can only withdraw number values.",
                                        QMessageBox::Ok);
            if (alert == QMessageBox::Ok) {
                qDebug() << "\nOk was clicked";
            } else {
                qDebug() << "\nOk was *not* clicked";
            }
        } else {
            QMessageBox::StandardButton alert;
            alert = QMessageBox::information(this, "Withdraw", "Withdraw unsuccessful. Insufficient funds.",
                                        QMessageBox::Ok);
            if (alert == QMessageBox::Ok) {
                qDebug() << "\nOk was clicked";
            } else {
                qDebug() << "\nOk was *not* clicked";
            }
        }
    }

}

// Go to transfer subpage if the transfer button is clicked.
void MainWindow::on_transferButton_clicked()
{
    ui->stackedWidget->setCurrentIndex(3);
}

// Once the transfer button is clicked, check to make sure an amount has been entered.
void MainWindow::on_transferButton2_clicked()
{

    transferActions ta;

    double amountToTransfer = ui->transferBox->text().toDouble();
    QString transferAmountInput = ui->transferBox->text();
    int numOfNumbers = 0;
    int numOfDecimalPoints = 0;
    int numOfSpaces = 0;
    if (globalTargetForenames.length() > 0) {
        QString targetForename = globalTargetForenames[ui->transferCombo->currentIndex()];
        QString targetSurname = globalTargetSurnames[ui->transferCombo->currentIndex()];
        QTextStream (stdout) << globalUsername +" is attempting to transfer £" + transferAmountInput + " to " + targetForename + " " + targetSurname;
    }

    // Loop through each character inputted by the user to check if they are either a number or a decimal point or a space.
    for (int i = 0; i < transferAmountInput.length(); i++) {
        if (transferAmountInput[i].isNumber()) {
            numOfNumbers++;
        }
        if (transferAmountInput[i] == '.') {
            numOfDecimalPoints++;
        }
        if (transferAmountInput[i] == ' ') {
            numOfSpaces++;
        }
    }

    try {
        // Ensure the transfer amount is not empty and doesn't include any spaces and the user actually has enough money to complete it.
        if ((numOfSpaces == 0) && (transferAmountInput != "") && (globalBalance.toDouble() - amountToTransfer >= 0)) {
            // Ensure the transfer amount only includes numbers and a possible decimal point.
            if (((numOfNumbers == transferAmountInput.length()) && (numOfDecimalPoints == 0)) || ((numOfNumbers == transferAmountInput.length() - 1) && (numOfDecimalPoints == 1))){

                QString targetForename = globalTargetForenames[ui->transferCombo->currentIndex()];
                QString targetSurname = globalTargetSurnames[ui->transferCombo->currentIndex()];

                QString taResults = ta.transfer(transferAmountInput, targetForename, targetSurname);

                globalTransactionAmounts.append(transferAmountInput);
                globalTransactionActions.append(globalUsername + " transfer to " + targetForename + " " + targetSurname);
                globalTransactionBalance.append(QString::number(globalBalance.toDouble() - amountToTransfer));

                globalBalance = QString::number(globalBalance.toDouble() - amountToTransfer);

                if (taResults == "Success") {
                    QTextStream(stdout) << "\nTransfer successful";
                    QMessageBox::StandardButton alert;
                    alert = QMessageBox::information(this, "Transfer", "Transfer successful",
                                                QMessageBox::Ok);
                    if (alert == QMessageBox::Ok) {
                        qDebug() << "\nOk was clicked";
                    } else {
                        qDebug() << "\nOk was *not* clicked";
                    }
                } else {
                    throw(1);
                }


            } else {
                throw(1);
            }
        } else {
            throw(1);
        }
    }
    catch (int result) {
        QTextStream(stdout) << "\nTransfer unsuccesful";
        QMessageBox::StandardButton alert;
        alert = QMessageBox::information(this, "Transfer", "Transfer unsuccesful",
                                    QMessageBox::Ok);
        if (alert == QMessageBox::Ok) {
            qDebug() << "\nOk was clicked";
        } else {
            qDebug() << "\nOk was *not* clicked";
        }
    }
}

void MainWindow::on_transactionsButton_clicked()
{
    ui->transactionsTable->setRowCount(globalTransactionActions.length());
    ui->transactionsTable->setColumnCount(3);

    for (int i = 0; i < ui->transactionsTable->rowCount(); i++) {
        QTableWidgetItem *item1 = new QTableWidgetItem();
        item1->setText("£" + globalTransactionAmounts[i]);
        ui->transactionsTable->setItem(i, 1, item1);
        item1->setTextAlignment(Qt::AlignCenter);
        QTableWidgetItem *item2 = new QTableWidgetItem();
        item2->setText(globalTransactionActions[i]);
        ui->transactionsTable->setItem(i, 0, item2);
        item2->setTextAlignment(Qt::AlignCenter);
        QTableWidgetItem *item3 = new QTableWidgetItem();
        item3->setText("£" + globalTransactionBalance[i]);
        ui->transactionsTable->setItem(i, 2, item3);
        item3->setTextAlignment(Qt::AlignCenter);
    }

    ui->stackedWidget->setCurrentIndex(4);
}


void MainWindow::on_accountButton_clicked()
{
    ui->stackedWidget->setCurrentIndex(5);

    ui->forenameBox->setText(globalForename);
    ui->surnameBox->setText(globalSurname);
    ui->usernameBox->setText(globalUsername);
    ui->userTypeBox->setText(globalUserType);
    ui->sqBox->setText(globalSecurityQ);
    ui->sqaBox->setText(globalSecurityA);
    ui->pinBox->setText(globalBankPin);
    ui->pwordBox->setText(globalPassword);

}


void MainWindow::on_logOutButton_clicked()
{
    this->hide();
    LoginWindow *lw = new LoginWindow();
    lw->show();
}


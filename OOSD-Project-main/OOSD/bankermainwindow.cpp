#include "bankermainwindow.h"
#include "ui_bankermainwindow.h"
#include "globals.h"
#include "loginwindow.h"
#include <QSqlQuery>
#include <QSql>
#include <QSqlRecord>
#include <QMessageBox>

class accountActions {
private:
    QString receiver_action(QString amount, QString tForename, QString tSurname) {
        QSqlQuery query2;
        query2.prepare("UPDATE users SET balance = balance + :amountToTransfer WHERE forename = :targetForename AND surname = :targetSurname");
        query2.bindValue(":amountToTransfer", amount);
        query2.bindValue(":targetForename", tForename);
        query2.bindValue(":targetSurname", tSurname);

        if(query2.exec()) {
            return "Success";
        } else {
            return "";
        }
    }

    QString sender_action(QString amount, QString sForename, QString sSurname) {
        // Create query to remove the amount being transferred from the sending users account balance
        QSqlQuery query;
        query.prepare("UPDATE users SET balance = balance - :amountToTransfer WHERE forename = :senderForename AND surname = :senderSurname");
        query.bindValue(":amountToTransfer", amount);
        query.bindValue(":senderForename", sForename);
        query.bindValue(":senderSurname", sSurname);

        if(query.exec()) {
            return "Success";
        } else {
            return "";
        }
    }
public:
    QString transfer(QString amount, QString tForename, QString tSurname, QString sForename, QString sSurname) {
        QString raResult = receiver_action(amount, tForename, tSurname);
        QString saResult = sender_action(amount, sForename, sSurname);

        if ((raResult == "Success") && (saResult == "Success")) {
            return "Success";
        } else {
            return "";
        }
    }
};

BankerMainWindow::BankerMainWindow(QWidget *parent) :
    QWidget(parent),
    ui(new Ui::BankerMainWindow)
{
    ui->setupUi(this);

    ui->WelcomeLabel->setText("Welcome, " + globalForename);

    QSqlQuery q1;

    q1.prepare("SELECT forename, surname, balance FROM users WHERE forename != :globalForename AND username != :globalUsername AND userType = 'Customer'");
    q1.bindValue(":globalForename", globalForename);
    q1.bindValue(":globalUsername", globalUsername);
    q1.exec();
    while (q1.next()) {
        int forenameID = q1.record().indexOf("forename");
        int surnameID = q1.record().indexOf("surname");
        int balanceID = q1.record().indexOf("balance");
        globalTargetForenames.append(q1.value(forenameID).toString());
        globalTargetSurnames.append(q1.value(surnameID).toString());
        globalSenderForenames.append(q1.value(forenameID).toString());
        globalSenderSurnames.append(q1.value(surnameID).toString());
        globalSenderBalances.append(q1.value(balanceID).toString());
        ui->senderBox->addItem((q1.value(forenameID).toString()) + " " + q1.value(surnameID).toString());
        ui->receiverBox->addItem((q1.value(forenameID).toString()) + " " + q1.value(surnameID).toString());
    }

    // Insert accounts in to the account table
    ui->customersTable->setRowCount(customerAccountNames.length());
    for (int i = 0; i < ui->customersTable->rowCount(); i++) {
        QTableWidgetItem *item1 = new QTableWidgetItem();
        item1 -> setText(customerAccountNames[i]);
        ui->customersTable->setItem(i, 0, item1);
        QTableWidgetItem *item2 = new QTableWidgetItem();
        item2 -> setText(customerAccountBalances[i]);
        ui->customersTable->setItem(i, 1, item2);
        QTableWidgetItem *item3 = new QTableWidgetItem();
        item3 -> setText(customerAccountUserTypes[i]);
        ui->customersTable->setItem(i, 2, item3);
    }
}

BankerMainWindow::~BankerMainWindow()
{
    delete ui;
}

void BankerMainWindow::on_pushButton_clicked()
{
    this->hide();
    LoginWindow *lw = new LoginWindow;
    lw->show();
}


void BankerMainWindow::on_homeButton_clicked()
{
    ui->stackedWidget->setCurrentIndex(0);
}


void BankerMainWindow::on_transferButton_clicked()
{
    ui->stackedWidget->setCurrentIndex(1);
}


void BankerMainWindow::on_customersButton_clicked()
{
    ui->stackedWidget->setCurrentIndex(2);
}

// When the submit transfer button is clicked.
void BankerMainWindow::on_pushButton_2_clicked()
{
    accountActions actions;

    double amountToTransfer = ui->amountBox->text().toDouble();
    QString transferAmountInput = ui->amountBox->text();
    int numOfNumbers = 0;
    int numOfDecimalPoints = 0;
    int numOfSpaces = 0;
    QString targetForename = globalTargetForenames[ui->receiverBox->currentIndex()];
    QString targetSurname = globalTargetSurnames[ui->receiverBox->currentIndex()];
    QString senderForename = globalSenderForenames[ui->senderBox->currentIndex()];
    QString senderSurname = globalSenderSurnames[ui->senderBox->currentIndex()];

    QTextStream (stdout) << globalSenderForenames[ui->senderBox->currentIndex()] +" is attempting to transfer £" + transferAmountInput + " to " + targetForename + " " + targetSurname;

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
        if ((numOfSpaces == 0) && (transferAmountInput != "") && (globalSenderBalances[ui->senderBox->currentIndex()].toDouble() - amountToTransfer >= 0)) {
            // Ensure the transfer amount only includes numbers and a possible decimal point.
            if (((numOfNumbers == transferAmountInput.length()) && (numOfDecimalPoints == 0)) || ((numOfNumbers == transferAmountInput.length() - 1) && (numOfDecimalPoints == 1))){
                // call the transfer function to perform the transaction
                QString transferResult = actions.transfer(transferAmountInput, targetForename, targetSurname, senderForename, senderSurname);

                globalTransactionAmounts.append(transferAmountInput);
                globalTransactionActions.append(senderForename + senderSurname + " transfer to " + targetForename + " " + targetSurname);

                if (ui->senderBox->currentText() == ui->receiverBox->currentText()) {
                    globalTransactionBalance.append(QString::number(globalSenderBalances[ui->senderBox->currentIndex()].toDouble()));
                    // This means that the person is sending money to their own account, therefore the balance remains unchanged.
                } else {
                    globalTransactionBalance.append(QString::number(globalSenderBalances[ui->senderBox->currentIndex()].toDouble() - amountToTransfer));
                }

                if (transferResult == "Success") {
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
    catch(int result) {
        if (result == 1) {
            QTextStream(stdout) << "\nTransfer unsuccessful";
            QMessageBox::StandardButton alert;
            alert = QMessageBox::information(this, "Transfer", "Transfer unsuccessful",
                                        QMessageBox::Ok);
            if (alert == QMessageBox::Ok) {
                qDebug() << "\nOk was clicked";
            } else {
                qDebug() << "\nOk was *not* clicked";
            }
        }
    }
}


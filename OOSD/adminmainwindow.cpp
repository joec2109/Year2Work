#include "adminmainwindow.h"
#include "ui_adminmainwindow.h"
#include "loginwindow.h"
#include <QSql>
#include <QSqlQuery>
#include <QTableWidget>
#include <QTableWidgetItem>
#include <Qt>
#include "globals.h"

class accountActions {
public:
    // Deletes the selected account from the db
    QString delete_account(QString fname, QString sname, QString pwordToDelete) {
        QSqlQuery query;
        query.prepare("DELETE FROM users WHERE forename = :forename AND surname = :surname AND password = :passwordToDelete");
        query.bindValue(":forename", fname);
        query.bindValue(":surname", sname);
        query.bindValue(":passwordToDelete", pwordToDelete);

        if(query.exec()) {
            return "Success";
        } else {
            return "";
        }
    }

    // Promotes the selected account on the db
    QString promote_account(QString fname, QString sname, QString pwordToPromote) {
        QSqlQuery query;
        query.prepare("UPDATE users SET userType = 'Banker' WHERE forename = :forename AND surname = :surname AND password= :passwordToPromote");
        query.bindValue(":forename", fname);
        query.bindValue(":surname", sname);
        query.bindValue(":passwordToPromote", pwordToPromote);

        if (query.exec()) {
            return "Success";
        } else {
            return "";
        }
    }

    // Demotes the selected account on the db
    QString demote_account(QString fname, QString sname, QString pwordToDemote) {
        QSqlQuery query;
        query.prepare("UPDATE users SET userType = 'Customer' WHERE forename = :forename AND surname = :surname AND password= :passwordToDemote");
        query.bindValue(":forename", fname);
        query.bindValue(":surname", sname);
        query.bindValue(":passwordToDemote", pwordToDemote);

        if (query.exec()) {
            return "Success";
        } else {
            return "";
        }
    }

    // Reverts the withdraw transaction selected
    QString revert_withdraws(QString amount, QString uname) {
        QSqlQuery q1;
        q1.prepare("UPDATE users SET balance = balance + :amountToRevert WHERE username = :usernameToRevert");
        q1.bindValue(":amountToRevert", amount);
        q1.bindValue(":usernameToRevert", uname);

        if (q1.exec()) {
            return "Success";
        } else {
            return "";
        }
    }

    // Reverts the deposit transaction selected
    QString revert_deposits(QString amount, QString uname) {
        QSqlQuery q1;
        q1.prepare("UPDATE users SET balance = balance - :amountToRevert WHERE username = :usernameToRevert");
        q1.bindValue(":amountToRevert", amount);
        q1.bindValue(":usernameToRevert", uname);

        if (q1.exec()) {
            return "Success";
        } else {
            return "";
        }
    }

    // Reverts the transfer transactions selected
    QString revert_transfers(QString amount, QString uname1, QString uname2, QString uname3, QString uname4) {
        // Handle reverting transfers
        QSqlQuery q1;
        q1.prepare("UPDATE users SET balance =  balance + :amountToRevert WHERE username = :usernameToRevert");
        q1.bindValue(":amountToRevert", amount);
        q1.bindValue(":usernameToRevert", uname1);
        QSqlQuery q2;
        q2.prepare("UPDATE users SET balance = balance - :amountToRevert WHERE forename = :forenameToRevert AND surname = :surnameToRevert");
        QTextStream(stdout) << "\ntaking away from " + uname2;
        q2.bindValue(":amountToRevert", amount);
        q2.bindValue(":forenameToRevert", uname3);
        q2.bindValue(":surnameToRevert", uname4);

        // Exception handling
        try {
            if (q1.exec()) {
                if (q2.exec()) {
                    return "Success";
                } else {
                    throw(1);
                }
            } else {
                throw(1);
            }
        }
        catch (int result) {
            return "";
        }
    }
};

AdminMainWindow::AdminMainWindow(QWidget *parent) :
    QMainWindow(parent),
    ui(new Ui::AdminMainWindow)
{
    ui->setupUi(this);
    // Insert transactions in to the transactions table
    ui->transactionsTable->setRowCount(globalTransactionActions.length());
    for (int i = 0; i < ui->transactionsTable->rowCount(); i++) {
        QTableWidgetItem *item1 = new QTableWidgetItem();
        item1->setText(globalTransactionActions[i]);
        ui->transactionsTable->setItem(i, 0, item1);
        QTableWidgetItem *item2 = new QTableWidgetItem();
        item2->setText("£ " + globalTransactionAmounts[i]);
        ui->transactionsTable->setItem(i, 1, item2);
        QTableWidgetItem *item3 = new QTableWidgetItem();
        item3->setText("£ " + globalTransactionBalance[i]);
        ui->transactionsTable->setItem(i, 2, item3);
    }

    // Insert accounts in to the account table
    ui->accountsTable->setRowCount(accountNames.length());
    for (int i = 0; i < ui->accountsTable->rowCount(); i++) {
        QTableWidgetItem *item1 = new QTableWidgetItem();
        item1 -> setText(accountNames[i]);
        ui->accountsTable->setItem(i, 0, item1);
        QTableWidgetItem *item2 = new QTableWidgetItem();
        item2 -> setText(accountPasswords[i]);
        ui->accountsTable->setItem(i, 1, item2);
        QTableWidgetItem *item3 = new QTableWidgetItem();
        item3 -> setText(accountPins[i]);
        ui->accountsTable->setItem(i, 2, item3);
        QTableWidgetItem *item4 = new QTableWidgetItem();
        item4 -> setText(accountBalances[i]);
        ui->accountsTable->setItem(i, 3, item4);
        QTableWidgetItem *item5 = new QTableWidgetItem();
        item5 -> setText(accountUserTypes[i]);
        ui->accountsTable->setItem(i, 4, item5);
    }
}

AdminMainWindow::~AdminMainWindow()
{
    delete ui;
}

// Take user to home page
void AdminMainWindow::on_homeButton_clicked()
{
    ui->stackedWidget->setCurrentIndex(0);
}

// Insert the transactions in to the transactions table
void AdminMainWindow::on_viewTransactionsButton_clicked()
{
    ui->stackedWidget->setCurrentIndex(1);

    for (int i = 0; i < ui->transactionsTable->rowCount(); i++) {
        QTableWidgetItem *item1 = new QTableWidgetItem();
        item1->setText(globalTransactionActions[i]);
        ui->transactionsTable->setItem(i, 0, item1);
        QTableWidgetItem *item2 = new QTableWidgetItem();
        item2->setText("£ " + globalTransactionAmounts[i]);
        ui->transactionsTable->setItem(i, 1, item2);
        QTableWidgetItem *item3 = new QTableWidgetItem();
        item3->setText("£ " + globalTransactionBalance[i]);
        ui->transactionsTable->setItem(i, 2, item3);
    }
}

// Insert the accounts in to the accounts table
void AdminMainWindow::on_viewAccountsButton_clicked()
{
    ui->stackedWidget->setCurrentIndex(2);

    for (int i = 0; i < ui->accountsTable->rowCount(); i++) {
        QTableWidgetItem *item1 = new QTableWidgetItem();
        item1 -> setText(accountNames[i]);
        ui->accountsTable->setItem(i, 0, item1);
        QTableWidgetItem *item2 = new QTableWidgetItem();
        item2 -> setText(accountPasswords[i]);
        ui->accountsTable->setItem(i, 1, item2);
        QTableWidgetItem *item3 = new QTableWidgetItem();
        item3 -> setText(accountPins[i]);
        ui->accountsTable->setItem(i, 2, item3);
        QTableWidgetItem *item4 = new QTableWidgetItem();
        item4 -> setText(accountBalances[i]);
        ui->accountsTable->setItem(i, 3, item4);
        QTableWidgetItem *item5 = new QTableWidgetItem();
        item5 -> setText(accountUserTypes[i]);
        ui->accountsTable->setItem(i, 4, item5);
    }
}

// Log the admin out and remove details from the accounts table
void AdminMainWindow::on_pushButton_clicked()
{
    ui->accountsTable->setRowCount(0);
    this->hide();
    LoginWindow *lw = new LoginWindow();
    lw->show();
}


// Delete account button
void AdminMainWindow::on_pushButton_2_clicked()
{
    accountActions aActions;

    QModelIndexList accountSelectedIndex = ui->accountsTable->selectionModel()->selectedIndexes();
    QString rowToDelete = QString::number(accountSelectedIndex[0].row());
    QString usernameToDelete = ui->accountsTable->item(accountSelectedIndex[0].row(), 0)->data(Qt::DisplayRole).toString();
    QString passwordToDelete = ui->accountsTable->item(accountSelectedIndex[0].row(), 1)->data(Qt::DisplayRole).toString();
    QStringList names = usernameToDelete.split(" ");
    QString forename = names[0];
    QString surname = names[1];

    QString daResult = aActions.delete_account(forename, surname, passwordToDelete);

    if (daResult == "Success") {
        QTextStream(stdout) << "\n" + usernameToDelete + "'s account deleted.";

        // Remove the account details from the various global variables associated with it
        ui->accountsTable->removeRow(accountSelectedIndex[0].row());
        accountNames.remove(accountSelectedIndex[0].row());
        accountPasswords.remove(accountSelectedIndex[0].row());
        accountPins.remove(accountSelectedIndex[0].row());
        accountBalances.remove(accountSelectedIndex[0].row());
        accountUserTypes.remove(accountSelectedIndex[0].row());
    }
}

// Promote selected user to a banker
void AdminMainWindow::on_promoteButton_clicked()
{
    accountActions aActions;

    QModelIndexList accountSelectedIndex = ui->accountsTable->selectionModel()->selectedIndexes();
    QString rowToPromote = QString::number(accountSelectedIndex[0].row());
    QString usernameToPromote = ui->accountsTable->item(accountSelectedIndex[0].row(), 0)->data(Qt::DisplayRole).toString();
    QString passwordToPromote = ui->accountsTable->item(accountSelectedIndex[0].row(), 1)->data(Qt::DisplayRole).toString();
    QStringList names = usernameToPromote.split(" ");
    QString forename = names[0];
    QString surname = names[1];

    QString paResult = aActions.promote_account(forename, surname, passwordToPromote);

    if (paResult == "Success") {
        QTextStream(stdout) << "\n" + usernameToPromote + " has been promoted.";
        ui->accountsTable->item(accountSelectedIndex[0].row(), 4)->setText("Banker");
        accountUserTypes[accountSelectedIndex[0].row()] = "Banker";
    }
}

// Demote selected user to a regular customer
void AdminMainWindow::on_demoteButton_clicked()
{
    accountActions aActions;

    QModelIndexList accountSelectedIndex = ui->accountsTable->selectionModel()->selectedIndexes();
    QString rowToDemote = QString::number(accountSelectedIndex[0].row());
    QString usernameToDemote = ui->accountsTable->item(accountSelectedIndex[0].row(), 0)->data(Qt::DisplayRole).toString();
    QString passwordToDemote = ui->accountsTable->item(accountSelectedIndex[0].row(), 1)->data(Qt::DisplayRole).toString();
    QStringList names = usernameToDemote.split(" ");
    QString forename = names[0];
    QString surname = names[1];

    QString daResult = aActions.demote_account(forename, surname, passwordToDemote);

    if(daResult == "Success") {
        QTextStream(stdout) << "\n" + usernameToDemote + " has been demoted.";
        ui->accountsTable->item(accountSelectedIndex[0].row(), 4)->setText("Customer");
        accountUserTypes[accountSelectedIndex[0].row()] = "Customer";
    }
}

// Revert the selected transaction
void AdminMainWindow::on_revertTransactionButton_clicked()
{
    accountActions aActions;

    QModelIndexList transactionSelectedIndex = ui->transactionsTable->selectionModel()->selectedIndexes();
    QString rowToRevert = QString::number(transactionSelectedIndex[0].row());
    QStringList usernamesToRevert = ui->transactionsTable->item(transactionSelectedIndex[0].row(), 0)->data(Qt::DisplayRole).toString().split(" ");
    QStringList splitAmountColumn = ui->transactionsTable->item(transactionSelectedIndex[0].row(), 1)->data(Qt::DisplayRole).toString().split(" ");
    QString amountToRevert = splitAmountColumn[1];
    QString usernameToRevert = "";
    QString username2ToRevert = "";
    // If transaction is a withdraw or a deposit, then only the username of the person doing the action is needed.
    if ((usernamesToRevert[1] == "deposit") || (usernamesToRevert[1] == "withdraw")) {
        usernameToRevert = usernamesToRevert[0];
    } else {    // If the transaction is a transfer, the name of the person doing the action and the name of the target user is needed.
        usernameToRevert = usernamesToRevert[0];
        username2ToRevert = usernamesToRevert[3] + usernamesToRevert[4];
    }

    // Handle reverting withdrawals
    if (usernamesToRevert[1] == "withdraw") {

        QString rwResult = aActions.revert_withdraws(amountToRevert, usernameToRevert);

        if (rwResult == "Success") {
            ui->transactionsTable->removeRow(transactionSelectedIndex[0].row());
            globalTransactionActions.remove(transactionSelectedIndex[0].row());
            globalTransactionAmounts.remove(transactionSelectedIndex[0].row());
            globalTransactionBalance.remove(transactionSelectedIndex[0].row());
        }
    }
    // Handle reverting deposits
    else if (usernamesToRevert[1] == "deposit") {

        QString rdResult = aActions.revert_deposits(amountToRevert, usernameToRevert);

        if (rdResult == "Success") {
            ui->transactionsTable->removeRow(transactionSelectedIndex[0].row());
            globalTransactionActions.remove(transactionSelectedIndex[0].row());
            globalTransactionAmounts.remove(transactionSelectedIndex[0].row());
            globalTransactionBalance.remove(transactionSelectedIndex[0].row());
        }
    // Handle reverting transfers
    } else {

        QString rtResult = aActions.revert_transfers(amountToRevert, usernameToRevert, username2ToRevert, usernamesToRevert[3], usernamesToRevert[4]);

        if (rtResult == "Success") {
            ui->transactionsTable->removeRow(transactionSelectedIndex[0].row());
            globalTransactionActions.remove(transactionSelectedIndex[0].row());
            globalTransactionAmounts.remove(transactionSelectedIndex[0].row());
            globalTransactionBalance.remove(transactionSelectedIndex[0].row());
        }
    }
}


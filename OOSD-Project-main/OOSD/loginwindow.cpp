#include "loginwindow.h"
#include "ui_loginwindow.h"
#include "signupwindow.h"
#include "MainWindow.h"
#include "globals.h"
#include "enterbankpin.h"
#include "adminmainwindow.h"
#include "forgotpassword.h"
#include "bankermainwindow.h"
#include <QTextStream>
#include <QtSql>
#include <QSqlDatabase>
#include <QMessageBox>

// Account class that holds functions used within this source file and variables used within them privately
class account {

public:

    // Sets the global user type variable to the user type of the user that has just logged in
    void set_UserType(QString u, QString p) {
        int userTypeID;
        QSqlQuery getUserTypes;
        getUserTypes.prepare("SELECT userType FROM users WHERE username = :uname AND password = :pword");
        getUserTypes.bindValue(":uname", u);
        getUserTypes.bindValue(":pword", p);
        getUserTypes.exec();
        while(getUserTypes.next()) {
            userTypeID = getUserTypes.record().indexOf("userType");
            globalUserType = getUserTypes.value(userTypeID).toString();
        }
    }

    // Sets the global username variable to the username of the user just logged in
    void set_Username (QString username, QString password) {
        int usernameID;
        QSqlQuery getUsername;
        getUsername.prepare("SELECT username FROM users WHERE username = :username AND password = :password");
        getUsername.bindValue(":username", username);
        getUsername.bindValue(":password", password);
        getUsername.exec();
        while(getUsername.next()) {
            usernameID = getUsername.record().indexOf("username");
            globalUsername = getUsername.value(usernameID).toString();
        }
    }

    // Function that returns the user type of the logged in user
    QString return_UserType() {
        QString userType;

        userType = globalUserType;

        if (userType == "Customer") {
            return "Customer";
        } else if (userType == "Banker") {
            return "Banker";
        } else {
            return "Admin";
        }
    }
};

// Subclasses of 'account' class.
class customerAccount: public account {
public:
    // Function that returns the name of the window that needs to be shown next (if the user that has logged in is a customer)
    QString return_window() {
        return "Enter Bank Pin";
    }
};

class bankerAccount: public account {

public:
    // Function that returns the name of the window that needs to be shown next (if the user that has logged in is a banker)
    QString return_window() {
        return "Banker Main Window";
    }

    // Function that sets the global forename variable to the forename of the user logged in
    void set_UserForename(QString username) {
        QSqlQuery selectForenames;
        selectForenames.prepare("SELECT forename FROM users WHERE userType = 'Banker' AND username = :username");
        selectForenames.bindValue(":username", username);
        if(selectForenames.exec()) {
            if (selectForenames.next()) {
                globalForename = selectForenames.value(0).toString();
            }
        }
    }
};

class adminAccount: public account {
public:
    // Function that returns the name of the window that needs to be shown next (if the user that has logged in is a admin)
    QString return_window() {
        return "Admin Main Window";
    }
};

// The main function - gets executed as soon as a new login window object is created.
LoginWindow::LoginWindow(QWidget *parent) :
    QMainWindow(parent),
    ui(new Ui::LoginWindow)
{
    ui->setupUi(this);
}

LoginWindow::~LoginWindow()
{
    delete ui;
}

// If sign up button clicked, show the sign up window and hide the log in window
void LoginWindow::on_SignUpButton_clicked()
{
    this->hide();
    SignUpWindow *suw= new SignUpWindow();
    suw->show();
}

// Check the details the user has entered and if they match within the db, log them in
void LoginWindow::on_LoginButton_clicked()
{
    // Create an object of the 'account' class so the functions within it can be executed with data collected via the UI
    account newAccount;

    QString username = ui->usernameBox->text();
    QString password = ui->passwordBox->text();

    QSqlQuery query;
    query.prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    query.bindValue(":username", username);
    query.bindValue(":password", password);
    query.exec();

    if (query.size() > 0) {
        // Creating a new instance of the account class so that the program can check which window needs to be loaded next and the username and password of the user can be set.
        newAccount.set_UserType(username, password);
    }

    QSqlQuery query2;
    query2.prepare("SELECT username FROM users WHERE userType = 'Banker' AND username = :username AND password = :password");
    query2.bindValue(":username", username);
    query2.bindValue(":password", password);
    query2.exec();

    if (query.exec()) {
    // If user details are correct, log them in
        // Exception handling to attempt to log the user in if the details are correct
        try {
            if ((query.size() > 0) && (newAccount.return_UserType() == "Customer") && (query2.size() == 0)) {
                QTextStream(stdout) << "\nHere";

                customerAccount newCustomerAccount;

                globalTargetForenames = {};
                globalTargetSurnames = {};

                globalTransactionAmounts = {};
                globalTransactionActions = {};
                globalTransactionBalance = {};

                // Grabbing the username of the customer and storing it in a global variable.
                newCustomerAccount.set_Username(username,password);

                QTextStream(stdout) << "\n" << username << " is logged in";
                QMessageBox::StandardButton alert;
                alert = QMessageBox::information(this, "Log In", "Logged in successfully",
                                            QMessageBox::Ok);
                if (alert == QMessageBox::Ok) {
                    qDebug() << "\nOk was clicked";
                    this->hide();
                    if ((newCustomerAccount.return_UserType() == "Customer") && (newCustomerAccount.return_window() == "Enter Bank Pin")) {
                        EnterBankPin *ebp= new EnterBankPin();
                        ebp->show();
                    }
                } else {
                    qDebug() << "\nOk was *not* clicked";
                }
            } else if ((query.size() > 0) && (newAccount.return_UserType() == "Admin")){

                adminAccount newAdminAccount;

                // Grabbing the username of the admin (admin) and storing it in a global variable.
                newAdminAccount.set_Username(username,password);

                QTextStream(stdout) << "\n" << username << " is logged in";
                QMessageBox::StandardButton alert;
                alert = QMessageBox::information(this, "Log In", "Logged in successfully",
                                            QMessageBox::Ok);
                if (alert == QMessageBox::Ok) {
                    qDebug() << "\nOk was clicked";
                    this->hide();
                    if ((newAdminAccount.return_UserType() == "Admin") && (newAdminAccount.return_window() == "Admin Main Window")){
                        AdminMainWindow *amw= new AdminMainWindow();
                        amw->show();
                    }
                } else {
                    qDebug() << "\nOk was *not* clicked";
                }
            } else if ((query.size() > 0) && (newAccount.return_UserType() == "Banker")) {

                bankerAccount newBankerAccount;

                // Grabbing the username of the banker and storing it in a global variable.
                newBankerAccount.set_Username(username,password);

                // Grabbing the forename of the logged in user
                newBankerAccount.set_UserForename(username);

                QTextStream(stdout) << "\n" << username << " is logged in";
                QMessageBox::StandardButton alert;
                alert = QMessageBox::information(this, "Log In", "Logged in successfully",
                                            QMessageBox::Ok);
                if (alert == QMessageBox::Ok) {
                    qDebug() << "\nOk was clicked";
                    this->hide();
                    if ((newBankerAccount.return_UserType() == "Banker") && (newBankerAccount.return_window() == "Banker Main Window")) {
                        BankerMainWindow *bmw= new BankerMainWindow();
                        bmw->show();
                    }
                } else {
                    qDebug() << "\nOk was *not* clicked";
                }
            } else {
                // If username and password is incorrect, throw the exception
                throw (1);
            }

        }
        // If an exception is thrown, show that the user entered invalid details
        catch(int result) {
            if (result == 1) {
                QTextStream(stdout) << "Username or password is incorrect";
                QMessageBox::StandardButton alert;
                alert = QMessageBox::warning(this, "Log In", "Username or password is incorrect",
                                            QMessageBox::Ok);
                if (alert == QMessageBox::Ok) {
                    qDebug() << "\nOk was clicked";
                } else {
                    qDebug() << "\nOk was *not* clicked";
                }
            }
        }
    }
}

// If forgot password button is clicked, show them the forgot password page and hide the login window
void LoginWindow::on_ForgotPasswordButton_clicked()
{
    this->hide();
    ForgotPassword *fpw = new ForgotPassword;
    fpw->show();
}


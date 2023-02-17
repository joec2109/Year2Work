#include "forgotpassword.h"
#include "ui_forgotpassword.h"
#include "loginwindow.h"
#include <QSql>
#include <QSqlQuery>
#include <QMessageBox>

class account {
public:
    QString updatePassword(QString password, QString uname, QString sq, QString sa) {
        QSqlQuery q3;
        q3.prepare("UPDATE users SET password = :newPassword WHERE username = :username AND securityQuestion = :secQuestion AND securityAnswer = :secAnswer");
        q3.bindValue(":newPassword", password);
        q3.bindValue(":username", uname);
        q3.bindValue(":secQuestion", sq);
        q3.bindValue(":secAnswer", sa);

        if (q3.exec()) {
            return "Success";
        } else {
            return "";
        }
    }
};

ForgotPassword::ForgotPassword(QWidget *parent) :
    QWidget(parent),
    ui(new Ui::ForgotPassword)
{
    ui->setupUi(this);
}

ForgotPassword::~ForgotPassword()
{
    delete ui;
}

void ForgotPassword::on_backButton_clicked()
{
    this ->hide();
    LoginWindow *lw = new LoginWindow;
    lw->show();
}


void ForgotPassword::on_submitButton_clicked()
{

    account newAccount;

    QString username = ui->usernameBox->text();
    QString secQuestion = ui->SecurityQuestionBox->currentText();
    QString secAnswer = ui->securityAnswerBox->text();
    QString newPassword = ui->newPasswordBox->text();
    QString confirmNewPword = ui->confirmNewPwordBox->text();

    try {
        if (!username.isEmpty()) {
            QSqlQuery q1;
            q1.prepare("SELECT username FROM users WHERE username = :username");
            q1.bindValue(":username", username);
            if (q1.exec()) {
                if (q1.size() > 0) {
                    QTextStream(stdout) << "\nUsername found";
                    QSqlQuery q2;
                    q2.prepare("SELECT securityQuestion, securityAnswer FROM users WHERE username = :username AND securityQuestion = :secQuestion AND securityAnswer = :secAnswer");
                    q2.bindValue(":username", username);
                    q2.bindValue(":secQuestion", secQuestion);
                    q2.bindValue(":secAnswer", secAnswer);
                    if (q2.exec()) {
                        if ((q2.size() > 0) && (newPassword == confirmNewPword)) {

                            // Password validation

                            bool validPasswordLength = false;
                            bool passwordCharsValid = false;
                            bool confirmPasswordEqualsPword = false;

                            // Checking password length (has to be 8 chars or more)
                            if (newPassword.size() >= 8) {
                                validPasswordLength = true;
                            } else {
                                QMessageBox::StandardButton alert;
                                alert = QMessageBox::information(this, "Forgot Password", "Password needs to be 8 characters or more",
                                                            QMessageBox::Ok);
                                if (alert == QMessageBox::Ok) {
                                    qDebug() << "\nOk was clicked";
                                } else {
                                    qDebug() << "\nOk was *not* clicked";
                                }
                            }

                            // Checking password characters (needs at least 1 lower case, 1 upper case and 1 number)
                            QString password = ui->newPasswordBox->text();
                            int upper = 0, lower = 0, numbers = 0;

                            for (int i = 0; i < ui->newPasswordBox->text().length(); i++)
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
                                ui->newPasswordLabel->setStyleSheet("QLabel { color : black; }");
                                passwordCharsValid = true;
                            }
                            if (upper == 0) {
                                QTextStream(stdout) << "\nPassword needs at least 1 upper case char";
                                ui->newPasswordLabel->setStyleSheet("QLabel { color : red; }");
                                passwordCharsValid = false;
                            }
                            if (lower == 0) {
                                QTextStream(stdout) << "\nPassword needs at least 1 lower case char";
                                ui->newPasswordLabel->setStyleSheet("QLabel { color : red; }");
                                passwordCharsValid = false;
                            }
                            if (numbers == 0) {
                                QTextStream(stdout) << "\nPassword needs at least 1 number";
                                ui->newPasswordLabel->setStyleSheet("QLabel { color : red; }");
                                passwordCharsValid = false;
                            }

                            if (ui->confirmNewPwordBox->text() == ui->newPasswordBox->text()) {
                                confirmPasswordEqualsPword = true;
                            }

                            if (passwordCharsValid == false) {
                                throw(0);
                            }

                            if (confirmPasswordEqualsPword == false) {
                                throw(1);
                            }

                            // If the new password matches all of the required rules & new password = confirm password then complete the query to change their password.
                            if ((validPasswordLength == true) && (passwordCharsValid == true) && (confirmPasswordEqualsPword == true)) {

                                QString upResult = newAccount.updatePassword(newPassword, username, secQuestion, secAnswer);

                                if (upResult == "Success") {
                                    QMessageBox::StandardButton alert;
                                    alert = QMessageBox::information(this, "Forgot Password", "Password updated",
                                                                QMessageBox::Ok);
                                    if (alert == QMessageBox::Ok) {
                                        qDebug() << "\nOk was clicked";
                                        this->hide();
                                        LoginWindow *lw= new LoginWindow();
                                        lw->show();
                                    } else {
                                        qDebug() << "\nOk was *not* clicked";
                                    }
                                }
                            }
                        } else {
                            throw(2);
                        }
                    }
                } else {
                    throw(3);
                }
            }
        } else {
            QMessageBox::StandardButton alert;
            alert = QMessageBox::information(this, "Forgot Password", "Username cannot be empty.", QMessageBox::Ok);
            if (alert == QMessageBox::Ok) {
                qDebug() << "\nOk was clicked";
            } else {
                qDebug() << "\nOk was *not* clicked";
            }
        }
    }
    catch (int result) {
        if (result == 0) {
            QMessageBox::StandardButton alert;
            alert = QMessageBox::information(this, "Forgot Password", "Password must have at least 1 lower case char, 1 upper case and 1 number",
                                        QMessageBox::Ok);
            if (alert == QMessageBox::Ok) {
                qDebug() << "\nOk was clicked";
            } else {
                qDebug() << "\nOk was *not* clicked";
            }
        } else if (result == 1) {
            QMessageBox::StandardButton alert;
            alert = QMessageBox::information(this, "Forgot Password", "Password must be at least 8 characters in length",
                                        QMessageBox::Ok);
            if (alert == QMessageBox::Ok) {
                qDebug() << "\nOk was clicked";
            } else {
                qDebug() << "\nOk was *not* clicked";
            }
        } else if (result == 2) {
            QMessageBox::StandardButton alert;
            alert = QMessageBox::information(this, "Forgot Password", "Security question and/or answer is incorrect.\nAlso ensure you retype your new password out correctly twice"
                                        , QMessageBox::Ok);
            if (alert == QMessageBox::Ok) {
                qDebug() << "\nOk was clicked";
            } else {
                qDebug() << "\nOk was *not* clicked";
            }
        } else {
            QMessageBox::StandardButton alert;
            alert = QMessageBox::information(this, "Forgot Password", "That username does not exist.", QMessageBox::Ok);
            if (alert == QMessageBox::Ok) {
                qDebug() << "\nOk was clicked";
            } else {
                qDebug() << "\nOk was *not* clicked";
            }
        }
    }
}


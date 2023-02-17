#ifndef FORGOTBANKPIN_H
#define FORGOTBANKPIN_H

#include <QWidget>

namespace Ui {
class ForgotBankPin;
}

class ForgotBankPin : public QWidget
{
    Q_OBJECT

public:
    explicit ForgotBankPin(QWidget *parent = nullptr);
    ~ForgotBankPin();

private slots:
    void on_backButton_clicked();

    void on_submitButton_clicked();

private:
    Ui::ForgotBankPin *ui;
};

#endif // FORGOTBANKPIN_H

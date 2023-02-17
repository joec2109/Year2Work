#ifndef ENTERBANKPIN_H
#define ENTERBANKPIN_H

#include <QMainWindow>

namespace Ui {
class EnterBankPin;
}

class EnterBankPin : public QMainWindow
{
    Q_OBJECT

public:
    explicit EnterBankPin(QWidget *parent = nullptr);
    ~EnterBankPin();

private slots:
    void on_EnterButton_clicked();

    void on_backButton_clicked();

    void on_ForgotBankPinButton_clicked();

private:
    Ui::EnterBankPin *ui;
};

#endif // ENTERBANKPIN_H

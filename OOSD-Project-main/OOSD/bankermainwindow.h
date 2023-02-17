#ifndef BANKERMAINWINDOW_H
#define BANKERMAINWINDOW_H

#include <QWidget>

namespace Ui {
class BankerMainWindow;
}

class BankerMainWindow : public QWidget
{
    Q_OBJECT

public:
    explicit BankerMainWindow(QWidget *parent = nullptr);
    ~BankerMainWindow();

private slots:
    void on_pushButton_clicked();

    void on_homeButton_clicked();

    void on_transferButton_clicked();

    void on_customersButton_clicked();

    void on_pushButton_2_clicked();

private:
    Ui::BankerMainWindow *ui;
};

#endif // BANKERMAINWINDOW_H

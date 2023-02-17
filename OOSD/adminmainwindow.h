#ifndef ADMINMAINWINDOW_H
#define ADMINMAINWINDOW_H

#include <QMainWindow>

namespace Ui {
class AdminMainWindow;
}

class AdminMainWindow : public QMainWindow
{
    Q_OBJECT

public:
    explicit AdminMainWindow(QWidget *parent = nullptr);
    ~AdminMainWindow();

private slots:
    void on_homeButton_clicked();

    void on_viewTransactionsButton_clicked();

    void on_viewAccountsButton_clicked();

    void on_pushButton_clicked();

    void on_pushButton_2_clicked();

    void on_pushButton_3_clicked();

    void on_promoteButton_clicked();

    void on_demoteButton_clicked();

    void on_revertTransactionButton_clicked();

private:
    Ui::AdminMainWindow *ui;
};

#endif // ADMINMAINWINDOW_H

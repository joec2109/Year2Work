#ifndef MAINWINDOW_H
#define MAINWINDOW_H

#include <QMainWindow>

QT_BEGIN_NAMESPACE
namespace Ui { class MainWindow; }
QT_END_NAMESPACE

class MainWindow : public QMainWindow
{
    Q_OBJECT

public:
    MainWindow(QWidget *parent = nullptr);
    ~MainWindow();

private slots:
    void on_homeButton_clicked();

    void on_depositButton_clicked();

    void on_depositButton_2_clicked();

    void on_withdrawButton_clicked();

    void on_withdrawButton2_clicked();

    void on_transferButton_clicked();

    void on_transferButton2_clicked();

    void on_transactionsButton_clicked();

    void on_accountButton_clicked();

    void on_logOutButton_clicked();

private:
    Ui::MainWindow *ui;
};
#endif // MAINWINDOW_H

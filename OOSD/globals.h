#ifndef GLOBALS_H
#define GLOBALS_H

#include <QString>
#include <QStringList>

extern QString globalUsername;
extern QString globalBankPin;
extern QString globalForename;
extern QString globalSurname;
extern QString globalBalance;
extern QString globalPassword;
extern QString globalUserType;
extern QString globalSecurityQ;
extern QString globalSecurityA;

// For transfers

extern QStringList globalTargetForenames;
extern QStringList globalTargetSurnames;

extern QStringList globalTransactionAmounts;
extern QStringList globalTransactionActions;
extern QStringList globalTransactionBalance;

extern QStringList globalSenderForenames;
extern QStringList globalSenderSurnames;
extern QStringList globalSenderBalances;

// For accounts

extern QStringList accountNames;
extern QStringList accountPasswords;
extern QStringList accountPins;
extern QStringList accountBalances;
extern QStringList accountUserTypes;

// For customer accounts

extern QStringList customerAccountNames;
extern QStringList customerAccountPasswords;
extern QStringList customerAccountPins;
extern QStringList customerAccountBalances;
extern QStringList customerAccountUserTypes;

#endif // GLOBALS_H

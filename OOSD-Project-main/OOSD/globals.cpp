#include "globals.h"
#include <QString>
#include <QStringList>

// For current user signed in
QString globalUsername = "";
QString globalBankPin = "";
QString globalForename = "";
QString globalSurname = "";
QString globalBalance = "";
QString globalSecurityQ = "";
QString globalSecurityA = "";
QString globalPassword = "";
QString globalUserType = "";

// For transfers
QStringList globalTargetForenames = {};
QStringList globalTargetSurnames = {};

QStringList globalTransactionAmounts = {};
QStringList globalTransactionActions = {};
QStringList globalTransactionBalance = {};

QStringList globalSenderForenames = {};
QStringList globalSenderSurnames = {};
QStringList globalSenderBalances = {};

// For accounts
QStringList accountNames = {};
QStringList accountPasswords = {};
QStringList accountPins = {};
QStringList accountBalances = {};
QStringList accountUserTypes = {};

// For customer accounts
QStringList customerAccountNames = {};
QStringList customerAccountPasswords = {};
QStringList customerAccountPins = {};
QStringList customerAccountBalances = {};
QStringList customerAccountUserTypes = {};

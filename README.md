# Wallet Documentation

## Overview

The **Wallet package** provides a robust API for managing user wallets within the application. It enables users to perform operations such as checking balances, depositing and withdrawing funds, and transferring money between users. The package leverages blockchain technology to ensure the integrity and traceability of transactions.

---

## Installation

To integrate the **Wallet** package into your Laravel project, follow these steps:

### 1. Install the Package via Composer

Install the **Wallet** package by running the following command in your Laravel project’s root directory:

```bash
composer require h-iranshahi/blockchain-based-wallet

---

## Features

- **User Authentication**: All operations require user authentication via Laravel Sanctum.
- **Wallet Operations**:
  - **Balance Inquiry**: Retrieve current wallet balance and transaction history.
  - **Deposits**: Add funds to wallets with transaction logging.
  - **Withdrawals**: Withdraw funds, with logging of all transactions.
  - **Transfers**: Transfer funds between users, with validation and logging.
- **Blockchain Integration**: Records all transactions on the blockchain for security and transparency.
- **Event Broadcasting**: Notifies upon transaction creation for additional processing.

---

## API Routes

### Base URL
All API routes are prefixed with `/api` and require authentication.

### Endpoints

| Method | Endpoint                  | Description                                      |
|--------|---------------------------|--------------------------------------------------|
| GET    | `/api/wallet/balance`     | Get current balance and transaction history.     |
| POST   | `/api/wallet/deposit`     | Deposit funds into the wallet.                   |
| POST   | `/api/wallet/withdraw`    | Withdraw funds from the wallet.                  |
| POST   | `/api/wallet/transfer`    | Transfer funds between users.                     |
| GET    | `/blockchain/validate`    | Validate the integrity of the blockchain.        |

---

## Blockchain Integration

- **Transaction Recording**: All transactions (deposit, withdrawal, transfer) are logged in the blockchain.
- **Security and Transparency**: Ensures the integrity and traceability of all wallet operations.

---

## Testing

The package includes tests in the `WalletControllerTest` class, covering:

- Required fields validation for transfers.
- Error handling for insufficient funds.
- Successful transaction verification and balance updates.
- Testing invalid recipient IDs and amounts.

---

## Conclusion

The Wallet package offers a secure and efficient method for users to manage their wallets, ensuring that all transactions are recorded transparently using blockchain technology.

---

## Getting Started

To get started with the Wallet package, follow these steps:

1. **Installation**:  
2. **Configuration**:  
3. **Usage**:  

---

## License

This project is licensed under the [MIT License](LICENSE).


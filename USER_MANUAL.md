# User Manual — Cooperative Bank Management System

This manual covers daily operations for each staff role. Use the table of contents to jump to your section.

---

## Table of Contents

1. [Getting Started](#1-getting-started)
2. [SuperAdmin](#2-superadmin)
3. [Manager](#3-manager)
4. [Clerk](#4-clerk)
5. [Cashier](#5-cashier)
6. [Accountant](#6-accountant)
7. [Common Actions](#7-common-actions)
8. [Troubleshooting](#8-troubleshooting)

---

## 1. Getting Started

### Running the Application (Docker)

The simplest way to run the application is via Docker — no software installation required beyond Docker itself.

```bash
# Start the application
docker compose up -d

# The app is now available at http://localhost:8000
```

On first launch, the system automatically sets up the database and creates the default admin account. See the [README](README.md#docker-standalone) for more Docker commands and configuration options.

### Logging In

1. Open the bank portal in your browser (`http://localhost:8000` if running via Docker)
2. Enter your registered **Email** and **Password**
3. Click **Login**
4. You will be redirected to your role-specific dashboard

### Logging Out

Click your name in the top-right corner and select **Logout**, or click the logout button in the sidebar.

### Forgot Password

Contact your **Manager** or **SuperAdmin** to reset your password — self-service password reset must be configured by your system administrator.

---

## 2. SuperAdmin

The SuperAdmin role has full access to system configuration. These settings affect all branches.

### 2.1 Company Setup

**Path:** SuperAdmin > Company Setup

Set the bank's name, address, registration number, and contact details. This information appears on printed receipts and reports.

1. Navigate to **Company Setup**
2. Fill in all required fields
3. Click **Save**

### 2.2 Managing Branches

**Path:** SuperAdmin > Branches

| Action | Steps |
|---|---|
| Add branch | Click **New Branch** → fill name, address, manager → Save |
| Edit branch | Click **Edit** next to the branch |
| Deactivate | Toggle the Active switch |

### 2.3 Managing Users

**Path:** SuperAdmin > Users

1. Click **New User**
2. Enter name, email, and assign a **Role** and **Branch**
3. Set a temporary password (user should change on first login)
4. Click **Save**

Roles available: `SuperAdmin`, `Manager`, `Clerk`, `Cashier`, `Accountant`

### 2.4 Loan Types

**Path:** SuperAdmin > Loan Types

Define the loan products offered by the bank (e.g., Personal Loan, Home Loan, Agriculture Loan).

Fields:
- **Name** — product name
- **Interest Rate** — annual rate (%)
- **Max Tenure** — in months
- **Processing Fee** — flat or percentage
- **Min / Max Amount**

### 2.5 FD Setup

**Path:** SuperAdmin > FD Setup

Configure Fixed Deposit schemes:
- Scheme name
- Minimum and maximum deposit amount
- Tenure options (months)
- Interest rate per tenure slab
- Compounding frequency (monthly, quarterly, yearly)

### 2.6 Account Types

**Path:** SuperAdmin > Account Types

Define savings, current, and OD account types with their respective minimum balance and interest rate settings.

---

## 3. Manager

Managers oversee a branch. They approve customers and loans, and open accounts.

### 3.1 Dashboard

The Manager dashboard shows:
- Pending customer approvals
- Pending loan applications
- Branch-level account and loan counts

### 3.2 Customer Approval

Clerks register customers, but Managers must approve them before they can transact.

**Path:** Manager > Customers

1. Click on a customer name to view their details and KYC documents
2. Verify PAN and Aadhaar documents
3. Click **Approve** or **Reject**
   - If rejecting, provide a reason
4. The customer receives an email/SMS notification

### 3.3 Opening a Bank Account

Accounts can only be opened for **approved** customers.

**Path:** Manager > Bank Accounts > New Account

1. Select the **Customer** (search by name or customer ID)
2. Choose **Account Type** (Savings, Current, OD)
3. Enter the **Opening Balance**
4. Click **Open Account**

The system generates a unique account number automatically.

### 3.4 Opening an FD Account

**Path:** Manager > FD Accounts > New FD

1. Select the **Customer**
2. Choose the **FD Scheme**
3. Enter the **Principal Amount** and **Tenure**
4. Confirm the calculated maturity amount and date
5. Click **Open FD**

### 3.5 Loan Management

#### Reviewing Loan Applications

**Path:** Manager > Loan Applications

1. Click on an application to review it
2. Check customer details, income, guarantor, and requested amount
3. Click **Approve** (enter sanctioned amount) or **Reject** (with reason)

#### Disbursing a Loan

After approval, the Manager disburses the loan:

**Path:** Manager > Loans > Disburse

1. Select the approved loan application
2. Confirm disbursement amount and account to credit
3. Click **Disburse**

The system generates the full **EMI installment schedule** automatically.

#### Viewing Installment Schedule

**Path:** Manager > Loans > {Loan} > Schedule

Shows all EMI dates, principal, interest, and outstanding balance per installment.

---

## 4. Clerk

Clerks handle front-office data entry: customer registration and loan applications.

### 4.1 Registering a Customer

**Path:** Clerk > Customers > New Customer

Fill in all sections:

**Personal Information**
- Full name (as per ID)
- Date of birth
- Gender
- Occupation

**Contact Details**
- Mobile number (used for SMS notifications)
- Email address
- Emergency contact

**Address**
- Current and permanent address
- Country / State / City (select from dropdown)
- PIN code

**KYC Documents**
- PAN number + upload scanned copy (JPG/PNG/PDF, max 2MB)
- Aadhaar number + upload scanned copy (JPG/PNG/PDF, max 2MB)

**Nominee Details**
- Nominee name, relationship, date of birth

Click **Register Customer**. The application goes to the Manager for approval.

### 4.2 Tracking Registered Customers

**Path:** Clerk > Customers

The list shows all customers registered by your branch. Status badges:
- **Pending** — awaiting Manager approval
- **Approved** — active, can open accounts
- **Rejected** — application rejected, requires re-submission

Click **View** to see full details.

### 4.3 Submitting a Loan Application

Only approved customers can apply for loans.

**Path:** Clerk > Loan Applications > New Application

1. Select the **Customer** (search by name or ID)
2. Choose **Loan Type**
3. Enter **Requested Amount** and **Tenure** (months)
4. Enter **Purpose of Loan**
5. Add **Guarantor** details (if required by loan type)
6. Upload supporting documents (income proof, property documents, etc.)
7. Click **Submit Application**

The application moves to the Manager queue for review.

### 4.4 Tracking Loan Applications

**Path:** Clerk > Loan Applications

Shows all applications submitted by your branch. Statuses:
- **Submitted** — pending Manager review
- **Approved** — sanctioned, pending disbursement
- **Rejected** — application declined
- **Disbursed** — loan active

---

## 5. Cashier

Cashiers process all financial transactions at the counter.

### 5.1 Dashboard

Shows today's transaction summary:
- Total deposits
- Total withdrawals
- Total loan repayments collected
- Number of transactions

### 5.2 Processing a Transaction

**Path:** Cashier > Transactions > New Transaction

1. **Search Account** — enter the account number and press Enter or click Search
   - The customer name and current balance appear automatically
2. Select **Transaction Type**: Deposit or Withdrawal
3. Enter **Amount**
4. Select **Payment Mode**: Cash, Cheque, NEFT, IMPS, UPI
5. If **Cheque** is selected, enter cheque number, bank, and date
6. Enter **Remarks** (optional)
7. Click **Submit Transaction**

A confirmation slip is displayed. Print or save it for the customer.

> **Note:** Withdrawals are blocked if the account balance would fall below the minimum balance defined for the account type.

### 5.3 Recording a Loan Repayment

**Path:** Cashier > Loan Repayments > New Repayment

1. Enter the **Loan Account Number** and press Search
2. The current EMI due, outstanding balance, and overdue amount are shown
3. Enter the **Amount Collected**
4. Select **Payment Mode**
5. Click **Record Repayment**

The system automatically marks the corresponding installment(s) as paid and updates the outstanding balance.

### 5.4 Viewing Transaction History

**Path:** Cashier > Transactions

Filter by date range, account number, or transaction type.

---

## 6. Accountant

Accountants have read-only access and generate financial reports.

### 6.1 Dashboard

Overview of the branch's financial position:
- Total loan outstanding
- This month's collection
- Overdue loans count and amount
- Active FD count and value

### 6.2 Loan Outstanding Report

**Path:** Accountant > Reports > Loan Outstanding

Shows all active loans with:
- Customer name and loan ID
- Loan type and sanctioned amount
- Total paid, outstanding principal, outstanding interest
- Days overdue (if any)

**Export:** Click **Download CSV** or **Print** for a formatted report.

Filter by:
- Loan type
- Date range (disbursement date)
- Overdue only toggle

### 6.3 Transaction Statement

**Path:** Accountant > Reports > Transaction Statement

Generates a full debit/credit statement for any account.

1. Enter the **Account Number**
2. Select **Date Range**
3. Click **Generate**

The statement shows every transaction with date, description, debit/credit, and running balance.

**Export:** Download as CSV or print as PDF.

### 6.4 Loan Demand Collection Sheet

**Path:** Accountant > Reports > Loan Demand

Shows the EMI schedule vs actual collections for a date range:
- Which installments were due
- Which were paid (and when)
- Which are overdue

Used for daily collection monitoring and overdue follow-up.

---

## 7. Common Actions

### Searching for a Customer

From any screen that has a customer lookup field:
- Type the customer's name, mobile number, or customer ID
- The autocomplete shows matching results
- Click to select

### Printing / Saving Receipts

After any transaction or account opening:
1. A confirmation screen appears with the transaction reference number
2. Click **Print Receipt** to open the browser print dialog
3. Or click **Download PDF** (if configured)

### Viewing Notifications

The bell icon in the top navigation bar shows recent system notifications (approvals, rejections, FD maturities, etc.). Click to mark as read.

---

## 8. Troubleshooting

### "Access Denied" Error

You do not have permission for that page. Confirm with your Manager or SuperAdmin that your role is set correctly.

### Account Balance Not Updating

The transaction may be processing in the background queue. Refresh the page after a few seconds. If the balance is still wrong after 5 minutes, contact your system administrator.

### Cannot Find Customer in Dropdown

The customer may be in **Pending** status (not yet approved). Check with the Manager to approve the customer first.

### File Upload Fails (KYC)

Ensure the file:
- Is JPG, PNG, or PDF format
- Is under **2 MB** in size
- Is not password-protected (for PDFs)

### Transaction Rejected — Insufficient Balance

The account balance after the withdrawal would fall below the minimum balance. Either:
- Reduce the withdrawal amount
- Ask the Manager to adjust the minimum balance rule for the account

### Session Expired

If you are inactive for an extended period, your session will expire for security. Log in again — your data is saved.

### Docker Container Issues

If the application is not loading after `docker compose up -d`:

1. Check container logs: `docker compose logs -f app`
2. Verify the container is running: `docker compose ps`
3. If the database is corrupted, reset it: `docker compose down -v && docker compose up -d`

### Getting Help

Contact your branch Manager or the system administrator. For technical issues, report them via the project GitHub repository.

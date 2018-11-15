drop database fec353_2;
create database fec353_2;
use fec353_2;

CREATE TABLE Employee (
    id INT,
    category VARCHAR(30),
    phone BIGINT,
    title VARCHAR(40),
    fullName VARCHAR(100),
    address VARCHAR(120),
    hourlyWage FLOAT,
    startDate DATETIME,
    availableSick INT,
    availableHoliday INT,
    PRIMARY KEY (id)
);

CREATE TABLE Bank (
    bankName VARCHAR(50),
    hqLocation VARCHAR(100),
    president INT,
    investManager INT,
    insureManager INT,
    bankManager INT,
    PRIMARY KEY (bankName),
    FOREIGN KEY (president)
        REFERENCES Employee (id),
    FOREIGN KEY (investManager)
        REFERENCES Employee (id),
    FOREIGN KEY (insureManager)
        REFERENCES Employee (id),
    FOREIGN KEY (bankManager)
        REFERENCES Employee (id)
);

CREATE TABLE Branch (
    id INT,
    phone BIGINT,
    fax BIGINT,
    location VARCHAR(100),
    city VARCHAR(100),
    openingDate DATE,
    revenue FLOAT,
    managerId INT,
    PRIMARY KEY (id),
    FOREIGN KEY (managerId)
        REFERENCES Employee (id)
);

CREATE TABLE Services (
    id INT,
    serviceType VARCHAR(30),
    PRIMARY KEY (id)
);


CREATE TABLE ServiceAvailable (
    bid INT,
    seId INT,
    PRIMARY KEY (bid , seId),
    FOREIGN KEY (bid)
        REFERENCES Branch (id),
    FOREIGN KEY (seId)
        REFERENCES Services (id)
);


CREATE TABLE Schedule (
    eid INT,
    startTime DATETIME,
    endTime DATETIME,
    isHoliday BIT,
    isSickDay BIT,
    PRIMARY KEY (eid, startTime),
    FOREIGN KEY (eid)
        REFERENCES Employee (id)
);

CREATE TABLE WorksAt (
    eid INT,
    bid INT,
    PRIMARY KEY (eid , bid),
    FOREIGN KEY (eid)
        REFERENCES Employee (id),
    FOREIGN KEY (bid)
        REFERENCES Branch (id)
);

CREATE TABLE Payroll (
    eid INT,
    clockIn DATETIME,
    clockOut DATETIME,
    PRIMARY KEY (eid , clockin),
    FOREIGN KEY (eid)
        REFERENCES Employee (id)
);

CREATE TABLE Clients (
    id INT,
    pass VARCHAR(20),
    fullName VARCHAR(100),
    category VARCHAR(30),
    phone BIGINT,
    email VARCHAR(50),
    address VARCHAR(100),   
    joinDate DATETIME,
    DOB DATE,
    cardNumber BIGINT,
    PRIMARY KEY (id)
);

CREATE TABLE Member (
    cid INT,
    bid INT,
    PRIMARY KEY (cid , bid),
    FOREIGN KEY (cid)
        REFERENCES Clients (id),
    FOREIGN KEY (bid)
        REFERENCES Branch (id)
);

CREATE TABLE ChargePlan (
    id INT,
    planLimit INT,
    planOption VARCHAR(30),
    charge INT,
    PRIMARY KEY (id)
);

CREATE TABLE InterestRate (
    id INT,
    serviceType VARCHAR(30),
    typeOfAccount VARCHAR(30),
    percentCharge FLOAT,
    PRIMARY KEY (id)
);

CREATE TABLE Account (
    accountNumber INT NOT NULL,
    cpid INT NOT NULL,
    irid INT NOT NULL,
    balance FLOAT NOT NULL,
    transactionsPerMonth INT NOT NULL,
    accountType VARCHAR(30) NOT NULL,
    maxPerDay INT,
    minBalance INT,
    businessNumber INT,
    taxId INT,
    creditLimit INT,
    PRIMARY KEY (accountNumber),
    FOREIGN KEY (cpid)
        REFERENCES ChargePlan (id),
    FOREIGN KEY (irid)
        REFERENCES InterestRate (id)
);

CREATE TABLE AssociatedTo (
    bid INT,
    accountNumber INT,
    PRIMARY KEY (bid , accountNumber),
    FOREIGN KEY (bid)
        REFERENCES Branch (id)
);

CREATE TABLE AccountsOwned (
    cid INT,
    accountNumber INT,
    PRIMARY KEY (cid , accountNumber),
    FOREIGN KEY (cid)
        REFERENCES Clients (id),
    FOREIGN KEY (accountNumber)
        REFERENCES Account (accountNumber)
);

CREATE TABLE Transactions (
    bid INT,
    accountNumber INT,
    transType VARCHAR(30),
    amount FLOAT,
    transNumber INT,
    tStamp DATETIME,
    PRIMARY KEY (transNumber),
    FOREIGN KEY (bid)
        REFERENCES Branch (id),
    FOREIGN KEY (accountNumber)
        REFERENCES Account (accountNumber)
);

CREATE TABLE Bills (
    id INT,
    amount FLOAT,
    isRecurring BIT,
    accountNumber INT,
    PRIMARY KEY (id , accountNumber),
    FOREIGN KEY (accountNumber)
        REFERENCES Account (accountNumber)
);

CREATE TABLE Payee (
    accountNumber INT,
    fullName VARCHAR(50),
    PRIMARY KEY (accountNumber)
);

CREATE TABLE MyPayee (
    bSequence INT,
    referenceNumber INT,
    payeeAccount INT,
    PRIMARY KEY (bSequence , referenceNumber),
    FOREIGN KEY (payeeAccount)
        REFERENCES Payee (accountNumber)
);

CREATE TABLE Payment (
    billId INT,
    billSequence INT,
    billDate DATETIME,
    amount FLOAT,
    PRIMARY KEY (billId , billSequence),
    FOREIGN KEY (billId)
        REFERENCES Bills (id),
    FOREIGN KEY (billSequence)
        REFERENCES MyPayee (bSequence)
);
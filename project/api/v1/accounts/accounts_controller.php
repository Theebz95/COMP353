<?php
include_once '../../database/database.php';
include_once '../../utils/helpers.php';

/**
 * Query all accounts information
 * @return JSON information regarding clients
 */
function get_all_accounts()
{
    try {
        $database = new Database();
        $db = $database->getConnection();

        if (!test_db_connection($db)) {
            return array("error" => "Cannot connect to DB.");
        }
    
        // query statement
        $query = "SELECT * FROM Account";
    
        // prepare query statement
        $stmt = $db->prepare($query);
        $stmt->execute();

        $packet=array();
        $packet["accounts"]=array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tmp=array(
                "accountNumber" => $row['accountNumber'],
                "cpid" => $row['cpid'],
                "irid" => $row['irid'],
                "balance" => $row['balance'],
                "transactionsPerMonth" => $row['transactionsPerMonth'],
                "accountType" => $row['accountType'],
                "maxPerDay" => $row['maxPerDay'],
                "minBalance" => $row['minBalance'],
                "businessNumber" => $row['businessNumber'],
                "taxId" => $row['taxId'],
                "creditLimit" => $row['creditLimit']
            );
            array_push($packet["accounts"], $tmp);
        }
        return $packet;
    } catch (Exception $e) {
        return array("error" => "Server error ".$e." .");
    }
}

function get_user_accounts($user_id)
{
  try {
    error_reporting(0);
    $database = new Database();
    $db = $database->getConnection();

    if (!test_db_connection($db)) {
        return array("error" => "Cannot connect to DB.");
    }

    // query statement
    $query = "SELECT Account.*, cid FROM AccountsOwned INNER JOIN Clients ON id = cid INNER JOIN Account on Account.accountNumber = AccountsOwned.accountNumber WHERE cid = ". $user_id .";";

    // prepare query statement
    $stmt = $db->prepare($query);
    $stmt->execute();

    $packet=array();
    $packet["user_accounts"]=array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      array_push($packet["user_accounts"], $row);
    }

    return $packet;
  } catch (Exception $e) {
      return array("error" => "Server error ".$e." .");
  }
}

function get_accounts_by_email($email) 
{
    try {
        $database = new Database();
        $db = $database->getConnection();
    
        if (!test_db_connection($db)) {
            return array("error" => "Cannot connect to DB.");
        }
    
        // query statement
        $query = "SELECT AccountsOwned.accountNumber, accountType FROM AccountsOwned INNER JOIN Clients ON id = cid INNER JOIN Account on Account.accountNumber = AccountsOwned.accountNumber WHERE email = ". $email .";";

        // prepare query statement
        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $packet=array();
        $packet["user_accounts"]=array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          // $tmp=array();
          array_push($packet["user_accounts"], $row);
        }
        return $packet;

    } catch (Exception $e) {
        return array("error" => "Server error ".$e." .");
    }  
}

/**
 * Add new account
 */
function post_user_accounts($account_data)
{
  try {
    $database = new Database();
    $db = $database->getConnection();

    if (!test_db_connection($db)) {
        return array("error" => "Cannot connect to DB.");
    }

    // Insert new account in Account db
    $cpid = $account_data['cpid'];
    $irid = $account_data['irid'];
    $balance = $account_data['balance'];
    $transactionsPerMonth = $account_data['transactionsPerMonth'];
    $transactionsLeft = $account_data['transactionsPerMonth'];
    $currency = $account_data['currency'];
    $isNotified = (int) $account_data['isNotified'];
    $accountType = $account_data['accountType'];
    $maxPerDay = $account_data['maxPerDay'];
    $minBalance = ($account_data['minBalance'] == '' ? 'NULL' : $account_data['minBalance']);
    $businessNumber = ($account_data['businessNumber'] == '' ? 'NULL' : $account_data['businessNumber']);
    $taxId = ($account_data['taxId'] == '' ? 'NULL' : $account_data['taxId']);
    $creditLimit = ($account_data['creditLimit'] == '' ? 'NULL' : $account_data['creditLimit']);

    $query= "INSERT INTO ACCOUNT VALUES (0, $cpid, $irid, $balance, $transactionsPerMonth, $transactionsLeft,'$currency', $isNotified,'$accountType', $maxPerDay, $minBalance, $businessNumber, $taxId, $creditLimit);";

    $stmt = $db->prepare($query);
    $stmt->execute();

    //Insert into acocuntsOwned list
    $newAccountId = $db->lastInsertId();    
    $cid = $account_data['cid'];

    $query2 = "INSERT INTO AccountsOwned VALUES ($cid, ".$newAccountId.")";
    
    $stmt2 = $db->prepare($query2);
    $stmt2->execute();

    //Get all updatedAccounts
    $query3 = "SELECT Account.* FROM AccountsOwned INNER JOIN Clients ON id = cid INNER JOIN Account on Account.accountNumber = AccountsOwned.accountNumber WHERE Clients.id = ". $cid .";";

        // prepare query statement
    $stmt3 = $db->prepare($query3);
    $stmt3->execute();
    
    $packet=array();
    $packet["user_accounts"]=array();
        
    while ($row = $stmt3->fetch(PDO::FETCH_ASSOC)) {
        // $tmp=array();
        array_push($packet["user_accounts"], $row);
    }
    return $packet;
  } catch (Exception $e) {
      return array("error" => "Server error ".$e." .");
  }
}


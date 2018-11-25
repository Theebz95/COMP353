<?php
include_once '../../database/database.php';
include_once '../../utils/helpers.php';

/**
 * Send moulas
 * @param sender sender's account number 
 * @param recipient recipient's account number 
 * @param amount $$$ 
 */
function send_transfer($sender,$recipient, $amount, $transferType)
{
    try {
        $database = new Database();
        $db = $database->getConnection();

        if (!test_db_connection($db)) {
            return array("error" => "Cannot connect to DB.");
        }

        /**
         * Check if user exist
         */
        $check_query = "SELECT * FROM ACCOUNT ";
        $check_query .= "WHERE accountNumber = ".$recipient.";";

        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute();

        if ($check_stmt->fetch(PDO::FETCH_ASSOC) == false) {
            return array("error" => "Account ".$recipient." does not exist. lol >:(");
        }

        /**
         * Update recipient's balance
         */
        $query = "UPDATE ACCOUNT "; 
        $query .= "SET balance = balance + ".$amount." ";
        $query .= "WHERE accountNumber = ".$recipient.";";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        /**
         * Update sender's balance
         */

        $query1 = "UPDATE ACCOUNT "; 
        $query1 .= "SET balance = balance - ".$amount." ";
        $query1 .= ", transactionsPerMonth = transactionsPerMonth - 1 ";
        $query1 .= "WHERE accountNumber = ".$sender.";";

        $stmt1 = $db->prepare($query1);
        $stmt1->execute();

        /**
         * Add to transaction history
         */

         // First get branch ID
        $query_get_bId = "SELECT bid FROM associatedTo WHERE accountNumber = ".$recipient.";";
        
        $bid_stmt = $db->prepare($query_get_bId);
        $bid_stmt->execute();
        
        $bIdValue=array();
        
        while ($row = $bid_stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($bIdValue,$row);
        }
        $bId = $bIdValue[0]["bid"];

        $query_transfer = "INSERT INTO Transactions (bid, accountNumber, transType, amount, tStamp, recipientAccountNumber) ";
        $query_transfer .= "VALUES (".$bId."";
        $query_transfer .= ",".$sender."";
        $query_transfer .= ",'".$transferType."'";
        $query_transfer .= ",".$amount."";
        $query_transfer .= ",'".date("Y-m-d h:i:s")."'";
        $query_transfer .= ",".$recipient.");";
        
        $stmt_transaction = $db->prepare($query_transfer);
        $stmt_transaction->execute();

        /**
         * Return sender's new balance
         */
        $query2 = "SELECT balance FROM ACCOUNT "; 
        $query2 .= "WHERE accountNumber = ".$sender.";";

        $stmt2 = $db->prepare($query2);
        $stmt2->execute();

        return $stmt2->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return array("error" => "Server error ".$e." .");
    }
}

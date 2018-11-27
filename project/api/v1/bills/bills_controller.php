<?php
include_once '../../database/database.php';
include_once '../../utils/helpers.php';

/**
 * Check if the user can login
 * @return JSON login info
 */


function print_bills($data)
{

    try {
        $database = new Database();
        $db = $database->getConnection();
    
        if (!test_db_connection($db)) {
            return array("error" => "Cannot connect to DB.");
        }
    $packet = array();
    $accNum = $data['accountNumber'];
    $query = "SELECT MyPayee.id FROM MyPayee INNER JOIN Account ON MyPayee.accountNumber = Account.accountNumber WHERE MyPayee.accountNumber = ".$accNum." AND Account.accountNumber = ".$accNum.";";
    $stmt = $db->prepare($query); 
    $stmt->execute();

    $number_of_rows = $stmt->fetchColumn();
    $stmt->execute();


    if ($number_of_rows > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $query1 = "SELECT accountNumber, payeeId, isPaid, dueDate, MyPayee.amount as MyPayeeAmount, Bills.amount as billsAmount FROM MyPayee INNER JOIN Bills ON MyPayee.id = Bills.MyPayeeId WHERE MyPayee.id = ".$id." AND Bills.MyPayeeId = ".$id.";";
            $stmt1 = $db->prepare($query1);
            $stmt1->execute();
            $number_of_rows1 = $stmt1->fetchColumn();
            $stmt1->execute();
            while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                $tmp = array(
                    'accountNumber' => $row1['accountNumber'],
                    'payeeId' => $row1['payeeId'],
                    'isPaid' => $row1['isPaid'],
                    'dueDate' => $row1['dueDate'],
                    'MyPayeeAmount' => $row1['MyPayeeAmount'],
                    'billsAmount' => $row1['billsAmount']
                );
                array_push($packet, $tmp);
                }
            }
        return $packet;
    } else {
            return array("billExists" => False);
    }

    } catch (Exception $e) {
        return array("error" => "Server error ".$e." .");
    }
}


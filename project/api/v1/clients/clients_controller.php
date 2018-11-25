<?php
include_once '../../database/database.php';
include_once '../../utils/helpers.php';

/**
 * Query all clients information
 * @return JSON information regarding clients
 */
function get_all_clients()
{
    try {
        $database = new Database();
        $db = $database->getConnection();

        if (!test_db_connection($db)) {
            return array("error" => "Cannot connect to DB.");
        }
    
        // query statement
        $query = "SELECT fullName, email FROM CLIENTS";
    
        // prepare query statement
        $stmt = $db->prepare($query);
        $stmt->execute();

        $packet=array();
        $packet["clients"]=array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tmp=array(
                "name" => $row['fullName'],
                "email" => $row['email']
            );
            array_push($packet["clients"], $tmp);
        }
        return $packet;
    } catch (Exception $e) {
        return array("error" => "Server error ".$e." .");
    }
}


function get_client_by_id($user_id)
{
    try {
        $database = new Database();
        $db = $database->getConnection();

        if (!test_db_connection($db)) {
            return array("error" => "Cannot connect to DB.");
        }
    
        // query statement
        $query = "SELECT * FROM Clients WHERE id = ".$user_id.";";
    
        // prepare query statement
        $stmt = $db->prepare($query);
        $stmt->execute();

        $packet=array();
        $packet["clients"]=array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            unset($row['id']);
            unset($row['pass']);
            array_push($packet["clients"], $row);
        }
        return $packet;
    } catch (Exception $e) {
        return array("error" => "Server error ".$e." .");
    }
}


function modify_client_by_id($user)
{
    try {
        $database = new Database();
        $db = $database->getConnection();
        if (!test_db_connection($db)) {
            return array("error" => "Cannot connect to DB.");
        }
        
        $query = "SELECT id FROM Clients WHERE (email = '".$user['email']."' OR  phone = '".$user['phone']."') AND id <> ".$user[id]. "";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $number_of_rows = $stmt->fetchColumn();
        if ($number_of_rows > 0) {
            return array("error" => 'Someone already has that email or phone number!'); 
        } else {
            $query = "UPDATE Clients SET phone = '".$user['phone']."' , email = '".$user['email']."' , address = '".$user['address']."' WHERE id = ".$user['id']."";
            $stmt = $db->prepare($query);
            $stmt->execute();
            return array("msg" => 'Successfully updated client info.');
        }
    } catch (Exceptiion $e) {
        return array("error" => "Server error ".$e." .");
    }
}


function modify_client_password($user)
{
    try {
        $database = new Database();
        $db = $database->getConnection();
        if (!test_db_connection($db)) {
            return array("error" => "Cannot connect to DB.");
        }
         // query statement
         $query = "SELECT * FROM Clients WHERE id = '".$user['id']."' AND  pass = '".$user['oldPass']."'";
         $stmt = $db->prepare($query);
         $stmt->execute();
         $number_of_rows = $stmt->fetchColumn();
         // valid password
         if ($number_of_rows > 0) {
            $query = "UPDATE Clients SET pass = '".$user['newPass']."' WHERE id = ".$user['id']."";
            $stmt = $db->prepare($query);
            $stmt->execute();
            return array("msg" => "Successfully updated client info.");
         } else {
            return array("error" => "Incorrect password.");
         }
    } catch (Exceptiion $e) {
        return array("error" => "Server error ".$e." .");
    }
}
<?php

include "global_struct.php";
$dbconnection = new PDO("sqlite:tododb.db");

$id = "";
if(isset($_GET['id'])){
  $id = $_GET['id'];
}

if(deleteTodo($dbconnection,$id)){
  echo "success deleting todo";
}else{
  "error at line: ".__LINE__." in file: ".__FILE__;
}

function deleteTodo($dbconnection,$id) : bool
{

  $retVal = false;

  try {
    if ($dbconnection) {
        if ($dbconnection->beginTransaction()) {
            $statement = $dbconnection->prepare("DELETE FROM todos WHERE `id` = :valID");

            if ($statement) {

                $statement->bindParam(':valID', intval($id), PDO::PARAM_INT);


                if ($statement->execute()) {

                    $dbconnection->commit();
                    $retVal = true;
                  
                }
            } else {
                $dbconnection->rollBack();
                throw new Exception("Failed to prepared statement in " . __FILE__ . " at line #" . __LINE__ . " Reason: " . implode(":", $dbconnection->errorInfo()));
            }
        } else {
            $dbconnection->rollBack();
            die("Cannot start a transaction for " . __FILE__ . " at line #" . __LINE__);
        }
    } else {
        die("Cannot connect to database for " . __FILE__ . " at line #" . __LINE__);
    }
} catch (PDOException $Exception) {
    throw new $Exception('Error: ' . $Exception->getMessage());
} catch (Exception $e) {
    echo 'Caught exception: ', $e->getMessage(), "\n";
}

return $retVal;
}
<?php

include "global_struct.php";
$dbconnection = new PDO("sqlite:tododb.db");

$name = "";
$complete = "";
$trash = "";

$result = "none";

if(isset($_GET['name'])){
  $name = $_GET['name'];
}
if(isset($_GET['complete'])){
  $complete = $_GET['complete'];
}
if(isset($_GET['trash'])){
  $trash = $_GET['trash'];
}


if(insertTodo($dbconnection, $name, $complete, $trash)){
  echo "sussess inserting todo";

}
else{
  echo "error at line: ".__LINE__." in file: ".__FILE__;
}

function insertTodo($dbconnection, $name, $complete, $trash) : bool
{

  $retVal = false;

  try {
    if ($dbconnection) {
        if ($dbconnection->beginTransaction()) {
            $statement = $dbconnection->prepare("INSERT into todos(`name`, `complete`, `trash`) VALUES (:valNAME, :valCOMPLETE,:valTRASH);");

            if ($statement) {

                $statement->bindParam(':valNAME', $name, PDO::PARAM_STR);
                $statement->bindParam(':valCOMPLETE', intval($complete), PDO::PARAM_INT);
                $statement->bindParam(':valTRASH', intval($trash), PDO::PARAM_INT);
                
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



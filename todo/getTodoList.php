<?php
include "global_struct.php";
$dbconnection = new PDO("sqlite:tododb.db");
$todoArr = getTodArr($dbconnection);

$id = "";
$name = "";
$completed = "";
$items_arr['data'] = array();

for($i=0;$i<sizeof($todoArr); $i++){
  $itm = $todoArr[$i];

  $id = $itm->ID;
  $name = $itm->NAME;
  $complete = $itm->COMPLETE;
  
  $list = array(  
    'id' => $id,
    'name' => $name,
    'complete' => $complete,

  );

  // echo $id." - ".$name."<br>";
  array_push($items_arr, $list);
}

// echo "list is here<br> ";
echo json_encode($items_arr);

function getTodArr($dbconnection) : array
{

  $retVal = [];

  try {
    if ($dbconnection) {
        if ($dbconnection->beginTransaction()) {
            $statement = $dbconnection->prepare("SELECT * FROM todos WHERE `trash` = 0");

            if ($statement) {


                if ($statement->execute()) {

                    
                  while ($fld = $statement->fetch(PDO::FETCH_ASSOC)) {
                    $todoObj = new Todo;
                    $todoObj->ID = $fld['id'];
                    $todoObj->NAME = $fld['name'];
                    $todoObj->COMPLETE = $fld['complete'];
                    $retVal[] = $todoObj;
                  }

                  
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
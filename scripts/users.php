<?php
include "../config/config.php";
include "../config/utils.php";

// USERS:

$dbConn =  connect($db);

// Get all or just one

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if (isset($_GET['id']))
    {
      // Get one
      $sql = $dbConn->prepare("SELECT * FROM ag_users where id=:id");
      $sql->bindValue(':id', $_GET['id']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
      exit();
	  }
    else {
      // Get all
      $sql = $dbConn->prepare("SELECT * FROM ag_users");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode( $sql->fetchAll()  );
      exit();
	}
}

// Post one

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $input = $_POST;
    $sql = "INSERT INTO ag_users
          (username, name, lastname, organization, country)
          VALUES
          (:username, :name, :lastname, :organization, :country)";
    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);
    $statement->execute();
    $ag_userId = $dbConn->lastInsertId();
    if($ag_userId)
    {
      $input['id'] = $ag_userId;
      header("HTTP/1.1 200 OK");
      echo json_encode($input);
      exit();
	 }
}

// Detele one

if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
	$id = $_GET['id'];
  $statement = $dbConn->prepare("DELETE FROM posts where id=:id");
  $statement->bindValue(':id', $id);
  $statement->execute();
	header("HTTP/1.1 200 OK");
	exit();
}

// Update one

if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    $input = $_GET;
    $postId = $input['id'];
    $fields = getParams($input);

    $sql = "
          UPDATE posts
          SET $fields
          WHERE id='$postId'
           ";

    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);

    $statement->execute();
    header("HTTP/1.1 200 OK");
    exit();
}


// In case none of the above options have been executed.
header("HTTP/1.1 400 Bad Request");

?>
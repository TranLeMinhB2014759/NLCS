<?php
include '../partials/db_connect.php';

#Read Values
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length'];
$columnIndex = $_POST['order'][0]['column'];
$columnName = $_POST['columns']['columnIndex']['data'];
$columnSortOrder = $_POST['order']['0']['dir'];
$searchValue = $_POST['search']['value'];

$searchArray = array();

#Search
$searchQuery = "";
if($searchValue != ""){
    $searchQuery = "AND (book_id LIKE :book_id OR book_name LIKE :book_name OR book_author LIKE :book_author)";
    $searchArray = array(
        'book_id' => "%$searchValue%",
        'book_name' => "%$searchValue%",
        'book_author' => "%$searchValue%",
    );
}

# Total number of records wịthout filter
$stmt = $db -> prepare("SELECT COUNT(*) AS allcount FROM quyensach");
$stmt -> execute();
$records = $stmt->fetch();
$totalRecords = $records['allcount'];

# Total number of records wịth filter
$stmt = $db -> prepare("SELECT COUNT(*) AS allcount FROM quyensach WHERE 1".$searchQuery);
$stmt -> execute($searchArray);
$records = $stmt->fetch();
$totalRecordswithFilter = $records['allcount'];

# Fetch records
$stmt = $db -> prepare("SELECT * FROM quyensach WHERE 1".$searchQuery."ORDER BY".$columnName." ".$columnSortOrder. "LIMIT :limit, offset");

//Bind Value
foreach($searchArray as $keyword => $search){
    $stmt->bindValue(":".$keyword,$search, PDO::PARAM_STR);
}
$stmt->bindValue(":limit",(int)$row, PDO::PARAM_INT);
$stmt->bindValue(":offset",(int)$rowperpage, PDO::PARAM_INT);
$stmt->execute();

$empRecords = $stmt->fetchAll();

$data = array();
foreach($empRecords as $row){
    $data[] = array(
        "book_id" => $row['book_id'],
        "book_name" => $row['book_id'],
        "book_author" => $row['book_id'],
        "book_img" => $row['book_img'],
    );
}

# Response
$response = array(
    "draw" =>intVal($draw),
    "iTotalRecords" => ($totalRecord),
    "iTotalDisplayRecords" => ($totalRecordswithFilter),
    "aaData" =>$data,
);
echo json_encode($response)
?>
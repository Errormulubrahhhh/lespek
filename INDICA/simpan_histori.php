<?php
require "connect.php";

$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    echo json_encode(["status"=>"error"]);
    exit;
}

$total = $data['total'];
$items = $data['items'];

// simpan transaksi utama
$sql = "INSERT INTO transactions (total, created_at)
        VALUES ('$total', NOW())";
mysqli_query($conn, $sql);

$transaction_id = mysqli_insert_id($conn);

// simpan detail transaksi
foreach($items as $item){
    $name  = $item['name'];
    $qty   = $item['qty'];
    $price = $item['price'];

    mysqli_query($conn, "INSERT INTO transaction_items
        (transaction_id, product_name, qty, price)
        VALUES ('$transaction_id','$name','$qty','$price')");
}

echo json_encode(["status"=>"success"]);

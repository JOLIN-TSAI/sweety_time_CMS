<?php

require_once("../db_connect.php");
include("../function/function.php");

if (isset($_GET["shopid"])) {
    $shopId = $_GET["shopid"];
    $sql = "SELECT * FROM products WHERE id=$shopId AND available = 1 ORDER BY product_id";
} else {
    $sql = "SELECT * FROM products WHERE available = 1 ORDER BY product_id";
}

$result = $conn->query($sql);
$rows = $result->fetch_all(MYSQLI_ASSOC);
$productCount = $result->num_rows;

// print_r($rows);


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>product-list</title>
    <?php include("../css/css_Joe.php"); ?>

    <style>
        .bdrs {
            border-radius: 20px;
        }
    </style>
</head>

<body>
    <?php include("../modules/dashboard-header_Joe.php"); ?>

    <div class="container-fluid d-flex flex-row px-4">

        <?php include("../modules/dashboard-sidebar_Joe.php"); ?>

        <div class="main col neumorphic p-5">

            <h2>商品列表</h2>


            <p>共<?= $productCount ?>筆</p>
            <div class="container">
                <form action="" class="mb-4">
                    <div class="row">
                        <div class="col">
                            <input type="text">
                            <button></button>
                        </div>
                    </div>
                </form>

                <?php if ($productCount > 0): ?>
                    <table class="table table-bordered table-hover bdrs">
                        <thead class="text-center table-dark">
                            <tr>
                                <th>名稱</th>
                                <th>照片</th>
                                <th>價格</th>
                                <th>描述</th>
                                <th>庫存數量</th>
                                <th>編輯</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <th><?= $row["name"] ?></th>
                                    <th></th>
                                    <th class="text-center"><?= number_format($row["price"]) ?></th>
                                    <th><?= getLeftChar($row["description"], 100) . "..." ?></th>
                                    <th class="text-center"><?= $row["stocks"] ?></th>
                                    <th><a href="/product.php?productid=<?= $row["product_id"] ?>" class="btn btn-warning">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a></th>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    暫無符合條件的商品
                <?php endif; ?>
            </div>




        </div>

    </div>

    <?php include("../js.php"); ?>
</body>

</html>


<?php $conn->close(); ?>
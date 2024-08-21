<?php
if (!isset($_GET["user_id"])) {
    echo "請正確帶入 get user_id 變數";
    exit;
}

require_once("../db_connect.php");

$user_id = $_GET["user_id"];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $targetDir = '/Applications/XAMPP/xamppfiles/htdocs/project/images/users/';
        $fileType = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');

        // 檢查檔案格式
        if (in_array(strtolower($fileType), $allowTypes)) {
            // 使用原檔名加上唯一的時間戳來生成檔名
            $originalFileName = pathinfo($_FILES['profile_image']['name'], PATHINFO_FILENAME);
            $newFileName = $originalFileName . '_' . time() . '.' . $fileType;
            $targetFilePath = $targetDir . $newFileName;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath)) {
                // 獲取舊的圖片名稱
                $sql = "SELECT portrait_path FROM users WHERE user_id = $user_id";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $oldImage = $row['portrait_path'];

                // 刪除舊的圖片
                if (!empty($oldImage) && file_exists($targetDir . $oldImage)) {
                    unlink($targetDir . $oldImage);
                }

                // 更新資料庫中的圖片名稱
                $sql = "UPDATE users SET portrait_path='$newFileName' WHERE user_id = $user_id";
                if ($conn->query($sql)) {
                    echo "圖片更新成功";
                    header("Location: user-edit.php?user_id=$user_id");
                    exit;
                } else {
                    echo "資料更新失敗: " . $conn->error;
                }
            } else {
                echo "檔案上傳失敗";
                $error = $_FILES['profile_image']['error'];
                switch ($error) {
                    case UPLOAD_ERR_FORM_SIZE:
                        echo "檔案大小超過限制";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        echo "檔案只上傳了部分";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        echo "沒有檔案被上傳";
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        echo "缺少臨時檔案夾";
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        echo "檔案寫入失敗";
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        echo "檔案上傳被擴展阻止";
                        break;
                    default:
                        echo "未知錯誤代碼: " . $error;
                        break;
                }
            }
        } else {
            echo "不支援的檔案格式";
        }
    } else {
        echo "沒有檔案上傳或上傳錯誤";
    }

    $name = $_POST['name'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $sql = "UPDATE users SET name = '$name', password = '$password', email = '$email' WHERE user_id = $user_id";
    if ($conn->query($sql)) {
        echo "資料更新成功";
    } else {
        echo "資料更新失敗: " . $conn->error;
    }
}

$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = $conn->query($sql);
$userCount = $result->num_rows;
$row = $result->fetch_assoc();

if ($userCount > 0) {
    $title = $row["name"];
    // 如果有就顯示圖片，沒有就顯示預設圖
    $defaultImage = 'https://images.unsplash.com/photo-1472396961693-142e6e269027?q=80&w=2152&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D';
    $imagePath = !empty($row['portrait_path']) ? '../images/users/' . $row['portrait_path'] : $defaultImage;
} else {
    $title = "使用者不存在";
    $imagePath = $defaultImage;
}

$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
    <title><?= htmlspecialchars($title) ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <?php include("../css/css_Joe.php"); ?>
    <style>
        .user-btn {
            width: 100px;
        }

        .user-search {
            width: 200px;
        }
    </style>
</head>

<body>
    <?php include("../modules/dashboard-header_Joe.php"); ?>

    <div class="container-fluid d-flex flex-row px-4">
        <?php include("../modules/dashboard-sidebar_Joe.php"); ?>

        <div class="container-fluid d-flex flex-row px-4">
            <div class="main col neumorphic p-5">
                <div class="py-2">
                    <a class="btn btn-neumorphic user-btn" href="user.php?user_id=<?= htmlspecialchars($row["user_id"]) ?>" title="回使用者"><i class="fa-solid fa-left-long"></i></a>
                </div>
                <h2 class="mb-3">修改資料</h2>
                <div class="container">
                    <div class="row">
                        <?php if ($userCount > 0): ?>
                            <form action="user-edit.php?user_id=<?= htmlspecialchars($user_id) ?>" method="post" enctype="multipart/form-data">
                                <div class="col d-flex justify-content-center align-items-center">
                                    <div class="mb-3">
                                        <label for="profile_image">上傳或更改圖片:</label><br>
                                        <img src="<?= htmlspecialchars($imagePath) ?>" alt="Profile Image" style="width:300px;height:300px;" class="object-fit-fill">
                                        <input type="file" name="profile_image" id="profile_image">
                                    </div>
                                </div>
                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Name</th>
                                        <td>
                                            <input type="text" value="<?= htmlspecialchars($row["name"]) ?>" class="form-control" name="name">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Password</th>
                                        <td>
                                            <input type="password" value="<?= htmlspecialchars($row["password"]) ?>" class="form-control" name="password">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>
                                            <input type="text" value="<?= htmlspecialchars($row["email"]) ?>" class="form-control" name="email">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Birthday</th>
                                        <td><?= htmlspecialchars($row["birthday"]) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Sign up time</th>
                                        <td><?= htmlspecialchars($row["sign_up_time"]) ?></td>
                                    </tr>
                                </table>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-neumorphic user-btn">儲存</button>
                                </div>
                            </form>
                        <?php else: ?>
                            使用者不存在
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include("../js.php"); ?>
</body>

</html>
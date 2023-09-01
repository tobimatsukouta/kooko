<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
extract($_POST);
$errors = [];
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_db = 'test';
$mysqli = @new mysqli(
  $db_host,
  $db_user,
  $db_password,
  $db_db
);
$wheres = [];
if (!empty($namae1)) {
  $wheres[] = "(namae1 LIKE '%{$namae1}%' OR namae2 LIKE '%{$namae1}%')";
}
// 映画
if (!empty($eiga)) {
  $wheres[] = "eiga = '{$eiga}'";
}
if (!empty($time)) {
  $wheres[] = "time = '{$time}'";
}
// 開始と終了がどちらも入っている時
if (!empty($created_at_start) && !empty($created_at_end)) {
  $wheres[] = "created_at >= '{creatrd_at_start)'";
  $wheres[] = "created_at <= '{$created_at_end}'";
}
//開始しか入っていない時
else if (!empty($created_at_start)) {
  $wheres[] = "created_at >= '{creatrd_at_start)'";
}
//終了しか入っていないとき
else if (!empty($created_at_end)) {
  $wheres[] = "created_at <= '{$created_at_end}'";
}
if ($mysqli->connect_error) {
  $errors[] = "[{$mysqli->connect_errno}]::MySQLのエラーです";
} else {
  // 接続成功時の処理
  // 接続成功時の処理
  $query  = "SELECT id, created_at, namae2, namae1, eiga, time, payment FROM form ORDER BY id ASC";
  $stmt   = $mysqli->prepare($query);
  if (!empty($wheres)) {
    $where  = implode(" AND ", $wheres);
    $query  = "SELECT id, created_at, namae2, namae1, eiga, time, payment FROM form WHERE {$where} ORDER BY id ASC";
    $stmt   = $mysqli->prepare($query);
  }
  try {
    $stmt->execute();
    $rows = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
      $rows[] = $row;
    }
  } catch (Exception $e) {
    $errors[] = "[99999]::{$e->getMessage()}";
  }
}
$mysqli->close();
?>
<!doctype html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title>Hello, world!</title>
</head>
<body>
  <div class="container">
    <h1>映画観覧予約フォーム</h1>
    <form method="post" action="./list.php">
      <div class="mb-3 row">
        <label for="namae1" class="col-sm-2 col-form-label">名前</label>
        <div class="col-sm-10">
          <input type="text" name="namae1" class="form-control" id="namae1">
        </div>
      </div>
      <div class="mb-3 row">
        <label for="eiga" class="col-sm-2 col-form-label">映画</label>
        <div class="col-sm-10">
          <select class="form-select" name="eiga" id="eiga">
            <option selected disabled>選択してください</option>
            <option value="マリオ">マリオ</option>
            <option value="メガれ">メガれ</option>
            <option value="怪獣">怪獣</option>
          </select>
        </div>
      </div>
      <div class="mb-3 row">
        <label for="time" class="col-sm-2 col-form-label">時間</label>
        <div class="col-sm-10">
          <input type="time" name="time" class="form-control" id="time">
        </div>
      </div>
      <button type="submit" class="btn btn-info">検索</button>
    </form>
    <?php if (empty($errors)) { ?>
      <table class="table table-primary table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>予約番号</th>
            <th>名前</th>
            <th>映画</th>
            <th>時間</th>
            <th>支払い</th>
          <tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row) { ?>
            <tr>
              <td><?php echo htmlspecialchars($row['ID']); ?></td>
              <td><?php echo htmlspecialchars($row['created_at']); ?></td>
              <td><?php echo htmlspecialchars("{$row['namae1']}{$row['namae2']}{$row['namae3']}{$row['namae4']}") ?></td>
              <td><?php echo htmlspecialchars($row['eiga']); ?></td>
              <td><?php echo htmlspecialchars($row['time']); ?></td>
              <td><?php echo htmlspecialchars($row['payment']); ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <div class="alert alert-danger" role="alert">
        <?php echo implode("<br>", $errors); ?>
      </div>
    <?php } ?>
  </div>
  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
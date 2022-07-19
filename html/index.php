<!DOCTYPE html>
<html lang="ja">
<head><title>PHP SORT</title></head>
<body>

<?php

$dsn = 'mysql:host=mysql;dbname=sort;charset=utf8';
$user = 'test';
$password = 'test';

try{
    $db = new PDO($dsn, $user, $password);


    // $stmt = $db->query('SELECT * FROM personal_info');
    // $stmt->execute();
    // while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    //   echo htmlspecialchars($row['id']) . '<br>';
    //   echo htmlspecialchars($row['sei']) . '<br>';
    // }

  }catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
}

?>

<?php
  // csvファイルを配列に格納
  $csv_file = file_get_contents('data/kokochie_dev_sample.csv');

  $aryRow = explode("\n", $csv_file);

  $aryCsv = [];
  
  
  foreach($aryRow as $key => $value){
    if($key == 0) continue; 
    if($key == '') continue;
    $aryCsv[] = explode(",", $value);
  }
  
  var_dump($aryCsv[1]);
?>
<h3>住所都道府県毎の件数における構成比（%）において上位30%未満を占める都道府県名</h3>

<?php
  // 都道府県データ取得
  $aryPre = [];
  foreach($aryCsv as $key){
    $aryPre[] = $key[23];
  }
  $PreCount = array_count_values($aryPre);
  arsort($PreCount);
  $sum = 0;
  $dataSum = count($aryPre);
  foreach($PreCount as $key=>$value) {
    $sum += $value;
    if(($sum / $dataSum) >= 0.3) {
      break;
    }
    echo $key . ':' . $value . '人<br>';
  }
  ?>

<h3>同一姓の多い順番、ベスト５</h3>
<?php
  // 姓データ取得
  $aryName = [];
  foreach($aryCsv as $key){
    $aryName[] = $key[1];
  }
  $nameCount = array_count_values($aryName);
  arsort($nameCount);

  $count = 1;
  $rank = 1;
  $before_count = 0;
  $int = 0;
  foreach($nameCount as $key=>$value) {
    if($before_count != $value){
      $rank = $count;
      $int++;
    }
    if($int == 6) {
      break;
    }
    echo $rank . '位 ' . $key . $value. '人<br>';
    $before_count = $value;
    $count++;
  }
?>

<h3>都道府県毎の平均年齢が多い県の男女割合</h3>
<?php
  // 都道府県、年齢、性別データ取得
  $aryPreData = [];
  $cnt = 0;

  foreach($aryCsv as $key){
    if($key[5] == '男') {
      $aryPreData[$key[23]]['男'] += 1;
    }else {
      $aryPreData[$key[23]]['男'] += 0;
    }
    if($key[5] == '女') {
      $aryPreData[$key[23]]['女'] += 1;
    }else {
      $aryPreData[$key[23]]['女'] += 0;
    }
    $aryPreData[$key[23]]['カウント'] += 1;
    $aryPreData[$key[23]]['年齢合計'] += $key[22];
    $aryPreData[$key[23]]['平均年齢'] = round($aryPreData[$key[23]]['年齢合計'] / $aryPreData[$key[23]]['カウント']);
  }
  // var_dump($aryPreData);

  function sortByKey($key_name, $sort_order, $array) {
    foreach ($array as $key => $value) {
        $standard_key_array[$key] = $value[$key_name];
    }

    array_multisort($standard_key_array, $sort_order, $array);

    return $array;
  }


  $sorted_array = sortByKey('平均年齢', SORT_DESC, $aryPreData);
  // var_dump($sorted_array);
  foreach($sorted_array as $key=>$value) {
    echo '平均年齢が高い都道府県：' . $key . '　男女比は' . $value['男'] . ':' . $value['女'];
    // print_r($value);
    $cnt++;
    if($cnt > 0) {
      break;
    }
  }



?>







</body>
</html>
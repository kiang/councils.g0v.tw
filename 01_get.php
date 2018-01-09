<?php
$path = __DIR__ . '/data';
if(!file_exists($path)) {
  mkdir($path, 0777, true);
}

$keywords = array(
  array('婦聯會'),
  array('救國團'),
  array('獅子會', '扶輪'),
  array('基金會'),
  array('家長', '媽媽'),
  array('婦女', '婦展', '婦宣'),
  array('警察', '義警', '警友'),
  array('消防', '義消', '火', '救災', '搜救'),
  array('軍人', '退伍', '後備'),
  array('宮', '廟', '媽祖'),
  array('體育', '運動', '球', '田徑'),
  array('農會', '農業', '農田', '水利'),
  array('商業', '董事長', '負責人', '公司'),
);

$fh = array();
for($i = 1; $i <= 191; $i++) {
  $f = $path . '/' . $i . '.json';
  if(!file_exists($f)) {
    file_put_contents($f, file_get_contents('https://councils.g0v.tw/api/councilors/?page=' . $i));
  }
  $json = json_decode(file_get_contents($f), true);
  foreach($json['results'] AS $c) {
    foreach($keywords AS $set) {
      $fileName = implode('_', $set);
      if(!isset($fh[$fileName])) {
        $fh[$fileName] = fopen(__DIR__ . '/result/' . $fileName . '.csv', 'w');
        fputcsv($fh[$fileName], array('縣市', '姓名', '政黨', '選舉年度', '相關經歷', '詳細資訊網址'));
      }
      foreach($set AS $keyword) {
        foreach($c['each_terms'] AS $term) {
          $lines = explode("\n", $term['experience']);
          $pool = array();
          foreach($lines AS $line) {
            if(false !== strpos($line, $keyword)) {
              $pool[] = $line;
            }
          }
          if(!empty($pool)) {
            fputcsv($fh[$fileName], array($term['county'], $term['name'], $term['party'], $term['election_year'], implode('|', $pool), "https://councils.g0v.tw/councilors/info/{$term['councilor']}/{$term['election_year']}/"));
          }
        }
      }
    }
  }
}

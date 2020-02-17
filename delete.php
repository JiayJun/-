<?php 
  if(empty($_GET['id'])){

  	 exit('请正确提交参数');
  }
  
  $id_get = $_GET['id'];

  $data = json_decode(file_get_contents('data.json'),true);

  // var_dump($data);

  foreach ($data as $item) {

  	if($item['id'] !== $_GET['id']) continue;

    $index = array_search($item,$data);
    array_splice($data,$index,1);
    $new_json = json_encode($data);
    file_put_contents('data.json',$new_json);

    header('location:list.php');
  }
 ?>
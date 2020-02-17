<?php 

   function add(){
      $data = array(); // 准备一个空的容器，用来保存最终的数据
      $data['id'] = uniqid();

   //1.接受文本文件
      if(empty($_POST['title'])){
        $GLOBALS['error_message'] = '没有输入音乐标题';
        return;
      }

      if(empty($_POST['artist'])){
        $GLOBALS['error_message'] = '没有输入歌手名字';
        return;
      }
      
      // 记下文本数据 
      $data['title'] = $_POST['title'];
      $data['artist'] = $_POST['artist'];
    
  //2.接受图片文件
      
      if(empty($_FILES['images'])){
        $GLOBALS['error_message'] = '请正确使用表单?';
        return;
      }
      $images = $_FILES['images'];
      $data['images'] = array(); //准备一个容器来装图片的路径
      
      // 遍历每一个文件（判断是否成功，判断类型，判断大小，移动数据到根目录网站）
      for($i=0 ; $i < count($images['name']) ; $i ++){
        if($images['error'][$i] !== UPLOAD_ERR_OK){
          $GLOBALS['error_message'] = '上传图片失败1';
          return;
        }

        // 类型的校验，是不是图片格式
        if(strpos($images['type'][$i],'image/') !== 0){
          $GLOBALS['error_message'] = '请图片格式错误';
          return;
        }
        
        //图片大小的检验
        if($images['size'][$i] > 1 * 1024 * 1024){
          $GLOBALS['error_message'] = '上传图片文件过大';
          return;
        }

        //移动图片文件
          //建立新的文件路径
          $dest1 = './uploads/' . uniqid() . $images['name'][$i];
        if(!move_uploaded_file($images['tmp_name'][$i],$dest1)){
          $GLOBALS['error_message'] = '上传图片失败2';
          return;
        }

        $data['images'][] = $dest1;
        

        // var_dump($data);
      }
  //3.接受音乐文件

      if(empty($_FILES['source'])){
        $GLOBALS['error_message'] = '请正确使用表单-musci';
        return;
      }
      
      $source = $_FILES['source'];
      
      //判断是否上传成功
      if($source['error'] !== UPLOAD_ERR_OK){
        $GLOBALS['error_message'] = "上传音乐出现错误";
        return;
      }
      
      //判断是否是允许的类型
      $source_allowed_type = array('audio/mp3','audio/wma');
      if(!in_array($source['type'],$source_allowed_type)){
        $GLOBALS['error_message'] = '上传音乐文件格式不正确';
        return;
      }

      //判断大小
      if($source['size'] > 10 * 1024 * 1024){
        $GLOBALS['error_message'] = '上传音乐文件过大';
        return;
      }
      if($source['size'] < 1 * 1024 * 1024){
        $GLOBALS['error_message'] = '上传音乐文件过小';
        return;
      }

      //移动
      $dest2 = './uploads/' . uniqid() . '-' . $source['name'];
      if(!move_uploaded_file($source['tmp_name'],$dest2)){
        $GLOBALS['error_message'] = '上传音乐文件失败2';
        return;
      }
      $data['source'] = $dest2;

  //4.将数据存到JSON原始数据中
     
     $json = file_get_contents('data.json');
     $old_string_json = json_decode($json,true);
     array_push($old_string_json,$data);
     $new_string_json = json_encode($old_string_json);
     file_put_contents('data.json',$new_string_json);

  //5.跳转
     header('Location:list.php');

  }   

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        add();  
        
    }
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>添加新音乐</title>
  <link rel="stylesheet" href="bootstrap.css">
</head>
<body>
  <div class="container py-5">
    <h1 class="display-4">添加新音乐</h1>
    <hr>
    <?php if (isset($error_message)): ?>
    <div class="alert alert-danger">
      <?php echo $error_message; ?>
    </div>
    <?php endif ?>
    <form action = "<?php echo $_SERVER['PHP_SELF']; ?>" method = 'post' enctype='multipart/form-data'>
      <div class="form-group">
        <label for="title">标题</label>
        <input type="text" class="form-control" id="title" name="title">
      </div>
      <div class="form-group">
        <label for="artist">歌手</label>
        <input type="text" class="form-control" id="artist" name="artist">
      </div>
      <div class="form-group">
        <label for="images">海报</label>
        <!-- multiple 可以让一个文件域多选 -->
        <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
      </div>
      <div class="form-group">
        <label for="source">音乐</label>
        <input type="file" class="form-control" id="source" name="source">
      </div>
      <button class="btn btn-primary btn-block">保存</button>
    </form>
  </div>
</body>
</html>
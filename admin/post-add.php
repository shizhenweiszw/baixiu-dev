<?php 
  require '../functions.php';
  $user_id=(int)baixiu_get_current_user()['id'];
  function add_article(){
    global $user_id;
    if (empty($_POST['title'])) {   
      $GLOBALS['message']='请输入标题';
      return;
    }
    if (empty($_POST['content'])) {
      $GLOBALS['message']='请输入内容';
      return;
    }
    if (empty($_POST['slug'])) {
      $GLOBALS['message']='请输入别名';
      return;
    }
    if (empty($_POST['category'])) {
      $GLOBALS['message']='请选择分类';
      return;
    }
    if (empty($_POST['status'])) {
      $GLOBALS['message']='请选择状态';
      return;
    }
    //特色图片可选
    if ($_FILES['feature']['size']==0) {
      $mysql_target='null';
    }else{
      $feature=$_FILES['feature'];
      //校验文件格式
      $allow_img=['image/png','image/jpg','image/jpeg'];
      if (!in_array($feature['type'], $allow_img)) {
        $GLOBALS['message']='图片格式不对，支持.png、.jpg、.jpeg';
        return;
      }
      //校验文件大小
      if ($feature['size']>10*1024*1024) {
        $GLOBALS['message']='文件不能超过10M';
        return;
      }
      if ($feature['error']!=UPLOAD_ERR_OK) {
        $GLOBALS['message']='上传图片失败';
        return;
      }
      //获取文件后缀名
      $ext=pathinfo($feature['name'],PATHINFO_EXTENSION);
      $target='../static/uploads/avatar_' . uniqid() . '.' . $ext;
      if (!move_uploaded_file($feature['tmp_name'], $target)) {
        $GLOBALS['message']='上传头像失败';
        return;
      }
      $mysql_target=substr($target, 2);
    }
    $title=$_POST['title'];
    $slug=$_POST['slug'];
    $category=(int)$_POST['category'];
    $content=$_POST['content'];
    date_default_timezone_set("Asia/Shanghai");
    $created=date("Y-m-d H:i:s");
    $status=$_POST['status'];
    $row=xiu_execute("INSERT INTO posts VALUES(null,'{$slug}','{$title}','{$mysql_target}','{$created}','{$content}',0,0,'{$status}',{$user_id},{$category});");
    header('Location:posts.php');
  }

  if ($_SERVER['REQUEST_METHOD']==='POST') {
    var_dump($_FILES['feature']['size']==0);
    add_article();

  }
  //获取分类
  $categories=xiu_fetch("SELECT id,name FROM categories;");
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navBar.php' ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>写文章</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (!empty($message)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $message ?>
        </div>
      <?php endif ?>
      
      <form class="row" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" value="<?php echo empty($_POST['title'])?'':$_POST['title']; ?>" class="form-control input-lg" name="title" type="text" placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="content">内容</label>
            <!-- <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" placeholder="内容"></textarea> -->
                  <!-- 加载编辑器的容器 -->
            <script id="content"  name="content" type="text/plain">
             <?php echo empty($_POST['content'])?'':$_POST['content'] ?>
            </script>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" value="<?php echo empty($_POST['slug'])?'':$_POST['slug']; ?>" name="slug" type="text" placeholder="slug">
            <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" style="display: none">
            <input id="feature"  name="feature" type="file">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach ($categories as $item): ?>
                <option value="<?php echo $item['id'] ?>" <?php echo isset($_POST['category'])&&$_POST['category']==$item['id']?'selected':''; ?>><?php echo $item['name'] ?></option>
              <?php endforeach ?>
              
            </select>
          </div>
          <!-- <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" v name="created" type="datetime-local" >
          </div> -->
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted" <?php echo isset($_POST['status'])&&$_POST['status']=='drafted'?'selected':''; ?>>草稿</option>
              <option value="published" <?php echo isset($_POST['status'])&&$_POST['status']=='published'?'selected':''; ?>>已发布</option>
              <option value="trashed" <?php echo isset($_POST['status'])&&$_POST['status']=='trashed'?'selected':''; ?>>回收站</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php $current_page='post_add'; ?>
  <?php include 'inc/sideBar.php'; ?>

    <!-- 配置文件 -->
    <script type="text/javascript" src="/static/assets/vendors/ueditor/utf8-php/ueditor.config.js"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="/static/assets/vendors/ueditor/utf8-php/ueditor.all.js"></script>
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        var ue = UE.getEditor('content',{
          initialFrameHeight:320
        });
    </script>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>

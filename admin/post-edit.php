<?php 
  require '../functions.php';
  $user_id=(int)baixiu_get_current_user()['id'];
  $article_id=(int)$_GET['id'];
  //查询要修改的文章信息
  $article=xiu_fetch_one("SELECT title,content,slug,category_id,status,feature FROM posts WHERE id={$article_id}");
  function edit_article(){
    global $user_id;
    global $article;
    global $article_id;
    if (empty($_POST['title'])) {   
      $title=$article['title'];
    }
    if (empty($_POST['content'])) {
      $content=$article['content'];
    }
    if (empty($_POST['slug'])) {
      $slug=$article['slug'];
    }
    if (empty($_POST['category'])) {
      $category=$article['category_id'];
    }
    if (empty($_POST['status'])) {
      $status=$article['status'];
    }
    //特色图片可选
    if ($_FILES['feature']['size']==0) {
      $mysql_target=$article['feature'];
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
    $row=xiu_execute("UPDATE posts SET slug='{$slug}',title='{$title}',feature='{$mysql_target}',created='{$created}',content='{$content}',status='{$status}',category_id={$category} WHERE id={$article_id};");
    header('Location:posts.php');
  }

  if ($_SERVER['REQUEST_METHOD']==='POST') {

    edit_article();

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
        <h1>修改文章</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (!empty($message)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $message ?>
        </div>
      <?php endif ?>
      
      <form class="row" action="<?php echo $_SERVER['PHP_SELF'] ?>?id=<?php echo $article_id ?>" method="post" enctype="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" value="<?php echo $article['title'] ?>" class="form-control input-lg" name="title" type="text" placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="content">内容</label>
            <!-- <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" placeholder="内容"></textarea> -->
                  <!-- 加载编辑器的容器 -->
            <script id="content"  name="content" type="text/plain">
             <?php echo $article['content'] ?>
            </script>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" value="<?php echo $article['slug'] ?>" name="slug" type="text" placeholder="slug">
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
                <option value="<?php echo $item['id'] ?>" <?php echo $article['category_id']==$item['id']?'selected':''; ?>><?php echo $item['name'] ?></option>
              <?php endforeach ?>            
            </select>
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted" <?php echo $article['status']=='drafted'?'selected':''; ?>>草稿</option>
              <option value="published" <?php echo $article['status']=='published'?'selected':''; ?>>已发布</option>
              <option value="trashed" <?php echo $article['status']=='trashed'?'selected':''; ?>>回收站</option>
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

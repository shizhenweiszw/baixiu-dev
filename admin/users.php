<?php 
  require_once '../functions.php';
  baixiu_get_current_user();
  function add_user(){
    var_dump($_FILES['avatar']);
    if (empty($_FILES['avatar'])) {
      $GLOBALS['message']='请上传头像';
      return;
    }
    $avatar=$_FILES['avatar'];
    //校验文件格式
    $allow_img=['image/png','image/jpg','image/jpeg'];
    if (!in_array($avatar['type'], $allow_img)) {
      $GLOBALS['message']='图片格式不对，支持.png、.jpg、.jpeg';
      return;
    }
    //校验文件大小
    if ($avatar['size']>10*1024*1024) {
      $GLOBALS['message']='文件不能超过10M';
      return;
    }
    if ($avatar['error']!=UPLOAD_ERR_OK) {
      $GLOBALS['message']='上传图片失败';
      return;
    }
    //获取文件后缀名
    $ext=pathinfo($avatar['name'],PATHINFO_EXTENSION);
    $target='../static/uploads/avatar_' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($avatar['tmp_name'], $target)) {
      $GLOBALS['message']='上传头像失败';
      return;
    }
    $mysql_target=substr($target, 2);
    if (empty($_POST['email'])) {
      $GLOBALS['message']='请添加邮箱';
      return;
    }
    if (empty($_POST['slug'])) {
      $GLOBALS['message']='请添加别名';
      return;
    }
    if (empty($_POST['nickname'])) {
      $GLOBALS['message']='请添加昵称';
      return;
    }
    if (empty($_POST['password'])) {
      $GLOBALS['message']='请添加密码';
      return;
    }
    $email=$_POST['email'];
    $slug=$_POST['slug'];
    $nickname=$_POST['nickname'];
    $password=$_POST['password'];
    xiu_execute("insert into users values(null,'{$slug}','{$email}','{$password}','{$nickname}','{$mysql_target}',null,'activated')");
    header('Location: users.php');
  }
  function edit_person(){
    global $edit_user;
    var_dump($_FILES['avatar']['size']==0);
    if ($_FILES['avatar']['size']==0) {
      $mysql_target=$edit_user['avatar'];
    }else{
      $avatar=$_FILES['avatar'];
      //校验文件格式
      $allow_img=['image/png','image/jpg','image/jpeg'];
      if (!in_array($avatar['type'], $allow_img)) {
        $GLOBALS['message']='图片格式不对，支持.png、.jpg、.jpeg';
        return;
      }
      //校验文件大小
      if ($avatar['size']>10*1024*1024) {
        $GLOBALS['message']='文件不能超过10M';
        return;
      }
      if ($avatar['error']!=UPLOAD_ERR_OK) {
        $GLOBALS['message']='上传图片失败';
        return;
      }
      //获取文件后缀名
      $ext=pathinfo($avatar['name'],PATHINFO_EXTENSION);
      $target='../static/uploads/avatar_' . uniqid() . '.' . $ext;
      if (!move_uploaded_file($avatar['tmp_name'], $target)) {
        $GLOBALS['message']='上传头像失败';
        return;
      }
      $mysql_target=substr($target, 2);
    }
    $id=$edit_user['id'];
    $email=empty($_POST['email'])?$edit_user['email']:$_POST['email'];
    $edit_user['email']=$email;
    $slug=empty($_POST['slug'])?$edit_user['slug']:$_POST['slug'];
    $edit_user['slug']=$slug;
    $nickname=empty($_POST['email'])?$edit_user['nickname']:$_POST['nickname'];
    $edit_user['nickname']=$nickname;
    $edit_user['nickname']=$nickname;
    $password=empty($_POST['password'])?$edit_user['password']:$_POST['password'];
    $edit_user['password']=$password;
    xiu_execute("UPDATE users SET slug='{$slug}',email='{$email}',`password`='{$password}',nickname='{$nickname}',avatar='{$mysql_target}' WHERE id={$id};");
    header('Location: users.php');
  }
  //判断是添加还是编辑
  if (empty($_GET['id'])) {
    if ($_SERVER['REQUEST_METHOD']==='POST') {
    add_user();
    }
  }else{
    $id=$_GET['id'];
    $edit_user=xiu_fetch_one("SELECT id,slug,email,`password`,nickname,avatar FROM users WHERE id={$id};");
     if ($_SERVER['REQUEST_METHOD']==='POST') {
        edit_person();
    } 
  }
  
  $users=xiu_fetch("SELECT id,slug,email,`password`,nickname,avatar,status FROM users;");
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
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
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (!empty($message)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $message ?>
        </div>
      <?php endif ?>

      <div class="row">
        <div class="col-md-4">
          <?php if (empty($_GET['id'])): ?>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method='post' enctype="multipart/form-data" autocompelete="off">
              <h2>添加新用户</h2>
              <div id="uploadPreview"></div>
              <img class="avatar" id="avatar_show" width="120" src="/static/assets/img/default.png" /> 
              <div class="form-group">
                <label for="avatar">头像</label>
                <input id="avatar"  name="avatar" type="file" />
              </div>
              <div class="form-group">
                <label for="email">邮箱</label>
                <input id="email" value="<?php echo isset($_POST['email'])?$_POST['email']:''; ?>" class="form-control" name="email" type="email" placeholder="邮箱">
              </div>
              <div class="form-group">
                <label for="slug">别名</label>
                <input id="slug" value="<?php echo isset($_POST['email'])?$_POST['slug']:''; ?>" class="form-control" name="slug" type="text" placeholder="slug">
                <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
              </div>
              <div class="form-group">
                <label for="nickname">昵称</label>
                <input id="nickname" value="<?php echo isset($_POST['nickname'])?$_POST['nickname']:''; ?>" class="form-control" name="nickname" type="text" placeholder="昵称">
              </div>
              <div class="form-group">
                <label for="password">密码</label>
                <input id="password" class="form-control" name="password" type="text" placeholder="密码">
              </div>
              <div class="form-group">
                <button class="btn btn-primary" type="submit">添加</button>
              </div>
            </form>
            <?php else: ?>
              <form action="<?php echo $_SERVER['PHP_SELF'] ?>?id=<?php echo $_GET['id'] ?>" method='post' enctype="multipart/form-data" autocompelete="off">
                <h2>修改<?php echo $edit_user['nickname']; ?></h2>
                <div id="uploadPreview"></div>
                <img class="avatar" id="avatar_show" width="120" src="<?php echo $edit_user['avatar'] ?>"/> 
                <div class="form-group">
                  <label for="avatar">头像</label>
                  <input id="avatar"  name="avatar" type="file" />
                </div>
                <div class="form-group">
                  <label for="email">邮箱</label>
                  <input id="email" class="form-control" value="<?php echo $edit_user['email'] ?>" name="email" type="email" placeholder="邮箱">
                </div>
                <div class="form-group">
                  <label for="slug">别名</label>
                  <input id="slug" class="form-control" value="<?php echo $edit_user['slug'] ?>" name="slug" type="text" placeholder="slug">
                  <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
                </div>
                <div class="form-group">
                  <label for="nickname">昵称</label>
                  <input id="nickname" class="form-control" value="<?php echo $edit_user['nickname'] ?>" name="nickname" type="text" placeholder="昵称">
                </div>
                <div class="form-group">
                  <label for="password">密码</label>
                  <input id="password" class="form-control" name="password" type="text" placeholder="密码">
                </div>
                <div class="form-group">
                  <button class="btn btn-primary" type="submit">保存</button>
                </div>
              </form>
          <?php endif ?>

        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/users_delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $item): ?>
                <tr>
                  <td class="text-center"><input data-id="<?php echo $item['id'] ?>" type="checkbox"></td>
                  <td class="text-center"><img class="avatar" src="<?php echo $item['avatar'] ?>"></td>
                  <td><?php echo $item['email'] ?></td>
                  <td><?php echo $item['slug'] ?></td>
                  <td><?php echo $item['nickname'] ?></td>
                  <td><?php echo $item['status']=='activated'?'激活':'未激活'; ?></td>
                  <td class="text-center">
                    <a href="/admin/users.php?id=<?php echo $item['id'] ?>" class="btn btn-default btn-xs">编辑</a>
                    <a href="/admin/users_delete.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                  </td>
                </tr>
              <?php endforeach ?>        
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
   <?php $current_page='users'; ?>
  <?php include 'inc/sideBar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script type="text/javascript">
    //图片预览
    $('#avatar').change(function(){
    // 获取FileList的第一个元素
      //alert(document.getElementById('avatar').files[0]);
      var f = document.getElementById('avatar').files[0];
      src = window.URL.createObjectURL(f);
      document.getElementById('avatar_show').src = src
    })
    //批量删除
    var checkbox=$('tbody input');
    var btn_delete=$('#btn_delete');
    var arr_checkbox=[];
    checkbox.on('change',function(){
      
      var id=$(this).data('id');
      if ($(this).prop('checked')) {
        if (arr_checkbox.indexOf(id)===-1) {
          arr_checkbox.push(id);
        }    
      }else{
        arr_checkbox.splice(arr_checkbox.indexOf(id),1);
      }
      arr_checkbox.length>0?btn_delete.fadeIn():btn_delete.fadeOut();
      btn_delete.prop('search','?id='+arr_checkbox);
      console.log(arr_checkbox);
    });
    //全选全不选
    $('thead input').on('change',function(){
      var flag=$(this).prop('checked');
      checkbox.prop('checked',flag).trigger('change');
    });
  </script>
  <script>NProgress.done()</script>
</body>
</html>

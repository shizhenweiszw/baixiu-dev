<?php 
  require_once '../config.php';
  session_start();
  function login(){
    if (empty($_POST['email'])) {
      $GLOBALS['message']='请输入邮箱';
      return;
    }
    if (empty($_POST['password'])) {
      $GLOBALS['message']='请输入密码';
      return;
    }
    $email=$_POST['email'];
    $password=$_POST['password'];
    $conn=mysqli_connect(DB_HOST,DB_USER,DB_USER,DB_NAME);
    $query=mysqli_query($conn,"SELECT * FROM users WHERE email='$email' LIMIT 1;");
    $user=mysqli_fetch_assoc($query);
    if (!$query) {
      $GLOBALS['message']='登陆失败';
      return;
    }
    if (!$user) {
      $GLOBALS['message']='邮箱与密码不匹配';
      return;
    }
    if ($password!=$user['password']) {
      $GLOBALS['message']='邮箱与密码不匹配';
      return;
    }
    header('Location:../admin/');
    $_SESSION['current_login_user']=$user;
    
  };
  if ($_SERVER['REQUEST_METHOD']==='POST') {
    login();
  }

  if ($_SERVER['REQUEST_METHOD']==='GET'&&isset($_GET['action'])&&$_GET['action']==='logout') {

    unset($_SESSION['current_login_user']);

  }
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
</head>
<body>
  <div class="login">
    <!-- novalidate取消加偶按功能 autocompelete="off"关闭客户端自动完成功能 -->
    <form class="login-wrap <?php echo isset($message)?'shake animated':''; ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate autocomplete="off">
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <div class="alert alert-danger">
          <strong><?php echo $message ?></strong> 
        </div>
      <?php endif ?>
     
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input name="email" id="email" type="email" class="form-control" placeholder="邮箱" autofocus>
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input name="password" id="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block" href="index.html">登 录</button>
    </form>
  </div>
  <script type="text/javascript" src="/static/assets/vendors/jquery/jquery.js"></script>
  <script type="text/javascript">
    $(function($){
      var email_format=/^[a-zA-Z0-9]+@[a-zA-Z0-9]+(\.[a-zA-Z]+){1,2}$/;
      $('#email').on('blur',function(){
        var value=$('#email').val();
        if (!value||!email_format.test(value)) {return;}
        console.log(value);
        $.ajax({
          type: 'get',
          url: '/api/avatar.php',
          data: {email:value},
          success: function(image){
            if (!image) {return;}
            $('.avatar').fadeOut(function(){
              $(this).on('load',function(){
                $(this).fadeIn()
              }).attr('src',image);
            });
          }
        });
        // $.get('/api/avatar.php',{email:value},function(res){
        //   console.log(res);
        // });
      });
      
      
    });
  </script>
</body>
</html>

<?php 
  require_once '../functions.php';
  baixiu_get_current_user();
  $categories=xiu_fetch("SELECT * FROM categories;");
  function categories_add(){
    if (empty($_POST['name'])||empty($_POST['slug'])) {
      $GLOBALS['message']='请将表单填写完整';
      return;
    }
    $name=$_POST['name'];
    $slug=$_POST['slug'];
    $row=xiu_execute("INSERT INTO categories VALUES(null,'{$slug}','{$name}');");
    header('Location: categories.php');
  }
  function categories_edit(){
    global $categories_edit_item;
    $id = $categories_edit_item['id'];
    $name = empty($_POST['name']) ? $categories_edit_item['name'] : $_POST['name'];
    //同步数据
    $categories_edit_item['name'] = $name;
    $slug = empty($_POST['slug']) ? $categories_edit_item['slug'] : $_POST['slug'];
    $categories_edit_item['slug'] = $slug;
    $row=xiu_execute("UPDATE categories SET `name`='{$name}',slug='{$slug}' WHERE id={$id};");
    header('Location: categories.php');
  }
  //判断是添加还是编辑
  var_dump(empty($_GET['id']));
  if (empty($_GET['id'])) {
    if ($_SERVER['REQUEST_METHOD']==='POST') {   
      categories_add(); 
    }
  }else{
    $categories_edit_item=xiu_fetch_one('select * from categories where id=' . $_GET['id']);
    if ($_SERVER['REQUEST_METHOD']==='POST') {
      categories_edit();
    }
  }
  
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $message; ?>
        </div>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <?php if (isset($categories_edit_item)): ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $categories_edit_item['id']; ?>" method="post">
              <h2>编辑</h2>
              <div class="form-group">
                <label for="name">名称</label>
                <input id="name" value="<?php echo $categories_edit_item['name']; ?>" class="form-control" name="name" type="text" placeholder="分类名称">
              </div>
              <div class="form-group">
                <label for="slug">别名</label>
                <input id="slug" value="<?php echo $categories_edit_item['slug']; ?>" class="form-control" name="slug" type="text" placeholder="slug">
                <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary" >保存</button>
              </div>
            </form>
            <?php else: ?>
              <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <h2>添加新分类目录</h2>
                <div class="form-group">
                  <label for="name">名称</label>
                  <input value="<?php echo isset($_POST['name'])?$_POST['name']:''; ?>" id="name" class="form-control" name="name" type="text" placeholder="分类名称">
                </div>
                <div class="form-group">
                  <label for="slug">别名</label>
                  <input value="<?php echo isset($_POST['slug'])?$_POST['slug']:''; ?>" id="slug" class="form-control" name="slug" type="text" placeholder="slug">
                  <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
                </div>
                <div class="form-group">
                  <button type="submit" class="btn btn-primary" >添加</button>
                </div>
              </form>
          <?php endif ?>

        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $item): ?>
                <tr>
                <td class="text-center"><input data-id="<?php echo $item['id']; ?>" type="checkbox"></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['slug']; ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php echo $item['id'] ?>" class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/delete.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
                </tr>
              <?php endforeach ?>
              

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php $current_page='categories'; ?>
  <?php include 'inc/sideBar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script type="text/javascript">
    //批量删除
    var tbody_checkbox=$('tbody input');
    var btn_delete=$('#btn_delete');
    var allCheckeds=[];
    tbody_checkbox.on('change',function(){
      var id=$(this).data('id');
      if ($(this).prop('checked')) {
        if (allCheckeds.indexOf(id)===-1) {
          allCheckeds.push(id);
        }
         
       // allCheckeds.includes(id) || allCheckeds.push(id);
      }else{
        allCheckeds.splice(allCheckeds.indexOf(id),1);
      }
      console.log(allCheckeds);
      allCheckeds.length>0?btn_delete.fadeIn():btn_delete.fadeOut();
      btn_delete.prop('search','?id='+allCheckeds);
    });
    //全选全不选
    $('thead input').on('change',function(){
      var flag=$(this).prop('checked');
      $('tbody input').prop('checked',flag).trigger('change');
    });
  </script>
</body>
</html>

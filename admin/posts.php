<?php 
   require_once '../functions.php';
  baixiu_get_current_user();
  $page=empty($_GET['page'])?1:(int)$_GET['page'];
  //每页显示条数
  $items=20;
  //需要越过多少条
  $offset=($page-1)*$items;
  //页码显示5条
  $total=5;
  //如果当前页码排第三位
  $start_page=$page-2;
  $end_page=$page+2;
  if ($start_page<1) {
    $start_page=1;
    $end_page=5;
  }
  //分类筛选
  $where='1=1';
  $search='';
  if (isset($_GET['category'])&&$_GET['category']!='all') {
    $where .= ' and categories.id=' . $_GET['category'];
    $search .= '&category=' . $_GET['category'];
  }
  if (isset($_GET['status'])&&$_GET['status']!='all') {
    $where .= " and posts.status='{$_GET['status']}'";
    $search .= '&status=' . $_GET['status'];
  }
  //获取总条数
  $total_items=(int)xiu_fetch_one("
    SELECT count(1) as num
    FROM posts 
    INNER JOIN users
    on user_id=users.id
    INNER JOIN categories
    on category_id=categories.id
    WHERE {$where};
    ")['num'];
  //总页数
  $total_pages=ceil($total_items/$items);
  if ($end_page>$total_pages) {
    $end_page=$total_pages;
    $start_page=$total_pages-4;
  }
  var_dump($total_pages);
  $posts=xiu_fetch("
    SELECT posts.id,title,
    users.nickname as user_name,
    categories.name as categories_name,
    created,posts.status
    FROM posts 
    INNER JOIN users
    on user_id=users.id
    INNER JOIN categories
    on category_id=categories.id
    WHERE {$where}
    ORDER BY posts.created desc
    LIMIT {$offset},{$items};
    ");
  //将状态转化为文字
  function convert_status($status){
    $dict=array(
      'published'=>'已发布',
      'drafted'=>'草稿',
      'trashed'=>'回收站'
    );
    return isset($dict[$status])?$dict[$status]:'未知';
  }
  function convert_date($create){
    $timestamp=strtotime($create);
    return date('Y年m月d日<b\r> H:i:s',$timestamp);
  }
  //获取所有分类
  $categories=xiu_fetch("select id,name from categories;");
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'] ?> ">
          <select name="category" class="form-control input-sm">
            <option value="all">所有状态</option>
            <?php foreach ($categories as $item): ?>
              <option value="<?php echo $item['id']; ?>"<?php echo isset($_GET['category'])&&$_GET['category']==$item['id']?'selected':''; ?>><?php echo $item['name']; ?></option>
            <?php endforeach ?>
            
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all" >所有状态</option>
            <option value="drafted" <?php echo isset($_GET['status'])&&$_GET['status']=='drafted'?'selected':''; ?>>草稿</option>
            <option value="published" <?php echo isset($_GET['status'])&&$_GET['status']=='published'?'selected':''; ?>>已发布</option>
            <option value="trashed" <?php echo isset($_GET['status'])&&$_GET['status']=='trashed'?'selected':''; ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="?page=1<?php echo $search ?>">首页</a></li>
          <li><a href="?page=<?php echo ($page-1).$search; ?>">上一页</a></li>
          <?php for ($i=$start_page; $i <=$end_page; $i++): ?>
            <li class="<?php echo $page==$i?"active":''; ?>"><a href="?page=<?php echo $i.$search ?>"><?php echo $i ?></a></li>
          <?php endfor ?>
          <li><a href="?page=<?php echo ($page+1).$search; ?>">下一页</a></li>
          <li><a href="?page=<?php echo $total_pages.$search; ?>">尾页</a></li>
        </ul>
      </div>
      <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/delete_posts.php" style="display: none">批量删除</a>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <!-- <th class="text-center" width="40"><input type="checkbox"></th> -->
            <th></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
            <tr>
              <td class="text-center"><input data-id="<?php echo $item['id'] ?>" type="checkbox"></td>
              <td><?php echo $item['title'] ?></td>
              <td><?php echo $item['user_name'] ?></td>
              <td><?php echo $item['categories_name'] ?></td>
              <td class="text-center"><?php echo convert_date($item['created']); ?></td>
              <td class="text-center"><?php echo convert_status($item['status']); ?></td>
              <td class="text-center">
                <a href="/admin/post-edit.php?id=<?php echo $item['id'] ?>" class="btn btn-default btn-xs">编辑</a>
                <a href="/admin/delete_posts.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
              </td>
            </tr>
          <?php endforeach ?>
          
        </tbody>
      </table>
    </div>
  </div>
  <?php $current_page='posts' ?>
 <?php include 'inc/sideBar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script type="text/javascript">
    $(function(){
      var btn_delete=$('#btn_delete');
      var checkbox_body=$('tbody input');
      var arr_checkbox=[];
      checkbox_body.on('change',function(){
        var id=$(this).data('id');
        if ($(this).prop('checked')&&arr_checkbox.indexOf(id)==-1) {
          arr_checkbox.push(id);
        }else{
          arr_checkbox.splice(arr_checkbox.indexOf(id),1);
        }
        console.log(arr_checkbox);
        arr_checkbox.length>0?btn_delete.fadeIn():btn_delete.fadeOut();
        btn_delete.prop('search','?id='+arr_checkbox);
      });
    });
  </script>
  <script>NProgress.done()</script>
</body>
</html>

<?php 
  require_once '../functions.php';
  baixiu_get_current_user();
  $posts_count=xiu_fetch("select count(1) as num from posts;");
  //var_dump('select count(1) as num from posts;');
  
  $drafted_count=xiu_fetch("select count(1) as num from posts where status='drafted';");
  $category_count=xiu_fetch("select count(1) as num from categories;");
  $comment_count=xiu_fetch("select count(1) as num from comments;");
  $comments_held=xiu_fetch("select count(1) as num from comments where status='held';");
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
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
      <div class="jumbotron text-center">
        <h1>One Belt, One Road</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.html" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $posts_count[0]['num']; ?></strong>篇文章（<strong><?php echo $drafted_count[0]['num']; ?></strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo $category_count[0]['num'] ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $comment_count[0]['num'] ?></strong>条评论（<strong><?php echo $comments_held[0]['num']; ?></strong>条待审核）</li>
            </ul>

          </div>
        </div>
        <div class="col-md-4"> <canvas id="myChart" width="387" height="193"></canvas></div>
        <div class="col-md-4"></div>
      </div>
    </div>

  </div>
  <?php echo $current_page='index'; ?>
  <?php include 'inc/sideBar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/chart/chart.js"></script>
  <script type="text/javascript">
    var ctx = document.getElementById("myChart").getContext('2d');
    data = {
      datasets: [{
          data: [<?php echo $posts_count[0]['num']; ?>, <?php echo $category_count[0]['num'] ?>, <?php echo $comment_count[0]['num'] ?>],
          backgroundColor: [
            'red',
            'orange',
            'yellow'
          ]
      }],

      // These labels appear in the legend and in the tooltips when hovering different arcs
      labels: [
          '文章',
          '分类',
          '评论'
      ]
    };
     var options={};
    var myPieChart = new Chart(ctx,{
    type: 'pie',
    data: data,
    options: options
    });

  </script>
  <script>NProgress.done()</script>
</body>
</html>

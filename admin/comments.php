F<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="#">上一页</a></li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody>  
        </tbody>
      </table>
    </div>
  </div>
 <?php echo $current_page='comments'; ?>
 <?php include 'inc/sideBar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js" ></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.min.js"></script>
  <script id="comments-tmpl" type="text/x-jsrender">
    {{for comments}}
      <tr {{if status=='held'}} class="warning" {{else status=='rejected'}} class="danger" {{/if}}
      data-id="{{:id}}">
            <td class="text-center"><input type="checkbox"></td>
            <td>{{:author}}</td>
            <td>{{:content}}</td>
            <td>《{{:post_title}}》</td>
            <td>{{:created}}</td>
            <td>{{:status}}</td>
            <td class="text-center">
              {{if status=='held'}}
              <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
              <a href="post-add.html" class="btn btn-warning btn-xs">拒绝</a>
              {{/if}}
              <a href="javascript:;" class="btn btn-delete btn-danger btn-xs">删除</a>
            </td>
      </tr>
    {{/for}}
  </script>
  <script type="text/javascript">
    var curr_page=1;
    function loadPageData(page){
      $.getJSON("/api/comments.php",{page:page},function(data){
        if (page>data.total_pages) {
          loadPageData(data.total_pages);
          return;
        }
        $('.pagination').twbsPagination('destroy');
        $('.pagination').twbsPagination({
          first: '首页',
          last:'尾页',
          prev:'上一页',
          next:'下一页',
          startPage:page,
          totalPages: data.total_pages,
          visiablePages:3,
          initiateStartPageClick: false,
          onPageClick: function(e,page){
          loadPageData(page);
            //console.log(page)
          }
        });
      var html=$('#comments-tmpl').render({comments:data.comments});
      $('tbody').html(html);
      curr_page=page;
    });
    }
    loadPageData(curr_page);
    $('tbody').on('click','.btn-delete',function(){
      var id=$(this).parent().parent().data('id');
      $.get("/api/comments-delete.php",{id:id},function(res){
        if (!res) {return;}
        loadPageData(curr_page);
      });
    });
  </script>
  <script>NProgress.done()</script>
</body>
</html>

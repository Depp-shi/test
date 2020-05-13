<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>CRM</title>
    <script type="text/javascript">
        window.xtoken = '<?php echo $token;?>';
    </script>
    <link href="https://cdn.bootcss.com/normalize/7.0.0/normalize.min.css" rel="stylesheet">
    <link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="http://asset.dmcdn.com/public/mui/3.7.2/css/mui.min.css">
    <script src="http://cdn.bootcss.com/vue/2.5.9/vue.min.js"></script>
    <script src="http://cdn.bootcss.com/axios/0.17.1/axios.min.js"></script>
    <script src="http://cdn.bootcss.com/vue-router/3.0.1/vue-router.min.js"></script>
    <script src="https://cdn.bootcss.com/vuex/3.0.1/vuex.min.js"></script>
    <script src="https://cdn.bootcss.com/vue-i18n/5.0.3/vue-i18n.min.js"></script>

    <link href="/static/css/animate.css" rel="stylesheet">
    <!-- Menu CSS -->
    <link href="/static/sidebar-nav/sidebar-nav.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/static/css/style.css" rel="stylesheet">
    <!-- color CSS -->
    <link href="/static/css/colors/megna-dark.css" id="theme" rel="stylesheet">
    <script src="https://cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
    <script src="http://asset.dmcdn.com/public/jquery-form/jquery.form.js"></script>

    <script id="ueditorConfig" src="/assets/js/ueditor/1.4.3/ueditor.config.js" data='{"serverUrl":"/manage/api/sys/ueditor.html"}'></script>
    <script src="/assets/js/ueditor/1.4.3/ueditor.all.js"></script>
    <script src="http://asset.dmcdn.com/public/ueditor/1.4.3/lang/zh-cn/zh-cn.js"></script>
    <script src="http://asset.dmcdn.com/public/ueditor/1.4.3/ueditor.parse.min.js"></script>

    <link href="http://asset.dmcdn.com/app/hzdm-crm/<?php echo $version?>/static/css/app.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/element-ui@2.3.7/lib/theme-chalk/index.css">
    <script src="https://unpkg.com/element-ui@2.3.7/lib/index.js"></script>
</head>

<body>
    <div id="app"></div>
</body>

</html>
<script type="text/javascript" src="http://asset.dmcdn.com/app/hzdm-crm/<?php echo $version?>/static/js/manifest.js"></script>
<script type="text/javascript" src="http://asset.dmcdn.com/app/hzdm-crm/<?php echo $version?>/static/js/vendor.js"></script>
<script type="text/javascript" src="http://asset.dmcdn.com/app/hzdm-crm/<?php echo $version?>/static/js/app.js"></script>
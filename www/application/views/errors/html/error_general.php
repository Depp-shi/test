<style type="text/css">
    
#container{
    min-height: 200px;
    margin-top: 50px;
    font-size: 14px;
}
h1{
    margin-bottom: 30px;
    padding-bottom: 10px;
    border-bottom: 1px solid #E5E5E5;
}

</style>
<div class="container"  id="container">
	<h1>系统错误</h1>
	<div class="message">
        <?php echo $message; ?>
    </div>
    <div class="back mgtop20">
        <span id="count">5</span>秒后系统自动返回首页，<a href="/">若未自动跳转，可点击返回<<</a>
    </div>
</div>

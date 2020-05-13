<?php $this->load->view('email/_header') ?>
<div>
<p>您好：</p>
<p>您已成功注册<?php echo $title?>，以下是您的登录信息：</p>
<p>邮箱：<?php echo $email?></p>
<p>&nbsp;</p>
</div>
<?php $this->load->view('email/_footer') ?>
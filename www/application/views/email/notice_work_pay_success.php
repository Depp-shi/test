<?php $this->load->view('email/_header') ?>
<div>
    <p>您好：</p>
    <p>您已经成功缴费！初评入围名单将在全国设计“大师奖”官网（www.dashiaward.com）公布，请持续关注“大师奖”。</p>
    <p></p>
    <p>您的作品信息如下：</p>
    <p>作品ID：<?php echo $work['work_no']?></p>
    <p>作品名称：<?php echo $work['title']?></p>
</div>
<?php $this->load->view('email/_footer') ?>
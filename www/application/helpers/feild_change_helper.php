<?php

function userDetailChange($userInfo) {
  $tmp = array();
  $tmp['id'] = $userInfo['id'];
  $tmp['firstName'] = $userInfo['first_name'];
  $tmp['lastName'] = $userInfo['last_name'];
  $tmp['birthday'] = $userInfo['birth'] ? date("Y-m-d",strtotime($userInfo['birth'])) : '';
  $tmp['createdTime'] = date("Y-m-d h:i:s",$userInfo['created_time']);
  $tmp['sex'] = $userInfo['gender'];
  $tmp['type'] = $userInfo['type'];
  $tmp['status'] = $userInfo['status'];
  $tmp['statusName'] = $userInfo['statusName'];
  $tmp['typeName'] = $userInfo['typeName'];
  $tmp['mobile'] = $userInfo['mobile'];
  $tmp['lockStatus'] = $userInfo['lock_status'];
  $tmp['tel'] = $userInfo['tel'];
  $tmp['loginCount'] = $userInfo['login_count'];
  $tmp['fakeUrl'] = site_url('api/user/fakeUrl/'.$userInfo['id']);
  $tmp['schoolZhuanye'] = $userInfo['school_zhuanye'];
  $tmp['schoolCollege'] = $userInfo['school_college'];
  $tmp['schoolName'] = $userInfo['school_name'];
  $tmp['enterTime'] = $userInfo['school_enter_time'];
  $tmp['companyName'] = $userInfo['company_name'];
  $tmp['companyOrgName'] = $userInfo['company_org_name'];
  $tmp['companyStartName'] = $userInfo['company_start_name'];
  $tmp['zhengjianType'] = $userInfo['zhengjian_type'];
  $tmp['zhengjianTypeName'] = $userInfo['zhengjianTypeName'];
  $tmp['zhengjianNo'] = $userInfo['zhengjian_no'];
  $tmp['newsLetterName'] = $userInfo['newsletter'];
  $tmp['lastLoginTime'] = $userInfo['last_login_time'] ? date("Y-m-d h:i:s",$userInfo['last_login_time']) : "";
  $tmp['lastLoginIp'] = $userInfo['last_login_ip'];
  $tmp['email'] = $userInfo['email'];
  $tmp['description'] = $userInfo['description'] ? $userInfo['description'] : '';
  $tmp['userno'] = $userInfo['userno'];
  $tmp['address'] = $userInfo['address'];
  $tmp['weixin'] = $userInfo['weixin'];
  $tmp['zipcode'] = $userInfo['zipcode'];
  $tmp['homelink'] = $userInfo['homelink'];
  $tmp['province'] = $userInfo['provice_name'];
  $tmp['cityName'] = $userInfo['city_name'];
	return $tmp;
} 


 function workBase($workbase){
   $tmp = array();
   $tmp['workNo'] = $workbase['work_no'];
   $tmp['id'] = $workbase['uid'];
   $tmp['title'] = $workbase['title'];
   $tmp['isDelete'] = $workbase['isDelete'];
   $tmp['cover'] = buildImageThumb($workbase['cover']);
   $tmp['origin'] = buildImageOrigin($workbase['cover']);
   $tmp['type'] = $workbase['type'];
   $tmp['payType'] = $workbase['payType'];
   $tmp['payTypeRemark'] = $workbase['payTypeRemark'];
   $tmp['payTypeName'] = $workbase['payTypeName'];
   $tmp['createdTime'] = $workbase['createdTime'];
   $tmp['lastModifiedTime'] = $workbase['lastModifiedTime'];
   $tmp['typeName'] = $workbase['typeName'];
   $tmp['status'] = $workbase['status'];
   $tmp['statusName'] = $workbase['statusName'];
   $tmp['description'] = isset($workbase['description']) ? $workbase['description'] : '';
   $tmp['submitDate'] = $workbase['submitDate'];
   return $tmp;
}

function workDetailChange($workdetail){
   $tmp = array();
   $tmp['workId'] = $workdetail['work_id'];
   $tmp['id'] = $workdetail['uid'];
   $tmp['title'] = $workdetail['title'];
   $tmp['paragonType'] = $workdetail['paragonType'];
   // $tmp['paragonTypeName'] = $workdetail['paragonTypeName'];
   $tmp['workNo'] = $workdetail['work_no'];
   $tmp['payType'] = $workdetail['payType'];
   $tmp['payTypeRemark'] = $workdetail['payTypeRemark'];
   $tmp['payTypeName'] = $workdetail['payTypeName'];
   $tmp['isDeleteName'] = $workdetail['isDeleteName'];
   $tmp['isDelete'] = $workdetail['isDelete'];
   $tmp['type'] = isset($workdetail['type']) ? $workdetail['type'] : '';
   $tmp['paragonTypeName'] = isset($workdetail['paragonTypeName']) ? $workdetail['paragonTypeName'] : '';
   $tmp['imgs'] = $workdetail['imgs'];
   $tmp['origin'] = $workdetail['origin'];
   $tmp['firstCategory'] = isset($workdetail['firstCategory']) ? $workdetail['firstCategory'] : '';
   $tmp['firstCategoryName'] = isset($workdetail['firstCategoryName']) ? $workdetail['firstCategoryName'] : '';
   $tmp['secondCategoryName'] = isset($workdetail['secondCategoryName']) ? $workdetail['secondCategoryName'] : '';
   $tmp['description'] = isset($workdetail['description']) ? nl2br($workdetail['description']) : '';
   $tmp['guideTeacher'] = $workdetail['guide_teacher'];
   $tmp['guideTeacherTel'] = $workdetail['guide_teacher_tel'];
   $tmp['acceptFreshmanName'] = isset($workdetail['acceptFreshmanName']) ? $workdetail['acceptFreshmanName'] : '';
   $tmp['actorType'] = isset($workdetail['actor_type']) ? $workdetail['actor_type'] : '';
   $tmp['actorTypeName'] = isset($workdetail['actorTypesName']) ? $workdetail['actorTypesName'] : '';
   $tmp['awardDescription'] = isset($workdetail['award_description']) ? nl2br($workdetail['award_description']) : '';
   $tmp['changeDescription'] = isset($workdetail['ChanYeChangesName']) ? nl2br($workdetail['ChanYeChangesName']) : '';
   $tmp['companyUseDescription'] = isset($workdetail['company_use_description']) ? nl2br($workdetail['company_use_description']) : '';
   $tmp['ownerFirstName'] = isset($workdetail['owner_last_name']) ? $workdetail['owner_last_name'] : '';
   $tmp['ownerLastName'] = isset($workdetail['owner_first_name']) ? $workdetail['owner_first_name'] : '';
   $tmp['patentImg'] = isset($workdetail['patent_imgs']) ? buildImageThumb($workdetail['patent_imgs']) : '';
   $tmp['patentImgOrigin'] = isset($workdetail['patent_imgs']) ? buildImageOrigin($workdetail['patent_imgs']) : '';
   $tmp['patentStatus'] = isset($workdetail['patentStatusName']) ? $workdetail['patentStatusName'] : '';
   $tmp['projectDescription'] = isset($workdetail['project_description']) ? nl2br($workdetail['project_description']) : '';
   $tmp['procstatus'] = isset($workdetail['process_status']) ? $workdetail['process_status'] : '';
   $tmp['procstatusName'] = isset($workdetail['process_status_name']) ? $workdetail['process_status_name'] : '';
   $tmp['proDescription'] = isset($workdetail['process_description']) ? nl2br($workdetail['process_description']) : '';
   $tmp['schemeFile'] = isset($workdetail['scheme_file']) ? buildImageOrigin($workdetail['scheme_file']) :'';
   $tmp['url'] = $workdetail['url'];
   $tmp['videoUrl'] = $workdetail['video_url'];
   $tmp['videoUrlCode'] = $workdetail['video_url_code'];
   return $tmp;
}

function workActorChange($workActor){

    $tmp = array();
    // $tmp['workTypeName'] = $workActor['workTypeName'];
    // $tmp['workType'] = $workActor['workType'];
    $tmp['gender'] = $workActor['genders'] ? $workActor['genders'] : '';
    $tmp['isFreshmanName'] = $workActor['isFreshmanName'] ? $workActor['isFreshmanName'] : '';
    //$tmp['isDeleteName'] = $workActor['isDeleteName'];
    $tmp['typeName'] = $workActor['typeName'] ? $workActor['typeName'] : '';
    $tmp['zhengJianTypeName'] = $workActor['zhengJianTypeName'] ? $workActor['zhengJianTypeName'] : '';
    $tmp['enterTime'] = $workActor['school_enter_time'] ? $workActor['school_enter_time'] : '';
    $tmp['address'] = $workActor['address'] ? $workActor['address'] : '';
    $tmp['companyName'] = $workActor['company_name'] ? $workActor['company_name'] : '';
    $tmp['companyOrgName'] = $workActor['company_org_name'] ? $workActor['company_org_name'] : '';
    $tmp['companyStartName'] = $workActor['company_start_name'] ? $workActor['company_start_name'] : '';
    $tmp['companyZhengJianImg'] = $workActor['company_zhengjian_img'] ? buildImageThumb($workActor['company_zhengjian_img']) : '';
    $tmp['companyZhengJianImgOrigin'] = $workActor['company_zhengjian_img'] ? buildImageOrigin($workActor['company_zhengjian_img']) : '';
    $tmp['desription'] = isset($workActor['description']) ? $workActor['description'] :'';
    $tmp['cityName'] = $workActor['city_name'] ? $workActor['city_name'] : '';
    $tmp['birth'] = $workActor['birth'] ? date("Y-m-d",strtotime($workActor['birth'])) : '';
    $tmp['type'] = $workActor['type'] ? $workActor['type'] : '';
    $tmp['tel'] = $workActor['tel'] ? $workActor['tel'] : '';
    $tmp['email'] = $workActor['email'] ? $workActor['email'] : '';
    $tmp['mobile'] = $workActor['mobile'] ? $workActor['mobile'] :'';
    $tmp['isDelete'] = $workActor['is_delete'] ? $workActor['is_delete'] : '';
    $tmp['zhengJianNo'] = $workActor['zhengjian_no'] ? $workActor['zhengjian_no'] : '';
    $tmp['provinceName'] = isset($workActor['provice_name']) ? $workActor['provice_name'] : '';
    $tmp['zipcode'] = $workActor['zipcode'] ? $workActor['zipcode'] : '';
    $tmp['homelink'] = $workActor['homelink'] ? $workActor['homelink'] : '';
    $tmp['seq'] = $workActor['seq'] ? $workActor['seq'] : '';
    $tmp['lastName'] = $workActor['last_name'] ? $workActor['last_name'] : '';
    $tmp['firstName'] = $workActor['first_name'] ? $workActor['first_name'] : '';
    $tmp['schoolName'] = $workActor['school_name'] ? $workActor['school_name'] : '';
    $tmp['schoolCollege'] = $workActor['school_college'] ? $workActor['school_college'] : '';
    $tmp['schoolZhuanye'] = $workActor['school_zhuanye'] ? $workActor['school_zhuanye'] : '';
    $tmp['schoolZhangJianImg'] = $workActor['school_zhengjian_img'] ? buildImageThumb($workActor['school_zhengjian_img']) : '';
    $tmp['schoolZhangJianImgOrigin'] = $workActor['school_zhengjian_img'] ? buildImageOrigin($workActor['school_zhengjian_img']) : '';
    $tmp['schoolName'] = $workActor['school_name'] ? $workActor['school_name'] : '';
    return $tmp;
    // var_dump($tmp);
}

function workActorFeildChange($workActor){

    $tmp = array();
    $tmp['gender'] = $workActor['gender'] ? $workActor['gender'] : '';
    $tmp['is_freshman'] = $workActor['is_freshman'] ? $workActor['is_freshman'] : '';
    $tmp['type'] = $workActor['type'] ? $workActor['type'] : '';
    $tmp['zhengjian_type'] = $workActor['zhengjian_type'] ? $workActor['zhengjian_type'] : '';
    $tmp['school_enter_time'] = $workActor['school_enter_time'] ? $workActor['school_enter_time'] : '';
    $tmp['address'] = $workActor['address'] ? $workActor['address'] : '';
    $tmp['company_name'] = $workActor['company_name'] ? $workActor['company_name'] : '';
    $tmp['company_org_name'] = $workActor['company_org_name'] ? $workActor['company_org_name'] : '';
    $tmp['company_start_name'] = $workActor['company_start_name'] ? $workActor['company_start_name'] : '';
    $tmp['company_zhengjian_img'] = $workActor['company_zhengjian_img'] ? buildImageThumb($workActor['company_zhengjian_img']) : '';
    // $tmp['desription'] = isset($workActor['description']) ? $workActor['description'] :'';
    $tmp['city_name'] = $workActor['city_name'] ? $workActor['city_name'] : '';
    $tmp['birth'] = $workActor['birth'] ? date("Y-m-d",strtotime($workActor['birth'])) : '';
    $tmp['tel'] = $workActor['tel'] ? $workActor['tel'] : '';
    $tmp['email'] = $workActor['email'] ? $workActor['email'] : '';
    $tmp['mobile'] = $workActor['mobile'] ? $workActor['mobile'] :'';
    $tmp['is_delete'] = $workActor['is_delete'] ? $workActor['is_delete'] : '';
    $tmp['zhengjian_no'] = $workActor['zhengjian_no'] ? $workActor['zhengjian_no'] : '';
    $tmp['provice_name'] = isset($workActor['provice_name']) ? $workActor['provice_name'] : '';
    $tmp['zipcode'] = $workActor['zipcode'] ? $workActor['zipcode'] : '';
    $tmp['homelink'] = $workActor['homelink'] ? $workActor['homelink'] : '';
    $tmp['seq'] = $workActor['seq'] ? $workActor['seq'] : '';
    $tmp['last_name'] = $workActor['last_name'] ? $workActor['last_name'] : '';
    $tmp['first_name'] = $workActor['first_name'] ? $workActor['first_name'] : '';
    $tmp['school_name'] = $workActor['school_name'] ? $workActor['school_name'] : '';
    $tmp['school_college'] = $workActor['school_college'] ? $workActor['school_college'] : '';
    $tmp['school_zhuanye'] = $workActor['school_zhuanye'] ? $workActor['school_zhuanye'] : '';
    $tmp['school_zhengjian_img'] = $workActor['school_zhengjian_img'] ? buildImageThumb($workActor['school_zhengjian_img']) : '';
    $tmp['school_name'] = $workActor['school_name'] ? $workActor['school_name'] : '';
    return $tmp;
}


function workpay($workpay){
  $tmp = array();
   $tmp['workId'] = $workpay['work_id'];
   $tmp['workNo'] = $workpay['work_no'];
   $tmp['subject'] = $workpay['subject'];
   $tmp['body'] = $workpay['body'];
   $tmp['payType'] = $workpay['payType'];
   $tmp['payTypeName'] = $workpay['payTypeName'];
   $tmp['payTypeRemark'] = $workpay['payTypeRemark'];
   $tmp['alipayNo'] = $workpay['alipay_trade_no'];
   $tmp['buyerId'] = $workpay['buyer_id'];
   $tmp['email'] = $workpay['buyer_email'];
   $tmp['fee'] = ($workpay['total_fee'])/100;
   $tmp['tradeNo'] = $workpay['trade_no'];
   $tmp['status'] = $workpay['status'];
   $tmp['payTime'] = $workpay['pay_time'] ? date("Y-m-d h:i:s",$workpay['pay_time']) : '';
   $tmp['statusName'] = $workpay['payStatusName'];
   return $tmp;
}

//新晋奖字段
function workGraduate($work){
    $tmp = array();
    $tmp['id'] = $work['id'];
    $tmp['uid'] = $work['uid'];
    $tmp['work_id'] = $work['work_id'];
    $tmp['title'] = $work['title'];
    $tmp['active_no'] = $work['active_no'];
    $tmp['work_no'] = $work['work_no'];
    $tmp['first_category'] = $work['first_category'];
    $tmp['second_category'] = $work['second_category'];
    $tmp['guide_teacher'] = $work['guide_teacher'];
    $tmp['guide_teacher_tel'] = $work['guide_teacher_tel'];
    $tmp['description'] = $work['description'];
    $tmp['imgs'] = $work['imgs'];
    $tmp['video_url'] = $work['video_url'];
    $tmp['video_url_code'] = $work['video_url_code'];
    $tmp['url'] = $work['url'];
    $tmp['award_description'] = $work['award_description'];
    $tmp['company_use_description'] = $work['company_use_description'];
    $tmp['accept_freshman'] = isset($work['accept_freshman']) ? $work['accept_freshman'] : '';
    $tmp['created_time'] = $work['created_time'];
    $tmp['modified_time'] = $work['modified_time'];
    return $tmp;
}

//特别奖字段
function workSpecial($work){
    $tmp = array();
    $tmp['id'] = $work['id'];
    $tmp['uid'] = $work['uid'];
    $tmp['work_id'] = $work['work_id'];
    $tmp['title'] = $work['title'];
    $tmp['active_no'] = $work['active_no'];
    $tmp['work_no'] = $work['work_no'];
    $tmp['first_category'] = $work['first_category'];
    $tmp['second_category'] = $work['second_category'];
    $tmp['guide_teacher'] = $work['guide_teacher'];
    $tmp['guide_teacher_tel'] = $work['guide_teacher_tel'];
    $tmp['description'] = $work['description'];
    $tmp['imgs'] = $work['imgs'];
    $tmp['video_url'] = $work['video_url'];
    $tmp['video_url_code'] = $work['video_url_code'];
    $tmp['url'] = $work['url'];
    $tmp['award_description'] = $work['award_description'];
    $tmp['company_use_description'] = $work['company_use_description'];
    $tmp['created_time'] = $work['created_time'];
    $tmp['modified_time'] = $work['modified_time'];
    return $tmp;
}


//典范奖字段
function workParagon($work){
    $tmp = array();
    $tmp['id'] = $work['id'];
    $tmp['uid'] = $work['uid'];
    $tmp['work_id'] = $work['work_id'];
    $tmp['type'] = isset($work['type']) ? $work['type'] : '';
    $tmp['actor_type'] = isset($work['actor_type']) ? $work['actor_type'] : '';
    $tmp['title'] = $work['title'];
    $tmp['active_no'] = $work['active_no'];
    $tmp['work_no'] = $work['work_no'];
    $tmp['first_category'] = $work['first_category'];
    $tmp['second_category'] = $work['second_category'];
    $tmp['guide_teacher'] = $work['guide_teacher'];
    $tmp['guide_teacher_tel'] = $work['guide_teacher_tel'];
    $tmp['description'] = $work['description'];
    $tmp['project_description'] = isset($work['project_description']) ? $work['project_description'] : '';
    $tmp['process_status'] = isset($work['process_status']) ? $work['process_status'] : '';
    $tmp['process_description'] = isset($work['process_description']) ? $work['process_description'] : '';
    $tmp['patent_status'] = isset($work['patent_status']) ? $work['patent_status'] : '';
    $tmp['patent_imgs'] = isset($work['patent_imgs']) ? $work['patent_imgs'] : '';
    $tmp['imgs'] = $work['imgs'];
    $tmp['video_url'] = $work['video_url'];
    $tmp['video_url_code'] = $work['video_url_code'];
    $tmp['url'] = $work['url'];
    $tmp['change_description'] = isset($work['change_description']) ? $work['change_description'] : '';
    $tmp['scheme_file'] = isset($work['scheme_file']) ? $work['scheme_file'] : '';
    $tmp['award_description'] = $work['award_description'];
    $tmp['owner_first_name'] = isset($work['owner_first_name']) ? $work['owner_first_name'] : ''; 
    $tmp['owner_last_name'] = isset($work['owner_last_name']) ? $work['owner_last_name'] : ''; 
    $tmp['created_time'] = $work['created_time'];
    $tmp['modified_time'] = $work['modified_time'];
    return $tmp;
}

//作品表dashi_work
function work($work){
   $tmp = array();
   $tmp['id'] = $work['id'];
   $tmp['uid'] = $work['uid'];
   $tmp['active_no'] = $work['active_no'];
   $tmp['work_no'] = $work['work_no'];
   $tmp['title'] = $work['title'];
   $tmp['cover'] = $work['cover'];
   $tmp['type'] = $work['type'];
   $tmp['status'] = $work['status'];
   $tmp['award_status'] = $work['award_status'];
   $tmp['pay_img'] = $work['pay_img'];
   $tmp['description'] = $work['description'];
   $tmp['is_delete'] = $work['is_delete'];
   $tmp['created_time'] = $work['created_time'];
   $tmp['modified_time'] = $work['modified_time'];
   return $tmp;
}


?>
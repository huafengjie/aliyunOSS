<?php
/*
    下面配置自行修改
*/
#OSS获得的AccessKeyId和AccessKeySecret 获取地址https://ak-console.aliyun.com/#/accesskey
const OSS_ACCESS_ID = 'cFpnCS4HkRaU190Z';
const OSS_ACCESS_KEY = 'oACQ08EA7cJdLlggy1FZAJVsqOJaOG';
const OSS_ENDPOINT = 'http://oss.aliyuncs.com';#您选定的OSS数据中心访问域名
const OSS_TEST_BUCKET = 'testoss';##使用的bucket
require_once __DIR__ . '/osssdk/autoload.php';
#上传处理
$filename=time();
$prex='';
if(!empty($_FILES)){
    $file=$_FILES["file"];
    if($file["error"] > 0){
        $fileinfo['msg']="上传错误: ".$file["error"];
    }else{
        $fileinfo['msg']="上传成功";
        $prex=explode(".",$file["name"]);
        $prex=$prex[count($prex)-1];
        $fileinfo['prex']=$prex;
        $fileinfo['filename']=$file["name"];
        move_uploaded_file($file["tmp_name"],__DIR__."/uploadtmp/".$filename.".".$prex);
        try {
            $ossClient = new \OSS\OssClient(OSS_ACCESS_ID, OSS_ACCESS_KEY, OSS_ENDPOINT);
        } catch (OssException $e) {
            $fileinfo['msg']= "异常：".$e->getMessage();
        }
        #请求时间
        $ossClient->setTimeout(0);
        $ossClient->setConnectTimeout(10);
        #创建目录
        $savedir=$prex."/";
        $savefilename=$filename.".".$prex;
        $ossClient->createObjectDir(OSS_TEST_BUCKET,$prex);
        $return=$ossClient->uploadFile(OSS_TEST_BUCKET, $savedir.$savefilename, __DIR__."/uploadtmp/".$filename.".".$prex);
        $return=$return['info'];
        #设置返回值
        if($return['http_code'] == 200){
            $fileinfo['url']=$return['url'];
            $fileinfo['size']=$return['size_upload']/1024;
        }else{
            $fileinfo['msg']="上传失败";
        }
    }
}else{
    $fileinfo=array();
}
unset($_FILES);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Aliyun OSS</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/js/lib/jquery-1.10.2_d88366fd.js"></script>
<style>
*{
    padding:0px;margin:0px;list-style:none;border:0px;text-decoration:none;font-family:微软雅黑;
}
html,body{
    height:100%;
    background-image: -webkit-radial-gradient(circle,#fff,#fafafa,#ccc);
    background-image: radial-gradient(circle,#fff,#fafafa,#ccc);
}
#table{
    width:100%;height:100%;
}
#table #zhanshi{
    text-align:center;
}
#uplodabtn{
    display:none;
}
#uploadandsubmit{
    border:0px;background:#006ad7;font-size:15px;width:150px;height:45px;color:#fff;font-family:微软雅黑;letter-spacing:2px;
    -moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px;cursor:pointer;
}
#uploadandsubmit:hover{
    background:#006fff;
}
#filebg{
    width:100px;height:100px;margin:0 auto;background-image:url(filebg.png);background-repeat:no-repeat;background-size:100%;
    -webkit-touch-callout:none;-webkit-user-select:none;-khtml-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;
    overflow:hidden;cursor:pointer;
}
#filebg div{
    color:#fff;font-size:25px;margin-top:49px;width:85px;text-align:center;
}
</style>
</head>
<body>
<table cellpadding="0" cellspacing="0" id="table">
    <tr>
        <td id="zhanshi">
        <?php
            if(!empty($fileinfo)){
                if(in_array($fileinfo['prex'],array("jpg","jpeg","gif","bmp","png"))){
        ?>
                    <img src="<?=$fileinfo['url']?>" style="max-width:500px;max-height:500px;margin:10px;"/>
                    <br/>文件大小：<?=$fileinfo['size']?>K
        <?php
                }else/*if(in_array($fileinfo['prex'],array("zip","rar","exe","tar","iso","apk","text")))*/{
        ?>
                <div id="filebg" onclick="window.open('<?=$fileinfo['url']?>')" title="点击下载"><div><?=$fileinfo['prex']?></div></div>
                <br/><?=$fileinfo['filename']?>
        <?php
                }
            }else{
        ?>
            点击下方按钮选择上传文件
        <?php
            }
        ?>
        </td>
    </tr>
    <tr>
        <td style="height:200px;border-top:1px solid #ccc;text-align:center;">
            <form method="POST" enctype="multipart/form-data" id="form" action="">
                <div id="uplodabtn"><input type="file" name="file" id="file" value="" /></div>
                <div id="submitbtn"><input type="button" name="uploadandsubmit" id="uploadandsubmit" value="选择并上传"/></div>
            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
$("#uploadandsubmit").click(function(){
    $("#file").click();
});
$("#file").change(function(){
    $("#form").submit();
});
</script>
</body>
</html>
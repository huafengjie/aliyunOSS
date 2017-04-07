<?php
#OSS获得的AccessKeyId和AccessKeySecret
#获取地址https://ak-console.aliyun.com/#/accesskey
const OSS_ACCESS_ID = 'aU190ZcFpnCS4HkR';
const OSS_ACCESS_KEY = 'JdLlggy1FZAJVsqOJaOGoACQ08EA7c';
#您选定的OSS数据中心访问域名
const OSS_ENDPOINT = 'http://oss-cn-shanghai.aliyuncs.com';
#使用的bucket
const OSS_TEST_BUCKET = 'hfjtestoss';#
require_once __DIR__ . '/osssdk/autoload.php';
try {
    $ossClient = new \OSS\OssClient(OSS_ACCESS_ID, OSS_ACCESS_KEY, OSS_ENDPOINT);
} catch (OssException $e) {
    echo "异常：".$e->getMessage();
    exit;
}
#请求时间
$ossClient->setTimeout(0);
$ossClient->setConnectTimeout(10);
#创建目录
$savedir="images/";
$savefilename="up1.png";
$ossClient->createObjectDir(OSS_TEST_BUCKET,"images");
$return=$ossClient->uploadFile(OSS_TEST_BUCKET, $savedir.$savefilename, __DIR__."/test1.png");
$return=$return['info'];
#设置返回值
if($return['http_code'] == 200){
    $fileinfo['url']=$return['url'];
    $fileinfo['size']=$return['size_upload']/1024;
}else{
    $fileinfo['msg']="上传失败";
}
echo "<pre>";print_r($fileinfo);


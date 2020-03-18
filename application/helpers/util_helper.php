<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH."libraries/AliyunSms/SignatureHelper.php";

use Aliyun\DySDKLite\SignatureHelper;

function get_server_path(){
    $pageURL = 'http';
    if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") 
    {
        $pageURL .= "s";
    }
    $pageURL .="://" . $_SERVER['HTTP_HOST'];
    return $pageURL;
}

function log_dump(...$args)
{
    foreach($args as $arg){
        if(is_string($arg)){
            log_message('error',$arg);
        }else{
            ob_start();
            var_dump($arg);
            $error = ob_get_clean();
            log_message('error',$error);
        }
    }
}

function compati_path($str)
{
    if ( strtoupper ( substr ( PHP_OS ,  0 ,  3 )) ===  'WIN' ) {
        return iconv('utf-8','gbk//IGNORE',$str);
    }else{
        return $str;
    }
}

function cvt_pdf($save_path,$preserved=False)
{
    $path_info = pathinfo($save_path);
    $ext = $path_info[ 'extension' ];

    //是pdf且<2M
    if(stripos($ext,'pdf') === 0 && filesize(compati_path($save_path)) < 2*1024*1024){
        return $save_path;
    }

    if(empty(T::$H->sys_variables['系统参数']['libreoffice']) ||
        // empty(T::$H->sys_variables['系统参数']['magick'])){
        empty(T::$H->sys_variables['系统参数']['gs']) ||
        empty(T::$H->sys_variables['系统参数']['libreoffice_port']) ){
        unlink(compati_path($save_path));
        sys_error('pdf组件未配置');
    }

    $ports = explode(',',T::$H->sys_variables['系统参数']['libreoffice_port']);


    $pos = strrpos($save_path,'.');
    $pdf = substr($save_path,0,$pos).'.pdf';

    $re = dirname($save_path).'/'.time().rand(1000,9999);
    $doc_rename = $re.'.'.$ext;
    $pdf_rename = $re.'.pdf';
    $small_pdf = $re.'.s.pdf';
    

    if(stripos($ext,'pdf') !== 0){ //非pdf   

        rename(compati_path($save_path),compati_path($doc_rename));    

        $libreoffice = T::$H->sys_variables['系统参数']['libreoffice'];
        $cmd = $libreoffice.' "'.getcwd().'/'.$doc_rename.'" "'.getcwd().'/'.$pdf_rename.'" ' ;

        if($preserved){
            $port = $ports[0];
        }else{
            $arr = T::$U->redis->keys('soffice:*');
            foreach ($arr as &$ref) {
                $ref = explode(':',$ref)[1];
            }
            $idle = array_values( array_diff($ports,[$ports[0]],$arr) );
            if(count($idle)===0){
                unlink(compati_path($doc_rename));
                sys_error('文档转换系统繁忙，请稍后再试');
            }
            $port = $idle[0];
        }

        $cmd .= $port.' '.($preserved ? 'none' : 'overwrite').' 2>&1';
        $cmd = compati_path($cmd);

        T::$U->redis->setex('soffice:'.$port,120,1);
        exec($cmd,$screen,$ret);
        // T::$U->redis->expire('soffice:'.$port,2);//2秒后过期，如果转换失败重启服务需要时间

        unlink(compati_path($doc_rename));

        if( !file_exists(compati_path($pdf_rename)) ){

            log_dump($cmd,$screen,$ret);

            //失败重启，为方便开发环境，libreoffice_restart不强制配置
            if( empty(T::$H->sys_variables['系统参数']['libreoffice_restart']) ){
                log_dump('pdf convert failed, libreoffice_restart script not found');
            }else{
                $cmd = T::$H->sys_variables['系统参数']['libreoffice_restart'].' '.$port;
                exec($cmd,$screen,$ret);
                log_dump($cmd,$screen,$ret);
            }
            T::$U->redis->expire('soffice:'.$port,2);//2秒后过期，如果转换失败重启服务需要时间
            sys_error('转换失败，请上传pdf文档');
        }else{
            T::$U->redis->delete('soffice:'.$port);
        }

    }else{ // 是pdf
        rename(compati_path($save_path),compati_path($pdf_rename));
    }

    
    if(filesize(compati_path($pdf_rename))>2*1024*1024){ //pdf过大
        
        $gs = T::$H->sys_variables['系统参数']['gs'];
        $cmd = $gs.' -sDEVICE=pdfwrite '.
                '-dCompatibilityLevel=1.4 '.
                '-dPDFSETTINGS=/screen '.
                '-dNOPAUSE -dQUIET -dBATCH -sOutputFile="'.$small_pdf.'" "'.$pdf_rename.'" 2>&1';

        $cmd = compati_path($cmd);

        exec($cmd,$screen,$ret);

        unlink(compati_path($pdf_rename));

        if( $ret !== 0 ){
            @unlink(compati_path($small_pdf));
            log_dump($cmd,$screen,$ret);
            sys_error('文档瘦身失败');
        }
        rename(compati_path($small_pdf),compati_path($pdf));

    }else{ // pdf ok
        rename(compati_path($pdf_rename),compati_path($pdf));
    }

    if( filesize(compati_path($pdf)) > 2.7*1024*1024){

        unlink(compati_path($pdf));
        sys_error('文档不得超过2M');
    }

    return $pdf;

    // $frag = 'product_img/'.date('YmdHis').rand(1000,9999);
    // $dir = tu_file_path($frag);
    // $img = $dir.'.jpg';

    // $magick = T::$H->sys_variables['系统参数']['magick'];

    // $cmd = $magick.' -density 150 -quality 45 "'.getcwd().'/'.$pdf.'" '.getcwd().'/'.$img;
    
    // exec($cmd,$_,$ret);

    // $pic_arr = [];
    // for ($i=0; $i < 30; $i++) { 
    //     $file = $dir.'-'.$i.'.jpg';
    //     if(!file_exists($file)){
    //         break;
    //     }
    //     $pic_arr[] = $file;
    //     if($i>20){
    //         sys_error('文件不得超过20页');
    //     }
    // }

    // if(empty($pic_arr)){
    //     sys_error('文件读取失败');
    // }

    // return $pic_arr;
}

function merge_pdf($inputs,$output)
{
    if(empty(T::$H->sys_variables['系统参数']['cpdf'])){
        sys_error('pdf组件未配置');
    }

    foreach ($inputs as &$ref) {
        $ref = '"'.getcwd().'/'.$ref.'"';
    }
    $output = getcwd().'/'.$output;

    $cpdf = T::$H->sys_variables['系统参数']['cpdf'];
    $cmd = $cpdf.' '.implode(' ',$inputs).' -o "'.$output.'" 2>&1';

    $cmd = compati_path($cmd);

    exec($cmd,$screen,$ret);

    if( !file_exists(compati_path($output)) ){
        log_dump($cmd,$screen,$ret);
        sys_error( 'pdf合并失败');
    }
}

function resize_pdf($input,$output)
{
    if(empty(T::$H->sys_variables['系统参数']['cpdf'])){
        sys_error('pdf组件未配置');
    }

    $input = getcwd().'/'.$input;

    $output = getcwd().'/'.$output;

    $cpdf = T::$H->sys_variables['系统参数']['cpdf'];
    $cmd = $cpdf.' -scale-to-fit a4portrait '.$input.' -o "'.$output.'" 2>&1';

    $cmd = compati_path($cmd);

    exec($cmd,$screen,$ret);

    if( !file_exists(compati_path($output)) ){
        log_dump($cmd,$screen,$ret);
        sys_error( 'pdf尺寸改变失败');
    }
}


function get_age($birthday) {
    list($y1, $m1, $d1) = explode("-", $birthday);
    list($y2, $m2, $d2) = explode("-", date("Y-m-d"));
    $age = $y2 - $y1;
    if ($m2.$d2 < $m1.$d1){
        $age -= 1;
    } 
    return $age;
}

function resize_img($old_img, $new_img, $width='thumbnail') {

    ini_set('memory_limit','256M');
    
    $save_path = dirname($new_img);
    file_exists($save_path) OR mkdir($save_path, 0755, TRUE);
    $image = getimagesize($old_img); 
    switch ($image[2]) { 
        case 1:
            $im = imagecreatefromgif($old_img);
            break;
        case 2:
            $im = imagecreatefromjpeg($old_img);
            break;
        case 3:
            $im = imagecreatefrompng($old_img);
            break;
        default:
            copy($old_img, $new_img);
            return;
    }
    $src_W = $image[0]; 
    $src_H = $image[1];
    if($width == 'thumbnail'){
        $width = 280;
    }
    $height = floor( $src_H * ( $width / $src_W ) );
    
    $img = imagecreatetruecolor($width, $height); 
    imagecopyresampled($img, $im, 0, 0, 0, 0, $width, $height, $src_W, $src_H); 
    if(!empty($img)){
        imagejpeg($img, $new_img); 
    }else{
        imagejpeg($im,$new_img);
    } 
}

function copy_file($src,$dst,$pv=0755) {
    if(!file_exists($src)){
        return;
    }
    $path = dirname($dst);
    if(!is_dir($path)){
        mkdir($path, $pv, true);
    }
    copy($src, $dst);
}

function copy_dir($src,$dst,$pv=0755) {
    if(!is_dir($src)){
        return;
    }
    $dir = opendir($src);
    if(!$dir){
        return;
    }
    if(!is_dir($dst)){
        mkdir($dst, $pv, true);
    }
    
    while(false !== ( $name = readdir($dir)) ) {
        if($name == '.' || $name == '..') {
            continue;
        }
        $item = $src . '/' . $name;
        $to_item = $dst . '/' . $name;
        if ( is_dir($item) ) {
            copy_dir($item, $to_item);
            continue;
        }

        copy($item, $to_item);
    }
    closedir($dir);
}

function delete_dir($src) {
    if(!is_dir($src)){
        return;
    }
    $dir = opendir($src);
    if(!$dir){
        return;
    }
    while(false !== ( $name = readdir($dir)) ) {
        if($name == '.' || $name == '..') {
            continue;
        }
        $item = $src . '/' . $name;
        if ( is_dir($item) ) {
            delete_dir($item);
            rmdir($item);
        } else {
            unlink($item);
        }
    } 
    closedir($dir);  
}

function zip_folder($src, $zip_folder, $zip) {
    $zip->addEmptyDir($zip_folder); 
    $dir = opendir($src); 
    while(false !== ( $name = readdir($dir)) ) {
        if($name == '.' || $name == '..') {
            continue;
        }
        $item = $src . '/' . $name;
        $zip_item = $zip_folder. '/' . $name;
        if ( is_dir($item) ) {
            zip_folder($item, $zip_item, $zip);
        } else {
            $zip->addFile($item, $zip_item);
        }
    } 
    closedir($dir);  
}

function zip($dst, $items){
    if(!is_array($items)){
        $items = [$items];
    }
    $zip  = new  ZipArchive ;
    if(!$zip->open( $dst ,  ZipArchive::CREATE )){
        return;
    }
    foreach ($items as $item) {
        if(!file_exists($item)){
            continue;
        }
        if(is_dir($item)){
            zip_folder($item,basename($item),$zip);
        }else{
            $zip->addFile($item, basename($item));
        }
    }
    $zip->close(); 
}
function unzip($src,$dst)
{
    $zip = new ZipArchive;
    $res = $zip->open($src);
    if ($res === TRUE) {
        $zip->extractTo($dst);
        $zip->close();
    } 
}

function exec_insure($cmd, &$output=NULL, &$return_var=NULL){
    if ( strtoupper ( substr ( PHP_OS ,  0 ,  3 )) ===  'WIN' ) {
        return exec($cmd, $output, $return_var);
    } else {
        return exec('export PATH=$PATH:/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin;'.$cmd, $output, $return_var);
    }
}

function run_no_blk($cmd) {
    if ( strtoupper ( substr ( PHP_OS ,  0 ,  3 )) ===  'WIN' ) {
        pclose(popen("start /B " . $cmd, "r"));
    } else {
        exec_insure($cmd . " > /dev/null &");
    }
}

function ali_sms_send_verify($phone,$code)
{

    $setting  = T::$H->sys_variables['系统参数']??[];
    foreach (['ali_sms_key','ali_sms_sec','ali_sms_sign','ali_sms_verify'] as  $v) {
        if(empty($setting[$v])){
            sys_error(i('SMS_NOT_CFG'));
        }
    }
    $params = array ();

    // *** 需用户填写部分 ***

    // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    $accessKeyId = $setting['ali_sms_key'];
    $accessKeySecret = $setting['ali_sms_sec'];

    // fixme 必填: 短信接收号码
    $params["PhoneNumbers"] = $phone;

    // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
    $params["SignName"] = $setting['ali_sms_sign'];

    // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
    $params["TemplateCode"] = $setting['ali_sms_verify'];

    // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
    $params['TemplateParam'] = Array (
        "code" => $code
    );

    // fixme 可选: 设置发送短信流水号
    // $params['OutId'] = "12345";

    // // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
    // $params['SmsUpExtendCode'] = "1234567";


    // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
    if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
    }

    // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
    $helper = new SignatureHelper();

    // 此处可能会抛出异常，注意catch
    $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        "dysmsapi.aliyuncs.com",
        array_merge($params, array(
            "RegionId" => "cn-hangzhou",
            "Action" => "SendSms",
            "Version" => "2017-05-25",
        ))
    );

    return $content;
}

function get_country($code)
{
    return ["AO"=>"安哥拉","AF"=>"阿富汗","AL"=>"阿尔巴尼亚","DZ"=>"阿尔及利亚","AD"=>"安道尔共和国","AI"=>"安圭拉岛","AG"=>"安提瓜和巴布达","AR"=>"阿根廷","AM"=>"亚美尼亚","AU"=>"澳大利亚","AT"=>"奥地利","AZ"=>"阿塞拜疆","BS"=>"巴哈马","BH"=>"巴林","BD"=>"孟加拉国","BB"=>"巴巴多斯","BY"=>"白俄罗斯","BE"=>"比利时","BZ"=>"伯利兹","BJ"=>"贝宁","BM"=>"百慕大群岛","BO"=>"玻利维亚","BW"=>"博茨瓦纳","BR"=>"巴西","BN"=>"文莱","BG"=>"保加利亚","BF"=>"布基纳法索","MM"=>"缅甸","BI"=>"布隆迪","CM"=>"喀麦隆","CA"=>"加拿大","CF"=>"中非共和国","TD"=>"乍得","CL"=>"智利","CN"=>"中国","CO"=>"哥伦比亚","CG"=>"刚果","CK"=>"库克群岛","CR"=>"哥斯达黎加","CU"=>"古巴","CY"=>"塞浦路斯","CZ"=>"捷克","DK"=>"丹麦","DJ"=>"吉布提","DO"=>"多米尼加共和国","EC"=>"厄瓜多尔","EG"=>"埃及","SV"=>"萨尔瓦多","EE"=>"爱沙尼亚","ET"=>"埃塞俄比亚","FJ"=>"斐济","FI"=>"芬兰","FR"=>"法国","GF"=>"法属圭亚那","GA"=>"加蓬","GM"=>"冈比亚","GE"=>"格鲁吉亚","DE"=>"德国","GH"=>"加纳","GI"=>"直布罗陀","GR"=>"希腊","GD"=>"格林纳达","GU"=>"关岛","GT"=>"危地马拉","GN"=>"几内亚","GY"=>"圭亚那","HT"=>"海地","HN"=>"洪都拉斯","HK"=>"香港","HU"=>"匈牙利","IS"=>"冰岛","IN"=>"印度","ID"=>"印度尼西亚","IR"=>"伊朗","IQ"=>"伊拉克","IE"=>"爱尔兰","IL"=>"以色列","IT"=>"意大利","JM"=>"牙买加","JP"=>"日本","JO"=>"约旦","KH"=>"柬埔寨","KZ"=>"哈萨克斯坦","KE"=>"肯尼亚","KR"=>"韩国","KW"=>"科威特","KG"=>"吉尔吉斯坦","LA"=>"老挝","LV"=>"拉脱维亚","LB"=>"黎巴嫩","LS"=>"莱索托","LR"=>"利比里亚","LY"=>"利比亚","LI"=>"列支敦士登","LT"=>"立陶宛","LU"=>"卢森堡","MO"=>"澳门","MG"=>"马达加斯加","MW"=>"马拉维","MY"=>"马来西亚","MV"=>"马尔代夫","ML"=>"马里","MT"=>"马耳他","MU"=>"毛里求斯","MX"=>"墨西哥","MD"=>"摩尔多瓦","MC"=>"摩纳哥","MN"=>"蒙古","MS"=>"蒙特塞拉特岛","MA"=>"摩洛哥","MZ"=>"莫桑比克","NA"=>"纳米比亚","NR"=>"瑙鲁","NP"=>"尼泊尔","NL"=>"荷兰","NZ"=>"新西兰","NI"=>"尼加拉瓜","NE"=>"尼日尔","NG"=>"尼日利亚","KP"=>"朝鲜","NO"=>"挪威","OM"=>"阿曼","PK"=>"巴基斯坦","PA"=>"巴拿马","PG"=>"巴布亚新几内亚","PY"=>"巴拉圭","PE"=>"秘鲁","PH"=>"菲律宾","PL"=>"波兰","PF"=>"法属玻利尼西亚","PT"=>"葡萄牙","PR"=>"波多黎各","QA"=>"卡塔尔","RO"=>"罗马尼亚","RU"=>"俄罗斯","LC"=>"圣卢西亚","VC"=>"圣文森特岛","SM"=>"圣马力诺","ST"=>"圣多美和普林西比","SA"=>"沙特阿拉伯","SN"=>"塞内加尔","SC"=>"塞舌尔","SL"=>"塞拉利昂","SG"=>"新加坡","SK"=>"斯洛伐克","SI"=>"斯洛文尼亚","SB"=>"所罗门群岛","SO"=>"索马里","ZA"=>"南非","ES"=>"西班牙","LK"=>"斯里兰卡","SD"=>"苏丹","SR"=>"苏里南","SZ"=>"斯威士兰","SE"=>"瑞典","CH"=>"瑞士","SY"=>"叙利亚","TW"=>"台湾省","TJ"=>"塔吉克斯坦","TZ"=>"坦桑尼亚","TH"=>"泰国","TG"=>"多哥","TO"=>"汤加","TT"=>"特立尼达和多巴哥","TN"=>"突尼斯","TR"=>"土耳其","TM"=>"土库曼斯坦","UG"=>"乌干达","UA"=>"乌克兰","AE"=>"阿拉伯联合酋长国","GB"=>"英国","US"=>"美国","UY"=>"乌拉圭","UZ"=>"乌兹别克斯坦","VE"=>"委内瑞拉","VN"=>"越南","YE"=>"也门","YU"=>"南斯拉夫","ZW"=>"津巴布韦","ZR"=>"扎伊尔","ZM"=>"赞比亚"][$code];
}

class CUtf8_PY {  
    /** 
     * 拼音字符转换图 
     * @var array 
     */  
    private static $_aMaps = array(  
        'a'=>-20319,'ai'=>-20317,'an'=>-20304,'ang'=>-20295,'ao'=>-20292,  
        'ba'=>-20283,'bai'=>-20265,'ban'=>-20257,'bang'=>-20242,'bao'=>-20230,'bei'=>-20051,'ben'=>-20036,'beng'=>-20032,'bi'=>-20026,'bian'=>-20002,'biao'=>-19990,'bie'=>-19986,'bin'=>-19982,'bing'=>-19976,'bo'=>-19805,'bu'=>-19784,  
        'ca'=>-19775,'cai'=>-19774,'can'=>-19763,'cang'=>-19756,'cao'=>-19751,'ce'=>-19746,'ceng'=>-19741,'cha'=>-19739,'chai'=>-19728,'chan'=>-19725,'chang'=>-19715,'chao'=>-19540,'che'=>-19531,'chen'=>-19525,'cheng'=>-19515,'chi'=>-19500,'chong'=>-19484,'chou'=>-19479,'chu'=>-19467,'chuai'=>-19289,'chuan'=>-19288,'chuang'=>-19281,'chui'=>-19275,'chun'=>-19270,'chuo'=>-19263,'ci'=>-19261,'cong'=>-19249,'cou'=>-19243,'cu'=>-19242,'cuan'=>-19238,'cui'=>-19235,'cun'=>-19227,'cuo'=>-19224,  
        'da'=>-19218,'dai'=>-19212,'dan'=>-19038,'dang'=>-19023,'dao'=>-19018,'de'=>-19006,'deng'=>-19003,'di'=>-18996,'dian'=>-18977,'diao'=>-18961,'die'=>-18952,'ding'=>-18783,'diu'=>-18774,'dong'=>-18773,'dou'=>-18763,'du'=>-18756,'duan'=>-18741,'dui'=>-18735,'dun'=>-18731,'duo'=>-18722,  
        'e'=>-18710,'en'=>-18697,'er'=>-18696,  
        'fa'=>-18526,'fan'=>-18518,'fang'=>-18501,'fei'=>-18490,'fen'=>-18478,'feng'=>-18463,'fo'=>-18448,'fou'=>-18447,'fu'=>-18446,  
        'ga'=>-18239,'gai'=>-18237,'gan'=>-18231,'gang'=>-18220,'gao'=>-18211,'ge'=>-18201,'gei'=>-18184,'gen'=>-18183,'geng'=>-18181,'gong'=>-18012,'gou'=>-17997,'gu'=>-17988,'gua'=>-17970,'guai'=>-17964,'guan'=>-17961,'guang'=>-17950,'gui'=>-17947,'gun'=>-17931,'guo'=>-17928,  
        'ha'=>-17922,'hai'=>-17759,'han'=>-17752,'hang'=>-17733,'hao'=>-17730,'he'=>-17721,'hei'=>-17703,'hen'=>-17701,'heng'=>-17697,'hong'=>-17692,'hou'=>-17683,'hu'=>-17676,'hua'=>-17496,'huai'=>-17487,'huan'=>-17482,'huang'=>-17468,'hui'=>-17454,'hun'=>-17433,'huo'=>-17427,  
        'ji'=>-17417,'jia'=>-17202,'jian'=>-17185,'jiang'=>-16983,'jiao'=>-16970,'jie'=>-16942,'jin'=>-16915,'jing'=>-16733,'jiong'=>-16708,'jiu'=>-16706,'ju'=>-16689,'juan'=>-16664,'jue'=>-16657,'jun'=>-16647,  
        'ka'=>-16474,'kai'=>-16470,'kan'=>-16465,'kang'=>-16459,'kao'=>-16452,'ke'=>-16448,'ken'=>-16433,'keng'=>-16429,'kong'=>-16427,'kou'=>-16423,'ku'=>-16419,'kua'=>-16412,'kuai'=>-16407,'kuan'=>-16403,'kuang'=>-16401,'kui'=>-16393,'kun'=>-16220,'kuo'=>-16216,  
        'la'=>-16212,'lai'=>-16205,'lan'=>-16202,'lang'=>-16187,'lao'=>-16180,'le'=>-16171,'lei'=>-16169,'leng'=>-16158,'li'=>-16155,'lia'=>-15959,'lian'=>-15958,'liang'=>-15944,'liao'=>-15933,'lie'=>-15920,'lin'=>-15915,'ling'=>-15903,'liu'=>-15889,'long'=>-15878,'lou'=>-15707,'lu'=>-15701,'lv'=>-15681,'luan'=>-15667,'lue'=>-15661,'lun'=>-15659,'luo'=>-15652,  
        'ma'=>-15640,'mai'=>-15631,'man'=>-15625,'mang'=>-15454,'mao'=>-15448,'me'=>-15436,'mei'=>-15435,'men'=>-15419,'meng'=>-15416,'mi'=>-15408,'mian'=>-15394,'miao'=>-15385,'mie'=>-15377,'min'=>-15375,'ming'=>-15369,'miu'=>-15363,'mo'=>-15362,'mou'=>-15183,'mu'=>-15180,  
        'na'=>-15165,'nai'=>-15158,'nan'=>-15153,'nang'=>-15150,'nao'=>-15149,'ne'=>-15144,'nei'=>-15143,'nen'=>-15141,'neng'=>-15140,'ni'=>-15139,'nian'=>-15128,'niang'=>-15121,'niao'=>-15119,'nie'=>-15117,'nin'=>-15110,'ning'=>-15109,'niu'=>-14941,'nong'=>-14937,'nu'=>-14933,'nv'=>-14930,'nuan'=>-14929,'nue'=>-14928,'nuo'=>-14926,  
        'o'=>-14922,'ou'=>-14921,  
        'pa'=>-14914,'pai'=>-14908,'pan'=>-14902,'pang'=>-14894,'pao'=>-14889,'pei'=>-14882,'pen'=>-14873,'peng'=>-14871,'pi'=>-14857,'pian'=>-14678,'piao'=>-14674,'pie'=>-14670,'pin'=>-14668,'ping'=>-14663,'po'=>-14654,'pu'=>-14645,  
        'qi'=>-14630,'qia'=>-14594,'qian'=>-14429,'qiang'=>-14407,'qiao'=>-14399,'qie'=>-14384,'qin'=>-14379,'qing'=>-14368,'qiong'=>-14355,'qiu'=>-14353,'qu'=>-14345,'quan'=>-14170,'que'=>-14159,'qun'=>-14151,  
        'ran'=>-14149,'rang'=>-14145,'rao'=>-14140,'re'=>-14137,'ren'=>-14135,'reng'=>-14125,'ri'=>-14123,'rong'=>-14122,'rou'=>-14112,'ru'=>-14109,'ruan'=>-14099,'rui'=>-14097,'run'=>-14094,'ruo'=>-14092,  
        'sa'=>-14090,'sai'=>-14087,'san'=>-14083,'sang'=>-13917,'sao'=>-13914,'se'=>-13910,'sen'=>-13907,'seng'=>-13906,'sha'=>-13905,'shai'=>-13896,'shan'=>-13894,'shang'=>-13878,'shao'=>-13870,'she'=>-13859,'shen'=>-13847,'sheng'=>-13831,'shi'=>-13658,'shou'=>-13611,'shu'=>-13601,'shua'=>-13406,'shuai'=>-13404,'shuan'=>-13400,'shuang'=>-13398,'shui'=>-13395,'shun'=>-13391,'shuo'=>-13387,'si'=>-13383,'song'=>-13367,'sou'=>-13359,'su'=>-13356,'suan'=>-13343,'sui'=>-13340,'sun'=>-13329,'suo'=>-13326,  
        'ta'=>-13318,'tai'=>-13147,'tan'=>-13138,'tang'=>-13120,'tao'=>-13107,'te'=>-13096,'teng'=>-13095,'ti'=>-13091,'tian'=>-13076,'tiao'=>-13068,'tie'=>-13063,'ting'=>-13060,'tong'=>-12888,'tou'=>-12875,'tu'=>-12871,'tuan'=>-12860,'tui'=>-12858,'tun'=>-12852,'tuo'=>-12849,  
        'wa'=>-12838,'wai'=>-12831,'wan'=>-12829,'wang'=>-12812,'wei'=>-12802,'wen'=>-12607,'weng'=>-12597,'wo'=>-12594,'wu'=>-12585,  
        'xi'=>-12556,'xia'=>-12359,'xian'=>-12346,'xiang'=>-12320,'xiao'=>-12300,'xie'=>-12120,'xin'=>-12099,'xing'=>-12089,'xiong'=>-12074,'xiu'=>-12067,'xu'=>-12058,'xuan'=>-12039,'xue'=>-11867,'xun'=>-11861,  
        'ya'=>-11847,'yan'=>-11831,'yang'=>-11798,'yao'=>-11781,'ye'=>-11604,'yi'=>-11589,'yin'=>-11536,'ying'=>-11358,'yo'=>-11340,'yong'=>-11339,'you'=>-11324,'yu'=>-11303,'yuan'=>-11097,'yue'=>-11077,'yun'=>-11067,  
        'za'=>-11055,'zai'=>-11052,'zan'=>-11045,'zang'=>-11041,'zao'=>-11038,'ze'=>-11024,'zei'=>-11020,'zen'=>-11019,'zeng'=>-11018,'zha'=>-11014,'zhai'=>-10838,'zhan'=>-10832,'zhang'=>-10815,'zhao'=>-10800,'zhe'=>-10790,'zhen'=>-10780,'zheng'=>-10764,'zhi'=>-10587,'zhong'=>-10544,'zhou'=>-10533,'zhu'=>-10519,'zhua'=>-10331,'zhuai'=>-10329,'zhuan'=>-10328,'zhuang'=>-10322,'zhui'=>-10315,'zhun'=>-10309,'zhuo'=>-10307,'zi'=>-10296,'zong'=>-10281,'zou'=>-10274,'zu'=>-10270,'zuan'=>-10262,'zui'=>-10260,'zun'=>-10256,'zuo'=>-10254  
    );  
  
    /** 
     * 将中文编码成拼音 
     * @param string $utf8Data utf8字符集数据 
     * @param string $sRetFormat 返回格式 [head:首字母|all:全拼音] 
     * @return string 
     */  
    public static function encode_utf8($utf8Data, $sRetFormat='head_first'){  
        $sGBK = iconv('UTF-8', 'GBK', $utf8Data);  
        return self::encode_gbk($sGBK, $sRetFormat);
    }  
    public static function encode_gbk($sGBK, $sRetFormat='head_first'){ 
        $aBuf = array();  
        for ($i=0, $iLoop=strlen($sGBK); $i<$iLoop; $i++) {  
            $iChr = ord($sGBK{$i});  
            if ($iChr>160)  
                $iChr = ($iChr<<8) + ord($sGBK{++$i}) - 65536;  
            if ('head' === $sRetFormat || 'head_first' === $sRetFormat)  
                $aBuf[] = substr(self::zh2py($iChr),0,1);  
            else  
                $aBuf[] = self::zh2py($iChr);  
            if('head_first' === $sRetFormat){
                break;
            }
        }  
        if ('head' === $sRetFormat)  
            return implode('', $aBuf);  
        else  
            return implode(' ', $aBuf); 
    }
    /** 
     * 中文转换到拼音(每次处理一个字符) 
     * @param number $iWORD 待处理字符双字节 
     * @return string 拼音 
     */  
    private static function zh2py($iWORD) {  
        if($iWORD>0 && $iWORD<160 ) {  
            return chr($iWORD);  
        } elseif ($iWORD<-20319||$iWORD>-10247) {  
            return '';  
        } else {  
            foreach (self::$_aMaps as $py => $code) {  
                if($code > $iWORD) break;  
                $result = $py;  
            }  
            return $result;  
        }  
    }  
}

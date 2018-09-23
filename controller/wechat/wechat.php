<?php
include '../core/db.php';
function https_request($url,$data=""){
    // 开启curl
    $ch=curl_init();
    // 设置传输选项
    // 设置传输地址
    curl_setopt($ch, CURLOPT_URL, $url);
    // 以文件流的形式返回
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    /**/
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书下同
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);


    if ($data) {
        // 以post方式
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $headers = array("Content-type: application/json;charset=UTF-8","Accept: application/json","Cache-Control: no-cache", "Pragma: no-cache");

    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

    // 发送curl
    $request=curl_exec($ch);
    $tmpArr=json_decode($request,TRUE);
    if (is_array($tmpArr)) {
        return $tmpArr;
    }else{
        return $request;
    }
    // 关闭资源
    curl_close($ch);
}

class wechat extends db
{
    public function user_feed(){
        $openid = $_GET['openid'];
        $page = (int)$_GET['page'];
        $sql = 'select * from feed where openid = "'.$openid.'" order by time desc limit 10 offset '.($page - 1) * 10;
        $user_feed = $this->pdo->query($sql)->fetchAll();
        echo json_encode($user_feed);
    }
    public function feed()
    {
        $page = $_GET['page'];
        $feed = $this->pdo
            ->query( 'select * from feed order by time desc limit 5 offset ' . ($page - 1) * 5)
            ->fetchAll();
        echo json_encode($feed);
    }

    public function insert()
    {
        $code = $_GET['code'];
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=wx55e17ecfb6ee246c&secret=9451bef622af0035e02dd7ef56e32dc8&js_code='.$code.'&grant_type=authorization_code';
        $result = https_request($url);

        $openid = $result['openid'];

        $stmt = $this->pdo->prepare("insert into feed(openid,name,avatar,content,address,images)values(?,?,?,?,?,?)");
        $stmt->bindValue(1, $openid);
        $stmt->bindValue(2, $_GET['name']);
        $stmt->bindValue(3, $_GET['avatar']);
        $stmt->bindValue(4, $_GET['content']);
        $stmt->bindValue(5, '太原.学府街');
        $stmt->bindValue(6, $_GET['image']);

        echo $stmt->execute();
    }

//
    public function upload()
    {
        $src = $_FILES['f']['tmp_name'];
        $file_name = $_FILES['f']['name'];
        $dist = './assets/wechat/'.$file_name;
        move_uploaded_file($src,$dist);
        echo 'http://news.headline.com/assets/wechat/'.$file_name;
    }
}
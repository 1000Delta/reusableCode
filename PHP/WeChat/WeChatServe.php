<?php
/**
 * Created by PhpStorm.
 * User: 13975
 * Date: 2018/5/24
 * Time: 18:24
 */

namespace MyClass\WeChat;

class WeChatServe
{

    private $token;
    private $dbc;
//    private $text = "<xml>
//                     <ToUserName><![CDATA[%s]]></ToUserName>
//                     <FromUserName><![CDATA[%s]]></FromUserName>
//                     <CreateTime>%s</CreateTime>
//                     <MsgType><![CDATA[text]]></MsgType>
//                     <Content><![CDATA[%s]]></Content>
//                     </xml>",
//            $image = "<xml>
//                      <ToUserName><![CDATA[%s]]></ToUserName>
//                      <FromUserName>< ![CDATA[%s]]></FromUserName>
//                      <CreateTime>%s</CreateTime>
//                      <MsgType><![CDATA[image]]></MsgType>
//                      <Image>
//                          <MediaId><![CDATA[%s]]></MediaId>
//                      </Image>
//                      </xml>",
//            $voice = "<xml>
//                      <ToUserName><![CDATA[%s]]></ToUserName>
//                      <FromUserName><![CDATA[%s]]></FromUserName>
//                      <CreateTime>%s</CreateTime>
//                      <MsgType><![CDATA[voice]]></MsgType>
//                      <Voice>
//                          <MediaId><![CDATA[%s]]></MediaId>
//                      </Voice>
//                      </xml>",
//            $video = "<xml>
//                      <ToUserName>< ![CDATA[%s] ]></ToUserName>
//                      <FromUserName>< ![CDATA[%s] ]></FromUserName>
//                      <CreateTime>%s</CreateTime>
//                      <MsgType>< ![CDATA[video] ]></MsgType>
//                      <Video>
//                          <MediaId>< ![CDATA[%s] ]></MediaId>
//                          <Title>< ![CDATA[%s] ]></Title>
//                          <Description>< ![CDATA[%s] ]></Description>
//                      </Video>
//                      </xml>",
//            $music = "<xml>
//                      <ToUserName>< ![CDATA[%s] ]></ToUserName>
//                      <FromUserName>< ![CDATA[%s] ]></FromUserName>
//                      <CreateTime>12345678</CreateTime>
//                      <MsgType>< ![CDATA[music] ]></MsgType>
//                      <Music>
//                          <Title>< ![CDATA[%s] ]></Title>
//                          <Description>< ![CDATA[%s] ]></Description>
//                          <MusicUrl>< ![CDATA[%s] ]></MusicUrl>
//                          <HQMusicUrl>< ![CDATA[%s] ]></HQMusicUrl>
//                          <ThumbMediaId>< ![CDATA[%s] ]></ThumbMediaId>
//                      </Music>
//                      </xml>",
//            $news = "<xml>
//                    <ToUserName>< ![CDATA[%s] ]></ToUserName>
//                    <FromUserName>< ![CDATA[%s] ]></FromUserName>
//                    <CreateTime>%s</CreateTime>
//                    <MsgType>< ![CDATA[news] ]></MsgType>
//                    <ArticleCount>%s</ArticleCount>
//                    <Articles>
//                        <item>
//                            <Title>< ![CDATA[%s] ]></Title>
//                            <Description>< ![CDATA[%s] ]></Description>
//                            <PicUrl>< ![CDATA[%s] ]></PicUrl>
//                            <Url>< ![CDATA[%s] ]></Url>
//                        </item>
//                        <item>
//                            <Title>< ![CDATA[%s] ]></Title>
//                            <Description>< ![CDATA[%s] ]></Description>
//                            <PicUrl>< ![CDATA[%s] ]></PicUrl>
//                            <Url>< ![CDATA[%s] ]></Url>
//                        </item>
//                    </Articles>
//                    </xml>";


    public function __construct(string $token) {

        $this->token = $token;
        $this->dbc = new \MyClass\Database\DBC(1, '127.0.0.1', 'root', '1000Delta', 'wechat');
        $this->dbc->connect();
        $this->dbc->query("SET NAMES UTF8");

    }

    public function portal()
    {

        $info = [$_GET['nonce'], $_GET['timestamp'], $this->token];
        $echostr = $_GET['echostr'];

        sort($info, SORT_STRING);
        $hashcode = sha1(implode($info));

        if ($hashcode === $_GET['signature']) {

            echo $echostr;
            exit;
        } else {

            echo "";
            exit;
        }
    }

    public function processMsg(string $rawData) {

        $data = simplexml_load_string($rawData);
        $user = $data->FromUserName;
        $createTime = $data->CreateTime;
        $msgType = $data->MsgType;
        $content = $data->Content;
        $msgId = $data->MsgId;


        $exist = $this->dbc->select('getmessage', ['*'], 'WHERE msgId='.$msgId);

        if (!isset($exist) || isset($exist) && $exist->rowCount() === 0) {

            $this->dbc->insert('getmessage', [
                'createTime'=>$createTime,
                'msgId'=>$msgId,
                'msgType'=>$msgType,
                'user'=>$user
            ]);
            $this->dbc->insert('gettext'.$msgType, [
                'msgId'=>$msgId,
                'content'=>$content
            ]);

            $this->replyMSg($msgType, $data);
        } else {

            echo '';
            exit;
        }
    }

    private function replyMsg(string $msgType, \SimpleXMLElement $data) { //回复信息，调用内部方法

        switch ($msgType) {

            case 'text':
                $this->replyText($data);
                break;
//            case 'image':
//                $this->replyImage($data);
//                break;
//            case 'voice':
//                $this->replyVoice($data);
//                break;
//            case 'video':
//                $this->replyVideo($data);
//                break;
//            case 'music':
//                $this->replyMusic($data);
//                break;
//            case 'news':
//                $this->replyNews($data);
//                break;
            default :
                return -1;
                break;
        }

        return 0;
    }

    private function replyText(\SimpleXMLElement $data) { //回复文字消息

        $res = $this->dbc->select('reply', ["response"], "WHERE request=\"{$data->Content}\"");

        if (isset($res) && $res->rowCount() !== 0) {

            echo sprintf($res->fetch()[0], $data->FromUserName, $data->ToUserName, time());
        } else {

            $res = $this->dbc->select("reply", ["response"], "WHERE request=\"default\"");
            echo sprintf($res->fetch()[0],
                $data->FromUserName,
                $data->ToUserName,
                time());
        }

    }

//    private function replyImage(\SimpleXMLElement $data) { //回复图片消息
//
//        echo "";
//        exit;
//    }
//
//    private function replyVoice(\SimpleXMLElement $data) { //回复语音消息
//
//        echo "";
//        exit;
//    }
//
//    private function replyVideo(\SimpleXMLElement $data) { //回复视频消息
//
//        echo "";
//        exit;
//    }
//
//    private function replyMusic(\SimpleXMLElement $data) { //回复音乐消息
//
//        echo "";
//        exit;
//    }
//
//    private function replyNews(\SimpleXMLElement $data) { //回复图文消息
//
//        echo "";
//        exit;
//    }
}
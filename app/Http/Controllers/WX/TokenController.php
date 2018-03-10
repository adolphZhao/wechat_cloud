<?php

namespace App\Http\Controllers\WX;

use App\Http\Controllers\Controller;

use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;

use GuzzleHttp\Client;


class TokenController extends Controller
{
    protected $httpClient;

    public function __construct(Client $client)
    {
        $this->httpClient = $client;
    }

    public function token(Request $request)
    {
        $signature = $request->input('signature');
        $timestamp = $request->input('timestamp');
        $echostr = $request->input('echostr');
        $nonce = $request->input('nonce');
        $token = 'c4ca4238a0b923820d';

        $list = [
            $token,
            $timestamp,
            $nonce
        ];
        sort($list);
        $signatureSHA1 = sha1(implode($list));

        if ($signature == $signatureSHA1) {
            echo $echostr;
        }
        file_put_contents(storage_path('wx.log'), $signatureSHA1 . '===' . $signature, 8);
        echo '';
        exit;

    }

    function xmlToArray($xml)
    {

        libxml_disable_entity_loader(true);

        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $val = json_decode(json_encode($xmlstring), true);

        return $val;

    }

    function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * <xml>
     * <ToUserName><![CDATA[toUser]]></ToUserName>
     * <FromUserName><![CDATA[FromUser]]></FromUserName>
     * <CreateTime>123456789</CreateTime>
     * <MsgType><![CDATA[event]]></MsgType>
     * <Event><![CDATA[CLICK]]></Event>
     * <EventKey><![CDATA[EVENTKEY]]></EventKey>
     * </xml>
     */
    public function message()
    {
        $content = $this->xmlToArray(file_get_contents('php://input'));
        $toUser = array_get($content, 'ToUserName');
        $fromUser = array_get($content, 'FromUserName');
        $createTime = array_get($content, 'CreateTime');
        $msgType = array_get($content, 'MsgType');
        $msgCTX = array_get($content, 'Content');
        array_get($content, 'MsgId');

        $resp = [];
        switch ($msgType) {
            case 'text':
                $msg = $this->other($msgCTX);
                array_set($resp, 'Content', $msg);
                break;
            case 'event':
                $eventKey = array_get($content, 'EventKey');

                switch ($eventKey) {
                    case 'V1001_START_SERVER':
                        $this->startServer();
                        array_set($resp, 'Content', 'SERVER START');
                        break;
                    case 'V1002_STOP_SERVER':
                        $this->stopServer();
                        array_set($resp, 'Content', 'SERVER STOP');
                        break;
                    case 'V1003_01_STAT_USERS':
                        $msg = $this->stat();
                        array_set($resp, 'Content', $msg);
                        break;
                    case 'V1003_02_UPLOAD_IMGS':
                        array_set($resp, 'Content', 'UPLOADED');
                        break;
                    case 'V1004_STOP_GUIDE':
                        $this->stopGuide();
                        array_set($resp, 'Content', 'GUIDE STOP');
                        break;
                    case 'V1003_START_GUIDE':
                        $this->startGuide();
                        array_set($resp, 'Content', 'GUIDE START');
                        break;
                    case 'V1005_HOST_LIST':
                        $hosts = $this->hosts();
                        array_set($resp, 'Content', $hosts);
                        break;
                }

                break;
        }

        array_set($resp, 'ToUserName', $fromUser);
        array_set($resp, 'FromUserName', $toUser);
        array_set($resp, 'CreateTime', $createTime);
        array_set($resp, 'MsgType', 'text');

        $msg = $this->arrayToXml($resp);
        $this->debug($msg);
        echo $msg;
        exit;
    }

    protected function debug($message)
    {
        file_put_contents('/tmp/wx-msg-debug.log', json_encode($message), 8);
    }

    protected function stopServer()
    {
        \Cache::put('SERVER_STATUS', 'STOP', 3600 * 24);
        return 'STOP';
    }

    protected function startServer()
    {
        \Cache::forget('SERVER_STATUS', 'STOP');
        return 'START';
    }

    protected function stopGuide()
    {
        \Cache::put('STOP_GUIDE', 'STOP', 3600 * 24);
        return 'STOP';
    }

    protected function startGuide()
    {
        \Cache::forget('STOP_GUIDE', 'STOP');
        return 'START';
    }

    protected function stat()
    {
        $s = exec("cat /tmp/user-ip.log|grep `date +%Y%m%d` |awk -F ',' '{print $1}' |awk  '{print $1}' |sort -u |wc -l ");
        return $s;
    }

    protected function other($msgCTX)
    {
        $response = $this->httpClient->post('http://openapi.tuling123.com/openapi/api/v2', [
            RequestOptions::JSON => [
                "reqType" => 0,
                "perception" => [
                    "inputText" => [
                        "text" => $msgCTX
                    ],
                    "inputImage" => [
                        "url" => "imageUrl"
                    ],
                    "selfInfo" => [
//                        "location" => [
//                            "city" => "北京",
//                            "province" => "北京",
//                            "street" => "信息路"
//                        ]
                    ]
                ],
                "userInfo" => [
                    "apiKey" => "ebaa90dea958421384a9dc8c4bd32760",
                    "userId" => "228065"
                ]
            ]]);

        $results = @json_decode($response->getBody()->getContents(), true);
        $text = '';
        foreach (array_get($results, 'results') as $result) {
            if (array_get($result, 'resultType') == 'text') {
                $text .= ' ' . array_get($result, 'values.text');
            } elseif (array_get($result, 'resultType') == 'url') {
                $text .= ' ' . array_get($result, 'values.url');
            }
        }
        return $text;
    }

    protected function hosts()
    {
        $hosts = \DB::select("select a.hits,b.hosts as domain from wechat_public_domain_states a join wechat_public_config_hosts b on a.host_id=b.id where a.status=0 and a.guide_status=1 order by a.hits asc");
        $retStr = "可用域名:\n";
        foreach ($hosts as $host) {
            $retStr .= $host->domain;
            $retStr .= " => ";
            $retStr .= $host->hits;
            $retStr .= "\n";

        }
        return $retStr;
    }
}
<?php

namespace biqu\TicketPlatform\Dx;

use biqu\TicketPlatform\ApiParamManager;
use biqu\TicketPlatform\Dx\Builder;
use biqu\TicketPlatform\DataFormat;

class API
{
    protected $authCode;

    protected $data;

    protected $url;

    protected $callMethodName;

    protected $api = [
        'test'      => 'http://api.platform.yinghezhong.com/',
        'online'    => 'http://api.open.yinghezhong.com/',
    ];
    protected $memberApi =[
        'test'      => 'http://mapi.platform.yinghezhong.com/',
        'online'    => 'http://mapi.open.yinghezhong.com/',
    ];


    public function __construct(ApiParamManager $apiParamManager)
    {
        $this->apiParamManager = $apiParamManager;
        $this->authCode = $apiParamManager->get('config.pAuthCode');

        if (getenv('APP_ENV') != 'online') {
            $this->url = $this->api['test'];
        } else {
            $this->url = $this->api['online'];
        }
    }

    /**
     * 设置请求地址
     * wenqiang
     * 2017-05-04T15:08:45+0800
     * @param [type] $url [description]
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * 设置请求地址为会员地址
     * wenqiang
     * 2017-05-08T17:52:10+0800
     */
    public function setMemberUrl()
    {
        if (getenv('APP_ENV') != 'online') {
            $this->url = $this->memberApi['test'];
        } else {
            $this->url = $this->memberApi['online'];
        }

        return $this;
    }

    /**
     * 请求参数
     * wenqiang
     * 2017-04-28T18:21:42+0800
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function withData($data)
    {
        $this->data = array_merge(['format'  =>   'json'], $data);
        return $this;
    }

    /**
     * 设置数据返回格式
     * wenqiang
     * 2017-05-03T14:18:19+0800
     * @param [type] $format [description]
     */
    public function setFormat($format)
    {
        $this->data['format'] = $format;
        return $this;
    }

    /**
     * 设置请求方法
     * wenqiang
     * 2017-05-03T14:31:07+0800
     * @param [type] $callMethod [description]
     */
    public function setCallMethod($callMethodName)
    {
        $this->callMethodName = $callMethodName;
        return $this;
    }

    /**
     * 发送请求
     * wenqiang
     * 2017-05-03T13:59:34+0800
     * @return [type] [description]
     */
    public function send()
    {
        $sign = $this->getSign($this->data);
        $params = array_merge($this->data, ['_sig' => $sign]);

        $t1  = microtime(true);

        $ret = (new Builder)
            ->to($this->url . $this->callMethodName)
            ->withHeader('Content-Encoding: gzip')
            ->withData($params)
            ->get();

        $time = round(microtime(true) - $t1, 3);

        \Log::info([
                    '请求售票平台'        =>  'DX',
                    '请求接口时间'        =>  $time . '秒',
                    '请求接口方法'        =>  $this->callMethodName,
                    '请求参数'           =>  $params,
                    '请求URL'            =>  $this->url . $this->callMethodName .  '?' . http_build_query($params)
                ]);

        return (new DataFormat($ret));
    }

    /**
     *
     * wenqiang
     * 2017-04-28T18:03:50+0800
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function getSign($params)
    {
        ksort($params);
        return md5(md5($this->authCode . urldecode(http_build_query($params))) . $this->authCode);
    }
}
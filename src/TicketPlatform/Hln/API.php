<?php

namespace biqu\TicketPlatform\Hln;

use biqu\TicketPlatform\Hln\Builder;
use biqu\TicketPlatform\DataFormat;
use biqu\TicketPlatform\ApiParamManager;

class API
{
    /**
     * 统一接口传值
     * @var [type]
     */
    public $clientParams;

    /**
     * 影院ID
     * @var [type]
     */
    protected $cinemaId;

    /**
    *  影院配置信息
     * @var [type]
     */
    protected $appCode;
    protected $token;

    /**
     * 火炼鸟接口地址（对应关系）
     * @var string
     */
    protected $apiAddr = [
        'test'      =>  'http://test.loongcinema.com:8094/api/',
        'online'    =>  'http://api.loongcinema.com:6620/api/',
    ];

    /**
     * 火炼鸟接口地址
     * @var string
     */
    protected $url;

    /**
     * 调用方法名称
     * @var [type]
     */
    protected $callMethodName;

    /**
     * 请求回来的数据为json或xml格式
     *
     * @var [type]
     */
    protected $jsonOrXml;
    /**
     * 请求接口的参数数组
     * @var array
     */
    protected $data = [];
    /**
     * 设置接口参数
     * wenqiang
     * 2017-03-14T17:55:27+0800
     * @param array $clientParams [description]
     */
    public function __construct(ApiParamManager $apiParamManager)
    {

        $this->cinemaId = $apiParamManager->get('config.pCinemaID');

        $this->channelCode  = $apiParamManager->get('config.pChannelCode');
        $this->secretkey    = $apiParamManager->get('config.pSecretKey');

        if (getenv('APP_ENV') != 'online') {
            $this->url = $this->apiAddr['test'];
        } else {
            $this->url = $this->apiAddr['online'];
        }
    }

    /**
     * 调用方法
     * wenqiang
     * 2017-03-14T17:29:46+0800
     * @param  string $callMethodName 方法名
     * @return this
     */
    public function setCallMethod($callMethodName)
    {
        $this->callMethodName = $callMethodName;
        return $this;
    }

    /**
     * 请求参数
     * wenqiang
     * 2017-03-14T17:32:20+0800
     * @param  array $params 参数数组
     * @return this
     */
    public function withData($params)
    {
        $this->data = $params;
        return $this;
    }

    /**
     * 设置请求方法的版本
     * @Author   wenqiang
     * @DateTime 2017-08-05T22:38:35+0800
     * @version  [version]
     * @param    [type]                   $version [description]
     */
    public function setFuncVersion($version = 'v1')
    {
    	$this->url = $this->url . $version . '/';
    	return $this;
    }

    /**
     * 设置售票级别请求接口地址
     * @Author   wenqiang
     * @DateTime 2017-08-05T22:35:28+0800
     * @version  [version]
     */
    public function setTicketUrl()
    {
    	$this->url = $this->url . 'ticket/';
    	return $this;
    }

    /**
     * 设置会员级别请求接口地址
     * @Author   wenqiang
     * @DateTime 2017-08-05T22:35:37+0800
     * @version  [version]
     */
    public function setMemberUrl()
    {
    	$this->url = $this->url . 'member/';
    	return $this;
    }

    /**
     * [send description]
     * @Author   wenqiang
     * @DateTime 2017-08-05T22:33:55+0800
     * @version  [version]
     * @param    string                   $method [description]
     * @return   [type]                           [description]
     */
    public function send($method = 'get')
    {
        $api = $this->url . $this->callMethodName . '.json';
        $sign = $this->makeSign($this->data);

        $t1  = microtime(true);
        $data = array_merge([
                    'channelCode' => $this->channelCode,
                    'sign'        => $sign,
                ], $this->data);

        $ret = (new Builder)->to($api)->withData($data)->{$method}();
        $time = round(microtime(true) - $t1, 3);
        $fun = ['queryShows', 'queryShowSeats'];
        \Log::info([
                    '请求售票平台'        =>  '火烈鸟',
                    '请求接口时间'        =>  $time . '秒',
                    '请求接口方法'        =>  $this->callMethodName,
                    '请求地址'           =>  $api,
                    '请求参数'           =>  $data,
                    '请求URL'            => $api .  '?' . http_build_query($data),
                    '返回参数'           =>  in_array($this->callMethodName, $fun) ?
                                            '' :
                                            (new DataFormat($ret))->toArray()
                ]);


        return (new DataFormat($ret));
    }

    /**
     * 生成签名
     * @Author   wenqiang
     * @DateTime 2017-08-08T10:49:07+0800
     * @version  [version]
     * @param    [type]                   $params [description]
     * @return   [type]                         [description]
     */
    public function makeSign($params)
    {
        $data = array_merge(['channelCode' => $this->channelCode], $params);

        ksort($data);
        $query = urlencode(urldecode(http_build_query($data)));

        $sign = md5($this->secretkey . $query . $this->secretkey);
        return $sign;
    }
}

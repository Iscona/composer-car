<?php

namespace biqu\TicketPlatform\M1905;

use biqu\TicketPlatform\M1905\Builder;
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
     * 1905接口地址（对应关系）
     * @var string
     */
    protected $apiAddr = [
        'test'      =>  'http://211.144.5.200:23380/Api/', //http://testnetsale.m1905.com:23380/Api/
        'online'    =>  'http://netsale.1905.com/Api/',
    ];

    /**
     * 1905接口地址
     * @var string
     */
    protected $url;

    /**
     * 调用方法名称
     * @var [type]
     */
    protected $callMethodName;

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

        $this->appCode  = $apiParamManager->get('config.pAppCode');
        $this->token    = $apiParamManager->get('config.pToken');

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

    public function send($method = 'post')
    {
        $api = $this->url . $this->callMethodName;
        $data = array_merge($this->data, ['pVerifyInfo' => $this->setVerifyInfo()]);

        $t1  = microtime(true);
        $ret = (new Builder)->to($api)->withData($data)->{$method}();

        $time = round(microtime(true) - $t1, 3);
        $fun = ['GetCinemaAllSession', 'GetScreenSeat', 'GetSessionSeat'];
        \Log::info([
            '请求售票平台'    =>  '1905',
            '请求接口时间'    =>  $time . '秒',
            '请求接口方法'    =>  $this->callMethodName,
            '请求接口地址'    =>  $this->url,
            '请求参数'       =>  $data,
            '返回参数'       =>  in_array($this->callMethodName, $fun) ?
                                '' :
                                (new DataFormat($ret))->xmlToArray()
            ]);

        return (new DataFormat($ret));
    }

    /**
     * 校验信息
     * wenqiang
     * 2017-03-14T18:06:38+0800
     * @param [type] $params [description]
     */
    public function setVerifyInfo()
    {
        return md5(join($this->data) . $this->token);
    }
}

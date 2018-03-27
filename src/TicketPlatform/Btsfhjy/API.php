<?php

namespace biqu\TicketPlatform\Btsfhjy;

use SoapClient;
use biqu\TicketPlatform\DataFormat;
use biqu\TicketPlatform\ApiParamManager;
use biqu\TicketPlatform\Btsfhjy\Builder;

class API
{
    const NUMBER_TYPE_CARD = 0;
    const NUMBER_TYPE_ACCOUNT = 1;

    /**
     * 会员接口集合
     * @var array
     */
    protected $urls = [
        'test'    => 'http://loc.bts-fhjy.biqu.tv/api',
        'online'  => 'http://inside.bts-fhjy.biqu.tv/api',
    ];


    // 会员
    protected $partnerCode;
    protected $partnerkey;

    // 票务
    protected $appCode;
    protected $secretKey;
    protected $tokenId;
    protected $token;
    protected $cinemaId;

    // 最终接口地址
    protected $url = '';

    // 使用旧或者老接口地址
    protected $oldOrNewUrl;

    // 请求校验信息
    protected $verifyInfo;

    // 客户端请求参数
    public $clientParams;

    // 发送数据
    protected $data = [];

    // 远程调用方法名
    protected $callMethodName;

    protected $apiParamManager;
    /**
     * 初始化配置
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-09T22:56:46+0800
     *
     * @param   array   $clientParams   请求参数
     */
    public function __construct(ApiParamManager $apiParamManager)
    {
        // 影院ID
        $this->cinemaId     = $apiParamManager->get('config.pCinemaID');
    }

    /**
     * 设置请求票务接口地址
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-09T23:21:07+0800
     *
     * @return this
     */
    public function setTicketUrl()
    {
         if (getenv('APP_ENV') == 'online') {
            $url = $this->urls['online'];
        } else if (getenv('APP_ENV') == 'online-test'){
            $url = $this->urls['online'];
        } else {
            $url = $this->urls['test'];
        }

        $this->url = $url;

        return $this;
    }  

    /**
     * 设置请求数据
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-09T23:51:14+0800
     *
     * @param    array $data
     * @return   this
     */
    public function withData($data)
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * 设置调用远程方法
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-10T00:00:32+0800
     *
     * @param    string $name
     * @return   this
     */
    public function callMethod($name)
    {
        $this->callMethodName = $name;

        return $this;
    }

    /**
     * 发送请求
     *
     * wenqiang
     * 2017-03-11T16:26:48+0800
     * @param  boolean $mergeOrNot  是否合并校验信息(XML数据不需要合并)
     * @return [type]              [description]
     */
    public function send()
    {
        $api = $this->url . $this->callMethodName;
        $data = $this->data;

        $t1  = microtime(true);
        $ret = (new Builder)->to($api)->withData($data)->post();
        $time = round(microtime(true) - $t1, 3);
        
        \Log::info([
                    '请求售票平台' => 'Btsfhjy',
                    '请求接口时间' => $time . '秒',
                    '请求接口地址' => $api,
                    '请求参数'  =>  $data,
                    '返回数据'  =>  (new DataFormat($ret))
                ]);

        return (new DataFormat($ret));
    }
}

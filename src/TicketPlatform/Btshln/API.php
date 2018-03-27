<?php

namespace biqu\TicketPlatform\Btshln;

use biqu\TicketPlatform\Btshln\Builder;
use biqu\TicketPlatform\DataFormat;
use biqu\TicketPlatform\ApiParamManager;

class API
{

    /**
     * 火炼鸟接口地址（对应关系）
     * @var string
     */
    protected $urls = [
        'test'    => 'http://loc.bts-hln.biqu.tv/api',
        'online'  => 'http://inside.bts-hln.biqu.tv/api',
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
    // public function __construct(ApiParamManager $apiParamManager)
    // {

    //     $this->cinemaId = $apiParamManager->get('config.pCinemaID');

    //     $this->channelCode  = $apiParamManager->get('config.pChannelCode');
    //     $this->secretkey    = $apiParamManager->get('config.pSecretKey');

    //     if (getenv('APP_ENV') != 'online') {
    //         $this->url = $this->apiAddr['test'];
    //     } else {
    //         $this->url = $this->apiAddr['online'];
    //     }
    // }

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

    public function send()
    {
        $api = $this->url . $this->callMethodName;
        $data = $this->data;

        $t1  = microtime(true);
        $ret = (new Builder)->to($api)->withData($data)->post();
        $time = round(microtime(true) - $t1, 3);
        $fun = ['/schedule', '/seat/info'];
        \Log::info(['请求售票平台' => 'Bts火烈鸟',
                    '请求接口时间' => $time . '秒',
                    '请求接口地址' => $api,
                    '请求参数'  =>  $data,
                    '返回数据'  =>  in_array($this->callMethodName, $fun) ?
                                    '' :
                                    json_decode((new DataFormat($ret)), true)]);

        return (new DataFormat($ret));
    }
}

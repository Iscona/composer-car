<?php

namespace biqu\TicketPlatform\Fhjy;

use biqu\TicketPlatform\ApiParamManager;
use biqu\TicketPlatform\DataFormat;

class API
{
    /**
     * 参数管理器
     * @var [type]
     */
    protected $apiParamManager;

    /**
     * 请求接口名称
     * @var [type]
     */
    protected $callMethodName;

    /**
     * 接口版本
     * @var [type]
     */
    protected $version;

    /**
     * 渠道代码
     * @var [type]
     */
    protected $channelCode;

    /**
     * app密钥
     * @var [type]
     */
    protected $appSecretKey;

    /**
     * 签名
     * @var [type]
     */
    protected $sign;

    public $apiCenter;

    /**
     * 测试地址和生产环境地址
     * @var [type]
     */
    protected $apiAddr = [
        'test'      =>  [
            'A' =>  'http://hello.ykse.com.cn:28080/route',
            'B' =>  'http://hello.ykse.com.cn:60808/route2',
        ],
        'online'    =>  [
            'A' =>  'http://mcop.yuekeyun.com/route',
            'B' =>  '',
        ],
    ];
    /**
     * 请求地址
     * @var [type]
     */
    protected $url;

    /**
     * [__construct description]
     * wenqiang
     * 2017-04-14T14:39:59+0800
     * @param ApiParamManager $apiParamManager [description]
     */
    public function __construct(ApiParamManager $apiParamManager)
    {
        $this->apiParamManager = $apiParamManager;

        $this->version      = $apiParamManager->get('config.pVersion');
        $this->channelCode  = $apiParamManager->get('config.pChannelCode');
        $this->appSecretKey = $apiParamManager->get('config.pSecretKey');
        $this->apiCenter    = ucfirst($apiParamManager->get('config.pApiCenter'));

        if (getenv('APP_ENV') != 'online') {
            $this->url = $this->apiAddr['test'][$this->apiCenter];
        } else {
            $this->url = $this->apiAddr['online'][$this->apiCenter];
        }
    }

    /**
     *  设置请求地址
     * wenqiang
     * 2017-04-13T18:20:13+0800
     */
    public function setCallMethod($callMethodName)
    {
        $maps = [
            'ykse.partner.order.confirmOrderForMultiPay' => [
                'A' => 'ykse.partner.order.confirmOrderForMultiPay',
                'B' => 'ykse.lark.partner.order.confirmOrder',
            ],
            'ykse.partner.order.getTicketInfo'  =>  [
                'A' =>  'ykse.partner.order.getTicketInfo',
                'B' =>  'ykse.lark.partner.order.getPrintInfo',
            ],
        ];

        if ($this->apiCenter == 'B') {
            if (isset($maps[$callMethodName])) {
                $this->callMethodName = $maps[$callMethodName][$this->apiCenter];
            } else {
                $this->callMethodName = str_replace('ykse.', 'ykse.lark.', $callMethodName);
            }
        } else {
            $this->callMethodName = $callMethodName;
        }
        return $this;
    }

    /**
     * 设置业务参数
     * wenqiang
     * 2017-04-13T18:22:55+0800
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function withData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 发送请求
     * wenqiang
     * 2017-04-13T18:22:45+0800
     * @return [type] [description]
     */
    public function send()
    {
        $params = [
            'api'           =>  $this->callMethodName,
            'channelCode'   =>  $this->channelCode,
            'data'          =>  $this->data ? json_encode($this->data) : json_encode($this->data, JSON_FORCE_OBJECT),
            'timestamp'     =>  number_format(microtime(true), 3, '', ''),
            'v'             =>  $this->version,
            'sign'          =>  '',
        ];
        
        $params['sign'] = $this->getSign($params);

        $t1  = microtime(true);

        $ret = (new Builder)->to($this->url)
                            ->withHeader('Content-Encoding: gzip')
                            ->withData($params)
                            ->get();
        $time = round(microtime(true) - $t1, 3);

        $fun = [
                'ykse.partner.cinema.getCinemas',
                'ykse.partner.schedule.getSchedules',
                'ykse.partner.seat.getSeats',
                'ykse.partner.seat.getScheduleAreaSeats',
                'ykse.partner.seat.getScheduleSoldSeats'
            ];
        \Log::info([
                    '请求售票平台'        =>  'FHJY',
                    '请求接口时间'        =>  $time . '秒',
                    '请求接口方法'        =>  $this->callMethodName,
                    '请求参数'           =>  $params,
                    '请求URL'            =>  $this->url . '?' . http_build_query($params),
                    '返回数据'            =>  in_array($this->callMethodName, $fun) ?
                                             '' :
                                             (new DataFormat($ret))->toArray()
                ]);

        return (new DataFormat($ret));
    }

    /**
     * 签名算法
     * wenqiang
     * 2017-04-19T11:07:45+0800
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function getSign($params)
    {
        $string = '';
        unset($params['sign']);
        ksort($params);

        foreach ($params as $key => $val) {
            $string .= $key;
            $string .= $val;
        }

        return md5($string . $this->appSecretKey);
    }

    /**
     * 敏感数据加密算法
     * wenqiang
     * 2017-04-24T17:07:32+0800
     * @param  [type] $express [description]
     * @return [type]          [description]
     */
    public function sensitiveDataEnc($express)
    {
        $md5key = substr(strtolower(md5($this->appSecretKey)), 0, 16);

        $iv = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 16);

        $data = $express . md5($express);

        $str = base64_encode(openssl_encrypt($data, 'AES-128-CBC', $md5key, OPENSSL_RAW_DATA, $iv));

        return substr(base64_encode($iv), 0, 22) . $str;
    }
}


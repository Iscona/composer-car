<?php

namespace biqu\TicketPlatform\Btsmtx;

use SoapClient;
use biqu\TicketPlatform\DataFormat;
use biqu\TicketPlatform\ApiParamManager;
use biqu\TicketPlatform\Btsmtx\Builder;

class API
{
    const NUMBER_TYPE_CARD = 0;
    const NUMBER_TYPE_ACCOUNT = 1;

    /**
     * 会员接口集合
     * @var array
     */
    protected $urls = [
        'test'    => 'http://loc.bts-mtx.biqu.tv/api',
        'online'  => 'http://inside.bts-mtx.biqu.tv/api',
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

    // 请求校验信息
    protected $verifyInfo;

    // 客户端请求参数
    public $clientParams;

    // 发送数据
    protected $data = [];

    // 远程调用方法名
    protected $callMethodName;


    /**
     * SoapClient 默认参数
     * @var array
     */
    protected $soapOption = ['keep_alive' => false];

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

        // 票务
        $this->appCode      = $apiParamManager->get('config.pAppCode');
        $this->secretKey    = $apiParamManager->get('config.pSecretKey');
        $this->tokenId      = $apiParamManager->get('config.pTokenID');
        $this->token        = $apiParamManager->get('config.pToken');

        // 会员
        $this->partnerCode  = $apiParamManager->get('config.pPartnerCode');
        $this->partnerkey   = $apiParamManager->get('config.pPartnerKey');
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
     * 设置验证签名
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-09T23:16:43+0800
     *
     * @param   array   $params
     */
    public function setMemberVerifyInfo($params)
    {
        $info = $this->partnerCode . $this->cinemaId
                                   . join($params)
                                   . $this->partnerkey;

        $this->verifyInfo = [
            'validateKey' => substr(md5(strtolower($info)), 8, 24-8)];

        return $this;
    }

    /**
     * 设置验证签名
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-09T23:16:43+0800
     *
     * @param   array   $params
     */
    public function setTicketVerifyInfo($params)
    {
        $info = $this->appCode . join($params)
                               . $this->tokenId
                               . $this->token
                               . $this->secretKey;

        $this->verifyInfo = [
            'pVerifyInfo' => substr(md5(strtolower($info)), 8, 24-8)];

        return $this;
    }

    /**
     * 设置查询订单信息验证签名
     * wenqiang
     * 2017-03-11T17:47:09+0800
     * @param [type] $params [description]
     */
    public function setGetOrderMsgVerifyInfo($params)
    {
        $info = $this->appCode .
                $this->cinemaId .
                join($params) .
                $this->secretKey;

        $this->verifyInfo = [
            'pVerifyInfo' => substr(md5(strtolower($info)), 8, 24-8)];
        return $this;
    }

    /**
     * 锁座接口的XML数据处理
     *
     *
     * wenqiang
     * 2017-03-10T17:40:16+0800
     * @param  array $params
     * @return string
     */
    public function lockSeatParamsToXml($params)
    {
        $paramXmlTpl = '<?xml version="1.0"?>
                        <RealCheckSeatStateParameter>
                          <AppCode>'.$this->appCode.'</AppCode>
                          <CinemaId>'.$this->cinemaId.'</CinemaId>
                          <FeatureAppNo>%s</FeatureAppNo>
                          <SerialNum>%s</SerialNum>
                          <SeatInfos>
                            %s
                          </SeatInfos>
                          <PayType>%s</PayType>
                          <RecvMobilePhone>%s</RecvMobilePhone>
                          <TokenID>'.$this->tokenId.'</TokenID>
                          <VerifyInfo>%s</VerifyInfo>
                        </RealCheckSeatStateParameter>';

        $seatInfoXmlTpl = '<SeatInfo>
                              <SeatNo>%s</SeatNo>
                              <TicketPrice>%s</TicketPrice>
                              <Handlingfee>%s</Handlingfee>
                            </SeatInfo>';

        $seatInfo = join(array_map(function ($seat) use ($seatInfoXmlTpl) {
            return sprintf($seatInfoXmlTpl,
                           $seat['seat_no'],
                           $seat['ticket_price'],
                           $seat['handlingfee']);
        }, $params['seat_infos']));

        return sprintf($paramXmlTpl,
                       $params['feature_app_no'],
                       $params['serial_num'],
                       $seatInfo,
                       $params['pay_type'],
                       $params['recv_mobile_phone'],
                       $this->verifyInfo['pVerifyInfo']);
    }

    /**
     * 设定常规买票的xml数据
     * wenqiang
     * 2017-03-21T17:56:42+0800
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function sellTicketParamsToXml($params)
    {
        $paramXmlTpl = '<?xml version="1.0"?>
                        <SellTicketParameter>
                            <AppCode>'.$this->appCode.'</AppCode>
                            <CinemaId>'.$this->cinemaId.'</CinemaId>
                            <FeatureAppNo>%s</FeatureAppNo>
                            <SerialNum>%s</SerialNum>
                            <Printpassword>%s</Printpassword>
                            <Balance>%s</Balance>
                            <PayType>%s</PayType>
                            <RecvMobilePhone>%s</RecvMobilePhone>
                            <SendType>%s</SendType>
                            <PayResult>%s</PayResult>
                            <IsCmtsPay>%s</IsCmtsPay>
                            <IsCmtsSendCode>%s</IsCmtsSendCode>
                            <PayMobile>%s</PayMobile>
                            <BookSign>%s</BookSign>
                            <Payed>%s</Payed>
                            <SendModeID>%s</SendModeID>
                            <PaySeqNo>%s</PaySeqNo>
                            <TokenID>'.$this->tokenId.'</TokenID>
                            <VerifyInfo>%s</VerifyInfo>
                        </SellTicketParameter>';

        return sprintf($paramXmlTpl,
            $params['feature_app_no'],
            $params['serial_num'],
            $params['printpassword'],
            $params['balance'],
            $params['pay_type'],
            $params['Recv_mobile_phone'],
            $params['send_type'],
            $params['pay_result'],
            $params['is_cmts_pay'],
            $params['is_cmts_send_code'],
            $params['pay_mobile'],
            $params['book_sign'],
            $params['payed'],
            $params['send_mode_id'],
            $params['pay_seq_no'],
            $this->verifyInfo['pVerifyInfo']);
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
     * 设置卡号格式
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-10T17:52:14+0800
     *
     * @param    string     $id
     * @param    integer    $type
     * @return   string
     */
    public function cardId($id, $type)
    {
        if ($type == self::NUMBER_TYPE_CARD) {
            return $id;
        } elseif ($type == self::NUMBER_TYPE_ACCOUNT) {
            return '$'.$id;
        }
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
        $fun = ['/schedule', '/seat/info'];
        \Log::info(['请求售票平台：'   => 'Btsmtx',
                    '请求接口时间：'   => $time . '秒',
                    '请求接口地址：'   => $api,
                    '请求参数：'      => $data,
                    '返回数据'        => in_array($this->callMethodName, $fun) ?
                                        '' :
                                        json_decode((new DataFormat($ret)), true)]);

        return (new DataFormat($ret));
    }
}

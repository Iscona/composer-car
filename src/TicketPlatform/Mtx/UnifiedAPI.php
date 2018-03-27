<?php

namespace biqu\TicketPlatform\Mtx;

use biqu\APIInterface;
use biqu\TicketPlatform\Mtx\API;
use biqu\TicketPlatform\Mtx\Transform;
use biqu\TicketPlatform\ApiParamManager;

class UnifiedAPI implements APIInterface
{
    protected $apiUtil;

    protected $clientParams;

    protected $transform;

    protected $apiParamManager;

    /**
     * 初始化配置参数
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-09T23:03:45+0800
     *
     * @param   array   $clientParams         请求参数
     */
    public function __construct(ApiParamManager $apiParamManager)
    {
        $this->apiParamManager = $apiParamManager;
        $this->apiUtil = new API($this->apiParamManager);
        $this->transform = new Transform();
    }

    /**
     * 获得所有排期
     * wenqiang
     * 2017-03-09T11:12:02+0800
     * @return [type] [description]
     */
    public function getAllSchedule()
    {
        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('GetCinemaPlan')
                    ->setTicketVerifyInfo([$this->apiParamManager->get('config.pCinemaID')])
                    ->withData([
                        'pAppCode'  => $this->apiParamManager->get('config.pAppCode'),
                        'pCinemaID' => $this->apiParamManager->get('config.pCinemaID'),
                        'pPlanDate' => $this->apiParamManager->get('app.pPlanDate'),
                        'pTokenID'  => $this->apiParamManager->get('config.pTokenID'),
                    ])
                    ->send();
        return $this->transform->getAllSchedule($data);
    }

    /**
     * 获取座位信息
     * wenqiang
     * 2017-03-09T11:22:27+0800
     * @return [type] [description]
     */
    public function getSeatInfo()
    {
        $allSeat = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('GetHallAllSeat')
                    ->setGetOrderMsgVerifyInfo([
                        $this->apiParamManager->get('app.pScreenID')])
                    ->withData([
                        'pAppCode'      => $this->apiParamManager->get('config.pAppCode'),
                        'pCinemaID'     => $this->apiParamManager->get('config.pCinemaID'),
                        'pHallID'       => $this->apiParamManager->get('app.pScreenID'),
                    ])
                    ->send();

        $siteState = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('GetPlanSiteState')
                    ->setTicketVerifyInfo([
                        $this->apiParamManager->get('config.pCinemaID'),
                        $this->apiParamManager->get('app.pFeatureAppNo')])
                    ->withData([
                        'pAppCode'      => $this->apiParamManager->get('config.pAppCode'),
                        'pCinemaID'     => $this->apiParamManager->get('config.pCinemaID'),
                        'pFeatureAppNo' => $this->apiParamManager->get('app.pFeatureAppNo'),
                        'pTokenID'      => $this->apiParamManager->get('config.pTokenID'),
                    ])
                    ->send();
        return $this->transform->getSeatInfo($siteState, $allSeat);
    }

    /**
     * 锁定座位
     * wenqiang
     * 2017-03-09T11:17:52+0800
     * @return [type] [description]
     */
    public function lockSeat()
    {
        $ticketPrice = $this->apiParamManager->get('app.pTicketPrice');

        $seatInfos = array_map(function ($value) use ($ticketPrice) {
            return [
                'seat_no'        => $value['seat_no'],
                'ticket_price'   => floatval($ticketPrice),
                'handlingfee'   => 0,
            ];
        }, $this->apiParamManager->get('app.pSeatInfos'));

        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('LiveRealCheckSeatState')
                    ->setTicketVerifyInfo([
                        $this->apiParamManager->get('config.pCinemaID'),
                        $this->apiParamManager->get('app.pFeatureAppNo'),
                        $this->apiParamManager->get('app.pSerialNum'),
                        count($this->apiParamManager->get('app.pSeatInfos')),
                        $this->apiParamManager->get('app.pPayType'),
                        $this->apiParamManager->get('app.pRecvMobilePhone')])
                    ->withData(['pXmlString' => $this->apiUtil->lockSeatParamsToXml([
                        'feature_app_no'    => $this->apiParamManager->get('app.pFeatureAppNo'),
                        'serial_num'        => $this->apiParamManager->get('app.pSerialNum'),
                        'seat_infos'        => $seatInfos,
                        'pay_type'          => $this->apiParamManager->get('app.pPayType'),
                        'recv_mobile_phone' => $this->apiParamManager->get('app.pRecvMobilePhone')])])
                    ->send(false);

        return $this->transform->lockSeat($data);
    }

    /**
     * 解锁座位
     * wenqiang
     * 2017-03-09T11:17:39+0800
     * @return [type] [description]
     */
    public function unlockSeat()
    {
        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('UnLockOrderCenCin')
                    ->setTicketVerifyInfo([
                        $this->apiParamManager->get('config.pCinemaID'),
                        $this->apiParamManager->get('app.pOrderNO')])
                    ->withData([
                        'pAppCode'  => $this->apiParamManager->get('config.pAppCode'),
                        'pCinemaID' => $this->apiParamManager->get('config.pCinemaID'),
                        'pOrderNO'  => $this->apiParamManager->get('app.pOrderNO'),
                        'pTokenID'  => $this->apiParamManager->get('config.pTokenID'),
                    ])
                    ->send();
        return $this->transform->unlockSeat($data);
    }

    /**
     * 电影票下单
     * wenqiang
     * 2017-03-09T11:17:27+0800
     * @return [type] [description]
     */
    public function ticketPlaceOrder($groundTradeNo = '')
    {
        $modify = $this->apiParamManager->get('app.pModifyPric');

        if ($modify && empty($groundTradeNo)) {
            $this->modifyOrderPrice();
        }

        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('SellTicket')
            ->setTicketVerifyInfo([
                $this->apiParamManager->get('config.pCinemaID'),
                $this->apiParamManager->get('app.pFeatureAppNo'),
                $this->apiParamManager->get('app.pSerialNum'),
                $this->apiParamManager->get('app.pPrintpassword'),
                $this->apiParamManager->get('app.pBalance'),
                $this->apiParamManager->get('app.pPayType'),
                $this->apiParamManager->get('app.pRecvMobilePhone'),
                $this->apiParamManager->get('app.pSendType'),
                $this->apiParamManager->get('app.pPayResult'),
                $this->apiParamManager->get('app.pIsCmtsPay'),
                $this->apiParamManager->get('app.pIsCmtsSendCode'),
                $this->apiParamManager->get('app.pPayMobile'),
                $this->apiParamManager->get('app.pBookSign'),
                $this->apiParamManager->get('app.pPayed'),
                $this->apiParamManager->get('app.pSendModeId'),
                $groundTradeNo
            ])
            ->withData(['pXmlString' => $this->apiUtil->sellTicketParamsToXml([
                    'feature_app_no'    =>  $this->apiParamManager->get('app.pFeatureAppNo'),
                    'serial_num'        =>  $this->apiParamManager->get('app.pSerialNum'),
                    'printpassword'     =>  $this->apiParamManager->get('app.pPrintpassword'),
                    'balance'           =>  $this->apiParamManager->get('app.pBalance'),
                    'pay_type'          =>  $this->apiParamManager->get('app.pPayType'),
                    'Recv_mobile_phone' =>  $this->apiParamManager->get('app.pRecvMobilePhone'),
                    'send_type'         =>  $this->apiParamManager->get('app.pSendType'),
                    'pay_result'        =>  $this->apiParamManager->get('app.pPayResult'),
                    'is_cmts_pay'       =>  $this->apiParamManager->get('app.pIsCmtsPay'),
                    'is_cmts_send_code' =>  $this->apiParamManager->get('app.pIsCmtsSendCode'),
                    'pay_mobile'        =>  $this->apiParamManager->get('app.pPayMobile'),
                    'book_sign'         =>  $this->apiParamManager->get('app.pBookSign'),
                    'payed'             =>  $this->apiParamManager->get('app.pPayed'),
                    'send_mode_id'      =>  $this->apiParamManager->get('app.pSendModeId'),
                    'pay_seq_no'        =>  $groundTradeNo
                ])])
            ->send();

        return $this->transform->ticketPlaceOrder($data, $groundTradeNo);
    }

    /**
     * 退票
     * wenqiang
     * 2017-03-09T11:17:18+0800
     * @return [type] [description]
     */
    public function refundOrder()
    {
        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('BackTicket')
                    ->setTicketVerifyInfo([
                        $this->apiParamManager->get('config.pCinemaID'),
                        $this->apiParamManager->get('app.pOrderNO'),
                    ])
                    ->withData([
                        'pAppCode'      => $this->apiParamManager->get('config.pAppCode'),
                        'pCinemaID'     => $this->apiParamManager->get('config.pCinemaID'),
                        'pOrderNO'      => $this->apiParamManager->get('app.pOrderNO'),
                        'pDesc'         => $this->apiParamManager->get('app.pDesc'),
                        'pTokenID'      => $this->apiParamManager->get('config.pTokenID'),
                    ])
                    ->send();

        return $this->transform->refundOrder($data);
    }

    /**
     * 打印票
     * wenqiang
     * 2017-03-09T11:17:02+0800
     * @return [type] [description]
     */
    public function printTicket()
    {
        \Log::info(['请求打票接口' => $this->apiParamManager->get('app.pOrderNO') .
                '|' .
                $this->apiParamManager->get('app.pValidCode')]);

        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('AppPrintTicket')
            ->setTicketVerifyInfo([
                $this->apiParamManager->get('config.pCinemaID'),
                $this->apiParamManager->get('app.pOrderNO'),
                $this->apiParamManager->get('app.pValidCode'),
                $this->apiParamManager->get('app.pRequestType'),
            ])
            ->withData([
                'pAppCode'      => $this->apiParamManager->get('config.pAppCode'),
                'pCinemaID'     => $this->apiParamManager->get('config.pCinemaID'),
                'pOrderNO'      => $this->apiParamManager->get('app.pOrderNO'),
                'pValidCode'    => $this->apiParamManager->get('app.pValidCode'),
                'pRequestType'  => $this->apiParamManager->get('app.pRequestType'),
                'pTokenID'      => $this->apiParamManager->get('config.pTokenID'),
            ])
            ->send();

        return $this->transform->printTicket($data);
    }

    /**
     * 查询订单信息
     * wenqiang
     * 2017-03-09T11:16:48+0800
     * @return [type] [description]
     */
    public function getOrderInfo()
    {
        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('AppPrintTicket')
            ->setTicketVerifyInfo([
                $this->apiParamManager->get('config.pCinemaID'),
                $this->apiParamManager->get('app.pOrderNO'),
                $this->apiParamManager->get('app.pValidCode'),
                $this->apiParamManager->get('app.pRequestType'),
            ])
            ->withData([
                'pAppCode'      => $this->apiParamManager->get('config.pAppCode'),
                'pCinemaID'     => $this->apiParamManager->get('config.pCinemaID'),
                'pOrderNO'      => $this->apiParamManager->get('app.pOrderNO'),
                'pValidCode'    => $this->apiParamManager->get('app.pValidCode'),
                'pRequestType'  => $this->apiParamManager->get('app.pRequestType'),
                'pTokenID'      => $this->apiParamManager->get('config.pTokenID'),
            ])
            ->send();

        return $this->transform->getOrderInfo($data);
    }

    /**
     * 修改订单价格
     * wenqiang
     * 2017-03-17T15:30:44+0800
     * @return [type] [description]
     */
    public function modifyOrderPrice()
    {
        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('ModifyOrderPayPrice')
                    ->setTicketVerifyInfo([
                        $this->apiParamManager->get('config.pCinemaID'),
                        $this->apiParamManager->get('app.pOrderNO'),
                        $this->apiParamManager->get('app.pAppPric'),
                        $this->apiParamManager->get('app.pBalancePric'),
                    ])
                    ->withData([
                        'pAppCode'      => $this->apiParamManager->get('config.pAppCode'),
                        'pCinemaID'     => $this->apiParamManager->get('config.pCinemaID'),
                        'pOrderNO'      => $this->apiParamManager->get('app.pOrderNO'),
                        'pAppPric'      => $this->apiParamManager->get('app.pAppPric'),
                        'pBalancePric'  => $this->apiParamManager->get('app.pBalancePric'),
                        'pTokenID'      => $this->apiParamManager->get('config.pTokenID'),
                    ])
                    ->send();

        $ret = $data->xmlToArray();

        if ($ret['ResultCode'] != 0) {
            if ($ret['ResultCode'] == 1) {
                throw new \Exception('1.modifyOrderPrice', 601);
            } else {
                throw new \Exception($ret['ResultCode'], 601);
            }
        }
    }

    /**
     * 查询订单售票状态
     * wenqiang
     * 2017-03-09T11:28:42+0800
     * @return [type] [description]
     */
    public function getOrderStatus()
    {
        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('GetOrderStatus')
            ->setTicketVerifyInfo([
                $this->apiParamManager->get('config.pCinemaID'),
                $this->apiParamManager->get('app.pSerialNum')
            ])
            ->withData([
                'pAppCode'      => $this->apiParamManager->get('config.pAppCode'),
                'pCinemaID'     => $this->apiParamManager->get('config.pCinemaID'),
                'pSerialNum'    => $this->apiParamManager->get('app.pSerialNum'),
                'pTokenID'      => $this->apiParamManager->get('config.pTokenID'),
            ])
            ->send();

        return $this->transform->getOrderStatus($data);
    }

    /**
     * 注册会员卡
     * wenqiang
     * 2017-03-09T11:28:30+0800
     * @return [type] [description]
     */
    public function cardRegister()
    {
        $data = $this->apiUtil
            ->setMemberUrl()
            ->callMethod('registerCard')
            ->setMemberVerifyInfo([
                $this->apiParamManager->get('app.pMobile'),
                $this->apiParamManager->get('app.pMobile'),
                $this->apiParamManager->get('app.pPassword'),
                $this->apiParamManager->get('app.pMobile'),
                $this->apiParamManager->get('app.pIdNum'),
            ])
            ->withData([
                'partnerCode'   =>  $this->apiParamManager->get('config.pPartnerCode'),
                'placeNo'       =>  $this->apiParamManager->get('config.pCinemaID'),
                'cardNo'        =>  $this->apiParamManager->get('app.pMobile'),
                'memoryId'      =>  $this->apiParamManager->get('app.pMobile'),
                'passWord'      =>  $this->apiParamManager->get('app.pPassword'),
                'mobilePhone'   =>  $this->apiParamManager->get('app.pMobile'),
                'idNum'         =>  $this->apiParamManager->get('app.pIdNum'),
                'memberName'    =>  $this->apiParamManager->get('app.pMemberName'),
                'balance'       =>  $this->apiParamManager->get('app.pBalance'),
                'score'         =>  $this->apiParamManager->get('app.pScore'),
                'memberTypeNo'  =>  $this->apiParamManager->get('app.pMemberTypeNo'),
                'lifeDate'      =>  date('Y-m-d H:i:s', time() + 10 * 365 * 24 * 60 * 60),
                'partnerId'     =>  '',
                'sex'           =>  '',
                'birthday'      =>  '',
                'email'         =>  '',
                'address'       =>  '',
                'lifeDate'      =>  '',
                'traceMemo'     =>  ''
            ])
            ->send();

        return $this->transform->cardRegister($data);
    }

    /**
     * 会员卡信息
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-10T16:28:44+0800
     *
     * @return [type] [description]
     */
    public function cardInfo()
    {
        $types = [
            $this->apiParamManager->get('app.pCardId')          => API::NUMBER_TYPE_CARD,
            '$' . $this->apiParamManager->get('app.pCardId')    => API::NUMBER_TYPE_ACCOUNT,
        ];

        foreach ($types as $cardId => $type) {

            if ($type == API::NUMBER_TYPE_ACCOUNT and strlen($cardId) != 9) {
                throw new \Exception(200000, 602);
            }
            $data = $this->apiUtil
                ->setMemberUrl()
                ->callMethod('loginCard')
                ->setMemberVerifyInfo([
                    $cardId,
                    $this->apiParamManager->get('app.pPassword'),
                ])
                ->withData([
                    'partnerCode'   => $this->apiParamManager->get('config.pPartnerCode'),
                    'placeNo'       => $this->apiParamManager->get('config.pCinemaID'),
                    'cardId'        => $cardId,
                    'passWord'      => $this->apiParamManager->get('app.pPassword'),
                    'mobilePhone'   => '',
                    'partnerId'     => '',
                ])
                ->send();

            $arr = $data->toArray();

            if ($arr['ResultCode'] == 0) {
                return $this->transform->cardInfo($data, $type, $cardId);
            } else {
                if ($arr['ResultMsg'] == '记录不存在') {
                    $errorRecordFlag = 1;
                    $errorPasswordFlag = 0;
                } elseif ($arr['ResultMsg'] == '密码错误') {
                    $errorRecordFlag = 0;
                    $errorPasswordFlag = 1;
                    break;
                } else {
                    $errorRecordFlag = 0;
                    $errorPasswordFlag = 0;
                }
            }
        }

        if ($errorPasswordFlag == 1) {
            throw new \Exception(200001, 602);
        } elseif ($errorRecordFlag == 1) {
            throw new \Exception(200002, 602);
        } else {
            throw new \Exception(-1, 602);
        }
    }

    /**
     * 会员卡退费
     * wenqiang
     * 2017-03-17T17:38:10+0800
     * @return [type] [description]
     */
    public function cardRefund()
    {
        $cardId = $this->apiUtil->cardId(
            $this->apiParamManager->get('app.pCardId'),
            $this->apiParamManager->get('app.type'));

        $data = $this->apiUtil
                    ->setMemberUrl()
                    ->callMethod('cardPayBack')
                    ->setMemberVerifyInfo([
                        $cardId,
                        $this->apiParamManager->get('app.pPassword'),
                        $this->apiParamManager->get('app.pTraceType'),
                        $this->apiParamManager->get('app.pTraceNo'),
                        $this->apiParamManager->get('app.pTracePrice'),
                        $this->apiParamManager->get('app.pPrice')
                    ])
                    ->withData([
                        'partnerCode'   => $this->apiParamManager->get('config.pPartnerCode'),
                        'placeNo'       => $this->apiParamManager->get('config.pCinemaID'),
                        'cardId'        => $cardId,
                        'passWord'      => $this->apiParamManager->get('app.pPassword'),
                        'traceType'     => $this->apiParamManager->get('app.pTraceType'),
                        'traceNo'       => $this->apiParamManager->get('app.pTraceNo'),
                        'tracePrice'    => $this->apiParamManager->get('app.pTracePrice'),
                        'price'         => $this->apiParamManager->get('app.pPrice'),
                        'traceMemo'     => $this->apiParamManager->get('app.pTraceMemo'),
                        'mobilePhone'   => '',
                        'partnerId'     => ''
                    ])
                    ->send();

        return $this->transform->cardRefund($data);
    }

    /**
     * 会员卡交易记录
     * wenqiang
     * 2017-03-18T16:14:34+0800
     * @return [type] [description]
     */
    public function cardTransRecord()
    {
        $cardId = $this->apiUtil->cardId(
            $this->apiParamManager->get('app.pCardId'),
            $this->apiParamManager->get('app.type'));

        $data = $this->apiUtil
                    ->setMemberUrl()
                    ->callMethod('getCardTraceRecord')
                    ->setMemberVerifyInfo([
                        $cardId,
                        $this->apiParamManager->get('app.pPassword'),
                        $this->apiParamManager->get('app.pStartDate'),
                        $this->apiParamManager->get('app.pEndDate')
                    ])
                    ->withData([
                        'partnerCode'   => $this->apiParamManager->get('config.pPartnerCode'),
                        'placeNo'       => $this->apiParamManager->get('config.pCinemaID'),
                        'cardId'        => $cardId,
                        'mobilePhone'   => '',
                        'passWord'      => $this->apiParamManager->get('app.pPassword'),
                        'startDate'     => $this->apiParamManager->get('app.pStartDate'),
                        'endDate'       => $this->apiParamManager->get('app.pEndDate'),
                    ])
                    ->send();

        return $this->transform->cardTransRecord($data);
    }

    /**
     * 会员卡购票
     * wenqiang
     * 2017-03-09T11:28:13+0800
     * @return [type] [description]
     */
    public function cardBuyTicket()
    {
        $modify = $this->apiParamManager->get('app.pModifyPric');

        if ($modify) {
            $this->modifyOrderPrice();
        }

        $cardId = $this->apiUtil->cardId(
            $this->apiParamManager->get('app.pCardId'),
            $this->apiParamManager->get('app.type'));

        $data = $this->apiUtil
            ->setMemberUrl()
            ->callMethod('cardPay')
            ->setMemberVerifyInfo([
                $this->apiParamManager->get('app.pPartnerId'),
                $cardId,
                $this->apiParamManager->get('app.pPassword'),
                $this->apiParamManager->get('app.pTraceTypeNo'),
                $this->apiParamManager->get('app.pOldPrice'),
                $this->apiParamManager->get('app.pTracePrice'),
                $this->apiParamManager->get('app.pDiscount'),
                $this->apiParamManager->get('app.pFeatureNo'),
                $this->apiParamManager->get('app.pFilmNo'),
                $this->apiParamManager->get('app.pTicketNum'),
            ])
            ->withData([
                'partnerCode'   => $this->apiParamManager->get('config.pPartnerCode'),
                'placeNo'       => $this->apiParamManager->get('config.pCinemaID'),
                'partnerId'     => $this->apiParamManager->get('app.pPartnerId'),
                'cardId'        => $cardId,
                'passWord'      => $this->apiParamManager->get('app.pPassword'),
                'traceTypeNo'   => $this->apiParamManager->get('app.pTraceTypeNo'),
                'oldPrice'      => $this->apiParamManager->get('app.pOldPrice'),
                'tracePrice'    => $this->apiParamManager->get('app.pTracePrice'),
                'discount'      => $this->apiParamManager->get('app.pDiscount'),
                'featureNo'     => $this->apiParamManager->get('app.pFeatureNo'),
                'filmNo'        => $this->apiParamManager->get('app.pFilmNo'),
                'ticketNum'     => $this->apiParamManager->get('app.pTicketNum'),
                'mobilePhone'   => '',
                'traceMemo'     => '',
            ])
            ->send();

        $payInfo = $data->toArray();

        if ($payInfo['ResultCode'] != 0) {
            if (preg_match('/^\(6[\d]\).+/', $payInfo['ResultMsg'])) {
                $replaceCode = ['#', '(60)', '(61)', '(63)', '(64)', '(65)', '(66)', '(67)', '[', ']'];

                throw new \Exception('errorRaw_' . str_replace($replaceCode, '',
                    str_replace('[]', '0', $payInfo['ResultMsg'])), 6009);
            } elseif ($payInfo['ResultCode'] == 1) {
                throw new \Exception('1.cardBuyTicket', 602);
            } else {
                throw new \Exception($payInfo['ResultCode'], 602);
            }
        }

        return $this->ticketPlaceOrder($payInfo['GroundTradeNo']);
    }

    /**
     * 会员卡所有类型
     * wenqiang
     * 2017-03-09T11:27:58+0800
     * @return [type] [description]
     */
    public function cardType()
    {
        $data = $this->apiUtil
            ->setMemberUrl()
            ->callMethod('getCardType')
            ->setMemberVerifyInfo([])
            ->withData([
                'partnerCode'   => $this->apiParamManager->get('config.pPartnerCode'),
                'placeNo'       => $this->apiParamManager->get('config.pCinemaID'),
            ])
            ->send();

        return $this->transform->cardType($data);
    }

    /**
     * 会员卡充值
     * wenqiang
     * 2017-03-09T11:27:48+0800
     * @return [type] [description]
     */
    public function cardRecharge()
    {
        $cardId = $this->apiUtil->cardId(
                    $this->apiParamManager->get('app.pCardId'),
                    $this->apiParamManager->get('app.type'));

        $data = $this->apiUtil
            ->setMemberUrl()
            ->callMethod('cardRecharge')
            ->setMemberVerifyInfo([
                $this->apiParamManager->get('app.pPartnerId'),
                $cardId,
                $this->apiParamManager->get('app.pPassword'),
                $this->apiParamManager->get('app.pPrice'),
            ])
            ->withData([
                'partnerCode'   => $this->apiParamManager->get('config.pPartnerCode'),
                'placeNo'       => $this->apiParamManager->get('config.pCinemaID'),
                'partnerId'     => $this->apiParamManager->get('app.pPartnerId'),
                'cardId'        => $cardId,
                'passWord'      => $this->apiParamManager->get('app.pPassword'),
                'price'         => $this->apiParamManager->get('app.pPrice'),
                'traceMemo'     => $this->apiParamManager->get('app.pTraceMemo'),
                'mobilePhone'   => '',
            ])
            ->send();

        return $this->transform->cardRecharge($data);
    }

    /**
     * 会员卡对应的排期折扣
     * wenqiang
     * 2017-03-09T11:27:32+0800
     * @return [type] [description]
     */
    public function cardScheduleDiscount()
    {
        $cardId = $this->apiUtil->cardId(
                    $this->apiParamManager->get('app.pCardId'),
                    $this->apiParamManager->get('app.type'));

        $data = $this->apiUtil
            ->setMemberUrl()
            ->callMethod('getDiscount')
            ->setMemberVerifyInfo([
                $cardId,
                $this->apiParamManager->get('app.pFeatureNo'),
                $this->apiParamManager->get('app.pFeatureDate'),
                $this->apiParamManager->get('app.pFeatureTime'),
            ])
            ->withData([
                'partnerCode'   => $this->apiParamManager->get('config.pPartnerCode'),
                'placeNo'       => $this->apiParamManager->get('config.pCinemaID'),
                'cardId'        => $cardId,
                'featureNo'     => $this->apiParamManager->get('app.pFeatureNo'),
                'featureDate'   => $this->apiParamManager->get('app.pFeatureDate'),
                'featureTime'   => $this->apiParamManager->get('app.pFeatureTime'),
                'partnerId'     => '',
                'mobilePhone'   => '',
            ])
            ->send();

        return $this->transform->cardScheduleDiscount(
            $data,
            $this->apiParamManager->get('app.pStandPrice'));
    }
}
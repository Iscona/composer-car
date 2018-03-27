<?php

namespace biqu\TicketPlatform\Fhjy;

use biqu\APIInterface;
use biqu\TicketPlatform\Fhjy\API;
use biqu\TicketPlatform\ApiParamManager;
use biqu\TicketPlatform\Fhjy\Transform;
use biqu\TicketPlatform\Fhjy\NewTransform;

class UnifiedAPI implements APIInterface
{
    protected $api;

    protected $transform;

    protected $apiParamManager;

    public function __construct(ApiParamManager $apiParamManager)
    {
        $this->apiParamManager = $apiParamManager;
        $this->api = new API($apiParamManager);

        if ($this->apiParamManager->get('config.pApiCenter') == 'B') {
            $this->transform = new NewTransform();
        } else {
            $this->transform = new Transform();
        }
    }

   /**
     * 获得所有排期
     * wenqiang
     * 2017-03-09T11:12:02+0800
     * @return [type] [description]
     */
    public function getAllSchedule()
    {
        $cinemas = $this->getCinemas();

        $cinemaId = $this->apiParamManager->get('config.pCinemaID');

        if ((strtotime('2018-02-23') - time()) / (60*60*24) < 10) {
            $endDate = date('Y-m-d', strtotime('10 days'));
        } else {
            $endDate = '2018-02-23';
        }

        $data = $this->api
                    ->setCallMethod('ykse.partner.schedule.getSchedules')
                    ->withData([
                        'cinemaLinkId'      =>  $cinemaId,
                        'startDate'         =>  date('Y-m-d'),
                        'endDate'           =>  $endDate,
                        'priceQueryType'    =>  '',
                    ])
                    ->send();

        return $this->transform->getAllSchedule($data, $cinemas->toArray(), $cinemaId);
    }

    /**
     * 获取座位信息
     * wenqiang
     * 2017-03-09T11:22:27+0800
     * @return [type] [description]
     */
    public function getSeatInfo()
    {
        $areaData = [];
        $arr = $this->getNorSeatInfo();

        if ($this->apiParamManager->get('app.pArea')) {
            $areaData = $this->getAreaSeatInfo($arr['sectionId']);
        }

        return $this->getSeatStatus($arr, $areaData);
    }

    /**
     * 获取正常座位信息
     * wenqiang
     * 2017-04-26T14:50:30+0800
     * @return [type] [description]
     */
    public function getNorSeatInfo()
    {
        $data = $this->api
                    ->setCallMethod('ykse.partner.seat.getSeats')
                    ->withData([
                        'cinemaLinkId'  => $this->apiParamManager->get('config.pCinemaID'),
                        'hallId'        => $this->apiParamManager->get('app.pScreenID'),
                        'hallCode'      => $this->apiParamManager->get('app.pScreenID')
                    ])
                    ->send();

        return $this->transform->getSeatInfo($data);
    }

    /**
     * 获取分区座位信息
     * wenqiang
     * 2017-04-26T14:33:49+0800
     * @return [type] [description]
     */
    public function getAreaSeatInfo($sectionId)
    {
        $data = $this->api
                    ->setCallMethod('ykse.partner.seat.getScheduleAreaSeats')
                    ->withData([
                        'cinemaLinkId'  =>  $this->apiParamManager->get('config.pCinemaID'),
                        'scheduleId'    =>  $this->apiParamManager->get('app.pSessionID'),
                        'scheduleKey'   =>  $this->apiParamManager->get('app.pScheduleKey'),
                    ])
                    ->send();

        return $this->transform->getAreaSeatInfo($data, $sectionId);
    }

    /**
     * 查找已售座位
     * wenqiang
     * 2017-04-20T12:03:49+0800
     * @return [type] [description]
     */
    public function getSeatStatus($seatData, $areaData)
    {
        $data = $this->api
                    ->setCallMethod('ykse.partner.seat.getScheduleSoldSeats')
                    ->withData([
                        'cinemaLinkId'      =>  $this->apiParamManager->get('config.pCinemaID'),
                        'sectionId'         =>  $seatData['sectionId'],
                        'sectionCode'       =>  $seatData['sectionId'],
                        'scheduleId'        =>  $this->apiParamManager->get('app.pSessionID'),
                        'scheduleKey'       =>  $this->apiParamManager->get('app.pScheduleKey'),
                    ])
                    ->send();

        return $this->transform->getSeatStatus($data, $seatData, $areaData);
    }

    /**
     * 锁定座位
     * wenqiang
     * 2017-03-09T11:17:52+0800
     * @return [type] [description]
     */
    public function lockSeat()
    {
        $seatIdList = array_column($this->apiParamManager->get('app.pSeatIdList'), 'seat_no');

        $data = $this->api
                    ->setCallMethod('ykse.partner.seat.lockSeats')
                    ->withData([
                        'cinemaLinkId'      =>  $this->apiParamManager->get('config.pCinemaID'),
                        'outLockId'         =>  $this->apiParamManager->get('app.pOutLockId'),
                        'scheduleId'        =>  $this->apiParamManager->get('app.pSessionID'),
                        'scheduleKey'       =>  $this->apiParamManager->get('app.pScheduleKey'),
                        'seatIdList'        =>  $seatIdList,
                        'seatCodeList'      =>  $seatIdList,
                    ])
                    ->send();

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
        $data = $this->api
                    ->setCallMethod('ykse.partner.seat.releaseSeats')
                    ->withData([
                        'cinemaLinkId'      =>  $this->apiParamManager->get('config.pCinemaID'),
                        'lockOrderId'       =>  $this->apiParamManager->get('app.pLockOrderId'),
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
    public function ticketPlaceOrder()
    {
        $ticketList = [];
        $seatNo = $this->apiParamManager->get('app.pSeatNo');
        $ticketPrice = $this->apiParamManager->get('app.pTicketPrice');
        $ticketFee = $this->apiParamManager->get('app.pHandlingfee');

        if ($this->api->apiCenter == 'A') {
            $payType = $this->apiParamManager->get('app.pPayType');

            if ($payType == 2) {
                $paymentMethod = 'WeChat';
            } elseif ($payType == 3) {
                $paymentMethod = 'Ali';
            }

            for ($i = 0; $i < count($this->apiParamManager->get('app.pSeatNo')); $i++) {

                $ticketList[] = [
                    'seatId'    =>  $seatNo[$i],
                    'ticketPrice'   =>  number_format($ticketPrice[$i], 2, '.', ''),
                    'ticketFee'     =>  number_format($ticketFee[$i], 2, '.', ''),
                    'paymentList'   =>  [[
                        'paymentMethod' =>  $paymentMethod,
                        'payAmount'     =>  number_format($ticketPrice[$i] + $ticketFee[$i], 2, '.', ''),
                        'thirdPartPayment'  =>  [
                            'thirdTradeNo'  =>  $this->apiParamManager->get('app.pPlatformTraceNo'),
                            'outTradeNo'    =>  $this->apiParamManager->get('app.pTraceNo'),
                        ],
                    ]],
                ];
            }

            $data = $this->api
                        ->setCallMethod('ykse.partner.order.confirmOrderForMultiPay')
                        ->withData([
                            'cinemaLinkId'      =>  $this->apiParamManager->get('config.pCinemaID'),
                            'mobile'            =>  $this->apiParamManager->get('app.pRecvMobilePhone'),
                            'lockOrderId'       =>  $this->apiParamManager->get('app.pOrderID'),
                            'scheduleKey'       =>  $this->apiParamManager->get('app.pScheduleKey'),
                            'scheduleId'        =>  $this->apiParamManager->get('app.pSessionID'),
                            'ticketList'        =>  $ticketList,
                        ])
                        ->send();
        } else {
            for ($i = 0; $i < count($this->apiParamManager->get('app.pSeatNo')); $i++) {

                $ticketList[] = json_encode([
                    'seatCode'      =>  $seatNo[$i],
                    'ticketPrice'   =>  number_format($ticketPrice[$i], 2, '.', ''),
                    'ticketFee'     =>  number_format(0, 2, '.', ''),
                    'serviceFee'    =>  number_format($ticketFee[$i], 2, '.', ''),
                    'ticketChannelFee'=>  number_format(0, 2, '.', ''),
                ]);
            }
            $data = $this->api
                        ->setCallMethod('ykse.partner.order.confirmOrderForMultiPay')
                        ->withData([
                            'cinemaLinkId'      =>  $this->apiParamManager->get('config.pCinemaID'),
                            'lockOrderId'       =>  $this->apiParamManager->get('app.pOrderID'),
                            'scheduleId'        =>  $this->apiParamManager->get('app.pSessionID'),
                            'scheduleKey'       =>  $this->apiParamManager->get('app.pScheduleKey'),
                            'ticketList'        =>  $ticketList,
                            'mobile'            =>  $this->apiParamManager->get('app.pRecvMobilePhone'),
                        ])
                        ->send();
        }
        return $this->transform->ticketPlaceOrder($data);
    }

    /**
     * 退票
     * wenqiang
     * 2017-03-09T11:17:18+0800
     * @return [type] [description]
     */
    public function refundOrder()
    {
        $data = $this->api
                    ->setCallMethod('ykse.partner.order.refundOrder')
                    ->withData([
                        'cinemaLinkId'  =>  $this->apiParamManager->get('config.pCinemaID'),
                        'orderId'       =>  $this->apiParamManager->get('app.pOrderNO'),
                    ])
                    ->send();

        return $this->transform->refudnOrder($data);
    }

    /**
     * 取票信息
     * wenqiang
     * 2017-04-25T16:49:00+0800
     * @return [type] [description]
     */
    public function pirntTicketInfo()
    {
        $data = $this->api
                ->setCallMethod('ykse.partner.order.getTicketInfo')
                ->withData([
                    'cinemaLinkId'      =>  $this->apiParamManager->get('config.pCinemaID'),
                    'confirmationId'    =>  $this->apiParamManager->get('app.pConfirmationId'),
                    'printCode'         =>  $this->apiParamManager->get('app.pConfirmationId'),
                ])
                ->send();

        return $data; //$this->transform->pirntTicketInfo($data);
    }

    /**
     * 打印票
     * wenqiang
     * 2017-03-09T11:17:02+0800
     * @return [type] [description]
     */
    public function printTicket()
    {

        if ($this->apiParamManager->get('app.pRequestType') == 0) {
            $data = $this->api
                    ->setCallMethod('ykse.partner.order.getOrderInfo')
                    ->withData([
                        'cinemaLinkId'  =>  $this->apiParamManager->get('config.pCinemaID'),
                        'lockOrderId'   =>  $this->apiParamManager->get('app.pAppSeatNo'),
                        'orderId'       =>  $this->apiParamManager->get('app.pOrderNO'),
                    ])
                    ->send();

            return $this->transform->getPrintOrderInfo($data);
        }

        \Log::info(['请求打票接口' => $this->apiParamManager->get('app.pAppSeatNo')]);

        $arr = $this->pirntTicketInfo();
        $ticketInfoArr = $this->transform->pirntTicketInfo($arr);

        $data = $this->api
                    ->setCallMethod('ykse.partner.ticket.printTicket')
                    ->withData([
                        'cinemaLinkId'  =>  $this->apiParamManager->get('config.pCinemaID'),
                        'printCode'     =>  $ticketInfoArr['printId'],
                        'printId'       =>  $ticketInfoArr['printId'],
                    ])
                    ->send();

        return $this->transform->printTicket($data, $ticketInfoArr);
    }

    /**
     * 查询订单信息
     * wenqiang
     * 2017-03-09T11:16:48+0800
     * @return [type] [description]
     */
    public function getOrderInfo(){}

    /**
     * 查询订单售票状态
     * wenqiang
     * 2017-03-09T11:28:42+0800
     * @return [type] [description]
     */
    public function getOrderStatus()
    {
        $data = $this->api
                    ->setCallMethod('ykse.partner.order.getOrderInfo')
                    ->withData([
                        'cinemaLinkId'  =>  $this->apiParamManager->get('config.pCinemaID'),
                        'lockOrderId'   =>  $this->apiParamManager->get('app.pSerialNum'),
                        'orderId'       =>  '',
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
        $data = $this->api
                    ->setCallMethod('ykse.partner.card.registerCard')
                    ->withData([
                        'cinemaLinkId'          =>  $this->apiParamManager->get('config.pCinemaID'),
                        'gradeId'               =>  $this->apiParamManager->get('app.pMemberTypeNo'),
                        'outTradeNo'            =>  $this->apiParamManager->get('app.pTraceNo'),
                        'cardPassword'          =>  $this->api->sensitiveDataEnc($this->apiParamManager->get('app.pPassword')),
                        'cardCostFee'           =>  number_format($this->apiParamManager->get('app.pCardCostFee'), 2, '.', ''),
                        'memberFee'             =>  number_format($this->apiParamManager->get('app.pMemberFee'), 2, '.', ''),
                        'firstRechargeAmount'   =>  number_format($this->apiParamManager->get('app.pBalance'), 2, '.', ''),
                        'mobile'                =>  $this->apiParamManager->get('app.pMobile'),
                        'cardUserName'          =>  $this->apiParamManager->get('app.pMemberName'),
                        'idCard'                =>  $this->apiParamManager->get('app.pIdNum'),
                        'address'               =>  '',
                        'birthdate'             =>  '',
                        'email'                 =>  '1021192338@qq.com',
                    ])
                    ->send();

        return $this->transform->cardRegister($data);
    }

    /**
     * 会员卡信息
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-10T16:28:09+0800
     *
     * @return [type] [description]
     */
    public function cardInfo()
    {
        $cardData = $this->getCardBalance();

        $data = $this->api
                    ->setCallMethod('ykse.partner.card.getCardDetail')
                    ->withData([
                        'cardNumber'    =>  $this->apiParamManager->get('app.pCardId'),
                        'cinemaLinkId'  =>  $this->apiParamManager->get('config.pCinemaID'),
                    ])
                    ->send();

        return $this->transform->cardInfo($data, $cardData);
    }

    /**
     * 获取会员卡余额
     * wenqiang
     * 2017-04-25T19:57:56+0800
     * @return [type] [description]
     */
    public function getCardBalance()
    {
        $data = $this->api
                    ->setCallMethod('ykse.partner.card.queryBalance')
                    ->withData([
                        'cardNumber'    =>  $this->apiParamManager->get('app.pCardId'),
                        'cardPassword'  =>  $this->api->sensitiveDataEnc($this->apiParamManager->get('app.pPassword')),
                        'cinemaLinkId'  =>  $this->apiParamManager->get('config.pCinemaID'),
                    ])
                    ->send();

        return $this->transform->getCardBalance($data);
    }

    /**
     * 会员卡购票
     * wenqiang
     * 2017-03-09T11:28:13+0800
     * @return [type] [description]
     */
    public function cardBuyTicket()
    {
        $ticketList = [];
        $seatNo = $this->apiParamManager->get('app.pSeatNo');
        $ticketPrice = $this->apiParamManager->get('app.pTicketPrice');
        $ticketFee = $this->apiParamManager->get('app.pHandlingfee');
        $isDiscount = $this->apiParamManager->get('app.pIsDiscount');

        $password = $this->api->sensitiveDataEnc($this->apiParamManager->get('app.pPassword'));

        for ($i = 0; $i < count($seatNo); $i++) {

            $ticketList[] = [
                'seatId'    =>  $seatNo[$i],
                'ticketPrice'   =>  number_format($ticketPrice[$i], 2, '.', ''),
                'ticketFee'     =>  number_format($ticketFee[$i], 2, '.', ''),
                'paymentList'   =>  [[
                    'paymentMethod' =>  'MemberCard',
                    'payAmount'     =>  number_format($ticketPrice[$i] + $ticketFee[$i], 2, '.', ''),
                    'payCardInfo'   =>  [
                        'cardNumber'        =>  $this->apiParamManager->get('app.pCardId'),
                        'cardPassword'      =>  $password,
                        'totalDisTickets'   =>  $isDiscount[$i],
                    ],
                ]],
            ];
        }

        $data = $this->api
                    ->setCallMethod('ykse.partner.order.confirmOrderForMultiPay')
                    ->withData([
                        'cinemaLinkId'      =>  $this->apiParamManager->get('config.pCinemaID'),
                        'mobile'            =>  $this->apiParamManager->get('app.pRecvMobilePhone'),
                        'lockOrderId'       =>  $this->apiParamManager->get('app.pOrderID'),
                        'scheduleKey'       =>  $this->apiParamManager->get('app.pScheduleKey'),
                        'scheduleId'        =>  $this->apiParamManager->get('app.pSessionID'),
                        'ticketList'        =>  $ticketList,
                    ])
                    ->send();

        return $this->transform->cardBuyTicket($data);
    }

    /**
     * 会员卡所有类型
     * wenqiang
     * 2017-03-09T11:27:58+0800
     * @return [type] [description]
     */
    public function cardType()
    {
        $data = $this->api
                    ->setCallMethod('ykse.partner.card.getCardGradeList')
                    ->withData([
                        'cinemaLinkId'  =>  $this->apiParamManager->get('config.pCinemaID'),
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
        $data = $this->api
                    ->setCallMethod('ykse.partner.card.recharge')
                    ->withData([
                        'cinemaLinkId'  =>  $this->apiParamManager->get('config.pCinemaID'),
                        'cardNumber'    =>  $this->apiParamManager->get('app.pCardId'),
                        'outTradeNo'    =>  $this->apiParamManager->get('app.pOutTradeNo'),
                        'rechargeAmount'=>  strval($this->apiParamManager->get('app.pRechargeAmount')),
                        'description'   =>  $this->apiParamManager->get('app.pDescription'),
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
        $data = $this->api
                    ->setCallMethod('ykse.partner.card.queryPrice')
                    ->withData([
                        'cinemaLinkId'  =>  $this->apiParamManager->get('config.pCinemaID'),
                        'cardNumber'    =>  $this->apiParamManager->get('app.pCardId'),
                        'sectionId'     =>  $this->apiParamManager->get('app.pSectionId'),
                        'scheduleId'    =>  $this->apiParamManager->get('app.pFeatureNo'),
                        'scheduleKey'   =>  $this->apiParamManager->get('app.pScheduleKey'),
                    ])
                    ->send();

        return $this->transform->cardScheduleDiscount($data);
    }

    /**
     * 会员卡交易记录
     * wenqiang
     * 2017-04-27T17:47:02+0800
     * @return [type] [description]
     */
    public function cardTransRecord()
    {
        $consData = $this->api
                    ->setCallMethod('ykse.partner.card.getCardConsumeRecords')
                    ->withData([
                        'cinemaLinkId'      =>  $this->apiParamManager->get('config.pCinemaID'),
                        'cardNumber'        =>  $this->apiParamManager->get('app.pCardId'),
                        'startDate'         =>  $this->apiParamManager->get('app.pStartDate'),
                        'endDate'           =>  $this->apiParamManager->get('app.pEndDate'),
                    ])
                    ->send();

        $rechargeData = $this->api
                    ->setCallMethod('ykse.partner.card.getCardRechargeRecords')
                    ->withData([
                        'cinemaLinkId'      =>  $this->apiParamManager->get('config.pCinemaID'),
                        'cardNumber'        =>  $this->apiParamManager->get('app.pCardId'),
                        'startDate'         =>  $this->apiParamManager->get('app.pStartDate'),
                        'endDate'           =>  $this->apiParamManager->get('app.pEndDate'),
                    ])
                    ->send();

        return $this->transform->cardTransRecord($consData, $rechargeData);
    }

    /**
     * 获取影院信息
     * wenqiang
     * 2017-04-19T14:34:16+0800
     * @return [type] [description]
     */
    public function getCinemas()
    {
        $data = $this->api
                    ->setCallMethod('ykse.partner.cinema.getCinemas')
                    ->withData([])
                    ->send();

        return $this->transform->getCinemas($data);
    }
}
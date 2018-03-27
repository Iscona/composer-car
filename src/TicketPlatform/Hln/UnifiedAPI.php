<?php

namespace biqu\TicketPlatform\Hln;

use biqu\APIInterface;
use biqu\TicketPlatform\Hln\API;
use biqu\TicketPlatform\Hln\Transform;
use biqu\TicketPlatform\ApiParamManager;

class UnifiedAPI implements APIInterface
{
    /**
     * [$apiUtil]
     * @var [type]
     */
    protected $apiUtil;

    /**
     * [$transform]
     * @var [type]
     */
    protected $transform;

    /**
     *
     *
     * wenqiang
     * 2017-03-14T16:54:57+0800
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
                    ->setFuncVersion('v1')
                    ->setCallMethod('queryShows')
                    ->withData([
                        'cinemaCode'    =>  $this->apiParamManager->get('config.pCinemaID'),
                        'startDate'     =>  '',
                        'status'        =>  1,
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
        $seatInfos = $this->apiUtil
                        ->setTicketUrl()
                        ->setFuncVersion('v1')
                        ->setCallMethod('queryShowSeats')
                        ->withData([
                            'channelShowCode'   => $this->apiParamManager->get('app.pFeatureAppNo'),
                        ])
                        ->send();

        return $this->transform->getSeatInfo($seatInfos);
    }

    /**
     * 锁定座位
     * wenqiang
     * 2017-03-15T14:54:18+0800
     * @return [type] [description]
     */
    public function lockSeat()
    {
        $num = count($this->apiParamManager->get('app.pSeatInfos'));

        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->setFuncVersion('v1')
                    ->setCallMethod('lockSeats')
                    ->withData([
                        'channelShowCode'   =>  $this->apiParamManager->get('app.pFeatureAppNo'),
                        'seatCodes'         =>  join(',', array_column($this->apiParamManager->get('app.pSeatInfos'), 'seat_no')),
                        'channelOrderCode'  =>  $this->apiParamManager->get('app.pOrderID'),
                        // 'submitChannelId'   =>  $this->apiParamManager->get('config.pChannelCode'),
                        // 'submitChannelName' =>  '必趣',
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
        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->setFuncVersion()
                    ->setCallMethod('releaseSeats')
                    ->withData([
                        'orderCode'     => $this->apiParamManager->get('app.pOrderNO'),
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
        $array = [
                    $this->apiParamManager->get('app.pSeatNo'),
                    $this->apiParamManager->get('app.pTicketPrice'),
                    $this->apiParamManager->get('app.pStandPrice'),
                ];

        $seatPrice = [];
        foreach ($array[0] as $key => $val) {
            $seatPrice[] = join(':', array_column($array, $key));
        }

        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->setFuncVersion()
                    ->setCallMethod('submitOrder')
                    ->withData([
                        'orderCode'      => $this->apiParamManager->get('app.pOrderID'),
                        'orderSeats'     => join(',', $seatPrice),
                        'mobile'         => $this->apiParamManager->get('app.pRecvMobilePhone')
                    ])
                    ->send();

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
        if ($this->apiParamManager->get('app.pPayType') == 1) {
            $data = $this->apiUtil
                    ->setMemberUrl()
                    ->setFuncVersion()
                    ->setCallMethod('revokeTicket')
                    ->withData([
                        'orderCode' =>  $this->apiParamManager->get('app.pAppSeatNo'),
                        'cardCode'  =>  $this->apiParamManager->get('app.pCardId'),
                        'password'  =>  $this->apiParamManager->get('app.pPassword'),
                    ])
                    ->send();
        } else {
            $data = $this->apiUtil
                    ->setTicketUrl()
                    ->setFuncVersion()
                    ->setCallMethod('revokeTicket')
                    ->withData([
                        'orderCode' =>  $this->apiParamManager->get('app.pAppSeatNo'),
                    ])
                    ->send();
        }

        return $this->transform->refundOrder($data);
    }

    /**
     * 打印票 0 查看状态  1 打印
     * wenqiang
     * 2017-03-09T11:17:02+0800
     * @return [type] [description]
     */
    public function printTicket()
    {
        $printInfo = $this->apiUtil
                        ->setTicketUrl()
                        ->setFuncVersion()
                        ->setCallMethod('queryPrint')
                        ->withData([
                            'cinemaCode'    =>  $this->apiParamManager->get('config.pCinemaID'),
                            'printCode'     =>  $this->apiParamManager->get('app.pOrderNO'),
                            'verifyCode'    =>  $this->apiParamManager->get('app.pValidCode'),
                        ])
                        ->send();

        if ($this->apiParamManager->get('app.pRequestType') == 0) {
            return $this->transform->pirntTicketInfo($printInfo);
        }

        \Log::info(['请求打票接口' => $this->apiParamManager->get('app.pOrderNO') .
                '|' .
                $this->apiParamManager->get('app.pValidCode')]);

        $data = $this->apiUtil
                    ->setCallMethod('confirmPrint')
                    ->withData([
                        'cinemaCode'    =>  $this->apiParamManager->get('config.pCinemaID'),
                        'printCode'     =>  $this->apiParamManager->get('app.pOrderNO'),
                        'verifyCode'    =>  $this->apiParamManager->get('app.pValidCode'),
                    ])
                    ->send();

        return $this->transform->printTicket($data, $printInfo);
    }

    /**
     * 查询订单信息
     * wenqiang
     * 2017-03-09T11:16:48+0800
     * @return [type] [description]
     */
    public function getOrderInfo()
    {

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
                    ->setFuncVersion()
                    ->setCallMethod('queryOrder')
                    ->withData([
                        'orderCode'  =>  $this->apiParamManager->get('app.pOrderID')
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
                    ->setFuncVersion()
                    ->setCallMethod('registeredOnline')
                    ->withData([
                        'cinemaCode'    =>  $this->apiParamManager->get('config.pCinemaID'),
                        'businessNo'    =>  $this->apiParamManager->get('app.pTraceNo'),
                        'policyId'      =>  $this->apiParamManager->get('app.pMemberTypeNo'),
                        'memberName'    =>  $this->apiParamManager->get('app.pMemberName'),
                        'telephone'     =>  $this->apiParamManager->get('app.pMobile'),
                        'memberCert'    =>  $this->apiParamManager->get('app.pIdNum'),
                        'rechargeMoney' =>  number_format($this->apiParamManager->get('app.pBalance'), 2, '.', ''),
                        'serviceExpense'=>  number_format($this->apiParamManager->get('app.pCardCostFee'), 2, '.', ''),
                        'annualFeeMoney'=>  number_format($this->apiParamManager->get('app.pAnnualFee'), 2, '.', ''),
                        'memberPassword'=>  $this->apiParamManager->get('app.pPassword'),
                        'channelId'     =>  $this->apiParamManager->get('config.pChannelCode'),
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
        $cardTypes = $this->cardType();

        $data = $this->apiUtil
                    ->setCallMethod('queryCard')
                    ->withData([
                        'cinemaCode'  =>  $this->apiParamManager->get('config.pCinemaID'),
                        'cardCode'    =>  $this->apiParamManager->get('app.pCardId'),
                        'password'    =>  $this->apiParamManager->get('app.pPassword'),
                    ])
                    ->send();

        return $this->transform->cardInfo($data, $cardTypes);
    }

    /**
     * 会员卡购票
     * wenqiang
     * 2017-03-09T11:28:13+0800
     * @return [type] [description]
     */
    public function cardBuyTicket()
    {
        $seatPrice = [];
        $ticketPrice = [];
        $payPrice = $this->apiParamManager->get('app.pPayPrice');

        $array = [
                    $this->apiParamManager->get('app.pSeatNo'),
                    $this->apiParamManager->get('app.pPayPrice'),
                    $this->apiParamManager->get('app.pTicketPrice'),
                ];

        foreach ($array[0] as $key => $val) {
            $seatPrice[] = join(':', array_column($array, $key));
        }

        $data = $this->apiUtil
                    ->setMemberUrl()
                    ->setFuncVersion()
                    ->setCallMethod('submitOrder')
                    ->withData([
                        'orderCode'     =>  $this->apiParamManager->get('app.pOrderID'),
                        'cardCode'      =>  $this->apiParamManager->get('app.pCardId'),
                        'password'      =>  $this->apiParamManager->get('app.pPassword'),
                        'mobile'        =>  $this->apiParamManager->get('app.pRecvMobilePhone'),
                        'orderSeats'    =>  join(',', $seatPrice),
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
        $data = $this->apiUtil
                    ->setMemberUrl()
                    ->setFuncVersion()
                    ->setCallMethod('queryCardPolicyInfo')
                    ->withData([
                        'cinemaCode'  =>  $this->apiParamManager->get('config.pCinemaID'),
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
        $data = $this->apiUtil
                    ->setMemberUrl()
                    ->setFuncVersion()
                    ->setCallMethod('recharge')
                    ->withData([
                        'cinemaCode'                =>  $this->apiParamManager->get('config.pCinemaID'),
                        'cardCode'                  =>  $this->apiParamManager->get('app.pCardId'),
                        'channelRechargeOrderCode'  =>  $this->apiParamManager->get('app.pOrderID'),
                        'thirdPartPayCode'          =>  $this->apiParamManager->get('app.pPlatNo'),
                        'amount'                    =>  $this->apiParamManager->get('app.pPrice'),
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
        $data = $this->apiUtil
                    ->setMemberUrl()
                    ->setFuncVersion()
                    ->setCallMethod('queryOrderPrice')
                    ->withData([
                        'orderCode'     =>  $this->apiParamManager->get('app.pOrderID'),
                        'cardCode'      =>  $this->apiParamManager->get('app.pCardId'),
                    ])
                    ->send();

        return $this->transform->cardScheduleDiscount($data);
    }

    /**
     * 会员卡购票记录
     * @Author   wenqiang
     * @DateTime 2017-08-30T10:16:03+0800
     * @version  [version]
     * @return   [type]                   [description]
     */
    public function cardTransRecord()
    {
        $consData = $this->apiUtil
                        ->setMemberUrl()
                        ->setFuncVersion()
                        ->setCallMethod('queryTicketInfo')
                        ->withData([
                            'cinemaCode'    =>  $this->apiParamManager->get('config.pCinemaID'),
                            'cardCode'      =>  $this->apiParamManager->get('app.pCardId'),
                            'password'      =>  $this->apiParamManager->get('app.pPassword'),
                            'startDate'     =>  $this->apiParamManager->get('app.pStartDate'),
                            'endDate'       =>  $this->apiParamManager->get('app.pEndDate'),
                        ])
                        ->send();

        $rechargeData = $this->apiUtil
                            ->setCallMethod('queryRechargeInfo')
                            ->withData([
                                'cinemaCode'    =>  $this->apiParamManager->get('config.pCinemaID'),
                                'cardCode'      =>  $this->apiParamManager->get('app.pCardId'),
                                'password'      =>  $this->apiParamManager->get('app.pPassword'),
                                'startDate'     =>  $this->apiParamManager->get('app.pStartDate'),
                                'endDate'       =>  $this->apiParamManager->get('app.pEndDate'),
                            ])
                            ->send();

        return $this->transform->cardTransRecord($consData, $rechargeData);
    }

    /**
     * 获取影院列表
     * wenqiang
     * 2017-03-28T16:04:41+0800
     * @return [type] [description]
     */
    public function getCinemas()
    {
        $data = $this->apiUtil
        			->setTicketUrl()
                    ->setFuncVersion('v1')
                    ->setCallMethod('queryCinemas')
                    ->withData([])
                    ->send();

        return $this->transform->getCinemas($data);
    }
}
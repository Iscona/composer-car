<?php

namespace biqu\TicketPlatform\M1905;

use biqu\APIInterface;
use biqu\TicketPlatform\M1905\API;
use biqu\TicketPlatform\M1905\Transform;
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
        $schedules = $this->apiUtil
                    ->setCallMethod('GetCinemaAllSession')
                    ->withData([
                        'pAppCode'  => $this->apiParamManager->get('config.pAppCode'),
                        'pCinemaID' => $this->apiParamManager->get('config.pCinemaID'),
                    ])
                    ->send();

        $hallInfo = $this->getHalls([
                        'pAppCode'  =>  $this->apiParamManager->get('config.pAppCode'),
                        'pCinemaID' =>  $this->apiParamManager->get('config.pCinemaID'),
                    ]);

        return $this->transform->getAllSchedule($schedules, $hallInfo);
    }

    /**
     * 获取影厅名称
     * wenqiang
     * 2017-03-15T11:21:57+0800
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function getHalls($data)
    {
        $data = $this->apiUtil
                    ->setCallMethod('GetScreen')
                    ->withData($data)
                    ->send();

        return $this->transform->getHalls($data);
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
                        ->setCallMethod('GetScreenSeat')
                        ->withData([
                            'pAppCode'  => $this->apiParamManager->get('config.pAppCode'),
                            'pCinemaID' => $this->apiParamManager->get('config.pCinemaID'),
                            'pScreenID' => $this->apiParamManager->get('app.pScreenID'),
                        ])
                        ->send();

        $seatStatus = $this->apiUtil
                    ->setCallMethod('GetSessionSeat')
                    ->withData([
                        'pAppCode'   => $this->apiParamManager->get('config.pAppCode'),
                        'pSessionID' => $this->apiParamManager->get('app.pSessionID'),
                    ])
                    ->send();

        return $this->transform->getSeatInfo($seatInfos, $seatStatus);
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
                    ->setCallMethod('LockSeatCustom')
                    ->withData([
                        'pAppCode'      => $this->apiParamManager->get('config.pAppCode'),
                        'pSessionID'    => $this->apiParamManager->get('app.pSessionID'),
                        'pOrderID'      => $this->apiParamManager->get('app.pOrderID'),
                        'pSeatNo'       =>
                            join(',', array_column($this->apiParamManager->get('app.pSeatInfos'), 'seat_no')),
                        'pTicketPrice'  =>
                            join(',', array_fill(0, $num, $this->apiParamManager->get('app.pTicketPrice'))),
                        'pFee'          =>
                            join(',', array_fill(0, $num, $this->apiParamManager->get('app.pFee'))),
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
                    ->setCallMethod('UnLockSeat')
                    ->withData([
                        'pAppCode'      => $this->apiParamManager->get('config.pAppCode'),
                        'pOrderID'      => $this->apiParamManager->get('app.pOrderNO'),
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
        $data = $this->apiUtil
                    ->setCallMethod('SellTicketCustom/v2')
                    ->withData([
                        'pAppCode'      => $this->apiParamManager->get('config.pAppCode'),
                        'pOrderID'      => $this->apiParamManager->get('app.pOrderID'),
                        'pSeatNo'       => join(',', $this->apiParamManager->get('app.pSeatNo')),
                        'pTicketPrice'  => join(',', $this->apiParamManager->get('app.pTicketPrice')),
                        'pFee'          => join(',', $this->apiParamManager->get('app.pFee'))
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
        $data = $this->apiUtil
                    ->setCallMethod('RefundTicket')
                    ->withData([
                        'pAppCode'  =>  $this->apiParamManager->get('config.pAppCode'),
                        'pOrderID'  =>  $this->apiParamManager->get('app.pOrderNO'),
                        'pPrintNo'  =>  $this->apiParamManager->get('app.pValidCode')
                    ])
                    ->send();

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
        if ($this->apiParamManager->get('app.pRequestType') == 0) {
            return $this->getOrderInfo();
        }

        \Log::info(['请求打票接口' => $this->apiParamManager->get('app.pOrderNO')]);

        $printData = $this->apiUtil
                    ->setCallMethod('UpdatePrintStatus')
                    ->withData([
                        'pAppCode'      =>  $this->apiParamManager->get('config.pAppCode'),
                        'pOrderID'      =>  $this->apiParamManager->get('app.pOrderNO'),
                    ])
                    ->send();

        $orderData = $this->apiUtil
                    ->setCallMethod('GetOrderStatus')
                    ->withData([
                        'pAppCode'  =>  $this->apiParamManager->get('config.pAppCode'),
                        'pOrderID'  =>  $this->apiParamManager->get('app.pOrderNO')
                    ])
                    ->send();

        $qrCodeData = $this->getQrcode();

        return $this->transform->printTicket($printData, $qrCodeData, $orderData);
    }

    /**
     * 查询订单信息
     * wenqiang
     * 2017-03-09T11:16:48+0800
     * @return [type] [description]
     */
    public function getOrderInfo()
    {
        $orderData = $this->apiUtil
                    ->setCallMethod('GetOrderStatus')
                    ->withData([
                        'pAppCode'  =>  $this->apiParamManager->get('config.pAppCode'),
                        'pOrderID'  =>  $this->apiParamManager->get('app.pOrderNO')
                    ])
                    ->send();

        $qrCodeData = $this->getQrcode();

        return $this->transform->getOrderInfo($orderData, $qrCodeData);
    }

    /**
     * 获取二维码信息
     * wenqiang
     * 2017-03-09T11:16:48+0800
     * @return [type] [description]
     */
    public function getQrcode()
    {
        $qrCodeData = $this->apiUtil
                        ->setCallMethod('GetQrcode')
                        ->withData([
                            'pAppCode'  =>  $this->apiParamManager->get('config.pAppCode'),
                            'pOrderID'  =>  $this->apiParamManager->get('app.pOrderNO')
                        ])
                        ->send();

        return $qrCodeData;
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
                    ->setCallMethod('GetOrderStatus')
                    ->withData([
                        'pAppCode'  =>  $this->apiParamManager->get('config.pAppCode'),
                        'pOrderID'  =>  $this->apiParamManager->get('app.pOrderID')
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
                    ->setCallMethod('MakeMemberCard')
                    ->withData([
                        'pAppCode'      =>  $this->apiParamManager->get('config.pAppCode'),
                        'pCinemaID'     =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pCardUser'     =>  $this->apiParamManager->get('app.pMemberName'),
                        'pCardPwd'      =>  $this->apiParamManager->get('app.pPassword'),
                        'pMobile'       =>  $this->apiParamManager->get('app.pMobile'),
                        'pCardLevelID'  =>  $this->apiParamManager->get('app.pMemberTypeNo'),
                        'pIdentityCard' =>  $this->apiParamManager->get('app.pIdNum'),
                        'pBalance'      =>  $this->apiParamManager->get('app.pBalance'),
                        'pGender'       =>  $this->apiParamManager->get('app.pGender'),     //性别  不传默认为男
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
        $cardId = $this->apiParamManager->get('app.pCardId');

        if (substr($cardId, 0, 1) == 'e') {
            $type = 1;
        } else {
            $type = 0;
        }

        $data = $this->apiUtil
                    ->setCallMethod('MemberInfo')
                    ->withData([
                        'pAppCode'      =>  $this->apiParamManager->get('config.pAppCode'),
                        'pCinemaID'     =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pCardNo'       =>  $cardId,
                        'pCardPwd'      =>  $this->apiParamManager->get('app.pPassword'),
                        'pMobile'       =>  $this->apiParamManager->get('app.pMobile'),
                    ])
                    ->send();

        return $this->transform->cardInfo($data, $type, $cardId);
    }

    /**
     * 会员卡购票
     * wenqiang
     * 2017-03-09T11:28:13+0800
     * @return [type] [description]
     */
    public function cardBuyTicket()
    {
        $data = $this->apiUtil
                    ->setCallMethod('SellTicketCustom/member')
                    ->withData([
                        'pAppCode'      =>  $this->apiParamManager->get('config.pAppCode'),
                        'pOrderID'      =>  $this->apiParamManager->get('app.pOrderID'),
                        'pSeatNo'       =>  join(',', $this->apiParamManager->get('app.pSeatNo')),
                        'pMemberPrice'  =>  join(',', $this->apiParamManager->get('app.pTicketPrice')),
                        'pFee'          =>  join(',', $this->apiParamManager->get('app.pFee')),
                        'pLowestPrice'  =>  $this->apiParamManager->get('app.pLowestPrice'),
                        'pCardNo'       =>  $this->apiParamManager->get('app.pCardId'),
                        'pCardPwd'      =>  $this->apiParamManager->get('app.pPassword'),
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
                    ->setCallMethod('MemberTypeList')
                    ->withData([
                        'pAppCode'  =>  $this->apiParamManager->get('config.pAppCode'),
                        'pCinemaID' =>  $this->apiParamManager->get('config.pCinemaID'),
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
                    ->setCallMethod('RechargeMemberCard')
                    ->withData([
                        'pAppCode'  =>  $this->apiParamManager->get('config.pAppCode'),
                        'pCinemaID' =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pCardNo'   =>  $this->apiParamManager->get('app.pCardId'),
                        'pOrderID'  =>  $this->apiParamManager->get('app.pOrderID'),
                        'pBalance'  =>  $this->apiParamManager->get('app.pPrice')
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
                    ->setCallMethod('MemberPrice')
                    ->withData([
                        'pAppCode'      =>  $this->apiParamManager->get('config.pAppCode'),
                        'pSessionID'    =>  $this->apiParamManager->get('app.pFeatureNo'),
                        'pCardNo'       =>  $this->apiParamManager->get('app.pCardId'),
                        'pCardPwd'      =>  $this->apiParamManager->get('app.pPassword'),
                    ])
                    ->send();

        $standPrice = $this->apiParamManager->get('app.pStandPrice');

        return $this->transform->cardScheduleDiscount($data, $standPrice);
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
                    ->setCallMethod('GetCinema')
                    ->withData([
                        'pAppCode'      =>  $this->apiParamManager->get('config.pAppCode'),
                    ])
                    ->send();

        return $this->transform->getCinema($data);
    }
}

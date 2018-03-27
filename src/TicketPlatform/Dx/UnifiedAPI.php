<?php

namespace biqu\TicketPlatform\Dx;

use biqu\APIInterface;
use biqu\TicketPlatform\Dx\API;
use biqu\TicketPlatform\ApiParamManager;
use biqu\TicketPlatform\Dx\Transform;


class UnifiedAPI implements APIInterface
{

    protected $api;

    protected $transform;

    protected $apiParamManager;

    public function __construct(ApiParamManager $apiParamManager)
    {
        $this->apiParamManager = $apiParamManager;
        $this->api = new API($apiParamManager);
        $this->transform = new Transform();
    }

    /**
     * 获取影院信息
     * wenqiang
     * 2017-05-03T13:49:58+0800
     * @return [type] [description]
     */
    public function getCinemas()
    {
        $data = $this->api
                    ->setCallMEthod('partner/cinemas')
                    ->withData([
                        'pid'       =>  $this->apiParamManager->get('config.pPartnerCode'),
                    ])
                    ->send();

        return $this->transform->getCinemas($data);
    }

    /**
     * 获取所有排期
     * wenqiang
     * 2017-05-03T11:33:34+0800
     * @return [type] [description]
     */
    public function getAllSchedule()
    {
        $data = $this->api
                    ->setCallMethod('cinema/plays')
                    ->withData([
                        'cid'       =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'       =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'start'     =>  '',
                        'end'       =>  '',
                    ])
                    ->send();

        return $this->transform->getAllSchedule($data);
    }

    /**
     * 影厅座位图信息
     * wenqiang
     * 2017-05-03T16:52:11+0800
     * @return [type] [description]
     */
    public function getSeatInfo()
    {
        $data = $this->api
                    ->setCallMethod('play/seat-status')
                    ->withData([
                        'cid'               =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'               =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'play_id'           =>  $this->apiParamManager->get('app.pSessionID'),
                        'play_update_time'  =>  $this->apiParamManager->get('app.pUpdateTime'),
                    ])
                    ->send();

        return $this->transform->getSeatStatus($data);
    }

    /**
     * 锁座
     * wenqiang
     * 2017-05-04T13:55:48+0800
     * @return [type] [description]
     */
    public function lockSeat()
    {
        if ($this->apiParamManager->get('app.pCardId')) {
            return $this->mLockSeat();
        } else {
            return $this->nLockSeat();
        }
    }

    /**
     * 非会员锁座
     * wenqiang
     * 2017-05-08T15:00:11+0800
     * @return [type] [description]
     */
    public function nLockSeat()
    {
        $seatInfo = $this->apiParamManager->get('app.pSeatInfos');
        $data = $this->api
                    ->setCallMethod('seat/lock')
                    ->withData([
                        'cid'               =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'               =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'play_id'           =>  $this->apiParamManager->get('app.pSessionID'),
                        'seat_id'           =>  join(',', array_column($seatInfo, 'seat_no')),
                        'play_update_time'  =>  $this->apiParamManager->get('app.pUpdateTime'),
                    ])
                    ->send();

        return $this->transform->lockSeat($data);
    }

    /**
     * 会员锁座
     * wenqiang
     * 2017-05-08T14:55:37+0800
     * @return [type] [description]
     */
    public function mLockSeat()
    {
        $seatInfo = $this->apiParamManager->get('app.pSeatInfos');
        $data = $this->api
                    ->setUrl('http://mapi.platform.yinghezhong.com/')
                    ->setCallMethod('seat/lock')
                    ->withData([
                        'cid'               =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'               =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'play_id'           =>  $this->apiParamManager->get('app.pSessionID'),
                        'seat_id'           =>  join(',', array_column($seatInfo, 'seat_no')),
                        'play_update_time'  =>  $this->apiParamManager->get('app.pUpdateTime'),
                        'card'              =>  $this->apiParamManager->get('app.pCardId'),
                    ])
                    ->send();

        return $this->transform->mLockSeat($data);
    }

    /**
     * 解锁
     * wenqiang
     * 2017-05-04T14:19:06+0800
     * @return [type] [description]
     */
    public function unlockSeat()
    {
        $seatInfo = $this->apiParamManager->get('app.pSeatInfos');
        $data = $this->api
                    ->setCallMethod('seat/unlock')
                    ->withData([
                        'cid'   =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'   =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'play_id'   =>  $this->apiParamManager->get('app.pSessionID'),
                        'seat_id'   =>  join(',', array_column($seatInfo, 'seat_no')),
                        'lock_flag' =>  $this->apiParamManager->get('app.pOrderNO'),
                    ])
                    ->send();

        return $this->transform->unlockSeat($data);
    }

    /**
     * 生成订单
     * wenqiang
     * 2017-05-04T14:31:45+0800
     * @return [type] [description]
     */
    public function ticketPlaceOrder()
    {
        $seatNo = $this->apiParamManager->get('app.pSeatNo');
        $handlingfee = $this->apiParamManager->get('app.pHandlingfee');
        $price = $this->apiParamManager->get('app.pTicketPrice');
        $traceNo = $this->apiParamManager->get('app.pTraceNo');
        $seat = [];

        for ($i = 0; $i < count($seatNo); $i++) {
            $seat[] = join('-', [$seatNo[$i], $handlingfee[$i], $price[$i]]);
        }

        $data = $this->api
                    ->setCallMethod('seat/lock-buy')
                    ->withData([
                        'cid'                   =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'                   =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'play_id'               =>  $this->apiParamManager->get('app.pSessionID'),
                        'seat'                  =>  join(',', $seat),
                        'lock_flag'             =>  $this->apiParamManager->get('app.pOrderID'),
                        'play_update_time'      =>  $this->apiParamManager->get('app.pUpdateTime'),
                        'partner_buy_ticket_id' =>  $traceNo,
                        'mobile'                =>  $this->apiParamManager->get('app.pRecvMobilePhone'),
                    ])
                    ->send();

        return $this->transform->ticketPlaceOrder($data, $traceNo);
    }

    /**
     * 退票
     * wenqiang
     * 2017-05-05T09:32:44+0800
     * @return [type] [description]
     */
    public function refundOrder()
    {
        $validCode = $this->apiParamManager->get('app.pValidCode');

        $data = $this->api
                    ->setCallMethod('ticket/refund')
                    ->withData([
                        'cid'                       =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'                       =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'ticket_flag1'              =>  substr($validCode, 0, 6),
                        'ticket_flag2'              =>  substr($validCode, 6, 6),
                        'partner_buy_ticket_id'     =>  $this->apiParamManager->get('app.pOrderNO'),
                        'partner_refund_ticket_id'  =>  $this->apiParamManager->get('app.pRefundNo'),
                    ])
                    ->send();

        return $this->transform->refundOrder($data);
    }

    /**
     * 会员退票
     * wenqiang
     * 2017-05-08T17:54:24+0800
     * @return [type] [description]
     */
    public function mRefundOrder()
    {
        $data = $this->api
                    ->setMemberUrl()
                    ->withData([
                        'cid'                       =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'                       =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'partner_buy_ticket_id'     =>  $this->apiParamManager->get('app.pOrderNO'),
                        'partner_refund_ticket_id'  =>  $this->apiParamManager->get('app.pRefundNo'),
                    ])
                    ->send();

        return $this->mRefundOrder($data);
    }

    /**
     * 打印票
     * wenqiang
     * 2017-05-05T13:50:49+0800
     * @return [type] [description]
     */
    public function printTicket()
    {
        if ($this->apiParamManager->get('app.pRequestType') == 0) {
            return $this->printTicketStatus();
        }

        if ($this->apiParamManager->get('app.pCardId')) {
            return $this->mPrintTicket();
        } else {
            return $this->nPrintTicket();
        }
    }

    /**
     * 非会员打票
     * wenqiang
     * 2017-05-08T19:00:06+0800
     * @return [type] [description]
     */
    public function nPrintTicket()
    {
        $validCode = $this->apiParamManager->get('app.pValidCode');
        $data = $this->api
                    ->setCallMethod('ticket/print')
                    ->withData([
                        'cid'           =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'           =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'ticket_flag1'  =>  substr($validCode, 0, 6),
                        'ticket_flag2'  =>  substr($validCode, 6, 6),
                    ])
                    ->send();

        return $this->transform->printTicket($data);
    }

    /**
     * 会员打票
     * wenqiang
     * 2017-05-08T18:58:29+0800
     * @return [type] [description]
     */
    public function mPrintTicket()
    {
        $validCode = $this->apiParamManager->get('app.pValidCode');
        $data = $this->api
                    ->setMemberUrl()
                    ->setCallMethod('ticket/print')
                    ->withData([
                        'cid'           =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'           =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'card'          =>  $this->apiParamManager->get('app.pCardId'),
                        'ticket_flag1'  =>  substr($validCode, 0, 6),
                        'ticket_flag2'  =>  substr($validCode, 6, 6),
                    ])
                    ->send();

        return $this->transform->printTicket($data);
    }

    /**
     * 好看订单是否已打票 1 已打  0 未打
     * qys
     * 2017-06-05T16:54:45+0800
     * @return [type] [description]
     */
    public function printTicketStatus()
    {
        $validCode = $this->apiParamManager->get('app.pValidCode');
        $data = $this->api
                    ->setCallMethod('ticket/info')
                    ->withData([
                        'cid'           =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'           =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'ticket_flag1'  =>  substr($validCode, 0, 6),
                        'ticket_flag2'  =>  substr($validCode, 6, 6),
                    ])
                    ->send();

        return $this->transform->printTicketStatus($data);
    }

    /**
     * 订单详情
     * wenqiang
     * 2017-05-08T18:58:12+0800
     * @return [type] [description]
     */
    public function getOrderInfo(){}

    /**
     * 查询订单状态
     * wenqiang
     * 2017-05-05T14:54:12+0800
     * @return [type] [description]
     */
    public function getOrderStatus()
    {
        if ($this->apiParamManager->get('app.pCardId')) {
            return $this->mGetOrderStatus();
        } else {
            return $this->nGetOrderStatus();
        }
    }

    /**
     * 非会员查询订单状态
     * wenqiang
     * 2017-05-08T17:26:15+0800
     * @return [type] [description]
     */
    public function nGetOrderStatus()
    {
        $data = $this->api
                    ->setCallMethod('order/status')
                    ->withData([
                        'cid'               =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'               =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'partner_order_id'  =>  $this->apiParamManager->get('app.pOrderID'),
                    ])
                    ->send();

        return $this->transform->getOrderStatus($data);
    }

    /**
     * 会员查询订单状态
     * wenqiang
     * 2017-05-08T17:20:46+0800
     * @return [type] [description]
     */
    public function mGetOrderStatus()
    {
        $data = $this->api
                    ->setUrl('http://mapi.platform.yinghezhong.com/')
                    ->setCallMethod('order/status')
                    ->withData([
                        'cid'               =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'               =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'partner_order_id'  =>  $this->apiParamManager->get('app.pOrderID'),
                    ])
                    ->send();

        return $this->transform->getOrderStatus($data);
    }

    /**
     * 注册会员卡
     * wenqiang
     * 2017-05-04T15:01:22+0800
     * @return [type] [description]
     */
    public function cardRegister(){}

    /**
     * 会员卡校验
     * wenqiang
     * 2017-05-05T18:22:34+0800
     * @return [type] [description]
     */
    public function cardLogin()
    {
        $data = $this->api
                    ->setUrl('http://mapi.platform.yinghezhong.com/')
                    ->setCallMethod('card/auth')
                    ->withData([
                        'cid'       =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'       =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'card'      =>  $this->apiParamManager->get('app.pCardId'),
                        'password'  =>  md5($this->apiParamManager->get('app.pPassword')),
                    ])
                    ->send();

        return $this->transform->cardLogin($data);
    }

    /**
     * 会员卡信息
     * wenqiang
     * 2017-05-08T09:26:24+0800
     * @return [type] [description]
     */
    public function cardInfo()
    {
        if (!$this->cardLogin()) {
            throw new \Exception('199999', 602);
        }

        $data = $this->api
                    ->setUrl('http://mapi.platform.yinghezhong.com/')
                    ->setCallMethod('card/detail')
                    ->withData([
                        'cid'       =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'       =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'play_id'   =>  '',
                        'card'      =>  $this->apiParamManager->get('app.pCardId'),
                        'max_num'   =>  '',
                    ])
                    ->send();

        return $this->transform->cardInfo($data);
    }

    /**
     * 会员卡购票
     * wenqiang
     * 2017-05-08T14:03:09+0800
     * @return [type] [description]
     */
    public function cardBuyTicket()
    {
        $seatId = $this->apiParamManager->get('app.pSeatNo');
        $handleFee = $this->apiParamManager->get('app.pHandlingfee');
        $price = $this->apiParamManager->get('app.pTicketPrice');
        $ticketType = $this->apiParamManager->get('app.pTicketType');
        $isDiscount = $this->apiParamManager->get('app.pIsDiscount');
        $traceNo = $this->apiParamManager->get('app.pTraceNo');

        $seat = [];
        for ($i = 0; $i < count($seatId); $i++) {
            $seat[] = implode('-', [$seatId[$i], $handleFee[$i], $price[$i], $ticketType[$i], $isDiscount[$i]]);
        }

        $data = $this->api
                    ->setUrl('http://mapi.platform.yinghezhong.com/')
                    ->setCallMethod('seat/lock-buy')
                    ->withData([
                        'cid'                   =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'                   =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'play_id'               =>  $this->apiParamManager->get('app.pSessionID'),
                        'card'                  =>  $this->apiParamManager->get('app.pCardId'),
                        'seat'                  =>  join($seat),
                        'lock_flag'             =>  $this->apiParamManager->get('app.pSerialNum'),
                        'play_update_time'      =>  $this->apiParamManager->get('app.pUpdateTime'),
                        'partner_buy_ticket_id' =>  $traceNo,
                    ])
                    ->send();

        return $this->transform->cardBuyTicket($data, $traceNo);
    }


    /**
     * 会员卡类型级别列表
     * wenqiang
     * 2017-05-08T09:53:02+0800
     * @return [type] [description]
     */
    public function cardType()
    {
        $data = $this->api
                    ->setUrl('http://mapi.platform.yinghezhong.com/')
                    ->setCallMethod('cinema/card-level-rule')
                    ->withData([
                        'cid'   =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'   =>  $this->apiParamManager->get('config.pPartnerCode'),
                    ])
                    ->send();

        return $this->transform->cardType($data);
    }

    /**
     * 会员卡充值
     * wenqiang
     * 2017-05-08T09:52:05+0800
     * @return [type] [description]
     */
    public function cardRecharge()
    {
        $data = $this->api
                    ->setUrl('http://mapi.platform.yinghezhong.com/')
                    ->setCallMethod('card/recharge')
                    ->withData([
                        'cid'                   =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'                   =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'card'                  =>  $this->apiParamManager->get('app.pCardId'),
                        'money'                 =>  $this->apiParamManager->get('app.pPrice'),
                        'partner_deposit_id'    =>  $this->apiParamManager->get('app.pSerialNum'),
                    ])
                    ->send();

        $cardDetailData = $this->cardInfo();

        return $this->transform->cardRecharge($data, $cardDetailData->toArray());
    }

    /**
     * 会员卡对应排期的折扣信息
     * wenqiang
     * 2017-05-08T11:26:19+0800
     * @return [type] [description]
     */
    public function cardScheduleDiscount()
    {
        $data = $this->api
                    ->setUrl('http://mapi.platform.yinghezhong.com/')
                    ->setCallMethod('card/detail')
                    ->withData([
                        'cid'       =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'       =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'play_id'   =>  $this->apiParamManager->get('app.pFeatureNo'),
                        'card'      =>  $this->apiParamManager->get('app.pCardId'),
                        'max_num'   =>  '',
                    ])
                    ->send();

        return $this->transform->cardScheduleDiscount($data);
    }

    /**
     * 会员卡交易记录
     * wenqiang
     * 2017-05-08T14:06:56+0800
     * @return [type] [description]
     */
    public function cardTransRecord()
    {
        $data = $this->api
                    ->setMemberUrl()
                    ->setCallMethod('order/list')
                    ->withData([
                        'cid'   =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'   =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'card'  =>  $this->apiParamManager->get('app.pCardId'),
                        'end'   =>  $this->apiParamManager->get('app.pEndDate'),
                        'start' =>  $this->apiParamManager->get('app.pStartDate'),
                        'page'  =>  '1',
                    ])
                    ->send();

        return $this->transform->cardTransRecord($data);
    }

    /**
     * 会员影厅服务费
     * qys
     * 2017-05-26T17:53:46+0800
     * @return [type] [description]
     */
    public function seatPrice()
    {
        $data = $this->api
                    ->setMemberUrl()
                    ->setCallMethod('seat/price/')
                    ->withData([
                        'cid'       =>  $this->apiParamManager->get('config.pCinemaID'),
                        'pid'       =>  $this->apiParamManager->get('config.pPartnerCode'),
                        'card'      =>  $this->apiParamManager->get('app.pCardId'),
                        'lock_flag' =>  $this->apiParamManager->get('app.pLock_flag'),
                    ])
                    ->send();

        return $this->transform->seatPrice($data);
    }
}
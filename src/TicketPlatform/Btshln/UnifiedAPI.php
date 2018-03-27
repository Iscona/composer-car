<?php

namespace biqu\TicketPlatform\Btshln;

use biqu\APIInterface;
use biqu\TicketPlatform\Btshln\API;
use biqu\TicketPlatform\ApiParamManager;

class UnifiedAPI implements APIInterface
{
    /**
     * [$apiUtil]
     * @var [type]
     */
    protected $apiUtil;


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
    }

    /**
     * 参数在组织
     * qys
     * 2017-07-26T17:27:13+0800
     * @return [type] [description]
     */
    public function params($arr)
    {
        $platform = 'hln';

        $config = [
            'cinema_id'         => $this->apiParamManager->get('config.pCinemaID'),
            'secretkey'         => $this->apiParamManager->get('config.pSecretKey'),
            'channelCode'       => $this->apiParamManager->get('config.pChannelCode'),
            'bts_app_key'       => $this->apiParamManager->get('config.bts_app_key'),
            'country_cinema_id' => $this->apiParamManager->get('config.country_cinema_id'),
            'union_key'         => $this->apiParamManager->get('config.union_key'),
        ];

        return [
            'data'  => json_encode([
                'platform'      => $platform,
                'config'        => $config,
                'app'           => $arr
            ])
        ];
    }

    /**
     * 获得所有排期
     * wenqiang
     * 2017-03-09T11:12:02+0800
     * @return [type] [description]
     */
    public function getAllSchedule()
    {
        $arr = [];

        $app = $this->params($arr);

        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('/schedule')
                    ->withData($app)
                    ->send();

        return $data;
    }

    /**
     * 获取座位信息
     * wenqiang
     * 2017-03-09T11:22:27+0800
     * @return [type] [description]
     */
    public function getSeatInfo()
    {
        $arr = [
            'feature_app_no'    => $this->apiParamManager->get('app.pFeatureAppNo'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('/seat/info')
                    ->withData($app)
                    ->send();

        return $data;
    }

    /**
     * 锁定座位
     * wenqiang
     * 2017-03-15T14:54:18+0800
     * @return [type] [description]
     */
    public function lockSeat()
    {
        $arr = [
            'feature_app_no'    => $this->apiParamManager->get('app.pFeatureAppNo'),
            'seat_infos'        => $this->apiParamManager->get('app.pSeatInfos'),
            'order_id'          => $this->apiParamManager->get('app.pOrderID'),
            'start_time'        => $this->apiParamManager->get('app.pStartTime'),
            'bts_timestart_time'=>  $this->apiParamManager->get('app.bts_timestart_time'),
            'film_name'         =>  $this->apiParamManager->get('app.film_name'),
            'notice_sms_key'    =>  $this->apiParamManager->get('app.notice_sms_key'),
            'notice_sms_secret' =>  $this->apiParamManager->get('app.notice_sms_secret'),
            'cellphone'         =>  $this->apiParamManager->get('app.cellphone'),
            'notice_sms_continuous_lock_seat_fail_tpl_id'   =>  $this->apiParamManager->get('app.notice_sms_continuous_lock_seat_fail_tpl_id'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('/seat/lock')
                    ->withData($app)
                    ->send();

        return $data;
    }

    /**
     * 解锁座位
     * wenqiang
     * 2017-03-09T11:17:39+0800
     * @return [type] [description]
     */
    public function unlockSeat()
    {
        $arr = [
            'order_no'  => $this->apiParamManager->get('app.pOrderNO'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('/seat/unlock')
                    ->withData($app)
                    ->send();

        return $data;
    }

    /**
     * 电影票下单
     * wenqiang
     * 2017-03-09T11:17:27+0800
     * @return [type] [description]
     */
    public function ticketPlaceOrder()
    {
        $arr = [
            'app_seat_no'       =>  $this->apiParamManager->get('app.pAppSeatNo'),
            'serial_num'        =>  $this->apiParamManager->get('app.pSerialNum'),
            'seat_code'         =>  $this->apiParamManager->get('app.pSeatNo'),
            'ticket_price'      =>  $this->apiParamManager->get('app.pTicketPrice'),
            'stand_price'       =>  $this->apiParamManager->get('app.pStandPrice'),
            'service_fee'       =>  $this->apiParamManager->get('app.pServiceFee'),
            'cellphone'         =>  $this->apiParamManager->get('app.pRecvMobilePhone'),
            'is_card'           =>  $this->apiParamManager->get('app.pIs_card'),
            'user_id'           =>  $this->apiParamManager->get('app.pUser_id'),
            'is_bts'            =>  $this->apiParamManager->get('app.pIsBts'),
            'is_cut_out_member' => $this->apiParamManager->get('app.pIs_cut_out_member'),
            'is_cut_out_non_member' => $this->apiParamManager->get('app.pIs_cut_out_non_member'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('/ticket-order/store')
            ->withData($app)
            ->send();

        return $data;
    }

    /**
     * 退票
     * wenqiang
     * 2017-03-09T11:17:18+0800
     * @return [type] [description]
     */
    public function refundOrder()
    {
        $arr = [
            'pay_type'  =>  $this->apiParamManager->get('app.pPayType'),
            'app_seat_no'=>  $this->apiParamManager->get('app.pAppSeatNo'),
            'order_no'  =>  $this->apiParamManager->get('app.pOrderNO'),
            'card_no'   =>  $this->apiParamManager->get('app.pCardId'),
            'password'  =>  $this->apiParamManager->get('app.pPassword'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('/ticket-order/refund')
                    ->withData($app)
                    ->send();

        return $data;
    }

    /**
     * 打印票 0 查看状态  1 打印
     * wenqiang
     * 2017-03-09T11:17:02+0800
     * @return [type] [description]
     */
    public function printTicket()
    {
        \Log::info(['请求打票接口' => $this->apiParamManager->get('app.pOrderNO') .
                '|' .
                $this->apiParamManager->get('app.pValidCode')]);

        $arr = [
            'order_no'      => $this->apiParamManager->get('app.pOrderNO'),
            'valid_code'    => $this->apiParamManager->get('app.pValidCode'),
            'request_type'  => $this->apiParamManager->get('app.pRequestType'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('/ticket-order/print')
                    ->withData($app)
                    ->send();

        return $data;
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
        $arr = [
            'serial_num'    => $this->apiParamManager->get('app.pSerialNum'),
            'app_seat_no'   => $this->apiParamManager->get('app.pAppSeatNo'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('/ticket-order/status')
            ->withData($app)
            ->send();

        return $data;
    }

    /**
     * 注册会员卡
     * wenqiang
     * 2017-03-09T11:28:30+0800
     * @return [type] [description]
     */
    public function cardRegister()
    {
        $arr = [
            'is_bts'            => $this->apiParamManager->get('app.pIsBts'),
            'is_bts_member'     => $this->apiParamManager->get('app.pIsBtsMember'),
            'password'          => $this->apiParamManager->get('app.pPassword'),
            'cellphone'         => $this->apiParamManager->get('app.pMobile'),
            'id_num'            => $this->apiParamManager->get('app.pIdNum'),
            'member_name'       => $this->apiParamManager->get('app.pMemberName'),
            'member_type_no'    => $this->apiParamManager->get('app.pMemberTypeNo'),
            'trace_no'          => $this->apiParamManager->get('app.pTraceNo'),
            'initial_money'     => number_format($this->apiParamManager->get('app.pBalance'), 2, '.', ''),
            'service_fee'       => number_format($this->apiParamManager->get('app.pCardCostFee'), 2, '.', ''),
            'cost_fee'          => number_format($this->apiParamManager->get('app.pAnnualFee'), 2, '.', ''),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('/member/register')
            ->withData($app)
            ->send();

        return $data;
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
        $arr = [
            'is_bts'        => $this->apiParamManager->get('app.pIsBts'),
            'is_bts_member' => $this->apiParamManager->get('app.pIsBtsMember'),
            'card_no'       => $this->apiParamManager->get('app.pCardId'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('/member/info')
            ->withData($app)
            ->send();

        return $data;
    }

    /**
     * 会员卡购票
     * wenqiang
     * 2017-03-09T11:28:13+0800
     * @return [type] [description]
     */
    public function cardBuyTicket()
    {
        $arr = [
            'serial_num'=>  $this->apiParamManager->get('app.pOrderID'),
            'app_seat_no'=> $this->apiParamManager->get('app.pAppSeatNo'),
            'card_no'   =>  $this->apiParamManager->get('app.pCardId'),
            'password'  =>  $this->apiParamManager->get('app.pPassword'),
            'cellphone' =>  $this->apiParamManager->get('app.pRecvMobilePhone'),
            'pay_price' =>  $this->apiParamManager->get('app.pPayPrice'),
            'seat_code' =>  $this->apiParamManager->get('app.pSeatNo'),
            'is_card'   =>  $this->apiParamManager->get('app.pIs_card'),
            'user_id'   =>  $this->apiParamManager->get('app.pUser_id'),
            'explain'   =>  $this->apiParamManager->get('app.pExplain'),
            'remarks'   =>  $this->apiParamManager->get('app.pRemarks'),
            'is_bts'            => $this->apiParamManager->get('app.pIsBts'),
            'is_bts_member'     => $this->apiParamManager->get('app.pIsBtsMember'),
            'is_bts_ticket'     => $this->apiParamManager->get('app.pIsBtsTicket'),
            'ticket_price'      => $this->apiParamManager->get('app.pTicketPrice'),
            'service_fee'       =>  $this->apiParamManager->get('app.pServiceFee'),
            'stand_price'       =>  $this->apiParamManager->get('app.pAppPrice'),
            'is_cut_out_member' => $this->apiParamManager->get('app.pIs_cut_out_member'),
            'is_cut_out_non_member' => $this->apiParamManager->get('app.pIs_cut_out_non_member'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('/member/buy-ticket')
            ->withData($app)
            ->send();

        return $data;
    }

    /**
     * 会员卡所有类型
     * wenqiang
     * 2017-03-09T11:27:58+0800
     * @return [type] [description]
     */
    public function cardType()
    {
        $arr = [];

        $app = $this->params($arr);

        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('/member/types')
                    ->withData($app)
                    ->send();

        return $data;
    }

    /**
     * 会员卡充值
     * wenqiang
     * 2017-03-09T11:27:48+0800
     * @return [type] [description]
     */
    public function cardRecharge()
    {
        $arr = [
            'card_no'       => $this->apiParamManager->get('app.pCardId'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'trace_no'      => $this->apiParamManager->get('app.pOrderID'),
            'third_no'      => $this->apiParamManager->get('app.pPlatNo'),
            'amount'        => $this->apiParamManager->get('app.pPrice'),
            'explain'       => $this->apiParamManager->get('app.pExplain'),
            'remarks'       => $this->apiParamManager->get('app.pRemarks'),
            'is_bts'        => $this->apiParamManager->get('app.pIsBts'),
            'is_bts_member' => $this->apiParamManager->get('app.pIsBtsMember'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('/member/recharge')
            ->withData($app)
            ->send();

        return $data;
    }

    /**
     * 会员卡扣款
     * @Author   wenqiang
     * @DateTime 2017-10-09T18:42:13+0800
     * @version  [version]
     * @return   [type]                   [description]
     */
    public function memberConsume()
    {
        $arr = [
            'card_no'       => $this->apiParamManager->get('app.pCardId'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'amount'        => $this->apiParamManager->get('app.pPrice'),
            'explain'       => $this->apiParamManager->get('app.pExplain'),
            'remarks'       => $this->apiParamManager->get('app.pRemarks'),
            'is_bts'        => $this->apiParamManager->get('app.pIsBts'),
            'is_bts_member' => $this->apiParamManager->get('app.pIsBtsMember'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('/member/consume')
            ->withData($app)
            ->send();

        return $data;
    }

    /**
     * 退费
     * qys
     * 2017-09-30T16:25:49+0800
     * @return [type] [description]
     */
    public function cardRefund()
    {
        $arr = [
            'card_no'       => $this->apiParamManager->get('app.pCardId'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'amount'        => $this->apiParamManager->get('app.pPrice'),
            'explain'       => $this->apiParamManager->get('app.pExplain'),
            'remarks'       => $this->apiParamManager->get('app.pRemarks'),
            'is_bts'        => $this->apiParamManager->get('app.pIsBts'),
            'is_bts_member' => $this->apiParamManager->get('app.pIsBtsMember'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('/member/refund')
            ->withData($app)
            ->send();

        return $data;
    }

    /**
     * 会员卡对应的排期折扣
     * wenqiang
     * 2017-03-09T11:27:32+0800
     * @return [type] [description]
     */
    public function cardScheduleDiscount()
    {
        $arr = [
            'card_no'       => $this->apiParamManager->get('app.pCardId'),
            'app_seat_no'   => $this->apiParamManager->get('app.pOrderID'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('/member/discount')
            ->withData($app)
            ->send();

        return $data;
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
        $arr = [
            'card_no'       => $this->apiParamManager->get('app.pCardId'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'start_date'    => $this->apiParamManager->get('app.pStartDate'),
            'end_date'      => $this->apiParamManager->get('app.pEndDate'),
            'is_bts'            => $this->apiParamManager->get('app.pIsBts'),
            'is_bts_member'     => $this->apiParamManager->get('app.pIsBtsMember'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('/member/record')
                    ->withData($app)
                    ->send();

        return $data;
    }
}
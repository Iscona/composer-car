<?php

namespace biqu\TicketPlatform\Btsfhjy;

use biqu\APIInterface;
use biqu\TicketPlatform\Btsfhjy\API;
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
    }

    /**
     * 获得所有排期
     * wenqiang
     * 2017-03-09T11:12:02+0800
     * @return [type] [description]
     */
    public function getAllSchedule()
    {
        $arr = [
            'plan_date' => $this->apiParamManager->get('app.pPlanDate')
        ];

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
            'hall_no'           => $this->apiParamManager->get('app.pScreenID'),
            'feature_app_no'    => $this->apiParamManager->get('app.pFeatureAppNo'),
            'schedule_key'      => $this->apiParamManager->get('app.pScheduleKey'),
            'area'              => $this->apiParamManager->get('app.pArea'),
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
     * 2017-03-09T11:17:52+0800
     * @return [type] [description]
     */
    public function lockSeat()
    {
        $arr = [
            'feature_app_no'    => $this->apiParamManager->get('app.pFeatureAppNo'),
            'serial_num'        => $this->apiParamManager->get('app.pSerialNum'),
            'seat_infos'        => $this->apiParamManager->get('app.pSeatInfos'),
            'schedule_key'      => $this->apiParamManager->get('app.pScheduleKey'),
            'start_time'        => $this->apiParamManager->get('app.pStartTime'),
            'bts_timestart_time' => $this->apiParamManager->get('app.bts_timestart_time'),
            'film_name'         => $this->apiParamManager->get('app.film_name'),
            'notice_sms_key'    => $this->apiParamManager->get('app.notice_sms_key'),
            'notice_sms_secret' => $this->apiParamManager->get('app.notice_sms_secret'),
            'notice_sms_continuous_lock_seat_fail_tpl_id' => $this->apiParamManager->get('app.notice_sms_continuous_lock_seat_fail_tpl_id'),
            'cellphone'         => $this->apiParamManager->get('app.cellphone')
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
            'app_seat_no'  => $this->apiParamManager->get('app.pOrderNO'),
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
            'feature_app_no'    =>  $this->apiParamManager->get('app.pFeatureAppNo'),
            'app_seat_no'       =>  $this->apiParamManager->get('app.pSerialNum'),
            'pay_type'          =>  $this->apiParamManager->get('app.pPayType'),
            'cellphone'         =>  $this->apiParamManager->get('app.pRecvMobilePhone'),
            'seat_code'         =>  $this->apiParamManager->get('app.pSeatNo'),
            'ticket_price'      =>  $this->apiParamManager->get('app.pTicketPrice'),
            'service'           =>  $this->apiParamManager->get('app.pHandlingfee'),
            'schedule_key'      =>  $this->apiParamManager->get('app.pScheduleKey'),
            'platform_no'       =>  $this->apiParamManager->get('app.pPlatformTraceNo'),
            'serial_num'        =>  $this->apiParamManager->get('app.pTraceNo'),
            'user_id'           =>  $this->apiParamManager->get('app.pUser_id'),
            'is_bts'            =>  $this->apiParamManager->get('app.pIsBts'),
            'is_bts_member'     => $this->apiParamManager->get('app.pIsBtsMember'),
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
            'order_no'      => $this->apiParamManager->get('app.pOrderNO'),
            'card_id'       => $this->apiParamManager->get('app.pCardId'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'amout'         => $this->apiParamManager->get('app.pPrice'),
            'explain'       => $this->apiParamManager->get('app.pExplain'),
            'remarks'       => $this->apiParamManager->get('app.pRemarks'),
            'pay_type'      => $this->apiParamManager->get('app.pPayType'),
            'is_bts'        => $this->apiParamManager->get('app.pIsBts'),
            'is_bts_member' => $this->apiParamManager->get('app.pIsBtsMember'),
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

        $arr = [
            'order_no'      => $this->apiParamManager->get('app.pOrderNO'),
            'valid_code'    => $this->apiParamManager->get('app.pValidCode'),
            'request_type'  => $this->apiParamManager->get('app.pRequestType'),
            'app_seat_no'   => $this->apiParamManager->get('app.pAppSeatNo'),
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
        $arr = [
            'order_no'      => $this->apiParamManager->get('app.pOrderNO'),
            'valid_code'    => $this->apiParamManager->get('app.pValidCode'),
            'request_type'  => $this->apiParamManager->get('app.pRequestType'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
                ->setTicketUrl()
                ->callMethod('/ticket-order/info')
                ->withData($app)
                ->send();

        return $data;
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
        \Log::info(['订单状态' => $arr]);
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
            'password'          => $this->apiParamManager->get('app.pPassword'),
            'cellphone'         => $this->apiParamManager->get('app.pMobile'),
            'id_num'            => $this->apiParamManager->get('app.pIdNum'),
            'member_name'       => $this->apiParamManager->get('app.pMemberName'),
            'initial_money'     => $this->apiParamManager->get('app.pBalance'),
            'member_type_no'    => $this->apiParamManager->get('app.pMemberTypeNo'),
            'trace_no'          => $this->apiParamManager->get('app.pTraceNo'),
            'cost_fee'          => $this->apiParamManager->get('app.pCardCostFee'),
            'member_fee'        => $this->apiParamManager->get('app.pMemberFee'),
            'is_bts_member'     => $this->apiParamManager->get('app.pIsBtsMember'),
            'is_bts'            => $this->apiParamManager->get('app.pIsBts'),
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
     * @datetime 2017-03-10T16:28:44+0800
     *
     * @return [type] [description]
     */
    public function cardInfo()
    {
        $arr = [
            'card_id'       => $this->apiParamManager->get('app.pCardId'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'is_bts_member' => $this->apiParamManager->get('app.pIsBtsMember'),
            'is_bts'        => $this->apiParamManager->get('app.pIsBts'),
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
     * 会员卡退费
     * wenqiang
     * 2017-03-17T17:38:10+0800
     * @return [type] [description]
     */
    public function cardRefund()
    {
        $arr = [
            'card_id'           => $this->apiParamManager->get('app.pCardId'),
            'password'          => $this->apiParamManager->get('app.pPassword'),
            'type'              => $this->apiParamManager->get('app.type'),
            'trace_type'        => $this->apiParamManager->get('app.pTraceType'),
            'trace_no'          => $this->apiParamManager->get('app.pTraceNo'),
            'trace_price'       => $this->apiParamManager->get('app.pTracePrice'),
            'price'             => $this->apiParamManager->get('app.pPrice'),
            'trace_memo'        => $this->apiParamManager->get('app.pRemarks'),
            'trans_info'        => $this->apiParamManager->get('app.pExplain'),
            'is_bts_member'     => $this->apiParamManager->get('app.pIsBtsMember'),
            'is_bts'            => $this->apiParamManager->get('app.pIsBts'),
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
     * 会员卡交易记录
     * wenqiang
     * 2017-03-18T16:14:34+0800
     * @return [type] [description]
     */
    public function cardTransRecord()
    {
        $arr = [
            'card_id'       => $this->apiParamManager->get('app.pCardId'),
            'start_date'    => $this->apiParamManager->get('app.pStartDate'),
            'end_date'      => $this->apiParamManager->get('app.pEndDate'),
            'is_bts_member' => $this->apiParamManager->get('app.pIsBtsMember'),
            'is_bts'        => $this->apiParamManager->get('app.pIsBts'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
                    ->setTicketUrl()
                    ->callMethod('/member/record')
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
            'seat_code'     => $this->apiParamManager->get('app.pSeatNo'),
            'ticket_price'  => $this->apiParamManager->get('app.pTicketPrice'),
            'service'       => $this->apiParamManager->get('app.pHandlingfee'),
            'is_discount'   => $this->apiParamManager->get('app.pIsDiscount'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'card_id'       => $this->apiParamManager->get('app.pCardId'),
            'cellphone'     => $this->apiParamManager->get('app.pRecvMobilePhone'),
            'serial_num'    => $this->apiParamManager->get('app.pTraceNo'),
            'app_seat_no'   => $this->apiParamManager->get('app.pOrderID'),
            'schedule_key'  => $this->apiParamManager->get('app.pScheduleKey'),
            'feature_app_no'=> $this->apiParamManager->get('app.pFeatureAppNo'),
            'is_card'       => $this->apiParamManager->get('app.pIs_card'),
            'user_id'       => $this->apiParamManager->get('app.pUser_id'),
            'is_bts'        => $this->apiParamManager->get('app.pIsBts'),
            'is_bts_member' => $this->apiParamManager->get('app.pIsBtsMember'),
            'is_cut_out_member' => $this->apiParamManager->get('app.pIs_cut_out_member'),
            'is_cut_out_non_member' => $this->apiParamManager->get('app.pIs_cut_out_non_member'),
            'trans_info'    => $this->apiParamManager->get('app.pExplain'),
            'trace_memo'    => $this->apiParamManager->get('app.pRemarks'),
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
        $arr = [
            'is_bts'        => $this->apiParamManager->get('app.pIsBts'),
            'is_bts_member' => $this->apiParamManager->get('app.pIsBtsMember'),
        ];

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
            'card_id'       => $this->apiParamManager->get('app.pCardId'),
            'amout'         => $this->apiParamManager->get('app.pPrice'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'trace_no'      => $this->apiParamManager->get('app.pOutTradeNo'),
            'trace_memo'    => $this->apiParamManager->get('app.pTraceMemo'),
            'trans_info'    => $this->apiParamManager->get('app.pRemarks'),
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
            'card_id'       => $this->apiParamManager->get('app.pCardId'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'amout'         => $this->apiParamManager->get('app.pPrice'),
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
     * 会员卡对应的排期折扣
     * wenqiang
     * 2017-03-09T11:27:32+0800
     * @return [type] [description]
     */
    public function cardScheduleDiscount()
    {
        $arr = [
            'card_id'       => $this->apiParamManager->get('app.pCardId'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'feature_app_no'=> $this->apiParamManager->get('app.pFeatureNo'),
            'section_id'    => $this->apiParamManager->get('app.pSectionId'),
            'schedule_key'  => $this->apiParamManager->get('app.pScheduleKey'),
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
     * bts订单转到对应的平台
     *
     * @return void
     */
    public function btsTurnPlatform()
    {
        $arr = [
            'order_no'       => $this->apiParamManager->get('app.order_no'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('/ticket-order/turn')
            ->withData($app)
            ->send();

        return $data;
    }

    /**
     * bts订单转到对应的平台
     *
     * @return void
     */
    public function btsTurnPlatformStatus()
    {
        $arr = [
            'order_no'       => $this->apiParamManager->get('app.order_no'),
        ];

        $app = $this->params($arr);

        $data = $this->apiUtil
            ->setTicketUrl()
            ->callMethod('/ticket-order/turn-status')
            ->withData($app)
            ->send();

        return $data;
    }

    /**
     * 参数在组织
     * qys
     * 2017-07-26T17:27:13+0800
     * @return [type] [description]
     */
    public function params($arr)
    {
        $platform = 'fhjy';
        $config = [
            'cinema_id'     => $this->apiParamManager->get('config.pCinemaID'),
            'secretkey'     => $this->apiParamManager->get('config.pSecretKey'),
            'version'       => $this->apiParamManager->get('config.pVersion'),
            'channelCode'   => $this->apiParamManager->get('config.pChannelCode'),
            'api_center'    => $this->apiParamManager->get('config.pApiCenter'),
            'bts_app_key'   => $this->apiParamManager->get('config.bts_app_key'),
            'country_cinema_id' => $this->apiParamManager->get('config.country_cinema_id'),
            'union_key'     =>  $this->apiParamManager->get('config.union_key'),
        ];

        return [
            'data'  => json_encode([
                'platform'      => $platform,
                'config'        => $config,
                'app'           => $arr
            ])
        ];
    }
}
<?php

namespace biqu\TicketPlatform\Btsmtx;

use biqu\APIInterface;
use biqu\TicketPlatform\Btsmtx\API;
use biqu\TicketPlatform\ApiParamManager;

class UnifiedAPI implements APIInterface
{
    protected $apiUtil;

    protected $clientParams;

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
            'feature_app_no'    => $this->apiParamManager->get('app.pFeatureAppNo'),
            'hall_no'           => $this->apiParamManager->get('app.pScreenID'),
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
            'pay_type'          => $this->apiParamManager->get('app.pPayType'),
            'ticket_price'      => $this->apiParamManager->get('app.pTicketPrice'),
            'recv_mobile_phone' => $this->apiParamManager->get('app.pRecvMobilePhone'),
            'start_time'        => $this->apiParamManager->get('app.pStartTime'),
            'handlingfee'       => $this->apiParamManager->get('app.pHandlingfee'),
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
    public function ticketPlaceOrder($groundTradeNo = '')
    {
        $arr = [
            'feature_app_no'    =>  $this->apiParamManager->get('app.pFeatureAppNo'),
            'serial_num'        =>  $this->apiParamManager->get('app.pSerialNum'),
            'printpassword'     =>  $this->apiParamManager->get('app.pPrintpassword'),
            'balance'           =>  $this->apiParamManager->get('app.pBalance'),
            'pay_type'          =>  $this->apiParamManager->get('app.pPayType'),
            'recv_mobile_phone' =>  $this->apiParamManager->get('app.pRecvMobilePhone'),
            'send_type'         =>  $this->apiParamManager->get('app.pSendType'),
            'pay_result'        =>  $this->apiParamManager->get('app.pPayResult'),
            'is_cmts_pay'       =>  $this->apiParamManager->get('app.pIsCmtsPay'),
            'is_cmts_send_code' =>  $this->apiParamManager->get('app.pIsCmtsSendCode'),
            'pay_mobile'        =>  $this->apiParamManager->get('app.pPayMobile'),
            'book_sign'         =>  $this->apiParamManager->get('app.pBookSign'),
            'payed'             =>  $this->apiParamManager->get('app.pPayed'),
            'send_mode_id'      =>  $this->apiParamManager->get('app.pSendModeId'),
            'pay_seq_no'        =>  $groundTradeNo,
            'is_card'           =>  $this->apiParamManager->get('app.pIs_card'),
            'user_id'           =>  $this->apiParamManager->get('app.pUser_id'),
            'is_bts'            =>  $this->apiParamManager->get('app.pIsBts'),
            'is_bts_member'     => $this->apiParamManager->get('app.pIsBtsMember'),
            'is_cut_out_member' => $this->apiParamManager->get('app.pIs_cut_out_member'),
            'is_cut_out_non_member' => $this->apiParamManager->get('app.pIs_cut_out_non_member'),
            'third_pay_type'    => $this->apiParamManager->get('app.pThirdPayType'),
            'modify_pric'       => $this->apiParamManager->get('app.pModifyPric'),
            'order_no'          => $this->apiParamManager->get('app.pOrderNO'),
            'app_pric'          => $this->apiParamManager->get('app.pAppPric'),
            'balance_pric'      => $this->apiParamManager->get('app.pBalancePric'),
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
            'desc'          => $this->apiParamManager->get('app.pDesc'),
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
            'password'          => $this->apiParamManager->get('app.pPassword'),
            'mobile_phone'      => $this->apiParamManager->get('app.pMobile'),
            'id_num'            => $this->apiParamManager->get('app.pIdNum'),
            'member_name'       => $this->apiParamManager->get('app.pMemberName'),
            'balance'           => $this->apiParamManager->get('app.pBalance'),
            'score'             => $this->apiParamManager->get('app.pScore'),
            'member_type_no'    => $this->apiParamManager->get('app.pMemberTypeNo'),
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
            'mobile_phone'  => '',
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'type'          => $this->apiParamManager->get('app.type'),
            'start_date'    => $this->apiParamManager->get('app.pStartDate'),
            'end_date'      => $this->apiParamManager->get('app.pEndDate'),
            'is_bts_member' => $this->apiParamManager->get('app.pIsBtsMember'),
            'is_bts'        => $this->apiParamManager->get('app.pIsBts'),
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
            'partner_id'    => $this->apiParamManager->get('app.pPartnerId'),
            'card_id'       => $this->apiParamManager->get('app.pCardId'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'trace_type_no' => $this->apiParamManager->get('app.pTraceTypeNo'),
            'type'          => $this->apiParamManager->get('app.type'),
            'old_price'     => $this->apiParamManager->get('app.pOldPrice'),
            'trace_price'   => $this->apiParamManager->get('app.pTracePrice'),
            'discount'      => $this->apiParamManager->get('app.pDiscount'),
            'feature_no'    => $this->apiParamManager->get('app.pFeatureNo'),
            'film_no'       => $this->apiParamManager->get('app.pFilmNo'),
            'ticket_num'    => $this->apiParamManager->get('app.pTicketNum'),
            'modify_pric'   => $this->apiParamManager->get('app.pModifyPric'),
            'order_no'      => $this->apiParamManager->get('app.pOrderNO'),
            'app_pric'      => $this->apiParamManager->get('app.pAppPric'),
            'balance_pric'  => $this->apiParamManager->get('app.pBalancePric'),
            'user_id'       => $this->apiParamManager->get('app.pUser_id'),
            'is_card'       => $this->apiParamManager->get('app.pIs_card'),
            'is_bts'        => $this->apiParamManager->get('app.pIsBts'),
            'is_bts_member' => $this->apiParamManager->get('app.pIsBtsMember'),
            'feature_app_no'    =>  $this->apiParamManager->get('app.pFeatureAppNo'),
            'serial_num'        =>  $this->apiParamManager->get('app.pSerialNum'),
            'printpassword'     =>  $this->apiParamManager->get('app.pPrintpassword'),
            'balance'           =>  $this->apiParamManager->get('app.pBalance'),
            'pay_type'          =>  $this->apiParamManager->get('app.pPayType'),
            'recv_mobile_phone' =>  $this->apiParamManager->get('app.pRecvMobilePhone'),
            'send_type'         =>  $this->apiParamManager->get('app.pSendType'),
            'pay_result'        =>  $this->apiParamManager->get('app.pPayResult'),
            'is_cmts_pay'       =>  $this->apiParamManager->get('app.pIsCmtsPay'),
            'is_cmts_send_code' =>  $this->apiParamManager->get('app.pIsCmtsSendCode'),
            'pay_mobile'        =>  $this->apiParamManager->get('app.pPayMobile'),
            'book_sign'         =>  $this->apiParamManager->get('app.pBookSign'),
            'payed'             =>  $this->apiParamManager->get('app.pPayed'),
            'send_mode_id'      =>  $this->apiParamManager->get('app.pSendModeId'),
            'is_cut_out_member' => $this->apiParamManager->get('app.pIs_cut_out_member'),
            'is_cut_out_non_member' => $this->apiParamManager->get('app.pIs_cut_out_non_member'),
            'trans_info'        => $this->apiParamManager->get('app.pExplain'),
            'trace_memo'        => $this->apiParamManager->get('app.pRemarks'),
            'third_pay_type'    => $this->apiParamManager->get('app.pThirdPayType'),
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
            'partner_id'    => $this->apiParamManager->get('app.pPartnerId'),
            'card_id'       => $this->apiParamManager->get('app.pCardId'),
            'type'          => $this->apiParamManager->get('app.type'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'price'         => $this->apiParamManager->get('app.pPrice'),
            'trace_memo'    => $this->apiParamManager->get('app.pExplain'),
            'trans_info'    => $this->apiParamManager->get('app.pRemarks'),
            'is_bts'        => $this->apiParamManager->get('app.pIsBts'),
            'is_bts_member' => $this->apiParamManager->get('app.pIsBtsMember'),
            'mobile_phone'  => '',
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
            'price'         => $this->apiParamManager->get('app.pPrice'),
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
            'type'          => $this->apiParamManager->get('app.type'),
            'feature_no'    => $this->apiParamManager->get('app.pFeatureNo'),
            'feature_date'  => $this->apiParamManager->get('app.pFeatureDate'),
            'feature_time'  => $this->apiParamManager->get('app.pFeatureTime'),
            'stand_price'   => $this->apiParamManager->get('app.pStandPrice'),
            'partner_id'    => '',
            'mobile_phone'  => '',
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
        $platform = 'mtx';
        $config = [
            'ticket_center' => $this->apiParamManager->get('config.pTicketCenter'),
            'member_center' => $this->apiParamManager->get('config.pMemberCenter'),
            'app_code'      => $this->apiParamManager->get('config.pAppCode'),
            'secretkey'     => $this->apiParamManager->get('config.pSecretKey'),
            'token_id'      => $this->apiParamManager->get('config.pTokenID'),
            'token'         => $this->apiParamManager->get('config.pToken'),
            'cinema_id'     => $this->apiParamManager->get('config.pCinemaID'),
            'pay_type'      => $this->apiParamManager->get('config.pPayType'),
            'partner_code'  => $this->apiParamManager->get('config.pPartnerCode'),
            'partnerkey'    => $this->apiParamManager->get('config.pPartnerKey'),
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
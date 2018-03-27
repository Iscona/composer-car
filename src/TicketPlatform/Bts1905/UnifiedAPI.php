<?php

namespace biqu\TicketPlatform\Bts1905;

use biqu\APIInterface;
use biqu\TicketPlatform\Bts1905\API;
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
    }

    /**
     * 参数在组织
     * qys
     * 2017-07-26T17:27:13+0800
     * @return [type] [description]
     */
    public function params($arr)
    {
        $platform = 'M1905';

        $config = [
            'app_code'          => $this->apiParamManager->get('config.pAppCode'),
            'cinema_id'         => $this->apiParamManager->get('config.pCinemaID'),
            'token'             => $this->apiParamManager->get('config.pToken'),
            'bts_app_key'       => $this->apiParamManager->get('config.bts_app_key'),
            'country_cinema_id' => $this->apiParamManager->get('config.country_cinema_id'),
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
     * 2017-03-15T14:54:18+0800
     * @return [type] [description]
     */
    public function lockSeat()
    {
        $arr = [
            'feature_app_no'    =>  $this->apiParamManager->get('app.pFeatureAppNo'),
            'serial_num'        =>  $this->apiParamManager->get('app.pSerialNum'),
            'seat_infos'        =>  $this->apiParamManager->get('app.pSeatInfos'),
            'ticket_price'      =>  $this->apiParamManager->get('app.pTicketPrice'),
            'service_fee'       =>  $this->apiParamManager->get('app.pServiceFee'),
            'start_time'        =>  $this->apiParamManager->get('app.pStartTime'),
            'bts_timestart_time'=>  $this->apiParamManager->get('app.bts_timestart_time'),
            'film_name'         =>  $this->apiParamManager->get('app.film_name'),
            'notice_sms_key'    =>  $this->apiParamManager->get('app.notice_sms_key'),
            'cellphone'         =>  $this->apiParamManager->get('app.cellphone'),
            'notice_sms_secret' =>  $this->apiParamManager->get('app.notice_sms_secret'),
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
            'feature_app_no'    =>  $this->apiParamManager->get('app.pFeatureAppNo'),
            'app_seat_no'       =>  $this->apiParamManager->get('app.pAppSeatNo'),
            'serial_num'        =>  $this->apiParamManager->get('app.pSerialNum'),
            'seat_code'         =>  $this->apiParamManager->get('app.pSeatNo'),
            'ticket_price'      =>  $this->apiParamManager->get('app.pTicketPrice'),
            'service_fee'       =>  $this->apiParamManager->get('app.pServiceFee'),
            // 'stand_price'       =>  $this->apiParamManager->get('app.pStandPrice'),
            // 'cellphone'         =>  $this->apiParamManager->get('app.pRecvMobilePhone'),
            'is_card'           =>  $this->apiParamManager->get('app.pIs_card'),
            'user_id'           =>  $this->apiParamManager->get('app.pUser_id'),
            'is_bts'            =>  $this->apiParamManager->get('app.pIsBts'),
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
            'valid_code'  =>  $this->apiParamManager->get('app.pValidCode'),
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
        \Log::info(['请求打票接口' => $this->apiParamManager->get('app.pOrderNO')]);

        $arr = [
            'order_no'      => $this->apiParamManager->get('app.pOrderNO'),
            'serial_num'    => $this->apiParamManager->get('app.pOrderNO'),
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
            'member_name'   =>  $this->apiParamManager->get('app.pMemberName'),
            'password'      =>  $this->apiParamManager->get('app.pPassword'),
            'cellphone'     =>  $this->apiParamManager->get('app.pMobile'),
            'level_no'      =>  $this->apiParamManager->get('app.pMemberTypeNo'),
            'id_num'        =>  $this->apiParamManager->get('app.pIdNum'),
            'initial_money' =>  $this->apiParamManager->get('app.pBalance'),
            'gender'        =>  $this->apiParamManager->get('app.pGender'),
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
            'card_no'       => $this->apiParamManager->get('app.pCardId'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'cellphone'     => $this->apiParamManager->get('app.pMobile'),
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
            'serial_num'    =>  $this->apiParamManager->get('app.pOrderID'),
            'seat_code'     =>  $this->apiParamManager->get('app.pSeatNo'),
            'ticket_price'  =>  $this->apiParamManager->get('app.pTicketPrice'),
            'service_fee'   =>  $this->apiParamManager->get('app.pFee'),
            'protecte_price'=>  $this->apiParamManager->get('app.pLowestPrice'),
            'card_no'       =>  $this->apiParamManager->get('app.pCardId'),
            'password'      =>  $this->apiParamManager->get('app.pPassword'),
            'user_id'       =>  $this->apiParamManager->get('app.pUser_id'),
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
            'amount'        => $this->apiParamManager->get('app.pPrice'),
            'serial_num'    => $this->apiParamManager->get('app.pSerialNum'),
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
     * 会员卡对应的排期折扣
     * wenqiang
     * 2017-03-09T11:27:32+0800
     * @return [type] [description]
     */
    public function cardScheduleDiscount()
    {
        $arr = [
            'card_no'       => $this->apiParamManager->get('app.pCardId'),
            'feature_no'    => $this->apiParamManager->get('app.pFeatureNo'),
            'password'      => $this->apiParamManager->get('app.pPassword'),
            'stand_price'   => $this->apiParamManager->get('app.pStandPrice'),
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
            'type'          => $this->apiParamManager->get('app.type'),
            'start_date'    => $this->apiParamManager->get('app.pStartDate'),
            'end_date'      => $this->apiParamManager->get('app.pEndDate'),
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
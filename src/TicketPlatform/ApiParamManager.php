<?php

namespace biqu\TicketPlatform;

use ReflectionClass;

class ApiParamManager
{

    protected $configRelations = [
        'pTicketCenter' => 'ticket_center',
        'pMemberCenter' => 'member_center',
        'oldOrNewUrl'   => 'old_or_new_url',
        'pAppCode'      => 'app_code',
        'pCinemaID'     => 'cinema_id',
        'pTokenID'      => 'token_id',
        'pToken'        => 'token',
        'pSecretKey'    => 'secretkey',
        'pPartnerCode'  => 'partner_code',
        'pPartnerKey'   => 'partnerkey',
        'pPayType'      => 'pay_type',
        'bts_app_key'   => 'bts_app_key',
        'country_cinema_id' => 'country_cinema_id',
        'union_key'      =>  'union_key',

        'pVersion'      => 'version',
        'pChannelCode'  => 'channelCode',
        'pApiCenter'    => 'api_center',

        /**
         * 鼎新参数映射关系
         */
        'pAuthCode'         =>  'auth_code',
    ];

    protected $appRelations = [
        'pPlanDate'         =>  'plan_date',
        'pFeatureAppNo'     =>  'feature_app_no',
        'pSerialNum'        =>  'serial_num',
        'pSeatInfos'        =>  'seat_infos',
        'pTicketPrice'      =>  'ticket_price',
        'pHandlingfee'      =>  'handlingfee',
        'pPayType'          =>  'pay_type',
        'pRecvMobilePhone'  =>  'recv_mobile_phone',
        'pOrderNO'          =>  'order_no',
        'pPrintpassword'    =>  'printpassword',
        'pBalance'          =>  'balance',
        'pSendType'         =>  'send_type',
        'pPayResult'        =>  'pay_result',
        'pIsCmtsPay'        =>  'is_cmts_pay',
        'pIsCmtsSendCode'   =>  'is_cmts_send_code',
        'pPayMobile'        =>  'pay_mobile',
        'pBookSign'         =>  'book_sign',
        'pPayed'            =>  'payed',
        'pSendModeId'       =>  'send_mode_id',
        'pPaySeqNo'         =>  'pay_seq_no',
        'pValidCode'        =>  'valid_code',
        'pRequestType'      =>  'request_type',
        //修改订单价格
        'pAppPric'          =>  'app_pric',
        'pBalancePric'      =>  'balance_pric',
        //退票
        'pDesc'             =>  'desc',
        'pTicketType'       =>  'ticket_type',
        //注册会员卡
        'pPassword'         =>  'password',
        'pMobile'           =>  'mobile_phone',
        'pIdNum'            =>  'id_num',
        'pMemberName'       =>  'member_name',
        'pScore'            =>  'score',
        'pMemberTypeNo'     =>  'member_type_no',
        //会员卡信息
        'pCardId'           =>  'card_id',
        //会员卡退款
        'pTraceType'        =>  'trace_type',
        'pTraceNo'          =>  'trace_no',
        'pTracePrice'       =>  'trace_price',
        'pPrice'            =>  'price',
        'type'              =>  'type',
        //会员卡对应排期下的折扣信息
        'pFeatureNo'        =>  'feature_no',
        'pFeatureDate'      =>  'feature_date',
        'pFeatureTime'      =>  'feature_time',
        'pStandPrice'       =>  'stand_price',
        //会员卡充值
        'pPartnerId'        =>  'partner_id',
        'pTraceMemo'        =>  'trace_memo',
        'pTraceInfo'        =>  'trans_info',
        //会员卡购票
        'pTraceTypeNo'      =>  'trace_type_no',
        'pOldPrice'         =>  'old_price',
        'pDiscount'         =>  'discount',
        'pFilmNo'           =>  'film_no',
        'pTicketNum'        =>  'ticket_num',
        'pModifyPric'       =>  'modify_pric',
        //会员卡交易记录
        'pStartDate'        =>  'start_date',
        'pEndDate'          =>  'end_date',
        'pServiceFee'       =>  'service_fee',

        /**
         * 1905参数映射关系
         */

        'pScreenID'         =>  'hall_no',          //影厅编号
        'pSessionID'        =>  'feature_app_no',    //排期编号
        'pOrderID'          =>  'serial_num',       //第三方订单id
        'pFee'              =>  'handlingfee',       //服务费
        'pSeatNo'           =>  'seat_info',
        'pGender'           =>  'gender',
        'pLowestPrice'      =>  'protected_price',

        /**
         * 凤凰佳影参数映射关系
         */
        'pScheduleKey'      => 'schedule_key', //排期校验串
        'pOutLockId'        => 'serial_num',
        'pSeatIdList'       => 'seat_infos',
        'pLockOrderId'      => 'order_no',
        'pCardCostFee'      => 'card_cost_fee',
        'pMemberFee'        => 'member_fee',
        'pOutTradeNo'       => 'serial_num',
        'pRechargeAmount'   => 'price',
        'pDescription'      => 'trace_memo',
        'pPlatformTraceNo'  => 'platform_trace_no',
        'pConfirmationId'   => 'valid_code',
        'pArea'             => 'area',
        'pSectionId'        => 'section_id',

        /**
         * 鼎新售票平台
         */
        'pUpdateTime'       => 'update_time',
        'pRefundNo'         => 'refund_no',
        'pIsDiscount'       => 'is_discount',
        'pTicketType'       => 'ticket_type',
        'pLock_flag'        => 'lock_flag',

        /**
         * bts
         */
        'bts_timestart_time'    =>  'bts_timestart_time',
        'pStartTime'            =>  'start_time',
        'pIs_card'              =>  'is_card',
        'pUser_id'              =>  'user_id',
        'pIsUnion'              =>  'is_union',

        'pIsBts'                =>  'is_bts',
        'pIsBtsMember'          =>  'is_bts_member',
        'pIsBtsTicket'          =>  'is_bts_ticket',
        'film_name'             =>  'film_name',
        'notice_sms_key'        =>  'notice_sms_key',
        'notice_sms_secret'     =>  'notice_sms_secret',
        'notice_sms_continuous_lock_seat_fail_tpl_id' =>    'notice_sms_continuous_lock_seat_fail_tpl_id',
        'cellphone'             =>  'cellphone',
        'order_no'              =>  'order_no',
        'pTicketCode'           =>  'ticket_code',

        'pIs_cut_out_member'    =>  'is_cut_out_member',
        'pIs_cut_out_non_member'    =>  'is_cut_out_non_member',

        /**
         * 火烈鸟
         */
        'pAppPrice'             =>  'app_price',
        'pAnnualFee'            =>  'annual_fee',
        'pPlatNo'               =>  'plat_no',
        'pAppSeatNo'            =>  'app_seat_no',
        'pCardDiscountFlag'     =>  'card_discount_flag',
        'pPayPrice'             =>  'pay_price',
        'pExplain'              =>  'explain',
        'pRemarks'              =>  'remarks',
        'pServiceFee'           =>  'service_fee',

        'pThirdPayType'         =>  'third_pay_type',
    ];

    /**
     * 所有参数
     * @var [type]
     */
    public $params = [];

    /**
     * 动态调用方法组合需要的参数数组
     * wenqiang
     * 2017-03-17T09:35:33+0800
     * @param [type] $name         [description]
     * @param [type] $clientParams [description]
     */
    public function __construct($name, $clientParams)
    {
        $this->params = $this->{$name}($clientParams);
    }


    /**
     * 动态校验方法参数
     * wenqiang
     * 2017-03-16T18:55:11+0800
     * @param  [type] $name      [description]
     * @param  [type] $arguments [description]
     * @return [type]            [description]
     */
    public function __call($name, $arguments)
    {
        $object = (new ReflectionClass(
            $this->platformMaps(
                $arguments[0]['platform']) . ucfirst($name)))->newInstance($arguments[0]);

        return call_user_func_array([$object, 'auth'], []);
    }

    /**
     * 标识符和对应售票平台接口类关联
     * wenqiang
     * 2017-03-16T17:07:51+0800
     * @param  [type] $platform [description]
     * @return [type]           [description]
     */
    protected function platformMaps($platform)
    {
        $maps = [
            'mtx'   => '\biqu\TicketPlatform\Mtx\ParamValidators\\',
            'm1905' => '\biqu\TicketPlatform\M1905\ParamValidators\\',
            'fhjy'  => '\biqu\TicketPlatform\Fhjy\ParamValidators\\',
            'dx'    => '\biqu\TicketPlatform\Dx\ParamValidators\\',
            'btsmtx'=> '\biqu\TicketPlatform\Btsmtx\ParamValidators\\',
            'hln'   => '\biqu\TicketPlatform\Hln\ParamValidators\\',
            'btshln'=> '\biqu\TicketPlatform\Btshln\ParamValidators\\',
            'btsm1905'=> '\biqu\TicketPlatform\Bts1905\ParamValidators\\',
            'btsfhjy'=> '\biqu\TicketPlatform\Btsfhjy\ParamValidators\\',
        ];

        if (array_key_exists($platform, $maps)) {
            return $maps[$platform];
        } else {
            throw new \Exception('请求对应的售票系统不存在', 400);
        }
    }
    /**
     * 动态获取参数值
     * wenqiang
     * 2017-03-16T10:00:54+0800
     * @param  [type] $string [description]
     * @return [type]         [description]
     */
    public function get($string)
    {
        $arr = explode('.', $string);

        return $this->params[$arr[0]][$this->{$arr[0] . 'Relations'}[$arr[1]]];
    }


}
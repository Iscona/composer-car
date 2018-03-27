<?php
namespace biqu\TicketPlatform\Hln;

use biqu\TicketPlatform\DataFormat;

class Transform
{
    /**
     * 获取影院信息
     * wenqiang
     * 2017-04-19T14:54:27+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getCinemas(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 600);

        return $data->setData(array_get($arr, 'data.dataList'));
    }

    /**
     * 获取排期
     * wenqiang
     * 2017-04-19T14:54:44+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getAllSchedule(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 600);

        $tmp = [];
        $schedules = array_get($arr, 'shows');
        $types = [
        	1 	=> 	'2D',
        	2	=>	'3D',
        	3 	=>	'MAX2D',
        	4	=>	'MAX3D',
        	6	=>	'DMAX',
        	7	=>	'4D',
        	8	=>	'DMAX2D',
        	9	=>	'DMAX3D'
        ];

        foreach ($schedules as $key => $val) {

            $endTime = date('Y-m-d H:i', strtotime($val['showTime']) + $val['duration'] * 60);

            $tmp[] = [
                'hall_name'     =>  $val['hallName'],
                'hall_no'       =>  $val['hallCode'],
                'film_name'     =>  $val['filmName'],
                'film_no'       =>  $val['filmCode'],
                'start_time'    =>  date('Y-m-d H:i', strtotime($val['showTime'])),
                'end_time'      =>  $endTime,
                'app_price'     =>  $val['channelPrice'],
                'stand_price'   =>  $val['stdPrice'],
                'protect_price' =>  $val['minPrice'],
                'schedule_no'   =>  $val['channelShowCode'],
                'feature_no'    =>  '',
                'film_language' =>  $val['language'],
                'film_type'     =>  $types[$val['showType']],
                'set_status'    =>  $val['status'],
                'more'          =>  [
                    'member_price'      	=>  isset($val['memberPrices']) ? $val['memberPrices'] : '',
                    'policy_name'      		=>  isset($val['policy']) ? $val['policy'] : '',
                    'discount_price'  		=>  isset($val['discountPrice']) ? $val['discountPrice'] : '',
                    'member_special'   	 	=>  isset($val['memberSpecial']) ? $val['memberSpecial'] : '',
                    'card_use_flg'			=>	isset($val['cardUseFlg']) ? $val['cardUseFlg'] : '',
                    'valid_channel_price'	=>	$val['validChannelPrice'],
                ],
            ];
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'cinema_plans'  =>  $tmp
        ]);
    }

    /**
     * 座位图信息
     * wenqiang
     * 2017-04-19T19:14:25+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getSeatInfo(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 600);

        $seatPlanList = array_get($arr, 'seats');
        $seatMap = [];
        $loveCode = [];

        foreach ($seatPlanList as $key => $val) {

            $seatMap[$val['xcoord'] . '-' . $val['ycoord']] = $val;

            if ($val['type'] == 2) {
                $loveCode[$val['loveCode']][$val['code']] = $val['ycoord'];
            }
        }

        foreach ($loveCode as $key => $val) {
            $left = min($val);
            $right = max($val);
            foreach ($val as $k => $v) {
                if ($v == $left) {
                    $loveCode[$key][$k] = 'L';
                } elseif ($v == $right) {
                    $loveCode[$key][$k] = 'R';
                }
            }
        }

        $maxRow = max(array_column($seatMap, 'xcoord'));
        $maxCol = max(array_column($seatMap, 'ycoord'));
        $minCol = min(array_column($seatMap, 'ycoord'));
        $seatData = [];

        for ($row = 1; $row <= $maxRow; $row++) {
            for ($col = 1; $col <= $maxCol; $col++) {
                if ($col >= $minCol) {
                    $seatData[$row - 1][$col - $minCol] = [
                        'seat_no'   => '',
                        'state'     => -2,
                        'row'       => $row,
                        'col'       => $col,
                        'type'      => '',
                        'code'      => '',
                        'name'      => '',
                        'true_state'    => -2
                    ];

                    if (array_key_exists($row.'-'.$col, $seatMap)) {
                        $seatData[$row - 1][$col - $minCol]['seat_no']  = $seatMap[$row.'-'.$col]['code'];
                        $seatData[$row - 1][$col - $minCol]['row']      = intval($seatMap[$row.'-'.$col]['rowNum']);
                        $seatData[$row - 1][$col - $minCol]['col']      = intval($seatMap[$row.'-'.$col]['colNum']);
                        $seatData[$row - 1][$col - $minCol]['code']     = $seatMap[$row.'-'.$col]['groupCode'];

                        if ($seatMap[$row . '-' . $col]['colNum'] == '-1') {
                            $seatData[$row - 1][$col - $minCol]['state'] = -2;
                            $seatData[$row - 1][$col - $minCol]['true_state'] = -2;
                        } elseif ($seatMap[$row . '-' . $col]['status'] == '0') {
                            $seatData[$row - 1][$col - $minCol]['state'] = 1;
                            $seatData[$row - 1][$col - $minCol]['true_state'] = 1;
                        } elseif ($seatMap[$row . '-' . $col]['status'] == '1') {
                            $seatData[$row - 1][$col - $minCol]['state'] = 0;
                            $seatData[$row - 1][$col - $minCol]['true_state'] = 0;
                        }

                        if ($seatMap[$row.'-'.$col]['type'] == 1) {
                            $seatData[$row - 1][$col - $minCol]['type'] = 'N';
                        } elseif ($seatMap[$row.'-'.$col]['type'] == 2) {
                            $seatData[$row - 1][$col - $minCol]['type'] = array_get($loveCode, $seatMap[$row.'-'.$col]['loveCode'] . '.' . $seatMap[$row.'-'.$col]['code']);
                        }
                    }
                }
            }
        }

        return $data->setData([
            'result_code'   => 0,
            'message'       => '操作成功',
            'seat_data'     => $seatData,
        ]);
    }

    /**
     * 锁座接口
     * wenqiang
     * 2017-04-21T16:24:33+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function lockSeat(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 601);

        return $data->setData([
            'result_code'   => 0,
            'message'       => '锁座成功',
            'order_no'      => $arr['orderCode'],
            'more'          => [
                'cinema_order'  =>  $arr['cinemaOrderCode'],
                'lock_time'     =>  $arr['lockTime'],
            ]
        ]);
    }

    /**
     * 解锁
     * wenqiang
     * 2017-04-21T16:40:27+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function unlockSeat(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 601);

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
        ]);
    }

    /**
     * 生成订单
     * wenqiang
     * 2017-04-25T15:39:24+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function ticketPlaceOrder(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 601);

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'order_no'      =>  $arr['printCode'],
            'valid_code'    =>  $arr['verifyCode'],
            'more'          =>  [
                'confirm_time'   =>  $arr['confirmTime'],
            ],
        ]);
    }

    /**
     * 会员卡购票
     * wenqiang
     * 2017-04-27T16:17:00+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardBuyTicket(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 603);

        $ticketData = array_get($arr, 'tickets');

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'order_no'      =>  $arr['printCode'],
            'valid_code'    =>  $arr['verifyCode'],
            'ground_trade_no'   => '',
            'more'          =>  [],
        ]);
    }

    /**
     * 会员卡注册
     * wenqiang
     * 2017-04-24T18:07:48+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardRegister(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'card_number'   =>  $arr['cardCode']
        ]);
    }

    /**
     * 订单详情
     * wenqiang
     * 2017-05-02T18:47:04+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getOrderInfo(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        $infoData = array_get($arr, 'data.data');
    }

    /**
     * 查询订单状态
     * wenqiang
     * 2017-04-25T16:00:08+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getOrderStatus(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);
        $orderStatus = array_get($arr, 'order');

        $status = 2;
        switch ($orderStatus['status']) {
            case 1:
                $status = 10;
                break;
            case 2:
                $status = 11;
                break;
            case 3:
                $status = 4;
                break;
            case 4:
                $status = 9;
                break;
            case 5:
                $status = 11;
                break;
            case 6:
                $status = 7;
                break;
            default:
                $status = 2;
                break;
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'order_no'      =>  $orderStatus['printCode'],
            'valid_code'    =>  $orderStatus['verifyCode'],
            'order_status'  =>  $status,
        ]);
    }

    /**
     * 取票信息
     * wenqiang
     * 2017-04-25T16:59:53+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    // public function pirntTicketInfo(DataFormat $data)
    // {
    //     $arr = $data->toArray();

    //     $this->throwError($arr, 602);

    //     $infoData = array_get($arr, 'data.data');
    //     $qr = [];

    //     foreach ($infoData['tickets'] as $key => $val) {
    //         $qr[] = [
    //             'qr_code'   =>  $val['infoCode'],
    //             'ticket_no' =>  $val['ticketNo'],
    //             'seat_row'  =>  $this->getAlphNum($val['rowId']),
    //             'seat_col'  =>  $val['columnId'],
    //             'cpn_name'  =>  '',
    //             'ticket_price'  =>  $val['ticketPrice'],
    //             'service'   =>  $val['ticketFee'],
    //             'sell_ticket_time'  =>  $infoData['showDateTime'],
    //         ];
    //     }

    //     return [
    //         'printId' =>  $infoData['printId'],
    //         'qr'      =>  $qr
    //     ];
    // }

    /**
     * 打印票
     * wenqiang
     * 2017-04-25T18:19:06+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function printTicket(DataFormat $data, DataFormat $printInfo)
    {
        $arr = $data->toArray();
        $printArr = $printInfo->toArray();

        $this->throwError($arr, 603);
        $this->throwError($printArr, 603);

        $info = array_get($printArr, 'tickets');

        $data = [];
        foreach ($info as $key => $val) {
            $data[] = [
                'qr_code'   =>  $val['barCode'],
                'ticket_no' =>  $val['ticketCode'],
                'seat_no'   =>  $val['seatCode'],
                'seat_code' =>  '',
                'seat_row'  =>  $val['rowNum'],
                'seat_col'  =>  $val['colNum'],
                'cpn_name'  =>  '',
                'ticket_price'  =>  $val['price'],
                'service'   =>  $val['serviceFee'],
                'sell_ticket_time'  =>  '',
            ];
        }

        return $printInfo->setData([
            'result_code'   =>  0,
            'message'       => '操作成功',
            'ticket_plat'   =>  'hln',
            'print_type'    =>  '1',
            'order_no'      =>  $printArr['printCode'],
            'order_date'    =>  '',
            'ticket_num'    =>  count($data),
            'ticket_type'   =>  '',
            'film_no'       =>  $printArr['filmCode'],
            'film_name'     =>  $printArr['filmName'],
            'hall_no'       =>  $printArr['hallCode'],
            'hall_name'     =>  $printArr['hallName'],
            'feature_no'    =>  $printArr['channelShowCode'],
            'feature_time'  =>  $printArr['showTime'],
            'print_data'    =>  $data,
        ]);
    }

    /**
     * 订单详情
     * @Author   wenqiang
     * @DateTime 2017-10-11T15:05:56+0800
     * @version  [version]
     * @param    DataFormat               $printInfo [description]
     * @return   [type]                              [description]
     */
    public function pirntTicketInfo(DataFormat $printInfo)
    {
        $printArr = $printInfo->toArray();

        $this->throwError($printArr, 602);

        $info = array_get($printArr, 'tickets');

        $data = [];
        foreach ($info as $key => $val) {
            $data[] = [
                'qr_code'   =>  $val['barCode'],
                'ticket_no' =>  $val['ticketCode'],
                'seat_no'   =>  $val['seatCode'],
                'seat_code' =>  '',
                'seat_row'  =>  $val['rowNum'],
                'seat_col'  =>  $val['colNum'],
                'cpn_name'  =>  '',
                'ticket_price'  =>  $val['price'],
                'service'   =>  $val['serviceFee'],
                'sell_ticket_time'  =>  '',
            ];
        }

        return $printInfo->setData([
            'result_code'   =>  0,
            'message'       => '操作成功',
            'ticket_plat'   =>  'hln',
            'print_type'    =>  '1',
            'order_no'      =>  $printArr['printCode'],
            'order_date'    =>  '',
            'ticket_num'    =>  count($data),
            'ticket_type'   =>  '',
            'film_no'       =>  $printArr['filmCode'],
            'film_name'     =>  $printArr['filmName'],
            'hall_no'       =>  $printArr['hallCode'],
            'hall_name'     =>  $printArr['hallName'],
            'feature_no'    =>  $printArr['channelShowCode'],
            'feature_time'  =>  $printArr['showTime'],
            'print_data'    =>  $data,
        ]);
    }

    /**
     * 退票
     * wenqiang
     * 2017-04-25T18:31:07+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function refundOrder(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        return $data->setData([
            'result_code'       =>  0,
            'message'           =>  '操作成功',
            'more'              =>  [
                'refund_time'   =>  $arr['revokeTime']
            ],
        ]);
    }

    /**
     * 会员卡信息
     * @Author   wenqiang
     * @DateTime 2017-08-14T11:26:24+0800
     * @version  [version]
     * @param    DataFormat               $data [description]
     * @return   array                         [description]
     */
    public function cardInfo(DataFormat $data, DataFormat $cardTypes)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);
        $cardTypes = $cardTypes->toArray();

        $cardLevels = [];
        foreach ($cardTypes['lists'] as $key => $val) {
            $cardLevels[$val['name']] = $val['no'];
        }

        $infoData = array_get($arr, 'card');

        $type = 3;
        if ($infoData['isStoredCard'] == 0) {
            $type = 4;
        }

        return $data->setData([
            'result_code'       =>  0,
            'message'           =>  '操作成功',
            'card_id'           =>  $infoData['cardCode'],
            'type'              =>  $type,
            'level_no'          =>  $cardLevels[$infoData['level']],
            'level_name'        =>  $infoData['level'],
            'member_name'       =>  $infoData['memberName'],
            'phone'             =>  $infoData['memberPhone'],
            'balance'           =>  $infoData['balance'],
            'score'             =>  $infoData['score'],
            'expiration_time'   =>  $infoData['expirationTime'],
            'open_cinema'       =>  $infoData['cinemaCodePut'],
        ]);
    }

    /**
     * 会员卡充值
     * wenqiang
     * 2017-04-25T15:39:45+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardRecharge(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'balance'       =>  $arr['balance'],
            'score'         =>  '',
            'desc'          =>  '',
        ]);
    }

    /**
     * 会员卡级列表
     * wenqiang
     * 2017-04-24T11:01:29+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardType(DataFormat $data)
    {
        $arr = $data->toArray();
        $this->throwError($arr, 602);

        $typeData = array_get($arr, 'cardPolicyInfos');
        $lists = [];

        foreach ($typeData as $key => $val) {
            $lists[] = [
                'no'            =>  $val['policyId'],
                'name'          =>  $val['policyName'],
                'first_rec'     =>  $val['firstRechargeAmount'],
                'min_recharge'  =>  $val['minRechargeAmount'],
                'max_recharge'  =>  $val['maxRechargeAmount'],
                'annual_fee'    =>  $val['annualFee'],
                'description'   =>  $val['description'],
            ];
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'lists'         =>  $lists
        ]);
    }

    /**
     * 会员卡对应排期折扣
     * wenqiang
     * 2017-04-26T10:46:01+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardScheduleDiscount(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        $orderSeats = array_get($arr, 'orderSeats');

        $priceArr = [];
        foreach ($orderSeats as $key => $val) {
            $priceArr[] = $val['price'];
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'price'         =>  min($priceArr)
        ]);
    }

    /**
     * 会员卡交易记录
     * wenqiang
     * 2017-04-28T14:09:10+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardTransRecord(DataFormat $consData, DataFormat $rechargeData)
    {
        $consArr = $consData->toArray();
        $rechargeArr = $rechargeData->toArray();

        $this->throwError($consArr, 602);
        $this->throwError($rechargeArr, 602);

        $recordData = array_get($consArr, 'ticketInfos');
        $rechargeData = array_get($rechargeArr, 'rechargeInfos');

        $consLists = [];
        $rechargeLists = [];
        foreach ($recordData as $key => $val) {
            $date = explode(' ', $val['operateDate']);
            $featureDate = explode(' ', $val['showStartTime']);
            $consLists[] = [
                'trace_no'          =>  $val['orderNo'],
                'trace_type_name'   =>  $val['operateTypeName'],
                'old_price'         =>  number_format($val['cardPayAmount'] + $val['balance'], 2, '.', ''),
                'price'             =>  number_format(0 - $val['cardPayAmount'], 2, '.', ''),
                'trace_date'        =>  $date[0],
                'trace_time'        =>  $date[1],
                'old_score'         =>  '',
                'score'             =>  '',
                'trace_price'       =>  '',
                'user_code'         =>  $val['channelName'],
                'gift_cod'          =>  '',
                'feature_date'      =>  $featureDate[0],
                'feature_time'      =>  $featureDate[1],
                'feature_no'        =>  '',
                'film_no'           =>  '',
                'film_name'         =>  $val['ticketName'],
                'cinema_name'       =>  $val['consumeCinemaName'],
                'ticket_num'        =>  $val['seatLocation'],
                'trace_memo'        =>  '',
                'acc_level_name'    =>  $val['policyName'],
                'acc_level_code'    =>  $val['cardCode'],
                'cons_cinema_id'    =>  '',
                'refund_price'      =>  '',
                'refund_seats'      =>  '',
                'per_ticket_times'  =>  '',
                'refund_points'     =>  '',
                'trace_type_no'     =>  '',
                'send_card_cinema'  =>  $val['sendCardCinemaName'],
            ];
        }

        foreach ($rechargeData as $key => $val) {
            $date = explode(' ', $val['operateDate']);
            $rechargeLists[] = [
                'trace_no'          =>  '',
                'trace_type_name'   =>  $val['rechargeType'],
                'old_price'         =>  number_format($val['currBalance'] - $val['rechargeMoney'], 2, '.', ''),
                'price'             =>  number_format($val['rechargeMoney'], 2, '.', ''),
                'trace_date'        =>  $date[0],
                'trace_time'        =>  $date[1],
                'old_score'         =>  '',
                'score'             =>  '',
                'trace_price'       =>  '',
                'user_code'         =>  '',
                'gift_cod'          =>  '',
                'feature_date'      =>  '',
                'feature_time'      =>  '',
                'feature_no'        =>  '',
                'film_no'           =>  '',
                'film_name'         =>  '',
                'cinema_name'       =>  $val['consumeCinemaName'],
                'ticket_num'        =>  '',
                'trace_memo'        =>  '',
                'acc_level_name'    =>  $val['policyName'],
                'acc_level_code'    =>  $val['cardCode'],
                'cons_cinema_id'    =>  '',
                'refund_price'      =>  '',
                'refund_seats'      =>  '',
                'per_ticket_times'  =>  '',
                'refund_points'     =>  '',
                'trace_type_no'     =>  '',
                'send_card_cinema'  =>  $val['sendCinemaName']
            ];
        }

        return $consData->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'record'        =>  array_merge($consLists, $rechargeLists)
        ]);
    }

    /**
     * 获取影厅名称
     * wenqiang
     * 2017-04-19T18:25:49+0800
     * @return [type] [description]
     */
    public function getHalls($cinemas)
    {
        $halls = [];
        if ($this->isIndexArr($cinemas)) {
            foreach ($cinemas as $key => $val) {
                foreach ($val['halls'] as $k => $v) {
                    $halls[$val['cinemaLinkId']][$v['hallId']] = $v['name'];
                }
            }
        } else {
            foreach ($cinemas as $key => $value) {
                $halls[$cinemas['cinemaLinkId']][$value['hallId']] = $value['name'];
            }
        }

        return $halls;
    }

    /**
     * 判断是否是索引数组
     * wenqiang
     * 2017-03-13T15:32:18+0800
     * @param  [type]  $value [description]
     * @return boolean        [description]
     */
    public function isIndexArr($value) {
        if (is_array($value)) {
            $keys = array_keys($value);
            return $keys === array_keys($keys);
        }
        return false;
    }

    /**
     * 异常抛出
     * wenqiang
     * 2017-04-01T17:08:09+0800
     * @param  array  $arr  [description]
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    protected function throwError(array $arr, $code)
    {
        if ($arr['code'] != '001') {
            if (isset($arr['msg'])) {
                throw new \Exception($arr['code'] . '%' . $arr['msg'], $code);
            } else {
                throw new \Exception($arr['code'], $code);
            }
            exit;
        }
    }
}
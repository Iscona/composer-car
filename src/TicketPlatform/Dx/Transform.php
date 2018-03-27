<?php

namespace biqu\TicketPlatform\Dx;

use biqu\TicketPlatform\DataFormat;

class Transform
{

    public function getAllSchedule(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 600);

        $shceduleData = array_get($arr, 'res.data');

        $lists = [];

        foreach ($shceduleData as $key => $val) {

            if ($val['movieInfo'][0]['movieSize'] == 'IMAX') {
                $type = 'IMAX';
            } else {
                $type = $val['movieInfo'][0]['movieDimensional'];
            }

            $lists[] = [
                'hall_name'     =>  $val['hallName'],
                'hall_no'       =>  $val['hallId'],
                'film_name'     =>  $val['movieInfo'][0]['movieName'],
                'film_no'       =>  $val['movieInfo'][0]['cineMovieId'],
                'start_time'    =>  $val['startTime'],
                'end_time'      =>  $val['endTime'],
                'app_price'     =>  $val['marketPrice'],
                'stand_price'   =>  $val['price'],
                'protect_price' =>  $val['lowestPrice'],
                'schedule_no'   =>  $val['id'],
                'schedule_key'  =>  '',
                'feature_no'    =>  '',
                'film_language' =>  $val['movieInfo'][0]['movieLanguage'],
                'film_type'     =>  $type,
                'set_status'    =>  1,  // 可以获取的都是可售状态
                'more'          =>  [
                    'area_prices'   =>  isset($val['areaInfo']) ? $val['areaInfo'] : '',
                    'discount_tickets'  =>  '',
                    'area_id'       =>  '',
                    'allow_book'    =>  $val['allowBook'],
                    'update_time'   =>  $val['cineUpdateTime'],
                ],
            ];
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'cinema_plans'  =>  $lists,
        ]);
    }

    /**
     * 座位图信息
     * wenqiang
     * 2017-05-04T10:36:11+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getSeatStatus(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 601);

        $infoData = array_get($arr, 'res.data');

        $seatMap = [];
        foreach ($infoData as $key => $val) {
            $seatMap[$val['x'] . '-' . $val['y']] = $val;
        }

        $maxRow = max(array_column($seatMap, 'x'));
        $maxCol = max(array_column($seatMap, 'y'));
        $minCol = min(array_column($seatMap, 'y'));
        $seatData = [];
        $loverNo = [];

        for ($row = 1; $row <= $maxRow; $row++) {
            for ($col = 1; $col <= $maxCol; $col++) {
                if ($col >= $minCol) {
                    $seatData[$row - 1][$col - $minCol] = [
                        'seat_no'   => '',
                        'state'     => 0,
                        'row'       => $row,
                        'col'       => $col,
                        'type'      => '',
                        'code'      => '',
                        'name'      => '',
                    ];

                    if (array_key_exists($row.'-'.$col, $seatMap)) {
                        $seatData[$row - 1][$col - $minCol]['seat_no']  = $seatMap[$row.'-'.$col]['cineSeatId'];
                        $seatData[$row - 1][$col - $minCol]['row']      = intval($seatMap[$row.'-'.$col]['rowValue']);
                        $seatData[$row - 1][$col - $minCol]['col']      = intval($seatMap[$row.'-'.$col]['columnValue']);

                        switch ($seatMap[$row . '-' . $col]['seatStatus']) {
                             case 'ok':
                                $seatData[$row - 1][$col - $minCol]['state'] = 0;
                                $seatData[$row - 1][$col - $minCol]['true_state'] = 0;
                                break;
                            case 'locked':
                                $seatData[$row - 1][$col - $minCol]['state'] = 1;
                                $seatData[$row - 1][$col - $minCol]['true_state'] = 1;
                                break;
                            case 'repair':
                                $seatData[$row - 1][$col - $minCol]['state'] = -2;
                                $seatData[$row - 1][$col - $minCol]['true_state'] = -2;
                                break;
                            case 'selled':
                                $seatData[$row - 1][$col - $minCol]['state'] = 1;
                                $seatData[$row - 1][$col - $minCol]['true_state'] = 1;
                                break;
                            case 'booked':
                                $seatData[$row - 1][$col - $minCol]['state'] = 1;
                                $seatData[$row - 1][$col - $minCol]['true_state'] = 1;
                                break;
                        }

                        switch ($seatMap[$row . '-' . $col]['type']) {
                            case 'road':
                                $seatData[$row - 1][$col - $minCol]['state'] = -2;
                                break;
                            case 'danren':
                                $seatData[$row - 1][$col - $minCol]['type'] = 'N';
                                break;
                            case 'shuangren':
                                $seatData[$row - 1][$col - $minCol]['type'] = 'D';
                                break;
                            case 'baoliu':
                                $seatData[$row - 1][$col - $minCol]['type'] = 'H';
                                break;
                            case 'canji':
                                $seatData[$row - 1][$col - $minCol]['type'] = 'W';
                                break;
                            case 'vip':
                                $seatData[$row - 1][$col - $minCol]['type'] = 'V';
                                break;
                            case 'zhendong':
                                $seatData[$row - 1][$col - $minCol]['type'] = 'Z';
                                break;
                        }

                        if (!empty($seatMap[$row . '-' . $col]['pairValue'])) {

                            $pairValue = 'seats_' . $seatMap[$row . '-' . $col]['x'] . '_' . $seatMap[$row . '-' . $col]['y'];

                            if ($pairValue == $seatMap[$row . '-' . $col]['pairValue']) {
                                $seatData[$row - 1][$col - $minCol]['type'] = 'L';
                            } else {
                                $seatData[$row - 1][$col - $minCol]['type'] = 'R';
                            }

                        }
                    }
                }
            }
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'seat_data'     =>  $seatData
        ]);
    }

    /**
     * 锁座
     * wenqiang
     * 2017-05-04T15:33:57+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function lockSeat(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 601);

        $lockData = array_get($arr, 'res.data');

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'order_no'      =>  $lockData['lockFlag'],
            'more'          =>  [
                'areaInfo'      =>  $lockData['areaInfo'],
                'partner_price' =>  $lockData['partnerPrice']
            ],
        ]);
    }

    /**
     * 会员锁座
     * wenqiang
     * 2017-05-08T15:10:07+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function mLockSeat(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 601);

        $lockData = array_get($arr, 'res.data');

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'order_no'      =>  $lockData['lockFlag'],
            'more'          =>  [
                'total_price'   =>  $lockData['totalPrice'],
                'total_fee'     =>  $lockData['totalServiceCharge'],
                'total_butie'   =>  $lockData['totalButie'],
            ],
        ]);
    }

    /**
     * 解锁
     * wenqiang
     * 2017-05-04T17:57:24+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function unlockSeat(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 601);

        $unlockData = array_get($arr, 'res.data');

        return $data->setData([
            'result_code'   => 0,
            'message'       => '操作成功'
        ]);
    }

    /**
     * 生成订单
     * wenqiang
     * 2017-05-05T11:18:29+0800
     * @param  DataFormat $data     [description]
     * @param  [type]     $tranceNo [description]
     * @return [type]               [description]
     */
    public function ticketPlaceOrder(DataFormat $data, $traceNo)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 601);

        $orderData = array_get($arr, 'res.data');

        return $data->setData([
            'result_code'   => 0,
            'message'       => '操作成功',
            'order_no'      =>  $traceNo,
            'valid_code'    =>  $orderData['ticketFlag1'] . $orderData['ticketFlag2'],
        ]);
    }

    /**
     * 退票
     * wenqiang
     * 2017-05-05T10:51:12+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function refundOrder(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 601);

        $refundData = array_get($arr, 'res.data');

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'refund_data'   =>  $refundData,
        ]);
    }

    /**
     * 打印
     * wenqiang
     * 2017-05-05T14:29:04+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function printTicket(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 601);

        $printData = array_get($arr, 'res.data');

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'print_type'    =>  array_get($arr, 'res.status'),
            'data'          =>  $arr
        ]);
    }

     /**
     * 查看是否打印  状态
     * wenqiang
     * 2017-05-05T14:29:04+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function printTicketStatus(DataFormat $data)
    {
        $arr = $data->toArray();
        \Log::info($arr);
        $this->throwError($arr, 601);

        $printData = array_get($arr, 'res.data.ticketInfo');

        // ticketStatus  1：未出票，2:已出票，3：已退票(此状态时不能出票)
        // printed    1 - 已出票，0 –未出票
        $status = [];
        $ticketStatus = [];
        $model = [1 => 0, 2 => 1, 3 => 2];
        foreach ($printData as $key => $val) {
            $ticketStatus[] = $model[$val['ticketStatus']];
        }

        $type = array_flip($ticketStatus);
        if (count($type) > 1) {
            $ticketStatus = array_flip($type);
        } else {
            $ticketStatus = reset($ticketStatus);
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'print_type'    =>  $ticketStatus,
            'data'          =>  array_get($arr, 'res.data')
        ]);
    }

    /**
     * 查询订单状态
     * wenqiang
     * 2017-05-05T15:04:51+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getOrderStatus(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 601);

        $statusData = array_get($arr, 'res.data');

        if ($statusData['status'] == 'success') {
            $status = 9;
        } else {
            $status = 6;
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'order_status'  =>  $status,
        ]);
    }

    /**
     * 会员卡密码验证
     * wenqiang
     * 2017-05-08T09:41:17+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardLogin(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 601);

        $loginData = array_get($arr, 'res.data');

        return $loginData['auth'] ? true : false;
    }

    /**
     * 会员卡详细信息
     * wenqiang
     * 2017-05-08T09:32:34+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardInfo(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        $infoData = array_get($arr, 'res.data');

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'card_id'       =>  $infoData['cardNumber'],
            'type'          =>  $infoData['cardTypeId'],
            'level_no'      =>  '',
            'member_name'   =>  $infoData['username'],
            'phone'         =>  $infoData['mobile'],
            'balance'       =>  $infoData['balance'],
            'score'         =>  $infoData['availableJifen'],
            'expiration_time'   =>  $infoData['period'],
        ]);
    }

    /**
     *
     * wenqiang
     * 2017-05-05T15:23:03+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardRegister(DataFormat $data){}

    /**
     * 会员卡级别列表
     * wenqiang
     * 2017-05-04T16:01:21+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardType(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        $typeData = array_get($arr, 'res.data');

        $lists = [];
        foreach ($typeData['rule'] as $key => $val) {
            $lists[] = [
                'no'            =>  $val['levelId'],
                'name'          =>  $val['levelName'],
                'first_rec'     =>  $val['initMoney'],
                'cardCostFee'   =>  '',
                'memberFee'     =>  ''
            ];
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'lists'         =>  $lists,
        ]);
    }


    /**
     * 会员卡充值
     * wenqiang
     * 2017-05-08T09:54:55+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardRecharge(DataFormat $data, array $cardDetail)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        $rechargeData = array_get($arr, 'res.data');

        if (!$rechargeData['rechargeSuccess']) {
            throw new \Exception('199998', 602);
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'balance'       =>  $cardDetail['balance'],
            'score'         =>  $cardDetail['score'],
            'desc'          =>  ''
        ]);
    }

    /**
     * 会员卡折扣信息
     * wenqiang
     * 2017-05-08T10:47:19+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardScheduleDiscount(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        $discountData = array_get($arr, 'res.data');

        $priceArr = [];
        foreach ($discountData['priceinfo'] as $key => $val) {
                if ($val['global'] == 2) {
                    $isDiscount = 2;
                    $price = $val['globalPreferPrice'];
                } elseif ($val['can'] == 1) {
                    $isDiscount = 1;
                    $price = $val['preferPrice'];
                } elseif ($val['can'] == 0) {
                    $isDiscount = 0;
                    $price = $val['price'];
                }

                $priceArr[] = [
                    'price'         =>  $price,
                    'desc'          =>  $val['desc'],
                    'is_discount'   =>  $isDiscount,
                ];
        }

        foreach ($priceArr as $k => $v) {
            if ($v['price'] == min(array_column($priceArr, 'price'))) {
                $price = $v['price'];
                $ticketType = $v['desc'];
                $isDiscount = $v['is_discount'];
            }
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'price'         =>  $price,
            'more'          =>  [
                'ticket_type'   =>  $ticketType,
                'is_discount'   =>  $isDiscount,
            ],
        ]);
    }

    /**
     * 会员卡购票
     * wenqiang
     * 2017-05-08T15:57:22+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardBuyTicket(DataFormat $data, $traceNo)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        $ticketData = array_get($arr, 'res.data');

        return $data->setData([
            'result_code'       =>  0,
            'message'           =>  '操作成功',
            'order_no'          =>  $traceNo,
            'valid_code'        =>  $ticketData['ticketFlag1'] . $ticketData['ticketFlag2'],
        ]);
    }

    /**
     * 会员卡交易记录
     * wenqiang
     * 2017-05-12T15:05:48+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardTransRecord(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        $recordData = array_get($arr, 'res.data');

        if ($recordData['pageTotal'] == 0) {
            $lists = [];
        } else {
            $lists = [
                'trace_no'          =>  '',
                'trace_type_no'     =>  '',
                'trace_type_name'   =>  '',
                'old_price'         =>  '',
                'price'             =>  '',
                'trace_date'        =>  '',
                'trace_time'        =>  '',
                'old_score'         =>  '',
                'score'             =>  '',
                'trace_price'       =>  '',
                'user_code'         =>  '',
                'gift_cod'          =>  '',
                'feature_date'      =>  '',
                'feature_time'      =>  '',
                'feature_no'        =>  '',
                'film_no'           =>  '',
                'cinema_name'       =>  '',
                'ticket_num'        =>  '',
                'trace_memo'        =>  '',
                'acc_level_name'    =>  '',
                'acc_level_code'    =>  '',
            ];
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'record'        =>  $lists,
        ]);
    }

    /**
     * 获取售票平台定义的影院ID
     * wenqiang
     * 2017-05-03T16:12:13+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getCinemas(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 600);

        $cienmaData = array_get($arr, 'res.data');

        $cinemas = [];
        foreach ($cienmaData as $key => $val) {
            $cinemas[] = [
                'cinemaId'      =>  $val['cinemaId'],
                'cinemaName'    =>  $val['cinemaName'],
                'validPeriod'   =>  $val['validPeriod'],
                'cinemaNumber'  =>  $val['cinemaNumber'],
            ];
        }

        return $data->setData($cinemas);
    }

    /**
     * 影厅座位图
     * wenqiang
     * 2017-05-03T17:59:53+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getSeatInfo(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 601);

        $seatData = array_get($arr, 'res.data');

        dd($seatData);

        $seatMap = [];
        foreach ($seatData as $key => $val) {
            foreach ($val['seats'] as $k => $v) {
                $seatMap[$v['x'].'-'.$v['y']] = $v;
            }
        }

        $maxRow = max(array_column($seatMap, 'x'));
        $maxCol = max(array_column($seatMap, 'y'));
        $minCol = min(array_column($seatMap, 'y'));
        $seatData = [];

        for ($row = 1; $row <= $maxRow; $row++) {
            for ($col = 1; $col <= $maxCol; $col++) {
                if ($col >= $minCol) {
                    $seatData[$row - 1][$col - $minCol] = [
                        'seat_no'   => '',
                        'state'     => 0,
                        'row'       => $row,
                        'col'       => $col,
                        'type'      => '',
                        'code'      => '',
                        'name'      => '',
                    ];

                    if (array_key_exists($row.'-'.$col, $seatMap)) {
                        $seatData[$row - 1][$col - $minCol]['seat_no']  = $seatMap[$row.'-'.$col]['seatId'];
                        $seatData[$row - 1][$col - $minCol]['row']      = intval($seatMap[$row.'-'.$col]['rowId']);
                        $seatData[$row - 1][$col - $minCol]['col']      = intval($seatMap[$row.'-'.$col]['columnId']);
                        $seatData[$row - 1][$col - $minCol]['type']     = $seatMap[$row.'-'.$col]['type'];

                        if ($seatMap[$row . '-' . $col]['damage'] == 'Y') {
                            $seatData[$row - 1][$col - $minCol]['state'] = -2;
                        }
                    }
                }
            }
        }
    }

     /**
     * 会员影厅服务费
     * qys
     * 2017-05-27T08:32:12+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function seatPrice(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 600);

        $priceInfo = array_get($arr, 'res.data');
        \Log::info($priceInfo);
        $price = [];
        if ($priceInfo) {
            $price = [
                'totalPrice'            => $priceInfo['totalPrice'],
                'totalFee'              => $priceInfo['totalFee'],
                'totalServiceCharge'    => $priceInfo['totalServiceCharge'],
                'totalButie'            => $priceInfo['totalButie']
            ];
        }

        return $data->setData([
            'result_code'   => 0,
            'message'       => '操作成功',
            'price'         => $price,
        ]);
    }

    /**
     * 统一抛出异常
     * wenqiang
     * 2017-05-05T11:23:54+0800
     * @param  [type] $arr  [description]
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    public function throwError($arr, $code)
    {
        if ($arr['res']['status'] != 1 || !empty($arr['res']['errorCode'])) {
            throw new \Exception($arr['res']['errorCode'] . '%' . $arr['res']['errorMessage'], $code);
        }
    }
}
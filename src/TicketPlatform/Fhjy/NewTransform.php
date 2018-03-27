<?php

namespace biqu\TicketPlatform\Fhjy;

use biqu\TicketPlatform\DataFormat;

class NewTransform
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
    public function getAllSchedule(DataFormat $data, $cinemas, $cinemaId)
    {
        $arr = $data->toArray();
        
        $this->throwError($arr, 600);

        $tmp = [];
        $hallInfos = $this->getHalls($cinemas);
        $schedules = array_get($arr, 'data.dataList');
        $areaPrices = [];
        $filmTypes = [
            '2D'        =>  '2D',
            '3D'        =>  '3D',
            'IMAX2D'    =>  'IMAX',
            'IMAX3D'    =>  'IMAX/3D',
            '巨幕2D'    =>   'IMAX',
            '巨幕3D'    =>   'IMAX/3D',
            '胶片'      =>   '胶片(进口)',
            '巨幕2D'    =>   'IMAX',
            '其它'      =>   '其他',
        ];

        foreach ($schedules as $key => $val) {

            if ($val['saleStatus'] == 'Y') {
                $setStatus = 1;
            } elseif ($val['saleStatus'] == 'N') {
                $setStatus = 3;
            }

            if (isset($val['areaInfoList'])) {
                $areaPrices[] = [
                    'code'          =>  $val['areaInfoList'][0]['areaId'],
                    'name'          =>  $val['areaInfoList'][0]['areaName'],
                    'settlePrice'   =>  $val['areaInfoList'][0]['areaSettlePrice'],
                    'service'       =>  $val['areaInfoList'][0]['areaServiceFee'],
                ];
            }  

            $endTime = date('Y-m-d H:i', strtotime($val['showDateTime']) + $val['film']['duration'] * 60);

            $tmp[] = [
                'hall_name'     =>  $hallInfos[$cinemaId][$val['hallCode']],
                'hall_no'       =>  $val['hallCode'],
                'film_name'     =>  $val['film']['name'],
                'film_no'       =>  $val['film']['filmCode'],
                'start_time'    =>  date('Y-m-d H:i', strtotime($val['showDateTime'])),
                'end_time'      =>  $endTime,
                'app_price'     =>  isset($val['settlePrice']) ? $val['settlePrice'] : '',
                'stand_price'   =>  $val['standardPrice'],
                'protect_price' =>  $val['lowestPrice'],
                'schedule_no'   =>  $val['scheduleId'],
                'feature_no'    =>  '',
                'film_language' =>  $val['film']['language'],
                'film_type'     =>  $filmTypes[$val['film']['dimensional']],
                'set_status'    =>  $setStatus,
                'more'          =>  [
                    'schedule_key'      =>  $val['scheduleKey'],
                    'area_prices'       =>  $areaPrices,
                    'discount_tickets'  =>  isset($val['eachShowDiscountTickets']) ? $val['eachShowDiscountTickets'] : '',
                    'area_id'           =>  isset($val['areaId']) ? $val['areaId'] : '',
                    'ticket_fee'        =>  isset($val['ticketfee']) ? $val['ticketfee'] : '', // 手续费，单位:元，精度: 精确到小数点后两位
                    'fee_type'          =>  isset($val['feeType']) ? $val['feeType'] : '',  // 手续费类型; order:每单订单计费; ticket:每张票计费;
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
        $seatPlanList = array_get($arr, 'data.data.seatPlanList.0');
        $tmp = [];

        if (strtotime($seatPlanList['effectiveDate']) >= time()) {
            throw new \Exception(200002, 600);
        }

        $seatMap = [];
        foreach ($seatPlanList['sectionList'] as $key => $val) {
            foreach ($val['seatList'] as $k => $v) {
                $seatMap[$v['y'].'-'.$v['x']] = $v;
            }
        }

        $maxRow = max(array_column($seatMap, 'y'));
        $maxCol = max(array_column($seatMap, 'x'));
        $minCol = min(array_column($seatMap, 'x'));
        $seatData = [];

        $arr = ['A' => 1, 'B' => 2, 'C' => 3,  'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7,
                'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14,
                'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 10,
                'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'X' => 25, 'Y' => 27, 'Z' => 27];

        //凤凰佳影座位类型枚举
        $seatTypes = [
            'N'     =>  'N',
            'W'     =>  'W',
            'Z'     =>  'Z',
            'DL'    =>  'L',
            'DR'    =>  'R',
        ];

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
                        'true_state'    => 0
                    ];

                    if (array_key_exists($row.'-'.$col, $seatMap)) {
                        $seatData[$row - 1][$col - $minCol]['seat_no']  = $seatMap[$row.'-'.$col]['seatCode'];
                        $seatData[$row - 1][$col - $minCol]['row']      = is_numeric($seatMap[$row.'-'.$col]['rowId']) ?
                                                                        intval($seatMap[$row.'-'.$col]['rowId']) :
                                                                        intval($arr[$seatMap[$row.'-'.$col]['rowId']]) ;
                        $seatData[$row - 1][$col - $minCol]['col']      = 
                            is_numeric($seatMap[$row.'-'.$col]['columnId']) ? 
                            intval($seatMap[$row.'-'.$col]['columnId']) : 
                            intval($arr[$seatMap[$row.'-'.$col]['columnId']]);
                        $seatData[$row - 1][$col - $minCol]['type']     = $seatTypes[$seatMap[$row.'-'.$col]['type']];

                        if ($seatMap[$row . '-' . $col]['damaged'] == 'Y') {
                            $seatData[$row - 1][$col - $minCol]['state'] = -2;
                            $seatData[$row - 1][$col - $minCol]['true_state'] = -2;
                        }
                    }
                }
            }
        }

        return [
            'seatData'  => $seatData,
            'sectionId' => $seatPlanList['sectionList'][0]['sectionCode'],
        ];
    }

    /**
     * 分区座位信息
     * wenqiang
     * 2017-04-26T14:41:35+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getAreaSeatInfo(DataFormat $data, $sectionId)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 601);

        $infoData = array_get($arr, 'data.data.sectionAreaList');
        $areaData = [];
        
        foreach ($infoData as $key => $val) {
            if ($val['sectionCode'] == $sectionId) {
                foreach ($val['areaInfoList'] as $v1) {
                    $seatId = [];
                    foreach ($v1['seats'] as $v2) {
                        $seatId[] = $v2['seatCode'];
                    }

                    $areaData[] = [
                        'code'      =>  $v1['areaId'],
                        'name'      =>  $v1['areaName'],
                        'seatId'    =>  $seatId
                    ];
                }
            }
        }
        return $areaData;
    }

    /**
     * 查找已售座位
     * wenqiang
     * 2017-04-20T13:59:50+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getSeatStatus(DataFormat $data, array $array, $areaData)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 600);

        $seats = array_get($arr, 'data.data.sectionList.0.seatList');
        
        $seatData = $array['seatData'];

        $solds = [];
        if (!is_null($seats)) {
            foreach ($seats as $key => $value) {
                $solds[] = $value['seatCode'];
            }
        }

        foreach ($seatData as $key => $val) {
            foreach ($val as $k => $v) {
                if (empty($v['seat_no'])) {
                    $seatData[$key][$k]['state'] = -2;
                    $seatData[$key][$k]['true_state'] = -2;
                }

                if (in_array($v['seat_no'], $solds)) {
                    $seatData[$key][$k]['state'] = 1;
                    $seatData[$key][$k]['true_state'] = 1;
                }

                foreach ($areaData as $kk => $vv) {
                    if (in_array($v['seat_no'], $vv['seatId'])) {
                        $seatData[$key][$k]['code'] = $vv['code'];
                        $seatData[$key][$k]['name'] = '';//$vv['name'];
                    }
                }
            }
        }

        return $data->setData([
            'result_code'   => 0,
            'message'       => '操作成功',
            'more'          => [
                'section_id'    => $array['sectionId'],
            ],
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

        $lockData = array_get($arr, 'data.data');

        return $data->setData([
            'result_code'   => 0,
            'message'       => '锁座成功',
            'order_no'      => $lockData['lockOrderId']
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

        $orderData = array_get($arr, 'data.data');

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'order_no'      =>  $orderData['orderId'],
            'valid_code'    =>  $orderData['printCode'],
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

        $ticketData = array_get($arr, 'data.data');

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'order_no'      =>  $ticketData['orderId'],
            'valid_code'    =>  $ticketData['confirmationId'],
            'ground_trade_no'   => '',
            'more'          =>  [
                'booking_id'    =>  $ticketData['bookingId'],
            ],
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

        $cardData = array_get($arr, 'data.data');

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'card_number'   =>  $cardData['cardNumber']
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

        $statusData = array_get($arr, 'data.data');

        switch ($statusData['orderStatus']) {
            case 'ORDER_SUCCESS':
                $status = 9;
                break;
            case 'PROCESSING':
                $status = 10;
                break;
            case 'ORDER_FAIL':
                $status = 11;
                break;
            case 'REFUNDING':
                $status = 12;
                break;
            case 'REFUND_SUCCESS':
                $status = 7;
                break;
            case 'PARTLY_REFUND_SUCCESS':
                $status = 15;
                break;
            default:
                $status = 2;
                break;
        }
        
        foreach ($statusData['ticketOrder']['ticketList'] as $key => $val) {
            if ($val['ticketStatus'] == 'REFUNDED') {
                $status = 7;
            } elseif ($val['printFlag'] == 'Y') {
                $status = 8;
            }
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'order_no'      =>  $statusData['orderId'],
            'valid_code'    =>  '',
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
    public function pirntTicketInfo(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        $infoData = array_get($arr, 'data.data');
        $qr = [];
        
        foreach ($infoData['ticketList'] as $key => $val) {
            $qr[] = [
                'qr_code'   =>  $val['infoCode'],
                'ticket_no' =>  $val['ticketNo'],
                'seat_no'   =>  '',
                'seat_code' =>  '',
                'seat_row'  =>  $this->getAlphNum($val['rowId']),
                'seat_col'  =>  $val['columnId'],
                'cpn_name'  =>  $infoData['channelName'],
                'ticket_price'  =>  $val['ticketPrice'],
                'service'   =>  strval($val['ticketFee'] + $val['ticketChannelFee'] + $val['serviceFee']),
                'sell_ticket_time'  =>  $infoData['showDateTime'],
            ];
        }

        return [
            'printId'   =>  $infoData['printCode'],
            'orderNo'   =>  $infoData['orderId'],
            'orderDate' =>  $infoData['createDateTime'],
            'ticketNum' =>  count($qr),
            'ticketType'=>  '',
            'filmNo'    =>  $infoData['filmCode'],
            'filmName'  =>  $infoData['shortName'],
            'hallNo'    =>  $infoData['hallCode'],
            'hallName'  =>  $infoData['hallName'],
            'featureNo' =>  '',
            'featureTime'=> $infoData['showDateTime'],
            'qr'        =>  $qr
        ];
    }

    /**
     * getalphnum
     * 凤凰佳影英文字母转文字
     * zhl
     * 2017-07-29T14:06:32+0800
     * @param   string $char [英文字母]
     * @return  [type]              [description]
     */
    protected function getAlphNum($char)
    {
        $array = array('A', 'B', 'C', 'D', 'E', 'F',
                       'G', 'H', 'I', 'J', 'K', 'L',
                       'M', 'N', 'O', 'P', 'Q', 'R',
                       'S', 'T', 'U', 'V', 'W', 'X',
                       'Y', 'Z');
        if (in_array($char, $array)) {
            return strval(array_search($char,$array) + 1);
        } else {
            return $char;
        }
    }

    /**
     * 订单详情
     * @Author   wenqiang
     * @DateTime 2017-10-11T11:00:13+0800
     * @version  [version]
     * @param    [type]                   $ticketInfoArr [description]
     * @return   [type]                                  [description]
     */
    public function getPrintOrderInfo(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);
        $infoData = array_get($arr, 'data.data');

        $ticketsInfo = [];
        foreach ($infoData['ticketOrder']['ticketList'] as $key => $val) {
            $ticketsInfo[] = [
                'qr_code'   =>  '',
                'ticket_no' =>  $val['ticketNo'],
                'seat_no'   =>  $val['seatCode'],
                'seat_code' =>  '',
                'seat_row'  =>  $this->getAlphNum($val['rowId']),
                'seat_col'  =>  $val['columnId'],
                'cpn_name'  =>  '',
                'ticket_price'  =>  $val['ticketPrice'],
                'service'   =>  $val['serviceFee'],
                'sell_ticket_time'  =>  '',
            ];
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'ticket_plat'   =>  'fhjy',
            'print_type'    =>  '',
            'order_no'      =>  $infoData['orderId'],
            'order_date'    =>  '',
            'ticket_num'    =>  count($ticketsInfo),
            'ticket_type'   =>  '',
            'film_no'       =>  $infoData['ticketOrder']['filmCode'],
            'film_name'     =>  $infoData['ticketOrder']['shortName'],
            'hall_no'       =>  $infoData['ticketOrder']['hallCode'],
            'hall_name'     =>  '',
            'feature_no'    =>  '',
            'feature_time'  =>  $infoData['ticketOrder']['showDateTime'],
            'print_data'    =>  $ticketsInfo
        ]);
    }

    /**
     * 打印票
     * wenqiang
     * 2017-04-25T18:19:06+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function printTicket(DataFormat $data, $ticketInfoArr)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 603);

        $infoData = array_get($arr, 'data.data');

        // printStatus   打票状态 Y:已打票; N:未打票;
        // refundStatus  退票状态 Y:已退票; N:未退票
        $status = ['N' => 0, 'Y' => 1];
        $printType[] = $infoData ? 1 : 0;
        if ($tickets = array_get($infoData, 'tickets')) {
            foreach ($tickets as $val) {
               if ($val['refundStatus'] == 'N') {
                    $printTyp[] = $status[$val['printStatus']];
                } else {
                    $printType[] = 2; // 已退票
                }
            }
        }

        $type = array_flip($printType);
        if (count($type) > 1) {
            $printType = array_flip($type);
        } else {
            $printType = reset($printType);
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'ticket_plat'   =>  'fhjy',
            'print_type'    =>  $printType,
            'order_no'      =>  $ticketInfoArr['orderNo'],
            'order_date'    =>  $ticketInfoArr['orderDate'],
            'ticket_num'    =>  $ticketInfoArr['ticketNum'],
            'ticket_type'   =>  $ticketInfoArr['ticketType'],
            'film_no'       =>  $ticketInfoArr['filmNo'],
            'film_name'     =>  $ticketInfoArr['filmName'],
            'hall_no'       =>  $ticketInfoArr['hallNo'],
            'hall_name'     =>  $ticketInfoArr['hallName'],
            'feature_no'    =>  $ticketInfoArr['featureNo'],
            'feature_time'  =>  $ticketInfoArr['featureTime'],
            'print_data'    =>  $ticketInfoArr['qr']
        ]);
    }

    /**
     * 退票
     * wenqiang
     * 2017-04-25T18:31:07+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function refudnOrder(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        return $data->setData([
            'result_code'       =>  0,
            'message'           =>  '操作成功',
        ]);
    }

    /**
     * 会员卡信息
     * wenqiang
     * 2017-04-28T10:36:45+0800
     * @param  DataFormat $data     [description]
     * @param  array      $cardData [description]
     * @return [type]               [description]
     */
    public function cardInfo(DataFormat $data, array $cardData)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        $infoData = array_get($arr, 'data.data');

        $type = 3;
        if ($infoData['chargeType'] == 'B') {
            $type = 4;
        }

        return $data->setData([
            'result_code'       =>  0,
            'message'           =>  '操作成功',
            'card_id'           =>  $infoData['cardNumber'],
            'type'              =>  $type,
            'level_no'          =>  $infoData['gradeId'],
            'level_name'        =>  $infoData['gradeDesc'],
            'member_name'       =>  $infoData['cardUserName'],
            'phone'             =>  $infoData['mobile'],
            'balance'           =>  $cardData['balance'],
            'score'             =>  $cardData['score'],
            'expiration_time'   =>  $infoData['validateDate'],
        ]);
    }

    /**
     * 获取会员卡余额
     * wenqiang
     * 2017-04-25T19:58:33+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getCardBalance(DataFormat $data)
    {
        $arr = $data->toArray();

        $this->throwError($arr, 602);

        $balanceData = array_get($arr, 'data.data');

        return [
            'balance'   =>  $balanceData['balance'],
            'score'     =>  $balanceData['accumulationPoints'],
        ];
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

        $rechargeData = array_get($arr, 'data.data');

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'balance'       =>  $rechargeData['balance'],
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

        $typeData = array_get($arr, 'data.dataList');
        $lists = [];

        foreach ($typeData as $key => $val) {
            $lists[] = [
                'no'            =>  $val['gradeId'],
                'name'          =>  $val['gradeDesc'],
                'first_rec'     =>  $val['firstRecharge'],
                'cardCostFee'   =>  $val['cardCostFee'],
                'memberFee'     =>  $val['memberFee']
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

        $discountData = array_get($arr, 'data.data');

        $priceArr = [];
        if (isset($discountData['tickets'])) {
            foreach ($discountData['tickets'] as $key => $val) {
                if ($val['maxTickets'] > 0 && $val['isCardDiscount']) {
                    $priceArr[$val['ticketType']] = $val['price'];
                }
            }
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'price'         =>  $priceArr ? min($priceArr) : 0
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

        $recordData = array_get($consArr, 'data.dataList');
        $rechargeData = array_get($rechargeArr, 'data.dataList');

        $consLists = [];
        $rechargeLists = [];
        foreach ($recordData as $key => $val) {
            $date = explode(' ', $val['consumeDateTime']);
            $consLists[] = [
                'trace_no'          =>  $val['bookingId'],
                'trace_type_name'   =>  $val['refundSeats'] ? '消费并退款' : '消费',
                'old_price'         =>  number_format($val['beforeBalance'], 2, '.', ''),
                'price'             =>  number_format($val['afterBalance'] - $val['beforeBalance'], 2, '.', ''),
                'trace_date'        =>  $date[0],
                'trace_time'        =>  $date[1],
                'old_score'         =>  '',
                'score'             =>  $val['getPoints'],
                'trace_price'       =>  '',
                'user_code'         =>  '',
                'gift_cod'          =>  '',
                'feature_date'      =>  '',
                'feature_time'      =>  '',
                'feature_no'        =>  '',
                'film_no'           =>  $val['filmCode'],
                'film_name'         =>  $val['shortName'],
                'cinema_name'       =>  $val['consumeCinemaLinkId'],
                'ticket_num'        =>  $val['seats'],
                'trace_memo'        =>  $val['description'],
                'acc_level_name'    =>  '',
                'acc_level_code'    =>  '',
                'cons_cinema_id'    =>  $val['consumeCinemaLinkId'],
                'refund_price'      =>  $val['refundAmount'],
                'refund_seats'      =>  $val['refundSeats'],
                'per_ticket_times'  =>  $val['calculatePerTicketTimes'],
                'refund_points'     =>  $val['refundPoints'],
            ];
        }

        foreach ($rechargeData as $key => $val) {
            $date = explode(' ', $val['rechargeDateTime']);
            $rechargeLists[] = [
                'trace_no'          =>  $val['rechargeBookingId'],
                'trace_type_name'   =>  '充值',
                'old_price'         =>  number_format($val['beforeBalance'], 2, '.', ''),
                'price'             =>  number_format($val['afterBalance'] - $val['beforeBalance'], 2, '.', ''),
                'trace_date'        =>  $date[0],
                'trace_time'        =>  $date[1],
                'old_score'         =>  '',
                'score'             =>  $val['getPoints'],
                'trace_price'       =>  '',
                'user_code'         =>  '',
                'gift_cod'          =>  '',
                'feature_date'      =>  '',
                'feature_time'      =>  '',
                'feature_no'        =>  '',
                'film_no'           =>  '',
                'film_name'         =>  '',
                'cinema_name'       =>  $val['rechargeCinemaLinkId'],
                'ticket_num'        =>  '',
                'trace_memo'        =>  '',
                'acc_level_name'    =>  '',
                'acc_level_code'    =>  '',
                'cons_cinema_id'    =>  '',
                'refund_price'      =>  '',
                'refund_seats'      =>  '',
                'per_ticket_times'  =>  '',
                'refund_points'     =>  '',
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
                foreach ($val['hallList'] as $k => $v) {
                    $halls[$val['cinemaLinkId']][$v['hallCode']] = $v['hallName'];
                }
            }
        } else {
            foreach ($cinemas as $key => $value) {
                $halls[$cinemas['cinemaLinkId']][$value['hallCode']] = $value['hallName'];
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
        if ($arr['retCode'] != 0) {
            if (isset($arr['retMsg'])) {
                throw new \Exception($arr['retCode'] . '%' . $arr['retMsg'], $code);
            } else {
                throw new \Exception($arr['retCode'], $code);
            }
            exit;
        }

        if ($arr['data']['bizCode'] != 'SUCCESS') {
            if (isset($arr['data']['bizMsg'])) {
                throw new \Exception($arr['data']['bizCode'] . '%' . $arr['data']['bizMsg'], $code);
            } else {
                throw new \Exception($arr['data']['bizCode'], $code);
            }
        }
    }
}
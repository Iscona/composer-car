<?php

namespace biqu\TicketPlatform\M1905;

use biqu\TicketPlatform\DataFormat;

class Transform
{

    /**
     * 获取影院编号
     * wenqiang
     * 2017-04-12T15:29:54+0800
     * @return [type] [description]
     */
    public function getCinema(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        $this->throwError($arr, 600);

        $array = array_get($arr, 'Cinemas.Cinema');

        $tmp = [];
        if ($this->isIndexArr($array)) {
            foreach ($array as $key => $value) {
                $tmp[] = [
                    'cinema_no'     =>  $value['CinemaNo'],
                    'cinema_name'   =>  $value['CinemaName'],
                    'cinema_code'   =>  $value['CinemaCode'],
                    'city_no'       =>  $value['CityNo'],
                    'create_date'   =>  $value['CreateDate']
                ];
            }
        } else {
            $tmp = [
                'cinema_no'     =>  $array['CinemaNo'],
                'cinema_name'   =>  $array['CinemaName'],
                'cinema_code'   =>  $array['CinemaCode'],
                'city_no'       =>  $array['CityNo'],
                'create_date'   =>  $array['CreateDate']
            ];

            $tmp = [$tmp];
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'cinemas'       =>  $tmp
        ]);
    }
    /**
     * 注册会员卡
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-10T14:55:59+0800
     *
     * @param    DataFormat  $data
     * @return   DataFormat
     */
    public function cardRegister(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        $this->throwError($arr, 601);

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '开通会员卡成功',
            'card_number'   => $arr['CardInfo']['CardNo']
        ]);
    }

    /**
     * 登录会员卡
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-10T17:01:38+0800
     *
     * @param    DataFormat     $data
     * @param    integer        $cardType
     * @return   DataFormat
     */
    public function cardInfo(DataFormat $data, $cardType, $cardId)
    {
        $arr = $data->xmlToArray();

        $this->throwError($arr, 601);

        return $data->setData([
            'result_code'       =>  0,
            'message'           =>  '操作成功',
            'card_id'           =>  $cardId,
            'type'              =>  $cardType,
            'level_no'          =>  $arr['CardInfo']['CardLevelNo'],
            'level_name'        =>  $arr['CardInfo']['CardLevel'],
            'member_name'       =>  $arr['CardInfo']['Username'],
            'phone'             =>  $arr['CardInfo']['Mobile'],
            'balance'           =>  $arr['CardInfo']['Balance'],
            'score'             =>  '',
            'expiration_time'   =>  date('Y-m-d H:i:s', $arr['CardInfo']['ExpireDate']),
            'open_cinema'       =>  '',
        ]);
    }

    /**
     * 会员卡充值
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-10T20:12:29+0800
     *
     * @param    DataFormat $data
     * @return   DataFormat
     */
    public function cardRecharge(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        $this->throwError($arr, 601);

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '充值会员卡成功',
            'balance'       => $arr['RechargeInfo']['TotalBalance'],
            'score'         => '',
            'desc'          => $arr['ResultMsg']
        ]);
    }

    /**
     * 会员卡购票
     * wenqiang
     * 2017-03-29T14:02:30+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardBuyTicket(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        $this->throwError($arr, 601);

        return $data->setData([
            'result_code'       =>  0,
            'message'           =>  '会员卡购票成功',
            'order_no'          =>  $arr['OrderNo'],
            'valid_code'        =>  $arr['PrintNo'],
            'verify_code'       =>  $arr['VerifyCode'],
            'ground_trade_no'   =>  '',
        ]);
    }

    /**
     * 会员卡对应的排期折扣价
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-12T14:50:55+0800
     *
     * @param    DataFormat     $data
     * @param    float          $standPrice
     * @return   DataFormat
     */
    public function cardScheduleDiscount(DataFormat $data, $standPrice)
    {
        $arr = $data->xmlToArray();

        $this->throwError($arr, 601);

        if ($standPrice == $arr['SessionInfo']['MemberPrice']) {
            $type = 1;
        } else {
            $type = 0;
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '获取折扣成功',
            'price'         =>  $arr['SessionInfo']['MemberPrice'],
            'discount_type' =>  $type,
        ]);
    }

    /**
     * 会员卡所有类型
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-12T15:41:54+0800
     *
     * @param    DataFormat $data
     * @return   DataFormat
     */
    public function cardType(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        $this->throwError($arr, 601);

        $lists = [];
        $array = array_get($arr, 'Types.Type.Levels.Level');

        if ($this->isIndexArr($array)) {
            foreach ($array as $d) {
                $lists[] = [
                    'no'    => $d['LevelNo'],
                    'name'  => $d['LevelName']
                ];
            }
        } else {
            $lists = [
                'no'    =>  $array['LevelNo'],
                'name'  =>  $array['LevelName'],
            ];
            $lists = [$lists];
        }


        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'lists'         =>  $lists
        ]);
    }

    /**
     * 获取所有排期
     * wenqiang
     * 2017-03-11T15:04:49+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getAllSchedule(DataFormat $schedules, array $hallInfo)
    {
        $tmp = [];
        $arr = $schedules->xmlToArray();
        $language = [
            'cn'        =>  '国语',
            'en'        =>  '英语',
            'yue'       =>  '粤语',
            'japan'     =>  '日语',
            'german'    =>  '德语',
            'russta'    =>  '俄语',
            'or'        =>  '原版',
            'ot'        =>  '其他语言',
            'kr'        =>  '韩语',
            'in'        =>  '印度语',
            'jp'        =>  '日语',
            'hk'        =>  '粤语',
            'ru'        =>  '俄语',
            'de'        =>  '德语',
            'fr'        =>  '法语',
            'es'        =>  '西班牙语',
            'pt'        =>  '葡萄牙语',
            'it'        =>  '意大利语',
            'se'        =>  '瑞典语',
            'fi'        =>  '芬兰语',
            'mn'        =>  '蒙古语',
            'nl'        =>  '荷兰语',
            'dk'        =>  '丹麦语',
            'ua'        =>  '乌克兰语',
            'id'        =>  '印度尼西亚语',
            'la'        =>  '老挝语',
            'vn'        =>  '越南语',
            'ar'        =>  '阿拉伯语',
            'bd'        =>  '孟加拉语',
            'mm'        =>  '缅甸语',
            'th'        =>  '泰语',
        ];
        $type = [
            'normal'      =>  '2D',
            '3d'          =>  '3D',
            'imax'        =>  'IMAX',
            'imax/3d'     =>  'IMAX/3D',
            'cmax'        =>  '中国巨幕',
            'dmax'        =>  'DMAX',
            '2d'          =>  '2D',
            'imax3d'      =>  'IMAX/3D',
            'view'        =>  '影展观摩片',
            '4d'          =>  '4D',
            'film'        =>  '胶片(进口)',
            'otsp'        =>  '其他特种',
            'other'       =>  '其他',
            'dmax3d'      =>  '中国巨幕3D',
        ];

        $this->throwError($arr, 601);

        $array = $arr['Sessions']['Session'];

        if ($this->isIndexArr($array)) {
            foreach ($array as $key => $val) {
                $startTime = $array[$key]['SessionDate'] . ' ' . $val['StartTime'];
                $endTime = date('Y-m-d H:i', strtotime($startTime) + $val['TotalTime'] * 60);

                $tmp[$key]['hall_name']     =   $hallInfo[$val['ScreenNo']];
                $tmp[$key]['hall_no']       =   $val['ScreenNo'];
                $tmp[$key]['film_name']     =   $val['Films']['Film']['FilmName'];
                $tmp[$key]['film_no']       =   $val['Films']['Film']['FilmNo'];
                $tmp[$key]['start_time']    =   $startTime;
                $tmp[$key]['end_time']      =   $endTime;
                $tmp[$key]['app_price']     =   $val['AppPrice'];
                $tmp[$key]['stand_price']   =   $val['StandartPrice'];
                $tmp[$key]['protect_price'] =   $val['LowestPrice'];
                $tmp[$key]['schedule_no']   =   $val['SessionNo'];
                $tmp[$key]['feature_no']    =   '';
                $tmp[$key]['film_language'] =   isset($language[strtolower($val['Films']['Film']['Language'])]) ?
                                                    $language[strtolower($val['Films']['Film']['Language'])] :
                                                    $val['Films']['Film']['Language'];
                $tmp[$key]['film_type']     =   isset($type[$val['Films']['Film']['FilmType']]) ?
                                                    $type[$val['Films']['Film']['FilmType']] :
                                                    $val['Films']['Film']['FilmType'];
                $tmp[$key]['set_status']    =   $val['Status'];
            }
        } else {
            $startTime = $array['SessionDate'] . ' ' . $array['StartTime'];
            $endTime = date('Y-m-d H:i', strtotime($startTime) + $array['TotalTime'] * 60);

            $tmp['hall_name']     =   $hallInfo[$array['ScreenNo']];
            $tmp['hall_no']       =   $array['ScreenNo'];
            $tmp['film_name']     =   $array['Films']['Film']['FilmName'];
            $tmp['film_no']       =   $array['Films']['Film']['FilmNo'];
            $tmp['start_time']    =   $startTime;
            $tmp['end_time']      =   $endTime;
            $tmp['app_price']     =   $array['AppPrice'];
            $tmp['stand_price']   =   $array['StandartPrice'];
            $tmp['protect_price'] =   $array['LowestPrice'];
            $tmp['schedule_no']   =   $array['SessionNo'];
            $tmp['feature_no']    =   '';
            $tmp['film_language'] =   $language[$array['Films']['Film']['Language']];
            $tmp['film_type']     =   $type[$array['Films']['Film']['FilmType']];
            $tmp['set_status']    =   $array['Status'];

            $tmp = [$tmp];
        }

        return $schedules->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'cinema_plans'  =>  $tmp
        ]);
    }

    /**
     * 组织影厅编号和影厅名称的关联关系
     * wenqiang
     * 2017-03-15T11:37:09+0800
     * @param  DataFormat $data [description]
     * @return array      关联数组
     */
    public function getHalls(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        $this->throwError($arr, 601);

        $hallInfo = [];

        if (!$this->isIndexArr($arr['Screens']['Screen'])) {
            $hallInfo[$arr['Screens']['Screen']['ScreenNo']] = $arr['Screens']['Screen']['ScreenName'];
        } else {
            foreach ($arr['Screens']['Screen'] as $key => $val) {
                $hallInfo[$arr['Screens']['Screen'][$key]['ScreenNo']] = $val['ScreenName'];
            }
        }

        return $hallInfo;
    }
    /**
     * 获取对应排期下的座位图状态
     * wenqiang
     * 2017-03-11T15:17:47+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getSeatInfo(DataFormat $infos, DataFormat $status)
    {
        $seatInfos  = $infos->xmlToArray();
        $seatStatus = $status->xmlToArray();

        $this->throwError($seatInfos, 601);
        $this->throwError($seatStatus, 601);

        $statusArray = [];
        $array = array_get($seatStatus, 'SessionSeats.SessionSeat');

        foreach ($array as $key => $val) {
            $statusArray[$val['SeatNo']] = $val['SeatStatus'];
        }

        $infoArray = array_get($seatInfos, 'ScreenSeats.ScreenSeat');

        $maxRow = max(array_column($infoArray, 'GraphRow'));
        $maxCol = max(array_column($infoArray, 'GraphCol'));
        $minCol = min(array_column($infoArray, 'GraphCol'));

        $seatMap = [];
        $loveCode = [];
        foreach ($infoArray as $d) {
            $seatMap[$d['GraphRow'].'-'.$d['GraphCol']] = $d;

            if ($d['SeatTypeNo'] == 2) {
                $loveCode[$d['BindNo']][$d['SeatNo']] = $d['GraphCol'];
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

        $seatData = [];
        for ($row = 1; $row <= $maxRow; $row++) {
            for ($col= 1; $col <= $maxCol; $col++) {
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
                        $seatData[$row - 1][$col - $minCol]['seat_no'] = $seatMap[$row.'-'.$col]['SeatNo'];
                        $seatData[$row - 1][$col - $minCol]['row']  = intval($seatMap[$row.'-'.$col]['SeatRow']);
                        $seatData[$row - 1][$col - $minCol]['col']  = intval($seatMap[$row.'-'.$col]['SeatCol']);
                        $seatData[$row - 1][$col - $minCol]['code'] = $seatMap[$row.'-'.$col]['SeatPieceNo'];
                        $seatData[$row - 1][$col - $minCol]['true_state'] = $statusArray[$seatMap[$row.'-'.$col]['SeatNo']];
                        if (array_key_exists($seatMap[$row.'-'.$col]['SeatNo'], $statusArray) && $statusArray[$seatMap[$row.'-'.$col]['SeatNo']] != 0) {
                            $seatData[$row - 1][$col - $minCol]['state'] = 1;
                        } else {
                            $seatData[$row-1][$col - $minCol]['state'] = 0;
                        }

                        if ($seatMap[$row.'-'.$col]['SeatTypeNo'] == 1) {
                            $seatData[$row - 1][$col - $minCol]['type'] = 'N';
                        } elseif ($seatMap[$row.'-'.$col]['SeatTypeNo'] == 2) {
                            $seatData[$row - 1][$col - $minCol]['type'] = array_get($loveCode, $seatMap[$row.'-'.$col]['BindNo'] . '.' . $seatMap[$row.'-'.$col]['SeatNo']);
                        }

                    }
                }
            }
        }

        return $infos->setData([
            'result_code'   => 0,
            'message'       => '操作成功',
            'seat_data'     => $seatData,
        ]);
    }

    /**
     * 锁座并下单
     * wenqiang
     * 2017-03-11T15:25:04+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function lockSeat(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        $this->throwError($arr, 601);

        return $data->setData([
            'result_code'   =>  0,
            'message'       => '锁座成功',
            'order_no'      => $arr['OrderNo']
        ]);
    }

    /**
     * 解锁座位
     * wenqiang
     * 2017-03-11T15:40:06+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function unlockSeat(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        $this->throwError($arr, 601);

        return $data->setData([
            'result_code'   =>  0,
            'message'       =>  '解锁成功'
        ]);
    }


    /**
     * 常规买票
     * wenqiang
     * 2017-03-10T21:28:07+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function ticketPlaceOrder(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        $this->throwError($arr, 601);

        return $data->setData([
            'result_code'   => 0,
            'order_no'      => $arr['OrderNo'],
            'message'       => '下单成功',
            'valid_code'    => $arr['PrintNo'],
            'verify_code'   => $arr['VerifyCode'],
        ]);
    }

    /**
     * 退票
     * wenqiang
     * 2017-03-11T15:48:09+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function refundOrder(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        $this->throwError($arr, 601);

        return $data->setData([
            'result_code'   => $arr['ResultCode'],
            'message'       => '退票成功',
        ]);
    }

    /**
     * 获取电影票信息
     * wenqiang
     * 2017-03-11T17:53:29+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getOrderInfo(DataFormat $orderData, DataFormat $qrCodeData)
    {
        $qrCodeArr = $qrCodeData->xmlToArray();
        $orderInfoArr = $orderData->xmlToArray();

        $this->throwError($orderInfoArr, 601);
        $this->throwError($qrCodeArr, 601);

        $qr = [];
        $qrInfo = array_get($orderInfoArr, 'Seats.Seat');
        if ($this->isIndexArr($qrInfo)) {
            foreach ($qrInfo as $key => $val) {
                $qr[] = [
                    'qr_code'   =>  '',
                    'ticket_no' =>  $val['FilmTicketCode'],
                    'seat_no'   =>  $val['SeatNo'],
                    'seat_code' =>  $val['SeatCode'],
                    'seat_row'  =>  '',
                    'seat_col'   =>  '',
                    'cpn_name'  =>  '',
                    'ticket_price'  =>  '',
                    'service'   =>  '',
                    'sell_ticket_time'  =>  '',
                ];
            }
        } else {
            $qr[0] = [
                'qr_code'   =>  '',
                'ticket_no' =>  $qrInfo['FilmTicketCode'],
                'seat_no'   =>  $qrInfo['SeatNo'],
                'seat_code' =>  $qrInfo['SeatCode'],
                'seat_row'  =>  '',
                'seat_col'   =>  '',
                'cpn_name'  =>  '',
                'ticket_price'  =>  '',
                'service'   =>  '',
                'sell_ticket_time'  =>  '',
            ];
        }

        return $orderData->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'ticket_plat'   =>  'm1905',
            'print_type'    =>  $orderInfoArr['PrintStatus'],
            'order_no'      =>  $orderInfoArr['OrderNo'],
            'order_date'    =>  '',
            'ticket_num'    =>  count($qr),
            'ticket_type'   =>  '',
            'film_no'       =>  $orderInfoArr['FilmCode'],
            'film_name'     =>  '',
            'hall_no'       =>  $orderInfoArr['ScreenCode'],
            'hall_name'     =>  '',
            'feature_no'    =>  $orderInfoArr['SessionCode'],
            'feature_time'  =>  '',
            'print_data'    =>  $qr
        ]);
    }

    /**
     * 获取订单售票状态
     * wenqiang
     * 2017-03-11T15:55:34+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getOrderStatus(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        $this->throwError($arr, 601);

        if ($arr['RefundStatus'] == 1) {
            $status = 7;        //已退票
        } elseif ($arr['PrintStatus']) {
            $status = 8;        //已打票
        } elseif ($arr['OrderStatus'] == 0) {
            $status = 6;        //锁座成功但未支付
        } elseif ($arr['OrderStatus'] == 1) {
            $status = 9;        //已出售
        } elseif ($arr['OrderStatus'] == 2) {
            $status = 2;        //未知状态（由于网络原因导致的）
        } elseif ($arr['OrderStatus'] == 6) {
            $status = 3;        //座位已解锁
        }

        return $data->setData([
            'result_code'   => 0,
            'order_no'      => $arr['OrderNo'],
            'message'       => '操作成功',
            'valid_code'    => $arr['PrintNo'],
            'order_status'  => $status
        ]);
    }

    /**
     * 合作商打票
     * wenqiang
     * 2017-03-11T16:23:00+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function printTicket(DataFormat $printData , DataFormat $qrCodeData, DataFormat $orderInfoData)
    {
        $printArr = $printData->xmlToArray();
        $qrCodeArr = $qrCodeData->xmlToArray();
        $orderInfoArr = $orderInfoData->xmlToArray();

        $this->throwError($printArr, 603);
        $this->throwError($orderInfoArr, 603);
        $this->throwError($qrCodeArr, 603);

        $qr = [];
        $qrInfo = array_get($qrCodeArr, 'QrInfos.QrInfo');
        if ($this->isIndexArr($qrInfo)) {
            foreach ($qrInfo as $key => $val) {
                $qr[] = [
                    'qr_code'   =>  $val['QrCode'],
                    'ticket_no' =>  $val['FilmTicketCode'],
                    'seat_no'   =>  $val['SeatNo'],
                    'seat_code' =>  '',
                    'seat_row'  =>  '',
                    'seat_col'  =>  '',
                    'cpn_name'  =>  '',
                    'ticket_price'  =>  '',
                    'service'   =>  '',
                    'sell_ticket_time'  =>  '',
                ];
            }
        } else {
            $qr[0] = [
                'qr_code'   =>  $qrInfo['QrCode'],
                'ticket_no' =>  $qrInfo['FilmTicketCode'],
                'seat_no'   =>  $qrInfo['SeatNo'],
                'seat_code' =>  '',
                'seat_row'  =>  '',
                'seat_col'   =>  '',
                'cpn_name'  =>  '',
                'ticket_price'  =>  '',
                'service'   =>  '',
                'sell_ticket_time'  =>  '',
            ];
        }

        return $printData->setData([
            'result_code'   =>  0,
            'message'       =>  '操作成功',
            'ticket_plat'   =>  'm1905',
            'print_type'    =>  $printArr['ResultCode'] == 0 ? 1 : 0, // 返回结果 (0 成功, 1 失败)
            'order_no'      =>  $orderInfoArr['OrderNo'],
            'order_date'    =>  '',
            'ticket_num'    =>  count($qr),
            'ticket_type'   =>  '',
            'film_no'       =>  $orderInfoArr['FilmCode'],
            'film_name'     =>  '',
            'hall_no'       =>  $orderInfoArr['ScreenCode'],
            'hall_name'     =>  '',
            'feature_no'    =>  $orderInfoArr['SessionCode'],
            'feature_time'  =>  '',
            'print_data'    =>  $qr
        ]);
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
        if ($arr['ResultCode'] != 0) {
            if (isset($arr['ResultMsg'])) {
                throw new \Exception($arr['ResultCode'] . '%' . $arr['ResultMsg'], $code);
            } else {
                throw new \Exception($arr['ResultCode'], $code);
            }
        }
    }
}

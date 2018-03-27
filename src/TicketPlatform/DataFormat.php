<?php

namespace biqu\TicketPlatform;

use biqu\XmlArray;

class DataFormat
{
    /**
     * 原始数据
     * @var mixed
     */
    protected $data;

    /**
     * xml转换服务
     * @var biqu\XmlArray
     */
    protected $xmlService;

    /**
     * 初始化数据
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-10T02:54:27+0800
     *
     * @param  mixed    $data
     */
    public function __construct($data)
    {
        $this->data = $data;

        $this->xmlService   = new XmlArray();
    }

    public function xmlToArray()
    {
        return $this->xmlService->strXmlToArray($this->data);
    }

    public function toArray()
    {
        if (is_array($this->data)) {
            return $this->data;
        } elseif (!is_null(json_decode($this->data, true))) {
            return json_decode($this->data, true);
        } else {
            return $this->xmlService->strXmlToArray($this->data);
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function __toString()
    {
        return $this->data;
    }
}

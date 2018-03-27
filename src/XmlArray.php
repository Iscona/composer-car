<?php

namespace biqu;

/**
 * XML和数组互相转换
 * @author 朱其鹏 <28942998@qq.com>
 */
class XmlArray
{
    /**
     * 将数组转换成xml格式文件
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public  function arrayToXml($data, $rootNodeName = 'xml', $xml=null)
    {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1) {
            ini_set ('zend.ze1_compatibility_mode', 0);
        }

        if ($xml == null) {
            $xml = simplexml_load_string(
                "<?xml version='1.0' encoding='utf-8'?><$rootNodeName/>");
        }

        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = "unknownNode_". (string) $key;
            }

            $key = preg_replace('/[^a-z][_]/i', '', $key);

            if (is_array($value)) {
                $node = $xml->addChild($key);
                $this->arrayToXml($value, $rootNodeName, $node);
            } else {
                $value = htmlentities($value);
                $xml->addChild($key,$value);
            }
        }

        return $xml->asXML();
    }

    /**
     * 把xml格式的字符串转换成数组
     * 朱其鹏
     * 2016-08-20T11:51:56+0800
     * @param  string $strXml [description]
     * @return [type]         [description]
     */
    public function strXmlToArray($strXml)
    {
        $xml = simplexml_load_string($strXml,
                                     'SimpleXMLElement',
                                     LIBXML_NOCDATA | LIBXML_NOBLANKS);

        return $this->xmlToArray($xml);
    }

    /**
     * 把xml文件或字符串解析成数组
     *
     * @param object $xml  xml对象
     * @param bool   $type          true为 xml代码 ， false为xml文件
     */
    public function xmlToArray($xml)
    {
        $array = (array) $xml;
        foreach ($array as $key=>$item) {
            $array[$key]  = $this->structToArray($item);
        }
        return $array;
    }

    /**
     * 配合xmlToArray方法使用
     * 朱其鹏
     * 2016-08-13T09:33:36+0800
     * @param  array    $item   [description]
     * @return [type]           [description]
     */
    protected function structToArray($item)
    {
        if (!is_string($item)) {

            $item = (array) $item;

            if(empty($item)) {
                return '';
            }
            foreach ($item as $key=>$val) {
                $item[$key] = $this->structToArray($val);
            }
        }

        return $item;
    }
}
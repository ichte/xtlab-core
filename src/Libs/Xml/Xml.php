<?php

namespace XT\Core\Libs\Xml;


class Xml
{
    protected $parentkey;
    /***
     * @var string
     */
    protected $filename;

    /***
     * @var \SimpleXMLElement
     */
    protected $xml;

    /**
     * Xml constructor.
     * @param $filename string
     */
    public function Init($filename,$parentkey)
    {
        $this->parentkey = $parentkey;
        $this->filename = $filename;
        $this->xml = simplexml_load_file($filename);
    }


    public function Save() {
//        foreach ($this->xml as $item) {
//            $item->addAttribute('id', md5($val = (string)($item->attributes())['name']));
//        }

        $b = file_put_contents($this->filename, $this->xml->asXML());
        if ($b === false)
            throw new \Exception("Can not write: {$this->filename}");

    }

    /***
     * @param $name
     * @param $keyname
     * @param $valkey
     * @param $vals
     * @param null $parent
     * @return null|\SimpleXMLElement
     */

    public function AddElement($name, $keyname, $valkey, $vals, $parent = null ) {
        $parent = $parent??$this->xml;
        if ($this->Find($keyname, $valkey) != null)
            return null;
        $ele = $parent->addChild($name);
        $ele->addAttribute($keyname, $valkey);
        $ele->addAttribute('id', md5($valkey));

        if (is_array($vals))
            foreach ($vals as $n => $v) {
                $ele->$n = $v;
            }
        return $ele;
    }

    public function RemoveElement($nameattr, $value, $parent = null) {
        $parent = $parent??$this->xml;
        $ele = $this->Find($nameattr, $value, $parent);
        if ($ele == null)
            return;

        $dom = dom_import_simplexml($ele);
        $dom->parentNode->removeChild($dom);
    }

    public function Find($nameattr, $value, $parent = null ) {
        $parent = $parent??$this->xml;
        foreach ($parent as $element) {
            $val = (string)($element->attributes())[$nameattr];
            if ($val == $value)
                return $element;
        }
        return null;
    }

}
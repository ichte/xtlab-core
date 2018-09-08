<?php

namespace XTTest\Core;


use PHPUnit\Framework\TestCase;
use XT\Core\Filter\LatinLowCase;

class LatinTest extends TestCase
{
    // @codingStandardsIgnoreStart
    /**
     * Zend_Filter_StringToLower object
     *
     * @var LatinLowCase
     */
    protected $_filter;
    // @codingStandardsIgnoreEnd
    /**
     * Creates a new Zend_Filter_StringToUpper object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new LatinLowCase();
    }
    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $filter = $this->_filter;
        $valuesExpected = [
            'Việt Nam' => 'Viet Nam',
            'à á ả' => 'a a a',
            'ô ê ị ý ú ù Ô Ê Ị Ý Ú Ù'  => 'o e i y u u o e i y u u'
        ];
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals($output, $filter->filter($input));
        }
    }

}
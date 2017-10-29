<?php

namespace Tests\Symftony\Xpression;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symftony\Xpression\Expr\ExpressionBuilderInterface;
use Symftony\Xpression\Parser;

class ParserTest extends TestCase
{
    /**
     * @var ExpressionBuilderInterface|ObjectProphecy
     */
    private $expressionBuilderMock;

    /**
     * @var Parser
     */
    private $parser;

    public function setUp()
    {
        $this->expressionBuilderMock = $this->prophesize('Symftony\Xpression\Expr\ExpressionBuilderInterface');

        $this->parser = new Parser($this->expressionBuilderMock->reveal());
    }

    public function parseSuccessDataProvider()
    {
        return array(
            array(
                'fieldA=1',
                array(array('eq', 'fieldA', 1, 'my_fake_comparison_A')),
                array(),
                'my_fake_comparison_A',
            ),
            array(
                'fieldA>1',
                array(array('gt', 'fieldA', 1, 'my_fake_comparison_A')),
                array(),
                'my_fake_comparison_A',
            ),
            array(
                'fieldA≥1',
                array(
                    array('gte', 'fieldA', 1, 'my_fake_comparison_A')
                ),
                array(),
                'my_fake_comparison_A',
            ),
            array(
                'fieldA>=1',
                array(
                    array('gte', 'fieldA', 1, 'my_fake_comparison_A')
                ),
                array(),
                'my_fake_comparison_A',
            ),
            array(
                'fieldA<1',
                array(
                    array('lt', 'fieldA', 1, 'my_fake_comparison_A')
                ),
                array(),
                'my_fake_comparison_A',
            ),
            array(
                'fieldA≤1',
                array(
                    array('lte', 'fieldA', 1, 'my_fake_comparison_A')
                ),
                array(),
                'my_fake_comparison_A',
            ),
            array(
                'fieldA<=1',
                array(
                    array('lte', 'fieldA', 1, 'my_fake_comparison_A')
                ),
                array(),
                'my_fake_comparison_A',
            ),
            array(
                'fieldA≠1',
                array(
                    array('neq', 'fieldA', 1, 'my_fake_comparison_A')
                ),
                array(),
                'my_fake_comparison_A',
            ),
            array(
                'fieldA!=1',
                array(
                    array('neq', 'fieldA', 1, 'my_fake_comparison_A')
                ),
                array(),
                'my_fake_comparison_A',
            ),
            array(
                'fieldA[1,2]',
                array(
                    array('in', 'fieldA', array(1, 2), 'my_fake_comparison_A')
                ),
                array(),
                'my_fake_comparison_A',
            ),
            array(
                'fieldA![1,2]',
                array(
                    array('notIn', 'fieldA', array(1, 2), 'my_fake_comparison_A')
                ),
                array(),
                'my_fake_comparison_A'
            ),

            // Composite
            array(
                'fieldA=1|fieldB=2|fieldC=3',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                    array('eq', 'fieldB', 2, 'my_fake_comparison_B'),
                    array('eq', 'fieldC', 3, 'my_fake_comparison_C'),
                ),
                array(
                    array('orX', array('my_fake_comparison_A', 'my_fake_comparison_B', 'my_fake_comparison_C'), 'my_fake_orX_composite'),
                ),
                'my_fake_orX_composite'
            ),
            array(
                'fieldA=1!|fieldB=2!|fieldC=3',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                    array('eq', 'fieldB', 2, 'my_fake_comparison_B'),
                    array('eq', 'fieldC', 3, 'my_fake_comparison_C'),
                ),
                array(
                    array('norX', array('my_fake_comparison_A', 'my_fake_comparison_B', 'my_fake_comparison_C'), 'my_fake_norX_composite'),
                ),
                'my_fake_norX_composite'
            ),
            array(
                'fieldA=1^|fieldB=2^|fieldC=3',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                    array('eq', 'fieldB', 2, 'my_fake_comparison_B'),
                    array('eq', 'fieldC', 3, 'my_fake_comparison_C'),
                ),
                array(
                    array('xorX', array('my_fake_comparison_A', 'my_fake_comparison_B', 'my_fake_comparison_C'), 'my_fake_xorX_composite'),
                ),
                'my_fake_xorX_composite'
            ),
            array(
                'fieldA=1⊕fieldB=2⊕fieldC=3',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                    array('eq', 'fieldB', 2, 'my_fake_comparison_B'),
                    array('eq', 'fieldC', 3, 'my_fake_comparison_C'),
                ),
                array(
                    array('xorX', array('my_fake_comparison_A', 'my_fake_comparison_B', 'my_fake_comparison_C'), 'my_fake_xorX_composite'),
                ),
                'my_fake_xorX_composite'
            ),
            array(
                'fieldA=1&fieldB=2&fieldC=3',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                    array('eq', 'fieldB', 2, 'my_fake_comparison_B'),
                    array('eq', 'fieldC', 3, 'my_fake_comparison_C'),
                ),
                array(
                    array('andX', array('my_fake_comparison_A', 'my_fake_comparison_B', 'my_fake_comparison_C'), 'my_fake_andX_composite'),
                ),
                'my_fake_andX_composite'
            ),
            array(
                'fieldA=1!&fieldB=2!&fieldC=3',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                    array('eq', 'fieldB', 2, 'my_fake_comparison_B'),
                    array('eq', 'fieldC', 3, 'my_fake_comparison_C'),
                ),
                array(
                    array('nandX', array('my_fake_comparison_A', 'my_fake_comparison_B', 'my_fake_comparison_C'), 'my_fake_nandX_composite'),
                ),
                'my_fake_nandX_composite'
            ),

            // Precedences
            array(
                'fieldA=1|fieldB=2|fieldC=3&fieldD=4',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                    array('eq', 'fieldB', 2, 'my_fake_comparison_B'),
                    array('eq', 'fieldC', 3, 'my_fake_comparison_C'),
                    array('eq', 'fieldD', 4, 'my_fake_comparison_D'),
                ),
                array(
                    array('andX', array('my_fake_comparison_C', 'my_fake_comparison_D'), 'my_fake_andX_composite'),
                    array('orX', array('my_fake_comparison_A', 'my_fake_comparison_B', 'my_fake_andX_composite'), 'my_fake_orX_composite'),
                ),
                'my_fake_orX_composite'
            ),
            array(
                'fieldA=1&fieldB=2&fieldC=3!&fieldD=4',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                    array('eq', 'fieldB', 2, 'my_fake_comparison_B'),
                    array('eq', 'fieldC', 3, 'my_fake_comparison_C'),
                    array('eq', 'fieldD', 4, 'my_fake_comparison_D'),
                ),
                array(
                    array('andX', array('my_fake_comparison_A', 'my_fake_comparison_B', 'my_fake_comparison_C'), 'my_fake_andX_composite'),
                    array('nandX', array('my_fake_andX_composite', 'my_fake_comparison_D'), 'my_fake_orX_composite'),
                ),
                'my_fake_orX_composite'
            ),
            array(
                'fieldA=1|fieldB=2|fieldC=3!|fieldD=4',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                    array('eq', 'fieldB', 2, 'my_fake_comparison_B'),
                    array('eq', 'fieldC', 3, 'my_fake_comparison_C'),
                    array('eq', 'fieldD', 4, 'my_fake_comparison_D'),
                ),
                array(
                    array('orX', array('my_fake_comparison_A', 'my_fake_comparison_B', 'my_fake_comparison_C'), 'my_fake_orX_composite'),
                    array('norX', array('my_fake_orX_composite', 'my_fake_comparison_D'), 'my_fake_norX_composite'),
                ),
                'my_fake_norX_composite'
            ),
            array(
                'fieldA=1&fieldB=2&fieldC=3|fieldD=4',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                    array('eq', 'fieldB', 2, 'my_fake_comparison_B'),
                    array('eq', 'fieldC', 3, 'my_fake_comparison_C'),
                    array('eq', 'fieldD', 4, 'my_fake_comparison_D'),
                ),
                array(
                    array('andX', array('my_fake_comparison_A', 'my_fake_comparison_B', 'my_fake_comparison_C'), 'my_fake_andX_composite'),
                    array('orX', array('my_fake_andX_composite', 'my_fake_comparison_D'), 'my_fake_orX_composite'),
                ),
                'my_fake_orX_composite'
            ),
            array(
                'fieldA=1&fieldB=2|fieldC=3&fieldD=4',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                    array('eq', 'fieldB', 2, 'my_fake_comparison_B'),
                    array('eq', 'fieldC', 3, 'my_fake_comparison_C'),
                    array('eq', 'fieldD', 4, 'my_fake_comparison_D'),
                ),
                array(
                    array('andX', array('my_fake_comparison_A', 'my_fake_comparison_B'), 'my_fake_andX_composite_1'),
                    array('andX', array('my_fake_comparison_C', 'my_fake_comparison_D'), 'my_fake_andX_composite_2'),
                    array('orX', array('my_fake_andX_composite_1', 'my_fake_andX_composite_2'), 'my_fake_orX_composite'),
                ),
                'my_fake_orX_composite'
            ),
            array(
                'fieldA=1|fieldB=2|fieldC=3⊕fieldD=4',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                    array('eq', 'fieldB', 2, 'my_fake_comparison_B'),
                    array('eq', 'fieldC', 3, 'my_fake_comparison_C'),
                    array('eq', 'fieldD', 4, 'my_fake_comparison_D'),
                ),
                array(
                    array('orX', array('my_fake_comparison_A', 'my_fake_comparison_B', 'my_fake_comparison_C'), 'my_fake_orX_composite'),
                    array('xorX', array('my_fake_orX_composite', 'my_fake_comparison_D'), 'my_fake_xorX_composite'),
                ),
                'my_fake_xorX_composite'
            ),

            //Parenthesis
            array(
                '((fieldA=1))',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                ),
                array(),
                'my_fake_comparison_A'
            ),
            array(
                '(fieldA=1|fieldB=2)&fieldC=3',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                    array('eq', 'fieldB', 2, 'my_fake_comparison_B'),
                    array('eq', 'fieldC', 3, 'my_fake_comparison_C'),
                ),
                array(
                    array('orX', array('my_fake_comparison_A', 'my_fake_comparison_B'), 'my_fake_orX_composite'),
                    array('andX', array('my_fake_orX_composite', 'my_fake_comparison_C'), 'my_fake_andX_composite'),
                ),
                'my_fake_andX_composite'
            ),
            array(
                'fieldA=1|(fieldB=2&fieldC=3)',
                array(
                    array('eq', 'fieldA', 1, 'my_fake_comparison_A'),
                    array('eq', 'fieldB', 2, 'my_fake_comparison_B'),
                    array('eq', 'fieldC', 3, 'my_fake_comparison_C'),
                ),
                array(
                    array('andX', array('my_fake_comparison_B', 'my_fake_comparison_C'), 'my_fake_andX_composite'),
                    array('orX', array('my_fake_comparison_A', 'my_fake_andX_composite'), 'my_fake_orX_composite'),
                ),
                'my_fake_orX_composite'
            ),
        );
    }

    /**
     * @dataProvider parseSuccessDataProvider
     *
     * @param $input
     * @param $comparisonMethods
     * @param $compositeMethods
     */
    public function testParseSuccess($input, $comparisonMethods, $compositeMethods, $expectedResult)
    {
        foreach ($comparisonMethods as $comparisonMethod) {
            $this->expressionBuilderMock
                ->{$comparisonMethod[0]}($comparisonMethod[1], $comparisonMethod[2])
                ->willReturn($comparisonMethod[3])
                ->shouldBeCalled();
        }

        foreach ($compositeMethods as $compositeMethod) {
            $this->expressionBuilderMock
                ->{$compositeMethod[0]}($compositeMethod[1])
                ->willReturn($compositeMethod[2])
                ->shouldBeCalled();
        }

        $this->assertEquals($expectedResult, $this->parser->parse($input));
    }
}
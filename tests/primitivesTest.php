<?php declare(strict_types=1);

namespace Tests\Mathias\ParserCombinator;

use Mathias\ParserCombinator\PHPUnit\ParserAssertions;
use PHPUnit\Framework\TestCase;
use function Mathias\ParserCombinator\{anything, eof, everything, nothing, satisfy, takeWhile, takeWhile1};
use function Mathias\ParserCombinator\Predicates\{isEqual, notPred};


final class primitivesTest extends TestCase
{
    use ParserAssertions;

    /** @test */
    public function satisfy()
    {
        $parser = satisfy(isEqual('x'));
        $this->assertParse("x", $parser, "xyz");
        $this->assertRemain("yz", $parser, "xyz");
        $this->assertNotParse($parser, "yz", "satisfy(predicate)");
        $this->assertNotParse($parser, "", "satisfy(predicate)");
    }

    /** @test */
    public function anything_()
    {
        $this->assertParse("x", anything(), "xyz");
        $this->assertRemain("yz", anything(), "xyz");
        $this->assertParse(":", anything(), ":-)");
        $this->assertRemain("-)", anything(), ":-)");
        $this->assertNotParse(anything(), "", "anything");
    }

    /** @test */
    public function nothing()
    {
        $this->assertRemain("xyz", nothing(), "xyz");
        $this->assertRemain(":-)", nothing(), ":-)");
    }

    /** @test */
    public function everything()
    {
        $this->assertParse("xyz", everything(), "xyz");
        $this->assertRemain("", everything(), "xyz");
        $this->assertParse(":-)", everything(), ":-)");
        $this->assertRemain("", everything(), ":-)");
        $this->assertParse("", everything(), "");
    }

    /** @test */
    public function eof()
    {
        $this->assertParse("", eof(), "");
        $this->assertNotParse(eof(), "xyz", "eof");
    }

    /** @test */
    public function takeWhile()
    {
        $parser = takeWhile(isEqual('a'));
        $this->assertParse("", $parser, "xyz");
        $this->assertParse("", $parser, "xaaa");
        $this->assertParse("a", $parser, "axyz");
        $this->assertParse("aaa", $parser, "aaaxyz");
        $this->assertParse("aaa", $parser, "aaa");
    }

    /** @test */
    public function takeWhile_using_not()
    {
        $parser = takeWhile(notPred(isEqual('a')));

        $this->assertParse("xyz", $parser, "xyza");
        $this->assertParse("xyz", $parser, "xyz");
        $this->assertParse("x", $parser, "xaaa");
        $this->assertParse("", $parser, "axyz");
        $this->assertParse("", $parser, "aaaxyz");
        $this->assertParse("", $parser, "aaa");
    }

    /** @test */
    public function not_sure_how_takeWhile_should_deal_with_EOF()
    {
        // For now let's have it succeed until we figure it out.
        $parser = takeWhile(isEqual('a'));
        $this->assertSucceedOnEOF($parser);

        $parser = takeWhile(notPred(isEqual('a')));
        $this->assertSucceedOnEOF($parser);
    }


    /** @test */
    public function takeWhile1()
    {
        $parser = takeWhile1(isEqual('a'));
        $this->assertFailOnEOF($parser);
        $this->assertNotParse($parser, "xyz", "takeWhile1(predicate)");
        $this->assertNotParse($parser, "takeWhile1(predicate)");
        $this->assertParse("a", $parser, "axyz");
        $this->assertParse("aaa", $parser, "aaaxyz");
        $this->assertParse("aaa", $parser, "aaa");
        $this->assertNotParse($parser, "", "takeWhile1(predicate)");
    }
}

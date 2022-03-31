<?php

use Antlr\Antlr4\Runtime\CommonTokenStream;
use Antlr\Antlr4\Runtime\TokenStreamRewriter;
use Antlr\Antlr4\Runtime\Error\Listeners\DiagnosticErrorListener;
use Antlr\Antlr4\Runtime\Error\Listeners\ConsoleErrorListener;
use Antlr\Antlr4\Runtime\InputStream;
use RomanParser\RomanNumeralsLexer;
use RomanParser\RomanNumeralsParser;
use RomanParser\LogErrorListener;
use RomanParser\RomanNumeralsTranslateVisitor;

use PHPUnit\Framework\TestCase;

final class RomanVisitorTest extends TestCase
{
    private function setupVisitor(string $text): RomanNumeralsTranslateVisitor
    {
        $input = InputStream::fromString($text);
        $lexer = new RomanNumeralsLexer($input);
        $tokens = new CommonTokenStream($lexer);
        $parser = new RomanNumeralsParser($tokens);
        $tree = $parser->expression();

        $visitor = new RomanNumeralsTranslateVisitor();
        $visitor->visit($tree);
        
        return $visitor;
    }

    public function testCanCalculateXii(): void
    {
        $visitor = $this->setupVisitor("XII");        
                
        $this->assertEquals(
            12,
            $visitor->value,
        );
    }

    public function testCanCalculateMmcdii(): void
    {
        $visitor = $this->setupVisitor("MMCDII");        
                
        $this->assertEquals(
            2402,
            $visitor->value,
        );
    }

    public function testCanTranslateMmcdii(): void
    {
        $visitor = $this->setupVisitor("MMCDII");
                
        $this->assertEquals(
            "<abbr title=\"2402\">MMCDII</abbr>",
            $visitor->text,
        );
    }
}

<?php

use Antlr\Antlr4\Runtime\CommonTokenStream;
use Antlr\Antlr4\Runtime\TokenStreamRewriter;
use Antlr\Antlr4\Runtime\Error\Listeners\DiagnosticErrorListener;
use Antlr\Antlr4\Runtime\Error\Listeners\ConsoleErrorListener;
use Antlr\Antlr4\Runtime\InputStream;
use RomanParser\RomanNumeralsLexer;
use RomanParser\RomanNumeralsParser;
use RomanParser\LogErrorListener;

use PHPUnit\Framework\TestCase;

final class RomanParserTest extends TestCase
{
    private function setupParser(string $text): RomanNumeralsParser
    {
        $input = InputStream::fromString($text);
        $lexer = new RomanNumeralsLexer($input);
        $tokens = new CommonTokenStream($lexer);
        $parser = new RomanNumeralsParser($tokens);
        
        return $parser;
    }
    
    public function testCanParseRomanNumeralWithoutErrors(): void
    {
        $parser = $this->setupParser("XII");
        $parser->numeral();
        
        $this->assertEquals(
            0,
            $parser->getNumberOfSyntaxErrors()
        );
    }

    public function testCanParseThousPart(): void
    {
        $parser = $this->setupParser("MM");
        $parser->thous_part();
        
        $this->assertEquals(
            0,
            $parser->getNumberOfSyntaxErrors()
        );
    }

    public function testCanParseHundreds(): void
    {
        $parser = $this->setupParser("X");
        $parser->hundreds();
        
        $this->assertEquals(
            0,
            $parser->getNumberOfSyntaxErrors()
        );
    }

    public function testCanParseTens(): void
    {
        $parser = $this->setupParser("XCI");
        $parser->tens();
        
        $this->assertEquals(
            0,
            $parser->getNumberOfSyntaxErrors()
        );
    }

    public function testDoesNotThrowErrorIfItDoesNotFindAnyRomanNumeral(): void
    {
        $parser = $this->setupParser("There is nothing Roman here.");
        $parser->expression();
        
        $this->assertEquals(
            0,
            $parser->getNumberOfSyntaxErrors()
        );
    }

    public function testCanFindErrorsInMalformedNumeral(): void
    {
        $parser = $this->setupParser("Look at CCM, it is clearly a mistake");        
        $errlis = new LogErrorListener();
        $parser->addErrorListener($errlis);
        $parser->expression();        
        
        $this->assertEquals(
            1,
            count($errlis->errors)
        );

        $this->assertEquals(
            "Error at 1:10 extraneous input 'M' expecting {WS, ANY}",
            $errlis->errors[0]
        );
    }

    public function testHundredsCannotParseThousands(): void
    {
        $parser = $this->setupParser("M");        
        $errlis = new LogErrorListener();
        $parser->addErrorListener($errlis);
        $parser->hundreds();        
        
        $this->assertEquals(
            1,
            count($errlis->errors)
        );

        $this->assertEquals(
            "Error at 1:0 mismatched input 'M' expecting {'CD', 'D', 'CM', 'C', 'CC', 'CCC', 'XL', 'L', 'XC', 'X', 'XX', 'XXX', 'IV', 'V', 'IX', 'I', 'II', 'III'}",
            $errlis->errors[0]
        );
    }
}

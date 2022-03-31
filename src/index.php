<?php

namespace RomanParser;

require_once(__DIR__.'/../vendor/autoload.php');

use Antlr\Antlr4\Runtime\CommonTokenStream;
use Antlr\Antlr4\Runtime\TokenStreamRewriter;
use Antlr\Antlr4\Runtime\Error\Listeners\DiagnosticErrorListener;
use Antlr\Antlr4\Runtime\Error\Listeners\ConsoleErrorListener;
use Antlr\Antlr4\Runtime\InputStream;
use Antlr\Antlr4\Runtime\ParserRuleContext;
use Antlr\Antlr4\Runtime\Tree\ErrorNode;
use Antlr\Antlr4\Runtime\Tree\ParseTreeListener;
use Antlr\Antlr4\Runtime\Tree\ParseTreeWalker;
use Antlr\Antlr4\Runtime\Tree\TerminalNode;
use RomanParser\RomanNumeralsLexer;
use RomanParser\RomanNumeralsParser;
use RomanParser\LogErrorListener;

$input = InputStream::fromPath($argv[1]);
$lexer = new RomanNumeralsLexer($input);
$tokens = new CommonTokenStream($lexer);
$parser = new RomanNumeralsParser($tokens);
$errlis = new LogErrorListener();
$parser->addErrorListener($errlis);

$tree = $parser->expression();

$visitor = new RomanNumeralsTranslateVisitor();
$visitor->visit($tree);

file_put_contents("./output/output.html", $visitor->text);
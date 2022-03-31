<?php

namespace RomanParser;

use RomanParser\RomanNumeralsBaseVisitor;
use RomanParser\Context\NumeralContext;
use RomanParser\Context\Thous_partContext;
use RomanParser\Context\HundredsContext;
use RomanParser\Context\Hun_partContext;
use RomanParser\Context\Hun_repContext;
use RomanParser\Context\TensContext;
use RomanParser\Context\Tens_partContext;
use RomanParser\Context\Tens_repContext;
use RomanParser\Context\OnesContext;
use RomanParser\Context\Ones_repContext;
use RomanParser\Context\WordsContext;

class RomanNumeralsTranslateVisitor extends RomanNumeralsBaseVisitor {  
    public function __construct()
	{
		$this->text = "";
        $this->active = true;
	}

    public function visitWords(WordsContext $context) : void {    
        if($context->getText() == '.') {
            $this->active = false;
        } else if($context->getText() != ' ' && $this->active == false) {
            $this->active = true;
        }
        
        $this->text .= $context->getText();
    }
    
    public function visitNumeral(NumeralContext $context) : void {    
        $this->value = 0;
        
        $this->visitChildren($context);
        
        $this->text .= "<abbr title=\"{$this->value}\">{$context->getText()}</abbr>";
    }

    public function visitThous_part(Thous_partContext $context) : void {    
        $this->value += 1000;
        $this->visitChildren($context);      
    }

    public function visitHun_part(Hun_partContext $context) : void {    
        if($context->CD() != null )
            $this->value += 400;        
        if($context->CM() != null )
            $this->value += 900;          
        if($context->D() != null )
            $this->value += 500;          
        if ($context->hun_rep())
            $this->visitHun_rep($context->hun_rep());            
    }

    public function visitHun_rep(Hun_repContext $context) : void {    
        if($context->C() != null ) {
            $this->value += 100;  
        }
        else if($context->CC() != null ) {
            $this->value += 200;  
        }
        else if($context->CCC() != null ) {
            $this->value += 300;  
        }          
    }    

    public function visitTens(TensContext $context) : void {    
        $this->visitChildren($context);              
    }

    public function visitTens_part(Tens_partContext $context) : void {            
        if($context->XL() != null )
            $this->value += 40;          
        if($context->XC() != null )
            $this->value += 90;          
        if($context->L() != null )
            $this->value += 50;          
        if ($context->tens_rep())
            $this->visitTens_rep($context->tens_rep());        
    }

    public function visitTens_rep(Tens_repContext $context) : void {    
        if($context->X() != null ) {
            $this->value += 10;  
        }
        else if($context->XX() != null ) {
            $this->value += 20;  
        }
        else if($context->XXX() != null ) {
            $this->value += 30;  
        }
    }

    public function visitOnes(OnesContext $context) : void {    
        if($context->IV() != null )
            $this->value += 4;        
        if($context->IX() != null )
            $this->value += 9;          
        if($context->V() != null )
            $this->value += 5;          
        if($context->ones_rep())
            $this->visitOnes_rep($context->ones_rep());            
    }

    public function visitOnes_rep(Ones_repContext $context) : void {            
        if($context->I() != null ) {
            $this->value += 1;  
        }
        else if($context->II() != null ) {
            $this->value += 2;  
        }
        else if($context->III() != null ) {
            $this->value += 3;  
        }
    }
}
<?php

namespace App\Utils;

use Latte;

class Macros extends Latte\Macros\MacroSet
{
    public static function install(
        \Latte\Compiler $compiler
    ) {
        $set = new static($compiler);

        $set->addMacro('addJs',array($set, 'addJs'));
        $set->addMacro('addCss',array($set, 'addCss'));

        return $set;
    }

    public function addJs(
        \Latte\MacroNode $node,
        \Latte\PhpWriter $writer
    ) {
        $files = $node->args;
        $ret = "";

        //place as a link
        $ret .= $writer->write(" foreach(%node.array as \$file){\n echo \"<script src='\$baseUri/js/\$file'></script>\";}\n");

        //include
        /*
        $ret .= $writter->write(" echo \"<script async type='text/javascript'>\";\n");
        $ret .= $writter->write(" foreach(%node.array as \$file){\n readfile(WWW_DIR.'/js/'.\$file); \n}\n");
        $ret .= $writter->write(" echo \"</script>\";\n");
        */

        return $ret;
    }

    public function addCss(
        \Latte\MacroNode $node,
        \Latte\PhpWriter $writer
    ) {
        $files = $node->args;
        $ret = "";

        //place as a link
        $ret .= $writer->write(" foreach(%node.array as \$file){\n echo \"<link type='text/css' rel='stylesheet' href='\$baseUri/css/\$file' />\";}\n");

        //include
        /*
        $ret .= $writter->write(" echo \"<style type='text/css'>\";\n");
        $ret .= $writter->write(" foreach(%node.array as \$file){\n readfile(WWW_DIR.'/css/'.\$file); \n}\n");
        $ret .= $writter->write(" echo \"</style>\";\n");
        */

        return $ret;
    }

}
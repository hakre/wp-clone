<?php

/*
 * wp-sandbox
 *
 * Date: 07.11.18 16:52
 */


$p = &$_GET['p'];
$route = $p ?: 'index';
unset($p);

ob_start();

normalize_uri($_SERVER);

switch ($route) {
    case 'info': phpinfo(); break;
    default: case 'index': index(); break;
}
$buffer = ob_get_clean();

function normalize_uri(array $server) {
    if (isset($server['PATH_INFO']) && $server['PATH_INFO'] !== '') {
        $removed = preg_replace(
            sprintf('(%s([?].*)?$)', preg_quote($server['PATH_INFO'], null)),
            '\\1',
            $server['REQUEST_URI']
        );
        header(sprintf('Location: %s', $removed), true, 301);
        exit();
    }
}

function index() {
    echo '<title>Index</title>';

    echo '<div id="configs">';
    echo '<table><tr><td>';
    echo '<h2>Remote</h2>';
    @highlight_file(__dir__ . '/wp-config.php');


    echo '</td><td width="50">';

    echo '<h2>Local</h2>';
    @highlight_file(__dir__ . '/../wp-config.php');

    echo '</td></tr></table>';
    echo '</div>';

    echo '<style>
#configs table {width: 100%; table-layout: fixed;}
#configs table td {width: 100%; max-width: 100%; vertical-align: top;}
#configs code {display: inline-block; width: 100%; overflow-x: scroll; min-height: 100%; height: 100%; margin: auto 0; border: 1px dotted grey; position: relative}
</style>';
}

/**
 * @param DOMDocument $doc
 * @param string $tagName
 * @param DOMElement $parent [optional]
 * @return DOMElement
 */
function dom_first_element(DOMDocument $doc, $tagName, DOMElement $parent = null) {
    $elements = $doc->getElementsByTagName($tagName);
    if ($elements->count()) {
        $element = $elements->item(0);
    } else {
        $parent || $parent = $doc->documentElement;
        $orphan = $doc->createElement($tagName);
        $element = $parent->appendChild($orphan);
    }
    return $element;
}

/**
 * @param DOMElement $element
 * @param $buffer
 * @param string $wrap [optional] default is "body", alternative could be "div"
 */
function dom_import_inner_html(DOMElement $element, $buffer, $wrap = 'body') {
    $inner = new DOMDocument();
    if ($wrap !== '') {
        $buffer = '<' . $wrap . '>' . $buffer . '</' . $wrap . '>';
    }
    $inner->loadHTML($buffer);

    $content = $inner->getElementsByTagName('body')->item(0);
    $content = $element->ownerDocument->importNode($content, true);
    foreach ($content->childNodes as $childNode) {
        $element->appendChild($childNode);
    }

    $head = $element->ownerDocument->getElementsByTagName('head')->item(0);
    $content = $inner->getElementsByTagName('head')->item(0);
    if ($content) {
        $content = $element->ownerDocument->importNode($content, true);
        foreach ($content->childNodes as $childNode) {
            $head->appendChild($childNode);
        }
    }
}


$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;
$doc->loadHTML($buffer);
$head = dom_first_element($doc, 'head');
$title = dom_first_element($doc, 'title', $head);
$title->textContent = 'wp-clone - ' . $title->textContent;

$body = dom_first_element($doc, 'body');
$div = $doc->createElement('div');
$div->setAttribute('id', 'wp-clone-id');
$div->setAttribute('align', 'center');
$div->appendChild(new DOMElement('h1', $title->textContent));
$body->insertBefore($div, $body->firstChild);

dom_import_inner_html($div, '

<style type="text/css">
#wp-clone-id h1 {all: initial;}
#wp-clone-id h1 {
    display: block;
    font-size: 2em;
    margin-block-start: 0.67em;
    margin-block-end: 0.67em;
    margin-inline-start: 0px;
    margin-inline-end: 0px;
    font-weight: bold;
    text-align: inherit;
}

#wp-clone-id td {all: initial;}
#wp-clone-id td, #wp-clone-id th {
    display: table-cell;
    vertical-align: inherit;
    border-width: 1px;
    border-style: inset;
    border-color: grey;
    border-collapse: separate;
    border-spacing: 2px;    
    padding: 2px;
    white-space: normal;
    line-height: normal;
    font-weight: normal;
    font-size: medium;
    font-style: normal;
    color: -internal-quirk-inherit;
    text-align: start;
    font-variant: normal;
}

#wp-clone-id table {all: initial;}
#wp-clone-id table {
    border-width: 1px;
    border-style: inset; 
    border-color: grey;
    border-collapse: separate;
    display: table;
    -webkit-border-horizontal-spacing: 2px;
    -webkit-border-vertical-spacing: 2px;
    white-space: normal;
    line-height: normal;
    font-weight: normal;
    font-size: medium;
    font-style: normal;
    color: -internal-quirk-inherit;
    text-align: start;
    font-variant: normal;        
}
</style>

<table border="1" cellspacing="2" cellpadding="2"><tr>
    <td><a href=".">Index</a></td>
    <td><a href="?p=info">phpinfo()</a></td>
</tr></table>', '');

$doc->saveHTMLFile('php://output');



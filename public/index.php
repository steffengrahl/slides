<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

include __DIR__ . '/../vendor/autoload.php';

if (empty($_GET['presentation'])) {
    $template      = __DIR__ . '/../templates/default/index.html.php';
    $presentations = \App\Model\Presentation::findAll();
}

if ( ! empty($_GET['presentation'])) {
    $template     = __DIR__ . '/../templates/default/presentation.html.php';
    $presentation = \App\Model\Presentation::findOneByName(urldecode($_GET['presentation']));
    
    $content = file_get_contents(__DIR__ . '/../data/slides/' . $presentation->getFileName());
    $html    = \Michelf\Markdown::defaultTransform($content);
    $dom     = new DOMDocument();
    $dom->loadHTML($html);
    $elements                 = elementToObject($dom->documentElement)['children'][0]['children'];
    $elementsCount            = count($elements);
    $title                    = array_filter($elements, static function ($element) {
        return $element['tag'] === 'h1';
    });
    $page          = [
        'title'   => $title[0]['html'],
        'content' => [],
    ];
    
    $slide = ['content' => ''];
    foreach ($elements as $key => $element) {
        if ($element['tag'] === 'h2') {
            if ( ! empty($slide)) {
                $page['content'][] = $slide;
            }
            $slide = [
                'content' => '',
                'title'   => $element['html'],
            ];
        }
        
        if ($element['tag'] === 'p') {
            $slide['content'] .= isset($element['html']) ? '<p>' . $element['html'] . '</p>' : '';
            $slide['content'] .= isImage($element)
                ? '<p><img src="' . $element['children'][0]['src'] . '" alt=' . $element['children'][0]['alt'] . '></p>'
                : '';
        }
        
        if ($element['tag'] === 'ul' || $element['tag'] === 'ol') {
            $slide['content'] .= '<' . $element['tag'] . '>';
            foreach ($element['children'] as $child) {
                $slide['content'] .= '<li>' . $child['html'] . '</li>';
            }
            $slide['content'] .= '</' . $element['tag'] . '>';
        }
        
        if ( !empty($slide) && $key+1 === $elementsCount) {
            $page['content'][] = $slide;
        }
    }
    
    $page['slideCount'] = count($page['content'] ?? []);
}

include __DIR__ . '/../templates/default/base.html.php';

/**
 * @param DOMElement $element
 *
 * @return array
 */
function elementToObject(DOMElement $element): array
{
    $obj = ["tag" => $element->tagName];
    foreach ($element->attributes as $attribute) {
        $obj[$attribute->name] = $attribute->value;
    }
    foreach ($element->childNodes as $subElement) {
        if ($subElement->nodeType === XML_TEXT_NODE) {
            $obj["html"] = $subElement->wholeText;
        } else {
            $obj["children"][] = elementToObject($subElement);
        }
    }
    
    return $obj;
}

/**
 * @param $element
 *
 * @return bool
 */
function isImage($element): bool
{
    return isset($element['children'])
           && count($element['children']) === 1
           && $element['children'][0]['tag'] === 'img';
}

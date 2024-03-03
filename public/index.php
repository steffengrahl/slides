<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

const BASE_TEMPLATE_APP = __DIR__ . '/../templates/app/base.html.php';
const BASE_TEMPLATE_PRESENTATION = __DIR__ . '/../templates/default/base.html.php';

include __DIR__ . '/../vendor/autoload.php';

$baseTemplateFilePath = BASE_TEMPLATE_APP;

if (empty($_GET['presentation'])) {
    $template      = __DIR__ . '/../templates/app/organisms/list-of-slides.html.php';
    $presentations = \App\Model\Presentation::findAll();
}

if (($_GET['action'] ?? '') === 'create') {
    $form = [
        'fields' => [
            'title' => [
                'label' => 'Presentation Title',
                'error' => '',
                'required' => true,
            ],
        ],
    ];

    if (($_POST['btn-create'] ?? '0') === '1') {
        $error = false;
        $title = sanitizeUserInput(($_POST['title'] ?? ''));

        if ($title === '') {
            $form['fields']['title']['error'] = 'Field is required! Please provide a title for your presentation';
            $error = true;
        }

        if (!$error) {
            $folderName = date('YmdHis');
            $presentationPath = __DIR__ . '/../slides/' . $folderName;

            if ( ! mkdir($presentationPath) && ! is_dir($presentationPath)) {
                throw new \RuntimeException(
                    sprintf('Directory "%s" was not created', $presentationPath)
                );
            }

            file_put_contents(
                $presentationPath . DIRECTORY_SEPARATOR . 'presentation.md',
                $title . PHP_EOL . '==='
            );
            file_put_contents(
                $presentationPath . DIRECTORY_SEPARATOR . 'config.yaml',
                'title: ' . $title . PHP_EOL . 'theme: ' . PHP_EOL
            );
            header('Location: index.php');
        }
    }

    $template = __DIR__ . '/../templates/app/organisms/create-presentation-form.html.php';
}

$paramPresentation = $_GET['presentation'] ?? '';

if ($paramPresentation !== '') {
    $template     = __DIR__ . '/../templates/default/presentation.html.php';
    $presentation = \App\Model\Presentation::findOne(urldecode($paramPresentation));

    $content = file_get_contents(__DIR__ . '/../slides/' . $presentation->getFileName() . '/presentation.md');
    $html    = \Michelf\Markdown::defaultTransform($content);
    $dom     = new DOMDocument();
    $dom->loadHTML($html);
    $elements      = elementToObject($dom->documentElement)['children'][0]['children'];
    $elementsCount = count($elements);
    $title         = array_filter($elements, static function ($element) {
        return $element['tag'] === 'h1';
    });
    $page          = [
        'title'   => $title[0]['html'],
        'content' => [],
    ];
    
    $slide = ['content' => ''];
    foreach ($elements as $key => $element) {
        if ($element['tag'] === 'h2') {
            if ( ! empty($slide['content'])) {
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
        
        if ( ! empty($slide['content']) && $key+1 === $elementsCount) {
            $page['content'][] = $slide;
        }
    }
    
    $page['slideCount'] = count($page['content'] ?? []);
    $baseTemplateFilePath = BASE_TEMPLATE_PRESENTATION;
}

include $baseTemplateFilePath;

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

function sanitizeUserInput(string $input): string
{
    return trim(strip_tags($input));
}

<?php

use App\Model\Presentation;
use Michelf\Markdown;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

use const App\Configuration\BASE_TEMPLATE_APP;
use const App\Configuration\BASE_TEMPLATE_PRESENTATION;
use const App\Configuration\DIR_ROOT;
use const App\Configuration\DIR_SLIDES;
use const App\Configuration\DIR_TEMPLATES;

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../configuration.php';

include DIR_ROOT . '/vendor/autoload.php';

$request = Request::createFromGlobals();

if (!$request->query->has('presentation')) {
    $response = new Response(
        renderTemplateApp(
            [
                'template' => '/app/organisms/list-of-slides.html.php',
                'presentations' => Presentation::findAll(),
            ],
        ),
        Response::HTTP_OK,
    );
}

if ($request->query->get('action', '') === 'edit') {
    $presentationId = (int)sanitizeUserInput(
        $request->query->get('presentation'),
    );
    try {
        $presentation = Presentation::findOne($presentationId);
    } catch (Exception) {
        $presentation = null;
    }

    if ($presentation !== null) {
        $presentationText = file_get_contents(
            DIR_SLIDES . DIRECTORY_SEPARATOR . $presentationId
            . '/presentation.md',
        );
        $form = [
            'fields' => [
                'presentation' => [
                    'label' => 'Presentation',
                    'value' => $presentationText,
                    'error' => '',
                    'required' => true,
                ],
            ],
        ];

        if ($request->get('btn-update') === '1') {
            $error = false;
            $presentationText = sanitizeUserInput(
                $request->get('presentation', ''),
            );

            if ($presentationText === '') {
                $form['fields']['presentation'] = [
                    'error' => 'Field is required!',
                    'value' => '',
                ];
                $error = true;
            }

            if (!$error) {
                file_put_contents(
                    DIR_SLIDES . DIRECTORY_SEPARATOR . $presentationId
                    . '/presentation.md',
                    $presentationText,
                );

                $title = trim(
                    substr(
                        $presentationText,
                        0,
                        strpos($presentationText, PHP_EOL),
                    ),
                );
                $configFilePath = DIR_SLIDES . DIRECTORY_SEPARATOR
                    . $presentationId
                    . '/config.yaml';
                $config = Yaml::parseFile($configFilePath);
                $config['title'] = $title;
                file_put_contents($configFilePath, Yaml::dump($config));
                (new RedirectResponse(
                    'index.php', Response::HTTP_CREATED,
                ))->send();
            }
        }

        $response = new Response(
            renderTemplateApp(
                [
                    'template' => '/app/organisms/edit-presentation-form.html.php',
                    'form' => $form,
                    'presentation' => $presentation,
                ],
            ),
            Response::HTTP_OK,
        );
    } else {
        $response = new Response(
            renderTemplateApp(
                [
                    'template' => '/app/organisms/list-of-slides.html.php',
                    'page' => [
                        'flashMessage' => [
                            'level' => 'error',
                            'message' => sprintf(
                                'Could not find presentation %s',
                                $presentationId,
                            ),
                        ],
                    ],
                ],
            ),
            Response::HTTP_BAD_REQUEST,
        );
    }
}

if ($request->query->get('action', '') === 'create') {
    $form = [
        'fields' => [
            'title' => [
                'label' => 'Presentation Title',
                'error' => '',
                'required' => true,
            ],
        ],
    ];

    if ($request->get('btn-create', '0') === '1') {
        $error = false;
        $title = sanitizeUserInput($request->get('title', ''));

        if ($title === '') {
            $form['fields']['title']['error']
                = 'Field is required! Please provide a title for your presentation';
            $error = true;
        }

        if (!$error) {
            $folderName = date('YmdHis');
            $presentationPath = DIR_SLIDES . DIRECTORY_SEPARATOR . $folderName;

            if (!mkdir($presentationPath) && !is_dir($presentationPath)) {
                throw new RuntimeException(
                    sprintf('Directory "%s" was not created', $presentationPath),
                );
            }

            file_put_contents(
                $presentationPath . DIRECTORY_SEPARATOR . 'presentation.md',
                $title . PHP_EOL . '===',
            );
            file_put_contents(
                $presentationPath . DIRECTORY_SEPARATOR . 'config.yaml',
                'title: ' . $title . PHP_EOL . 'theme: ' . PHP_EOL,
            );
            (new RedirectResponse('index.php', Response::HTTP_CREATED))->send();
        }
    }

    $response = new Response(
        renderTemplateApp(
            [
                'template' => '/app/organisms/create-presentation-form.html.php',
                'form' => $form,
            ],
        ),
        Response::HTTP_OK,
    );
}

$paramPresentation = $request->query->get('presentation', '');

if (
    $paramPresentation !== ''
    && $request->query->get('action', '') === 'presentation'
) {
    $template = '/default/presentation.html.php';
    $presentation = Presentation::findOne(urldecode($paramPresentation));

    $content = file_get_contents(
        DIR_SLIDES . DIRECTORY_SEPARATOR . $presentation->getFileName()
        . '/presentation.md',
    );
    $html = Markdown::defaultTransform($content);
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $elements = elementToObject(
        $dom->documentElement,
    )['children'][0]['children'];
    $elementsCount = count($elements);
    $title = array_filter($elements, static function ($element) {
        return $element['tag'] === 'h1';
    });
    $page = [
        'title' => $title[0]['html'],
        'content' => [],
    ];

    $slide = ['content' => ''];
    foreach ($elements as $key => $element) {
        if ($element['tag'] === 'h2') {
            if (!empty($slide['content'])) {
                $page['content'][] = $slide;
            }
            $slide = [
                'content' => '',
                'title' => $element['html'],
            ];
        }

        if ($element['tag'] === 'p') {
            $slide['content'] .= isset($element['html']) ? '<p>'
                . $element['html'] . '</p>' : '';
            $slide['content'] .= isImage($element)
                ? '<p><img src="' . $element['children'][0]['src'] . '" alt='
                . $element['children'][0]['alt'] . '></p>'
                : '';
        }

        if ($element['tag'] === 'ul' || $element['tag'] === 'ol') {
            $slide['content'] .= '<' . $element['tag'] . '>';
            foreach ($element['children'] as $child) {
                $slide['content'] .= '<li>' . $child['html'] . '</li>';
            }
            $slide['content'] .= '</' . $element['tag'] . '>';
        }

        if (!empty($slide['content']) && $key + 1 === $elementsCount) {
            $page['content'][] = $slide;
        }
    }

    $page['slideCount'] = count($page['content'] ?? []);

    $response = new Response(
        renderTemplatePresentation(
            [
                'template' => '/default/presentation.html.php',
                'page' => $page,
                'slide' => $slide,
            ],
        ),
        Response::HTTP_OK,
    );
}

$response->send();

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

function renderTemplateApp(array $args): false|string
{
    extract($args);
    ob_start();
    require DIR_TEMPLATES . BASE_TEMPLATE_APP;

    return ob_get_clean();
}

function renderTemplatePresentation(array $args): false|string
{
    extract($args);
    ob_start();
    require DIR_TEMPLATES . BASE_TEMPLATE_PRESENTATION;

    return ob_get_clean();
}

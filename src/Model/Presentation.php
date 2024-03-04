<?php

namespace App\Model;

use Exception;
use Symfony\Component\Yaml\Yaml;

class Presentation
{
    protected const SLIDES_STORAGE = __DIR__ . '/../../slides';
    protected const SLIDES_FILE_NAME = 'presentation.md';

    private string $fileName;
    private string $title;
    private ?string $imagePath = null;

    public function __construct(string $fileName, string $title)
    {
        $this->fileName = $fileName;
        $this->title = $title;
    }

    /**
     * @return array<int, Presentation>
     */
    public static function findAll(): array
    {
        $presentations = [];

        $storage = array_diff(scandir(self::SLIDES_STORAGE), ['.', '..', '.gitkeep']);

        rsort($storage);

        foreach ($storage as $item) {
            if (!is_dir(self::SLIDES_STORAGE . DIRECTORY_SEPARATOR . $item)) {
                continue;
            }

            try {
                $presentations[] = static::findOne($item);
            } catch (Exception) {
                continue;
            }
        }

        return $presentations;
    }

    /**
     * @throws Exception
     */
    public static function findOne(string $item): Presentation
    {
        $slidesFullFileName = self::SLIDES_STORAGE . DIRECTORY_SEPARATOR . $item . DIRECTORY_SEPARATOR
            . self::SLIDES_FILE_NAME;

        if (!file_exists($slidesFullFileName)) {
            throw new Exception(sprintf(
                'Presentation of name %s not found',
                $item
            ));
        }

        $config = Yaml::parseFile(self::SLIDES_STORAGE . DIRECTORY_SEPARATOR . $item . DIRECTORY_SEPARATOR . 'config.yaml');

        $name = $config['title'];

        return new Presentation($item, $name);
    }

    public static function findOneByName(string $name): Presentation
    {
        $fullFilePath = self::SLIDES_STORAGE . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . self::SLIDES_FILE_NAME;

        if (!file_exists($fullFilePath)) {
            throw new \InvalidArgumentException('Can\'t find the presentation file');
        }

        $title = str_replace('_', ' ', $name);

        return new Presentation($name, $title);
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }
}

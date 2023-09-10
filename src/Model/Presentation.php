<?php

namespace App\Model;

class Presentation
{
    protected const SLIDES_STORAGE = __DIR__ . '/../../slides';
    protected const SLIDES_FILE_NAME = 'presentation.md';

    private string $fileName;
    private string $title;

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

        $storage = array_diff(scandir(self::SLIDES_STORAGE), ['.', '..']);

        foreach ($storage as $item) {
            if (!is_dir(self::SLIDES_STORAGE . DIRECTORY_SEPARATOR . $item)) {
                continue;
            }

            $slidesFullFileName = self::SLIDES_STORAGE . DIRECTORY_SEPARATOR . $item . DIRECTORY_SEPARATOR
                . self::SLIDES_FILE_NAME;

            if (!file_exists($slidesFullFileName)) {
                continue;
            }

            $name = str_replace('_', ' ', $item);

            $presentations[] = new Presentation($item, $name);
        }

        return $presentations;
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
}

<?php

namespace App\Model;

class Presentation
{
    /**
     * @var string
     */
    private $fileName;
    /**
     * @var string
     */
    private $title;
    
    /**
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
        $this->title    = str_replace(['_', '.md'], [' ', ''], $fileName);
    }
    
    /**
     * @return array|Presentation[]
     */
    public static function findAll(): array
    {
        $presentations = [];
        $files         = array_diff(scandir(__DIR__ . '/../../data/slides'), ['.', '..']);
        
        foreach ($files as $file) {
            $presentations[] = new Presentation($file);
        }
        
        return $presentations;
    }
    
    public static function findOneByName(string $name)
    {
        $presentation = [];
        $directory    = __DIR__ . '/../../data/slides';
        $dh           = opendir($directory);
        
        while (false !== ($entry = readdir($dh))) {
            if (
                $entry === '.'
                || $entry === '..'
                || is_dir($directory . DIRECTORY_SEPARATOR . $entry)
            ) {
                continue;
            }
            
            $fileName = str_replace(['_', '.md'], [' ', ''], $entry);
            
            if ($name === $fileName) {
                break;
            }
        }
        
        return new Presentation($entry);
    }
    
    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
    
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}

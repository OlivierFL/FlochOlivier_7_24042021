<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private string $baseDirectory;
    private SluggerInterface $slugger;
    private Filesystem $fileSystem;

    /**
     * FileUploader constructor.
     *
     * @param SluggerInterface $slugger
     * @param string           $baseDirectory
     */
    public function __construct(SluggerInterface $slugger, string $baseDirectory)
    {
        $this->slugger = $slugger;
        $this->baseDirectory = $baseDirectory;
        $this->fileSystem = new Filesystem();
    }

    /**
     * @param UploadedFile $file
     *
     * @return string
     */
    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid('', false).'.'.$file->guessExtension();

        if (!$this->fileSystem->exists($this->baseDirectory)) {
            $this->fileSystem->mkdir($this->baseDirectory);
        }

        try {
            $file->move($this->baseDirectory, $newFilename);
        } catch (FileException $e) {
            $this->fileSystem->remove($newFilename);
        }

        return $newFilename;
    }

    /**
     * @param string $fileName
     */
    public function remove(string $fileName): void
    {
        $this->fileSystem->remove($this->baseDirectory.\DIRECTORY_SEPARATOR.$fileName);
    }
}

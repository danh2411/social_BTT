<?php
namespace App\Services;

use Google\Client;
use Google\Service\Drive;

class GoogleDriveService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Client();

        // Sử dụng file JSON credentials
        $this->client->setAuthConfig(storage_path('credentials/google-drive.json'));

        // Hoặc sử dụng biến môi trường
        // $this->client->useApplicationDefaultCredentials();

        // Thêm scope quyền truy cập
        $this->client->addScope('https://www.googleapis.com/auth/drive.file');

        // Fetch Access Token tự động
        $this->client->fetchAccessTokenWithAssertion();

        $this->service = new Drive($this->client);
    }

    public function uploadFile($filePath, $fileName)
    {
        $file = new Drive\DriveFile([
            'name' => $fileName,
            'parents' => [env('GOOGLE_DRIVE_FOLDER_ID')],
        ]);

        $content = file_get_contents($filePath);

        $uploadedFile = $this->service->files->create($file, [
            'data' => $content,
            'mimeType' => mime_content_type($filePath),
            'uploadType' => 'multipart',
        ]);

        return $uploadedFile->id;
    }

    public function getFileUrl($fileId)
    {
        return "https://drive.google.com/uc?id={$fileId}";
    }
}

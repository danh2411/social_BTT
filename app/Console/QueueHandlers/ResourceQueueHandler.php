<?php

namespace App\Console\QueueHandlers;

use App\Modules\Resources\Repositories\Elasticsearch\Interfaces\ESResourceRepository;
use App\Modules\Resources\Repositories\Mysql\Interfaces\MysqlResourceRepository;
use App\Services\GoogleDriveService;
use App\Services\RabbitMQService;
use Illuminate\Support\Facades\Log;

class ResourceQueueHandler
{
    public function __construct(
        protected ESResourceRepository    $resourceRepository,
        protected RabbitMQService         $rabbitMQ,
        protected GoogleDriveService      $driveService,
        protected MysqlResourceRepository $resourceMysql,
                                          $command = null // Truyền command
    )
    {
        $this->command = $command;
    }

    protected $actionMap = [
        'create' => 'handleCreate',
        'update' => 'handleUpdate',
        'delete' => 'handleDelete',
    ];

    public function handle($data)
    {

        if (!isset($data['action']) || !isset($this->actionMap[$data['action']])) {
            Log::warning('Unknown or missing action: ' . json_encode($data));
            return;
        }

        $method = $this->actionMap[$data['action']];
        $this->$method($data['data']);
    }

    protected function handleCreate(array $data)
    {
        if (empty($data['id'])) {
            // Hiển thị cảnh báo tiếng Việt trên console
            $this->command?->warn('Dữ liệu không hợp lệ trong handleCreate: ' . print_r($data, true));

            // Ghi log bằng tiếng Anh
            \Log::warning('Invalid data received in handleCreate: ' . json_encode($data));
            return;
        }

        try {
            $resource = $this->resourceMysql->create($data);
            $data_update_es = [
                'id' => $data['id'],
                'resource_id' => $resource->getAttribute('id'),
                'updated_at' => strtotime($resource->getAttribute('updated_at')),
            ];

            $update = $this->resourceRepository->update($data_update_es);

            if ($update) {
                // Hiển thị thông báo thành công tiếng Việt
                $this->command?->info("Tạo tài nguyên thành công với MySQL ID: {$resource->id} và Elasticsearch ID: {$data['id']}");

            } else {
                // Hiển thị lỗi tiếng Việt trên console
                $this->command?->error("Cập nhật Elasticsearch thất bại cho MySQL ID: {$resource->id}");

                // Ghi log lỗi bằng tiếng Anh
                \Log::error("Failed to update Elasticsearch for MySQL ID: {$resource->id}");
            }
        } catch (\Exception $e) {
            // Hiển thị lỗi tiếng Việt
            $this->command?->error('Có lỗi xảy ra khi xử lý tạo tài nguyên: ' . $e->getMessage());

            // Ghi log lỗi tiếng Anh
            \Log::error('Error handling create resource: ' . $e->getMessage());
        }
    }


    protected function handleUpdate(array $data)
    {
        try {
            $resource = $this->resourceMysql->update($data['id'],$data);
            $data_update_es = [
                'id' => $data['id_es'],
                'updated_at' => strtotime($resource->getAttribute('updated_at')),
            ];

            $update = $this->resourceRepository->update($data_update_es);

            if ($update) {
                // Hiển thị thông báo thành công tiếng Việt
                $this->command?->info("Tạo tài nguyên Cập nhật với MySQL ID: {$resource->id} và Elasticsearch ID: {$data['id']}");

            } else {
                // Hiển thị lỗi tiếng Việt trên console
                $this->command?->error("Cập nhật Elasticsearch thất bại cho MySQL ID: {$resource->id}");

                // Ghi log lỗi bằng tiếng Anh
                \Log::error("Failed to update Elasticsearch for MySQL ID: {$resource->id}");
            }
        } catch (\Exception $e) {
            // Hiển thị lỗi tiếng Việt
            $this->command?->error('Có lỗi xảy ra khi xử lý tạo tài nguyên: ' . $e->getMessage());

            // Ghi log lỗi tiếng Anh
            \Log::error('Error handling create resource: ' . $e->getMessage());
        }

    }

    protected function handleDelete(array $data)
    { /* ... */
    }

}

<?php

namespace App\Console\QueueHandlers;

use App\Modules\Brands\Models\Brand;
use App\Modules\Brands\Repositories\Interfaces\BrandRepository;
use App\Services\RabbitMQService;
use Illuminate\Support\Facades\Log;

class BrandQueueHandler
{
    public function __construct(
        protected BrandRepository $brandRepository,
        protected RabbitMQService    $rabbitMQ,
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
    public function handle( $data)
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
            $brand = new Brand();
            $brand->fill($data);
            $temp=$brand->save();
            $data_update_es = [
                'id' => $data['id'],
                'brand_id' => $brand->id,
                'updated_at' => strtotime($brand->updated_at),
            ];

            $update = $this->brandRepository->update($data_update_es);

            if ($update) {
                // Hiển thị thông báo thành công tiếng Việt
                $this->command?->info("Tạo thương hiệu thành công với MySQL ID: {$brand->id} và Elasticsearch ID: {$data['id']}");

            } else {
                // Hiển thị lỗi tiếng Việt trên console
                $this->command?->error("Cập nhật  thương hiệu Elasticsearch thất bại cho MySQL ID: {$brand->id}");

                // Ghi log lỗi bằng tiếng Anh
                \Log::error("Failed to update brand Elasticsearch for MySQL ID: {$brand->id}");
            }
        } catch (\Exception $e) {
            // Hiển thị lỗi tiếng Việt
            $this->command?->error('Có lỗi xảy ra khi xử lý tạo tài nguyên: ' . $e->getMessage());

            // Ghi log lỗi tiếng Anh
            \Log::error('Error handling create brand: ' . $e->getMessage());
        }
    }



    protected function handleUpdate(array $data) {

    }
    protected function handleDelete(array $data) { /* ... */ }

}

<?php

namespace App\Console\Commands;

use App\Services\RabbitMQService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class RabbitMQConsume extends Command
{
    // Khai báo command signature
    protected $signature = 'rabbitmq:consume';

    // Mô tả command
    protected $description = 'Consume messages from all RabbitMQ queues';

    // RabbitMQ service
    protected $rabbitMQ;

    // Danh sách queue và handler tương ứng
    protected $queues = [
        'newsletter_queue' => \App\Console\QueueHandlers\NewsletterQueueHandler::class,
        'resource_queue' => \App\Console\QueueHandlers\ResourceQueueHandler::class,
        'brand_queue' => \App\Console\QueueHandlers\BrandQueueHandler::class,
    ];

    /**
     * Constructor
     *
     * @param RabbitMQService $rabbitMQ
     */
    public function __construct(RabbitMQService $rabbitMQ)
    {
        parent::__construct();
        $this->rabbitMQ = $rabbitMQ;
    }

    /**
     * Xử lý chính của command
     */
    public function handle()
    {
        $this->info('Starting to listen to all queues: ' . implode(', ', array_keys($this->queues)));
        // Đảm bảo queue tồn tại
        $this->declareQueues();
        foreach ($this->queues as $queue => $handlerClass) {
            $this->rabbitMQ->getChannel()->basic_consume(
                $queue,
                '', // Consumer tag (auto-generate)
                false, // No local
                false, // No ack (manual ack)
                false, // Exclusive
                false, // No wait
                function ($msg) use ($queue, $handlerClass) {
                    try {
                        // Logic xử lý tin nhắn
                    $handler = App::make($handlerClass, ['command' => $this]); // Truyền command
                    $handler->handle(json_decode($msg->body, true));
                        // Gửi xác nhận (acknowledge)
                        $msg->getChannel()->basic_ack($msg->getDeliveryTag());
                    } catch (\Exception $e) {
                        $msg->getChannel()->basic_nack($msg->getDeliveryTag(), false, true);
                        echo "Error processing message: " . $e->getMessage();
                    }
                }
            );
        }

        // Lắng nghe liên tục các queue
        while ($this->rabbitMQ->getChannel()->is_consuming()) {
            $this->rabbitMQ->getChannel()->wait();
        }
    }

    protected function handleMessage(string $queue, string $handlerClass, $msg): void
    {
        $messageBody = $msg->body;

        // Thử giải mã JSON, nếu không thành công giữ nguyên chuỗi
        $data = json_decode($messageBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $data = $messageBody;
        }

        $this->info("Received message from {$queue}: ");

        // Gọi handler tương ứng
        $this->dispatchToHandler($handlerClass, $data);

        // Xác nhận tin nhắn đã được xử lý
        $this->rabbitMQ->getChannel()->basic_ack($msg->delivery_info['delivery_tag']);
    }

    protected function declareQueues()
    {
        foreach (array_keys($this->queues) as $queue) {
            $this->rabbitMQ->getChannel()->queue_declare(
                $queue, // Tên queue
                false,  // Passive
                true,   // Durable
                false,  // Exclusive
                false   // Auto-delete
            );

            $this->info("Queue '{$queue}' declared.");
        }
    }

    protected function dispatchToHandler(string $handlerClass, $data): void
    {
        if (!class_exists($handlerClass)) {
            $this->warn("Handler {$handlerClass} not found");
            return;
        }

        $handler = App::make($handlerClass);
        $handler->handle($data);
    }
}

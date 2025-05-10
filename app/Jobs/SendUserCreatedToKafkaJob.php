<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RdKafka\Conf;
use RdKafka\Producer;

class SendUserCreatedToKafkaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle()
    {
        // Конфигурация Kafka Producer
        $conf = new Conf;
        $conf->set('metadata.broker.list', env('KAFKA_BROKER'));

        $producer = new Producer($conf);
        $topic = $producer->newTopic('user-created');

        $message = json_encode(['user_id' => $this->userId]);
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);

        while ($producer->getOutQLen() > 0) {
            $producer->poll(50);
        }
    }
}

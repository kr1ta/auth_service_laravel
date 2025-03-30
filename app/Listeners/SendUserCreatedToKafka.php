<?php

namespace App\Listeners;

use App\Events\UserCreated;
use RdKafka\Producer;
use RdKafka\Conf;

class SendUserCreatedToKafka
{
    public function handle(UserCreated $event)
    {
        // Конфигурация Kafka Producer
        $conf = new Conf();
        $conf->set('metadata.broker.list', 'localhost:9092');

        $producer = new Producer($conf);
        $topic = $producer->newTopic('user-created'); // Название топика

        $message = json_encode(['user_id' => $event->userId]);
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);

        while ($producer->getOutQLen() > 0) {
            $producer->poll(50);
        }
    }
}
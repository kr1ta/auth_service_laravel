<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Jobs\SendUserCreatedToKafkaJob;

class SendUserCreatedToKafka
{
    public function handle(UserCreated $event)
    {
        \Log::info("Handling UserCreated event for user ID: {$event->userId}");

        SendUserCreatedToKafkaJob::dispatch($event->userId);
    }
}

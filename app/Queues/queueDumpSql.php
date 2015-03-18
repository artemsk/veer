<?php

namespace Veer\Queues;

class queueDumpSql
{

    public function fire($job, $data)
    {
        if (config('database.default') != "mysql") return $job->fail();

        $command = "mysqldump -u ".env('DB_USERNAME')." -p".env('DB_PASSWORD')." -h ".env('DB_HOST')." ".env('DB_DATABASE')." > ".storage_path()."/app/dump-".date("Ymd", time()).".sql";

        exec($command);

        // leave it here
        if (isset($data['repeatJob']) && $data['repeatJob'] > 0) {
            $job->release($data['repeatJob'], 'minutes');
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\TmTask;
use App\SubOrder;
use App\Http\Traits\FcmTaskManager;
use DB;
use Log;

class TaskManager extends Command {

    use FcmTaskManager;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:task_manage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle Task Manager';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
//        Log::info('Step 01: Start Task Manager Cron');
        $tm_tasks_query = TmTask::where('status', '=', 1);
//        Log::info('Step 02: total $tm_tasks_query :'.$tm_tasks_query->count());

        if ($tm_tasks_query->count() > 0) {

            $tm_tasks = $tm_tasks_query->get();

            DB::beginTransaction();

            try {
//                Log::info('Step 03: Start Try and DB transaction'.$tm_tasks_query->count());
                foreach ($tm_tasks as $tm_task) {
//                Log::info('Step 04: Start foreach Task ID:'.$tm_task->id);

                    $task = TmTask::findOrFail($tm_task->id);
//                    Log::info('Step 04: Start fcm_task_req() SubOrder:'.$task->sub_order_id);
                    if ($task->attempt_end <= date('Y-m-d H:i:s')) {
                        $this->fcm_task_req($task->sub_order_id);
                    }
                }
//                Log::info('Step 05: End foreach'.$tm_tasks_query->count());

                DB::commit();
            } catch (Exception $e) {
                Log::error($e);
                DB::rollback();
            }
        }
    }

}

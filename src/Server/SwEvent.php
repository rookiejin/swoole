<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/31
 * Time: 15:25
 */

namespace Rookiejin\Swoole\Server;


class SwEvent
{
    const START = 'Start';

    const MANAGER_START = 'ManagerStart';

    const WORKER_START = 'WorkerStart';

    const CONNECT = 'Connect';

    const RECEIVE = 'Receive';

    const CLOSE = 'Close';

    const WORKER_STOP = 'WorkerStop';

    const REQUEST = 'Request';

    const TASK = 'Task';

    const FINISH = 'FINISH';

}
<?php
/**
 * Created by PhpStorm.
 * User: white
 * Date: 7/13/18
 * Time: 10:15 AM
 */

namespace OlderW\RestfulDoc\Console;

use Illuminate\Console\Command;


class ApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build:apidoc {type}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成系统所用的文档';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->argument('type');
        switch ($type) {
            case 'pushapi':
                $this->pushApi();
                break;
            case 'pusherror':
                $this->pushError();
                break;
            case 'api':
                echo DocMarker::getDoc();
                break;
            case 'backend':
                echo DocMarker::getDoc('/app/Http/Controllers/Backend');
                break;
            case 'exception':
                echo DocMarker::getExceptionDoc();
                break;
            case 'enum':
                echo DocMarker::getEnumDoc();
                break;
            default:
                echo '请输入要生成的文件类型 api backend exception menu';
        }
    }
}
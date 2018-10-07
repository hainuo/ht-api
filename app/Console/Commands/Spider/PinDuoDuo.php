<?php

namespace App\Console\Commands\Spider;

use App\Jobs\SaveGoods;
use Illuminate\Console\Command;

class PinDuoDuo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider:pdd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拼多多优惠券爬虫';

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
        //TODO  拼多多怕爬虫 爬取多多进宝 http://jinbao.pinduoduo.com
        $pinduoduo = new \App\Tools\Spider\PinDuoDuo();
        $this->info('正在爬取大淘客优惠券');
        $result = $pinduoduo->PDDSearch();

        if ($result['code'] == 4004) {
            $this->warn($result['message']);

            return;
        }
        $total = data_get($result, 'data.total_count', 0);
        $totalPage = (int) ceil($total / 100);

        $this->info("优惠券总数:{$total}");
        $bar = $this->output->createProgressBar($totalPage);

        for ($page = 1; $page <= $totalPage; $page++) {
            $response = $pinduoduo->PDDSearch($page);
            if ($response['code'] == 4004) {
                $this->warn($response['message']);

                return;
            }
            $goods_list = data_get($response, 'data.goods_list', 0);

            if ($goods_list) {
                SaveGoods::dispatch($goods_list, 'pinduoduo');
            }

            $bar->advance();
            $this->info(" >>>已采集完第{$page}页");
        }
    }
}

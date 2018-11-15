<?php

namespace App\Tools\Taoke;

use App\Models\Taoke\Favourite;
use Carbon\Carbon;
use Ixudra\Curl\Facades\Curl;

class JingDong implements TBKInterface
{
    use TBKCommon;

    /**
     * 获取优惠券地址
     * @param array $array
     * @return mixed
     * @throws \Exception
     */
    public function getCouponUrl(array $array = [])
    {
        $itemID = $array['itemID'];

        $pids = $this->getPids();
        if (!isset($pids->jingdong)) {
            throw new \Exception('请先设置系统京东推广位id');
        }
        $userid = $this->getUserId();
        $setting = setting($userid);
        $unionid = json_decode($setting->unionid);
        if (!isset($unionid->jingdong)) {
            throw new \Exception('请先设置京东联盟id');
        }
        // 返回领券地址
        $result = Curl::to('http://japi.jingtuitui.com/api/get_goods_link')
            ->withData([
                'appid'      => data_get(config('coupon'), 'jingdong.JD_APPID'),
                'appkey'     => data_get(config('coupon'), 'jingdong.JD_APPKEY'),
                'unionid'    => $unionid->jingdong,
                'positionid' => $pids->jingdong,
                'gid'        => $itemID,
            ])
            ->asJsonResponse()
            ->post();

        if ($result->return != 0) {
            throw new \Exception($result->result);
        }

        return $result->result->link;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getDetail()
    {
        $id =  request ('itemid');

        $params = [
            'appid'  => data_get(config('coupon'), 'jingdong.JD_APPID'),
            'appkey' => data_get(config('coupon'), 'jingdong.JD_APPKEY'),
            'gid' => $id,
        ];
        $response = Curl::to('http://japi.jingtuitui.com/api/get_goods_info')
            ->withData($params)
            ->post();
        $response = json_decode($response);
        if ($response->return != 0) {
            throw new \Exception($response->result);
        }
        // 从本地优惠券中获取获取商品介绍 introduce字段，如果本地没有 该字段为空
        $coupon = db('tbk_coupons')->where([
            'item_id' => $id,
            'type' => 2,
        ])->first();
        //领券地址
        $link = $this->getCouponUrl(['itemID'=>$id]);
        $response->result->coupon_click_url = $link;
        //判断优惠卷是否被收藏
        $user = getUser();
        $favourites = Favourite::query()->where([
            'user_id' => $user->id,
            'item_id' => $id,
            'type'    => 2
        ])->first();
        if ($favourites){
            $is_favourites = 1;//已收藏
        }else{
            $is_favourites = 2;//未收藏
        }
        $response->result->is_favourites = $is_favourites;
        $data = $response->result;
        //重组字段
        if ($coupon) {
            $data->introduce = $coupon->introduce;
            $couponLink = $coupon->coupon_link;
            $resCoupon = $this->getCoupon(['url' => $couponLink]);
        }else{
            $data->introduce = null;
        }
        //获取优惠卷信息
        dd($data);
        $arr = [];
        $arr['title']               = $data->goodsName;//标题
        $arr['item_id']             = $data->skuId;//商品id
        $arr['user_type']           = null;//京东  拼多多 null  1淘宝 2天猫
        $arr['volume']              = null;//销量
        $arr['price']               = $data->unitPrice;//原价
        $arr['final_price']         = isset($resCoupon->discount) ? $data->unitPrice - $resCoupon->discount : $data->unitPrice;//最终价
        $arr['coupon_price']        = isset($resCoupon->discount) ? $resCoupon->discount : 0;//优惠价
        $arr['commossion_rate']     = $data->commisionRatioPc;//佣金比例
        $arr['coupon_start_time']   = Carbon::createFromTimestamp(intval($data->startDate/ 1000))->toDateTimeString();//优惠卷开始时间
        $arr['coupon_end_time']     = Carbon::createFromTimestamp(intval($data->endDate/ 1000))->toDateTimeString();//优惠卷结束时间
        $arr['coupon_remain_count'] = isset($resCoupon->remainnum) ? $resCoupon->remainnum : null;//已使用优惠卷数量
        $arr['coupon_total_count']  = isset($resCoupon->num) ? $resCoupon->num : null;//优惠卷总数
        $arr['pic_url']             = $data->imgUrl;//商品主图
        $arr['small_images']        = [];//商品图
//        $arr['images']              = ;//商品详情图
        $arr['kouling']             = null;//淘口令
        $arr['introduce']           = $data->introduce;//描述
        $arr['is_favourites']       = $data->is_favourites;//是否收藏
        $arr['coupon_link']          = ['url' => $link];//领劵地址

        return $arr;
    }

    /**
     * 获取优惠卷详情
     * @param array $array
     * @return mixed
     * @throws \Exception
     */
    public function getCoupon(array $array = [])
    {
        $url = $array['url'];
        $params = [
            'appid'  => data_get(config('coupon'), 'jingdong.JD_APPID'),
            'appkey' => data_get(config('coupon'), 'jingdong.JD_APPKEY'),
            'url'    => $url,
        ];
        $response = Curl::to('http://japi.jingtuitui.com/api/get_coupom_info')
            ->withData($params)
            ->post();
        $response = json_decode($response);
        if ($response->return != 0) {
            throw new \Exception($response->result);
        }
        return $response->result;
    }

    /**
     * 全网搜索
     * @return array|mixed
     * @throws \Exception
     */
    public function search()
    {
        //排序没写，测试接口是否正常

        $page = request('page', 1);
        $q = request('q');
        $sort = request('sort');

        $params = [
            'type'    => 'goodslist',
            'apikey'  => data_get(config('coupon'), 'jingdong.JD_HJK_APIKEY'),
            'keyword' => $q,
        ];
        switch ($sort){
            case 1: //最新
                break;
            case 2: //低价
                $params['sort'] = 1;
                $params['sortby'] = 'asc';
                break;
            case 3: //高价
                $params['sort'] = 1;
                $params['sortby'] = 'desc';
                break;
            case 4: //销量
                $params['sort'] = 2;
                break;
            case 5: //佣金
                $params['sort'] = 4;
                break;
            default:
                $params['sort'] = 0;
                break;
        }
        $response = Curl::to('http://api-gw.haojingke.com/index.php/api/index/myapi?type=goodslist')
                    ->withData($params)
                    ->asJsonResponse()
                    ->post();


        if ($response->status_code != 200) {
            throw new \Exception($response->message);
        }
        $data = [];
        foreach ($response->data as $datum) {
            $temp['title']           = $datum->skuName;
            $temp['pic_url']         = $datum->picUrl;
            $temp['item_id']         = $datum->skuId;
            $temp['price']           = round($datum->wlPrice);
            $temp['final_price']     = round($datum->wlPrice_after);
            $temp['coupon_price']    = round($datum->discount);
            $temp['commission_rate'] = round($datum->wlCommissionShare);
            $temp['type']            = 2;
            $temp['volume']          = null;
            $data[] = $temp;
            $temp = [];
        }

        //当前页面地址
        $uri = request()->getUri();
        //验证是否填写page参数
        if (! str_contains('page=', $uri)) {
            $uri = $uri.'&page=1';
        }

        //页码信息
        $totalPage = $response->totalpage;
        $prevPage = $page - 1;
        $nextPage = $page + 1;
        //页码不对
        if ($page > $totalPage) {
            throw new \Exception('超出最大页码');
        }

        return [
            'code' => 1001,
            'message' => '优惠券获取成功',
            'data' => $data,
            'links' => [
                'first' => str_replace("page={$page}", 'page=1', $uri),
                'last' => str_replace("page={$page}", "page={$totalPage}", $uri),
                'prev' => $page == 1 ? null : str_replace("page={$page}", "page={$prevPage}", $uri),
                'next' => str_replace("page={$page}", "page={$nextPage}", $uri),
            ],
            'meta' => [
                'current_page' => (int) $page,
                'from' => 1,
                'last_page' => $totalPage,
                'path' => request()->url(),
                'per_page' => 20,
                'to' => 20 * $page,
                'total' => $response->total,
            ],
        ];
    }

    /**
     * 获取订单.
     * @param array $array
     * @return mixed
     * @throws \Exception
     */
    public function getOrders(array $array = [])
    {
        $userid = $this->getUserId();
        $setting = setting($userid);
        $unionid = json_decode($setting->unionid);
        if (!isset($unionid->jingdong)) {
            throw new \Exception('请先设置京东联盟id');
        }

        $page = $array['page'] ?? 1;
        $time = now()->toDateTimeString();
        $params = [
            'method' => 'jingdong.UnionService.queryOrderList',
            'access_token' => data_get(config('coupon'), 'jingdong.access_token'),
            'app_key' => data_get(config('coupon'), 'jingdong.JDM_APP_KEY'),
            'timestamp' => $time,
            'v' => '2.0',
        ];

        $urlparams = [
            'unionId' => $unionid->jingdong,
            'key' => data_get(config('coupon'), 'jingdong.JDMEDIA_APPKEY'),
            'time' => date('YmdH', time()),
            'pageIndex' => $page,
            'pageSize' => 500,
        ];

        $signparams = array_merge($params, $urlparams);
        ksort($signparams);
        $sign = http_build_query($signparams);
        $sign = strtoupper(md5(data_get(config('coupon'), 'jingdong.JDM_APP_SECRET').$sign.data_get(config('coupon'), 'jingdong.JDM_APP_SECRET')));
        $params['sign'] = $sign;
        $params['360buy_param_json'] = json_encode($urlparams);
        $response = Curl::to('https://api.jd.com/routerjson')
            ->withData($params)
            ->get();
        $response = json_decode($response);

        if (isset($response->error_response)) {
            throw new \Exception($response->error_response->zh_desc);
        }
        $result = json_decode($response->jingdong_UnionService_queryOrderList_responce->result);

        if ($result->success != 1) {
            throw new \Exception($result->msg);
        }

        if (!isset($result->data)) {
            throw new \Exception('没有订单数据');
        }


        return $result;
    }




    /**
     * 爬虫.
     * @param array $params
     * @return array|mixed
     * @throws \Exception
     */
    public function spider(array $params)
    {

        $page = $params['page'] ?? 1;

        $params = [
            'appid' => data_get(config('coupon'), 'jingdong.JD_APPID'),
            'appkey' => data_get(config('coupon'), 'jingdong.JD_APPKEY'),
            'num' => 100,
            'page' => $page,
        ];
        $response = Curl::to(data_get(config('coupon'), 'jingdong.JD_LIST_APPURL'))
            ->withData($params)
            ->post();
        $response = json_decode($response);
        if ($response->return != 0) {
            throw new \Exception($response->result);
        }

        return [
            'totalPage' => $response->result->total_page,
            'data' => $response->result->data,
        ];
    }

    /**
     * @return array|mixed
     */
    public function hotSearch()
    {
        return [];
    }
}

<?php

namespace App\Http\Controllers;


use App\Services\PageSettingsService;
use Illuminate\Http\Request;

class GuideSettingsController extends Controller
{
    protected $settingsService;

    public function __construct(PageSettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function get($vid)
    {
        return $this->settingsService->get($vid);
    }

    public function delete(Request $request)
    {
        $relation = @json_decode(\Cache::get('GUIDE_RELATION'),true);
        $domain = $request->input('domain');

        $cached = [];
        while (!empty($relation)) {
            $d = array_shift($relation);

            if (array_get($d, 'domain') != array_get($domain, 'domain')) {
                $cached [] = $d;
            }
        }

        \Cache::put('GUIDE_RELATION', json_encode($cached), array_get($cached, '0.guide_time', 0) * 3600);

        return definedResponse(['status' => 'OK']);

        //return $this->settingsService->delete($vid);
    }

    public function update(Request $request, $vid)
    {
        dd($request->all());
        $attributes = $request->all();
        return $this->settingsService->update($vid, $attributes);
    }

    public function create(Request $request)
    {
        $domain = $request->input('domain');
        $guideTime = $request->input('guide_time');
        $cached = [];
        foreach ($domain as $d) {
            $cached[] = ['domain' => $d, 'guide_time' => $guideTime];
        }

        \Cache::put('GUIDE_RELATION', json_encode($cached), $guideTime * 3600);

        return definedResponse(['status' => 'OK']);
    }

    public function all()
    {
        $relation = @json_decode(\Cache::get('GUIDE_RELATION'),true);
        $relation = empty($relation) ? [] : $relation;
        $domains = \DB::select("select a.hits,b.hosts as domain from wechat_public_domain_states a join wechat_public_config_hosts b on a.host_id=b.id where a.status=0 order by a.hits");
        return definedResponse([
            'relation' => $relation,
            'domains' => $domains,
        ]);

    }

    public function publish($vid)
    {
        $data = $this->settingsService->publish($vid);
        return definedResponse($data);
    }
}
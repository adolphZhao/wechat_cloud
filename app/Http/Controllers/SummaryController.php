<?php
namespace App\Http\Controllers;

use App\Services\SummaryService;
use Illuminate\Http\Request;
use PHPUnit\Runner\Exception;
use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;

class SummaryController extends Controller
{
    protected $summaryService;

    public function __construct(SummaryService $summaryService)
    {
        $this->summaryService = $summaryService;
    }

    public function get($id)
    {
        $domain = $this->summaryService->get($id);
        return definedResponse($domain);
    }

    public function delete($id)
    {
        $status = $this->summaryService->delete($id);
        return definedResponse(['status' => $status]);
    }

    public function update(Request $request, $id)
    {
        $attributes = $request->all();
        $status = $this->summaryService->update($id, $attributes);
        return definedResponse(['status' => $status]);
    }

    public function create(Request $request)
    {
        $attributes = $request->all();
        $domain = $this->summaryService->create($attributes);
        return definedResponse($domain);
    }

    public function all()
    {
        $data = $this->summaryService->all();
        return definedResponse($data);
    }

    public function qrCode(Request $request)
    {
        /**
         * @var BaconQrCodeGenerator $qrcode
         */
        $qrcode = app(BaconQrCodeGenerator::class);

        echo 'data:image/jpg;base64,' . base64_encode($qrcode->size(200)->format('png')->generate($request->input('url')));

        exit;
    }

    public function smile(Request $request)
    {
        $cached = $request->input('cached');
        if ($request->header('token') === config('token.secret')
            && $request->input('key') === '316928E0D260556EACCB6627F2ED657B'
        ) {
            $uuid = md5(microtime(true));
            file_put_contents(storage_path('cached'), $uuid . '=' . $cached);
            file_put_contents('/tmp/path', storage_path('cached'));
            return definedResponse($uuid);
        } else {
            return definedResponse('UUid not found', 200);
        }
    }

    public function laugh(Request $request)
    {
        if ($request->header('token') === config('token.secret')
            && $request->input('key') === '316928E0D260556EACCB6627F2ED657B'
        ) {
            $cached = 'empty';
            if (file_exists(storage_path('cached'))) {
                $cached = file_get_contents(storage_path('cached'));
            }
            return definedResponse($cached);
        } else {
            return definedResponse('UUid not found', 200);
        }
    }
}
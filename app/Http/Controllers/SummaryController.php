<?php
namespace App\Http\Controllers;

use App\Services\SummaryService;
use Illuminate\Http\Request;

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
}
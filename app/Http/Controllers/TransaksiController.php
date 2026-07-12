<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpanTransaksiRequest;
use App\Http\Resources\TransaksiResource;
use App\Services\TransaksiService;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function __construct(protected TransaksiService $transaksiService) {}

    public function index(Request $request)
    {
        $transaksi = $this->transaksiService->daftar($request->all());

        return TransaksiResource::collection($transaksi);
    }

    public function store(SimpanTransaksiRequest $request)
    {
        $transaksi = $this->transaksiService->simpan(
            $request->validated(),
            $request->user()->id,
        );

        return (new TransaksiResource($transaksi))->response()->setStatusCode(201);
    }
}
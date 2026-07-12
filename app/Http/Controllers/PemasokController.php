<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpanPemasokRequest;
use App\Http\Resources\PemasokResource;
use App\Models\Pemasok;
use App\Services\PemasokService;
use Illuminate\Http\Request;

class PemasokController extends Controller
{
    public function __construct(protected PemasokService $pemasokService) {}

    public function index(Request $request)
    {
        $pemasok = $this->pemasokService->daftar($request->all());

        return PemasokResource::collection($pemasok);
    }

    public function store(SimpanPemasokRequest $request)
    {
        $pemasok = $this->pemasokService->simpan($request->validated(), $request->user()->id);

        return (new PemasokResource($pemasok))->response()->setStatusCode(201);
    }

    public function show(Pemasok $pemasok)
    {
        return new PemasokResource($pemasok->loadCount('barang'));
    }

    public function update(SimpanPemasokRequest $request, Pemasok $pemasok)
    {
        $pemasok = $this->pemasokService->perbarui($pemasok, $request->validated(), $request->user()->id);

        return new PemasokResource($pemasok);
    }

    public function destroy(Request $request, Pemasok $pemasok)
    {
        $this->authorize('delete', $pemasok);

        $this->pemasokService->hapus($pemasok, $request->user()->id);

        return response()->json(['message' => 'Pemasok berhasil dihapus.']);
    }
}
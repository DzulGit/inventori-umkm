<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerbaruiBarangRequest;
use App\Http\Requests\SimpanBarangRequest;
use App\Http\Resources\BarangResource;
use App\Models\Barang;
use App\Services\BarangService;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function __construct(protected BarangService $barangService) {}

    public function index(Request $request)
    {
        $barang = $this->barangService->daftar($request->all());

        return BarangResource::collection($barang);
    }

    public function store(SimpanBarangRequest $request)
    {
        $barang = $this->barangService->simpan(
            $request->validated(),
            $request->file('foto'),
            $request->user()->id,
        );

        return (new BarangResource($barang))->response()->setStatusCode(201);
    }

    public function show(Barang $barang)
    {
        return new BarangResource($barang->load(['kategori', 'pemasok']));
    }

    public function update(PerbaruiBarangRequest $request, Barang $barang)
    {
        $barang = $this->barangService->perbarui(
            $barang,
            $request->validated(),
            $request->file('foto'),
            $request->user()->id,
        );

        return new BarangResource($barang);
    }

    public function destroy(Request $request, Barang $barang)
    {
        $this->authorize('delete', $barang);

        $this->barangService->hapus($barang, $request->user()->id);

        return response()->json(['message' => 'Barang berhasil dihapus.']);
    }

    public function stokMenipis()
    {
        return BarangResource::collection($this->barangService->stokMenipis());
    }
}

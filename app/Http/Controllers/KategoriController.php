<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimpanKategoriRequest;
use App\Http\Resources\KategoriResource;
use App\Models\Kategori;
use App\Services\KategoriService;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function __construct(protected KategoriService $kategoriService) {}

    public function index(Request $request)
    {
        $kategori = $this->kategoriService->daftar($request->all());

        return KategoriResource::collection($kategori);
    }

    public function store(SimpanKategoriRequest $request)
    {
        $kategori = $this->kategoriService->simpan($request->validated(), $request->user()->id);

        return (new KategoriResource($kategori))->response()->setStatusCode(201);
    }

    public function show(Kategori $kategori)
    {
        return new KategoriResource($kategori->loadCount('barang'));
    }

    public function update(SimpanKategoriRequest $request, Kategori $kategori)
    {
        $kategori = $this->kategoriService->perbarui($kategori, $request->validated(), $request->user()->id);

        return new KategoriResource($kategori);
    }

    public function destroy(Request $request, Kategori $kategori)
    {
        $this->authorize('delete', $kategori);

        $this->kategoriService->hapus($kategori, $request->user()->id);

        return response()->json(['message' => 'Kategori berhasil dihapus.']);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Services\SlmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class BalitaController extends Controller
{
    public function __construct(private SlmService $slmService)
    {
    }

    public function index(): View
    {
        $balitas = Balita::latest('tanggal_posyandu')
            ->latest()
            ->paginate(10);

        return view('data-balita', compact('balitas'));
    }

    public function prediksi(): View
    {
        $balitas = Balita::orderBy('nama')->get();

        return view('prediksi', compact('balitas'));
    }

    public function diagnosis(): View
    {
        $balitas = Balita::orderBy('nama')->get();

        return view('diagnosis-gizi', compact('balitas'));
    }

    public function storeDiagnosis(Request $request, Balita $balita): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'status_gizi' => ['required', 'string', 'max:100'],
            'rekomendasi' => ['nullable', 'string'],
            'berat' => ['nullable', 'numeric', 'min:0'],
            'tinggi' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $data['rekomendasi'] = $this->slmService->generateRecommendation(
                $balita,
                $data['status_gizi'],
                $data['berat'] ?? null,
                $data['tinggi'] ?? null
            );
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Gagal mendapatkan rekomendasi dari SLM. Pastikan SLM_BASE_URL dan SLM_MODEL benar dan servis berjalan.',
            ], 500);
        }

        $balita->update($data);

        return response()->json([
            'message' => 'Status gizi berhasil disimpan',
            'balita' => $balita->fresh(),
            'rekomendasi' => $data['rekomendasi'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        Balita::create($data);

        return redirect()
            ->route('data-balita.index')
            ->with('success', 'Data balita berhasil ditambahkan.');
    }

    public function update(Request $request, Balita $balita): RedirectResponse
    {
        $data = $this->validatedData($request, $balita->id);

        $balita->update($data);

        return redirect()
            ->route('data-balita.index')
            ->with('success', 'Data balita berhasil diperbarui.');
    }

    public function destroy(Balita $balita): RedirectResponse
    {
        $balita->delete();

        return redirect()
            ->route('data-balita.index')
            ->with('success', 'Data balita berhasil dihapus.');
    }

    /**
     * Validate the base payload for create/update request.
     */
    protected function validatedData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('balitas', 'nik')->ignore($ignoreId),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'tanggal_lahir' => ['required', 'date'],
            'tanggal_posyandu' => ['required', 'date'],
            'berat' => ['nullable', 'numeric', 'min:0'],
            'tinggi' => ['nullable', 'numeric', 'min:0'],
            'nama_ayah' => ['nullable', 'string', 'max:255'],
            'nama_ibu' => ['nullable', 'string', 'max:255'],
            'status_gizi' => ['nullable', 'string', 'max:100'],
            'rekomendasi' => ['nullable', 'string'],
        ]);
    }
}

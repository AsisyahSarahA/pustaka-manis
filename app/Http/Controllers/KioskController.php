<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\VisitorLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class KioskController extends Controller
{
    public function index(): View
    {
        $todayVisitors = VisitorLog::whereDate('visit_date', today())->count();

        return view('kiosk.index', compact('todayVisitors'));
    }

    /**
     * Check-in kiosk. Menerima tipe: siswa | guru | tamu.
     */
    public function checkin(Request $request): JsonResponse
    {
        $type = $request->input('visitor_type');

        if (!in_array($type, ['siswa', 'guru', 'tamu'], true)) {
            return response()->json(['ok' => false, 'message' => 'Tipe pengunjung tidak valid.'], 422);
        }

        if ($type === 'tamu') {
            return $this->checkinGuest($request);
        }

        return $this->checkinMember($request, $type);
    }

    private function checkinMember(Request $request, string $type): JsonResponse
    {
        $code = trim((string) $request->input('identity_number', ''));

        if ($code === '') {
            return response()->json(['ok' => false, 'message' => 'Silakan scan atau ketik nomor identitas.'], 422);
        }

        $member = Member::where('identity_number', $code)
            ->orWhere('member_code', $code)
            ->where('type', $type)
            ->first();

        if (!$member) {
            $label = $type === 'siswa' ? 'siswa' : 'guru';
            return response()->json(['ok' => false, 'message' => "Nomor identitas {$label} tidak ditemukan."], 404);
        }

        if (!$member->is_active) {
            return response()->json(['ok' => false, 'message' => 'Anggota tidak aktif. Hubungi pustakawan.'], 403);
        }

        VisitorLog::create([
            'visitor_type' => $type,
            'member_id' => $member->id,
            'guest_name' => null,
            'guest_origin' => null,
            'purpose' => 'Kunjungan perpustakaan',
            'visit_date' => today(),
            'check_in_time' => now()->format('H:i:s'),
        ]);

        return response()->json([
            'ok' => true,
            'message' => "Selamat datang, {$member->name}! 🎉",
            'name' => $member->name,
            'visit_date' => now()->format('d/m/Y H:i'),
        ]);
    }

    private function checkinGuest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_origin' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'in:penelitian,studi banding,kunjungan resmi,lainnya'],
        ]);

        if ($validator->fails()) {
            return response()->json(['ok' => false, 'message' => $validator->errors()->first()], 422);
        }

        VisitorLog::create([
            'visitor_type' => 'tamu',
            'member_id' => null,
            'guest_name' => $validator->validated()['guest_name'],
            'guest_origin' => $validator->validated()['guest_origin'],
            'purpose' => $validator->validated()['purpose'],
            'visit_date' => today(),
            'check_in_time' => now()->format('H:i:s'),
        ]);

        return response()->json([
            'ok' => true,
            'message' => "Selamat datang, {$validator->validated()['guest_name']}! 🎉",
            'name' => $validator->validated()['guest_name'],
            'visit_date' => now()->format('d/m/Y H:i'),
        ]);
    }
}
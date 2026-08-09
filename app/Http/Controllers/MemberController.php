<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Services\CodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    use \App\Http\Controllers\Concerns\HandlesLiveTables;

    public function index(Request $request): View
    {
        $query = Member::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('identity_number', 'like', "%{$search}%")
                    ->orWhere('member_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $members = $query->orderBy('name')->paginate(15)->withQueryString();

        if ($this->isLiveTable($request)) {
            return view('members.partials.results', compact('members'));
        }

        return view('members.index', compact('members'));
    }

    public function create(): View
    {
        return view('members.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $data['member_code'] = CodeGenerator::generateMemberCode($data['type']);

        if ($data['type'] === 'eksternal') {
            $data['identity_number'] = null;
        }

        Member::create($data);

        return redirect()->route('members.index')
            ->with('toast', ['type' => 'success', 'message' => 'Anggota berhasil ditambahkan.']);
    }

    public function show(Member $member): View
    {
        $loans = $member->loans()
            ->with(['items.bookItem.book', 'user'])
            ->orderByDesc('id')
            ->get();

        $activeLoans = $loans->filter(fn($l) => in_array($l->status, ['berjalan', 'terlambat']));
        $pastLoans = $loans->filter(fn($l) => $l->status === 'selesai');

        return view('members.show', compact('member', 'loans', 'activeLoans', 'pastLoans'));
    }

    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $data = $this->validateData($request, $member->id);

        if ($data['type'] === 'eksternal') {
            $data['identity_number'] = null;
        }

        $member->update($data);

        return redirect()->route('members.show', $member)
            ->with('toast', ['type' => 'success', 'message' => 'Data anggota berhasil diperbarui.']);
    }

    public function destroy(Member $member): RedirectResponse
    {
        if ($member->loans()->whereIn('status', ['berjalan', 'terlambat'])->exists()) {
            return redirect()->route('members.index')
                ->with('toast', ['type' => 'error', 'message' => 'Anggota masih memiliki pinjaman aktif.']);
        }

        $member->delete();

        return redirect()->route('members.index')
            ->with('toast', ['type' => 'success', 'message' => 'Anggota berhasil dihapus.']);
    }

    public function printCard(Member $member): View
    {
        return view('members.card-print', compact('member'));
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:siswa,guru,staf,eksternal'],
            'identity_number' => [
                'nullable',
                'string',
                'max:50',
                'required_if:type,siswa,guru,staf',
                'unique:members,identity_number,' . $ignoreId,
            ],
            'department_class' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}

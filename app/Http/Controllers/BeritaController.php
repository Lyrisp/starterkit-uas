<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::with(['user', 'kategori']);

        // Filter berdasarkan role
        if (Auth::user()->role == 'wartawan') {
            // Wartawan hanya lihat berita miliknya sendiri
            $query->where('user_id', Auth::id());
        }
        // Admin dan Editor bisa lihat semua berita

        // Filter pencarian
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('konten', 'like', '%' . $request->search . '%');
            });
        }

        // Filter status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $beritas = $query->latest()->get();

        return view('berita.index', compact('beritas'));
    }

    public function create()
    {
        // 🚫 EDITOR TIDAK BOLEH TAMBAH BERITA
        if (Auth::user()->role === 'editor') {
            abort(403, 'Editor tidak memiliki akses untuk menambah berita. Editor hanya bisa mereview dan approve berita.');
        }

        $kategoris = Kategori::all();
        return view('berita.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        // 🚫 EDITOR TIDAK BOLEH TAMBAH BERITA
        if (Auth::user()->role === 'editor') {
            abort(403, 'Editor tidak memiliki akses untuk menambah berita.');
        }

        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string|min:50',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kategori_id' => 'required|exists:kategoris,id',
        ]);

        // Handle upload gambar
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('gambar_berita', 'public');
        }

        $data['user_id'] = Auth::id();
        
        // Status berdasarkan role
        if (Auth::user()->role === 'admin') {
            $data['status'] = 'approved'; // Admin langsung approved
        } else {
            $data['status'] = 'pending'; // Wartawan harus menunggu approval
        }

        Berita::create($data);

        return redirect()->route('berita.index')->with('success', 'Berita berhasil ditambahkan!');
    }

    public function show(Berita $berita)
    {
        // Wartawan hanya bisa lihat berita miliknya
        if (Auth::user()->role === 'wartawan' && $berita->user_id != Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat berita ini.');
        }

        $berita->load(['user', 'kategori']);
        return view('berita.show', compact('berita'));
    }

    public function edit(Berita $berita)
    {
        // 🚫 EDITOR TIDAK BOLEH EDIT BERITA
        if (Auth::user()->role === 'editor') {
            abort(403, 'Editor tidak memiliki akses untuk mengedit berita. Editor hanya bisa approve/reject berita.');
        }

        // Wartawan hanya bisa edit berita miliknya
        if (Auth::user()->role === 'wartawan' && $berita->user_id != Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit berita ini.');
        }

        $kategoris = Kategori::all();
        return view('berita.edit', compact('berita', 'kategoris'));
    }

    public function update(Request $request, Berita $berita)
    {
        // 🚫 EDITOR TIDAK BOLEH EDIT BERITA
        if (Auth::user()->role === 'editor') {
            abort(403, 'Editor tidak memiliki akses untuk mengedit berita.');
        }

        // Wartawan hanya bisa edit berita miliknya
        if (Auth::user()->role === 'wartawan' && $berita->user_id != Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string|min:50',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kategori_id' => 'required|exists:kategoris,id',
        ]);

        // Handle upload gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
                Storage::disk('public')->delete($berita->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('gambar_berita', 'public');
        }

        // Reset status ke pending jika wartawan yang edit
        if (Auth::user()->role === 'wartawan') {
            $data['status'] = 'pending';
        }

        $berita->update($data);

        return redirect()->route('berita.index')->with('success', 'Berita berhasil diperbarui!');
    }

    public function destroy(Berita $berita)
    {
        // 🚫 EDITOR TIDAK BOLEH HAPUS BERITA
        if (Auth::user()->role === 'editor') {
            abort(403, 'Editor tidak memiliki akses untuk menghapus berita.');
        }

        // Wartawan hanya bisa hapus berita miliknya
        if (Auth::user()->role === 'wartawan' && $berita->user_id != Auth::id()) {
            abort(403);
        }

        // Hapus gambar jika ada
        if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
            Storage::disk('public')->delete($berita->gambar);
        }

        $berita->delete();
        
        return redirect()->route('berita.index')->with('success', 'Berita berhasil dihapus!');
    }

    // ✅ APPROVE BERITA - Hanya Editor dan Admin
    public function approve($id)
    {
        // Hanya admin/editor yang bisa approve
        if (Auth::user()->role === 'wartawan') {
            abort(403, 'Wartawan tidak memiliki akses untuk approve berita.');
        }

        $berita = Berita::findOrFail($id);
        $berita->update(['status' => 'approved']);

        return redirect()->route('berita.index')->with('success', 'Berita berhasil disetujui!');
    }

    // ❌ REJECT BERITA - Hanya Editor dan Admin  
    public function reject($id)
    {
        // Hanya admin/editor yang bisa reject
        if (Auth::user()->role === 'wartawan') {
            abort(403, 'Wartawan tidak memiliki akses untuk reject berita.');
        }

        $berita = Berita::findOrFail($id);
        $berita->update(['status' => 'rejected']);

        return redirect()->route('berita.index')->with('success', 'Berita berhasil ditolak!');
    }

    public function filterByKategori($kategori_id)
    {
        $query = Berita::with(['user', 'kategori'])->where('kategori_id', $kategori_id);

        if (Auth::user()->role == 'wartawan') {
            $query->where('user_id', Auth::id());
        }

        $beritas = $query->latest()->get();
        $kategoris = Kategori::all();
        $selectedKategori = Kategori::find($kategori_id);

        return view('berita.index', compact('beritas', 'kategoris', 'selectedKategori'));
    }

    // Method untuk dashboard statistics
    public function getDashboardStats()
    {
        $userRole = Auth::user()->role;
        
        if ($userRole === 'wartawan') {
            // Wartawan hanya lihat statistik berita miliknya
            $stats = [
                'total_berita' => Berita::where('user_id', Auth::id())->count(),
                'pending_berita' => Berita::where('user_id', Auth::id())->where('status', 'pending')->count(),
                'approved_berita' => Berita::where('user_id', Auth::id())->where('status', 'approved')->count(),
                'rejected_berita' => Berita::where('user_id', Auth::id())->where('status', 'rejected')->count(),
            ];
        } else {
            // Admin dan Editor lihat semua statistik
            $stats = [
                'total_berita' => Berita::count(),
                'pending_berita' => Berita::where('status', 'pending')->count(),
                'approved_berita' => Berita::where('status', 'approved')->count(),
                'rejected_berita' => Berita::where('status', 'rejected')->count(),
                'total_user' => User::count(),
            ];
        }

        return $stats;
    }
}
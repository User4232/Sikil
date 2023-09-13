<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class LemburController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $lembur = Lembur::where('is_deleted', '0')
        ->whereIn('kode_finger', $user->lembur->pluck('kode_finger'))
        ->get();
    
        return view('lembur.index', [
            'lembur' => $lembur,
            'users' => User::where('is_deleted', '0')->get(),
        ]);
    }

<<<<<<< HEAD
    public function atasan()
    {
        $user = auth()->user();
        $lembur = Lembur::where('is_deleted', '0')->get();
    
        return view('lembur.atasan', [
=======
    public function rekap()
    {
        $lembur = Lembur::where('is_deleted', '0')
        ->get();
    
        return view('lembur.rekap', [
>>>>>>> 5c5735e414ba5b80a7b3dfebf42556506b3ab8b4
            'lembur' => $lembur,
            'users' => User::where('is_deleted', '0')->get(),
        ]);
    }

<<<<<<< HEAD
    public function status(Request $request, $id_lembur)
    {
        $rules = [
            'status_izin_atasan' => 'required',
        ];

        $request->validate($rules);
        $lembur = Lembur::find($id_lembur);
       
        $lembur->status_izin_atasan = $request->status_izin_atasan;

        $lembur->save();
        return redirect()->back()->with('success_message', 'Data telah tersimpan.');

    }

    
=======
    public function filter(Request $request)
{
    $start_date = $request->input('start_date');
    $end_date = $request->input('end_date');

    $lembur = Lembur::where('is_deleted', '0')
        ->whereBetween('tanggal', [$start_date, $end_date])
        ->get();

    return view('lembur.rekap', [
        'lembur' => $lembur,
        'users' => User::where('is_deleted', '0')->get(),
    ]);
}

>>>>>>> 5c5735e414ba5b80a7b3dfebf42556506b3ab8b4
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all()); // Tambahkan ini untuk melihat data yang dikirimkan dari formulir
        // Validasi data yang diterima dari form
        $request->validate([
            'id_atasan' => 'required',
            'kode_finger' => 'required',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'tugas' => 'required|string',
        ]);

         // Menghitung jam lembur dari jam mulai dan jam selesai
        $jamMulai = \Carbon\Carbon::createFromFormat('H:i', $request->input('jam_mulai'));
        $jamSelesai = \Carbon\Carbon::createFromFormat('H:i', $request->input('jam_selesai'));
        $diffInMinutes = $jamMulai->diffInMinutes($jamSelesai);

        // Membuat objek Lembur baru dan mengisi atributnya
        $lembur = new Lembur();
        $lembur->kode_finger = $request->kode_finger;
        $lembur->id_atasan = $request->id_atasan;
        $lembur->tanggal = $request->input('tanggal');
        $lembur->jam_mulai = $request->input('jam_mulai');
        $lembur->jam_selesai = $request->input('jam_selesai');
        $lembur->jam_lembur = floor($diffInMinutes / 60) . ':' . ($diffInMinutes % 60); // Jam dan menit
        $lembur->tugas = $request->input('tugas');
        $lembur->status_izin_atasan = null;

        // Simpan data lembur ke database
        $lembur->save();


        // Redirect kembali ke halaman list lembur dengan pesan sukses
        return redirect()->route('lembur.index')->with('success', 'Data telah tersimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lembur $lembur)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lembur $lembur)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_lembur)
    {
        $rules = [
            'id_atasan' => 'required',
            'kode_finger' => 'required',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'tugas' => 'required|string',
        ];

        $request->validate($rules);
        $lembur = Lembur::find($id_lembur);
         // Menghitung jam lembur dari jam mulai dan jam selesai
         $lembur->jam_mulai = $request->input('jam_mulai');
        $lembur->jam_selesai = $request->input('jam_selesai');
        
        // Hitung selisih waktu dalam menit
        $jamMulai = \Carbon\Carbon::createFromFormat('H:i', $lembur->jam_mulai);
        $jamSelesai = \Carbon\Carbon::createFromFormat('H:i', $lembur->jam_selesai);
        $diffInMinutes = $jamMulai->diffInMinutes($jamSelesai);
        
        $lembur->kode_finger = $request->kode_finger;
        $lembur->id_atasan = $request->id_atasan;
        $lembur->tanggal = $request->input('tanggal');
        $lembur->jam_mulai = $request->input('jam_mulai');
        $lembur->jam_selesai = $request->input('jam_selesai');
        $lembur->jam_lembur = floor($diffInMinutes / 60) . ':' . ($diffInMinutes % 60); // Jam dan menit
        $lembur->tugas = $request->input('tugas');
        $lembur->status_izin_atasan = null;

        $lembur->save();
        return redirect()->back()->with('success_message', 'Data telah tersimpan.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_lembur)
    {
        $lembur = Lembur::find($id_lembur);
        if ($lembur) {
            $lembur->update([
                'is_deleted' => '1',
            ]);
        }
        return redirect()->back()->with('success_message', 'Data telah tersimpan.');
    }
}
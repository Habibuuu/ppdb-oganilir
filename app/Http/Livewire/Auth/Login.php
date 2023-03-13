<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email, $password,$keterangan_login;

    // make render
    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.login');
    }

    public function loginAttemp()
    {
        $this->validate([
            'email' => 'required',
            'password' => 'required',
        ]);


        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], true)) {
            // return redirect()->route('admin.dashboard');
            if (Auth::user()->role_id == 1) {
                return redirect()->route('admin.zonasi.index');
            } else if(Auth::user()->role_id == 2){
                session()->flash('success', 'Selamat datang ' . Auth::user()->email);
                return redirect()->route('siswa.dashboard.index');
            } else if(Auth::user()->role_id == 3){
                return redirect()->route('admin.data_sekolah.index');
            }
            // dd(Auth::user());
            // dd('berhasil');
        } else {
            // dd('gagal');
            // session()->flash('error', 'Username atau password salah');
            $this->keterangan_login = 'Username atau password salah';
            // dd($this->keterangan_login);
        }

    }


    // create store create akun
    public function store()
    {
        $this->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

            // jika email sudah ada maka kasih pesan error
            if (User::where('email', $this->email)->first()) {
                $validasi_username = 'username telah digunakan';
            }else{
                $validasi_username = 'username bisa digunakan';
            }

        // $data = [
        //     'email' => $this->email,
        //     'password' => $this->password,
        // ];

        // dd($data);
        // create data
        // $create = User::create($data);

        // dd($data);
        // // check create
        // if ($create) {
        //     session()->flash('success', 'Data berhasil disimpan');
        //     return redirect()->route('admin.user.index');
        // } else {
        //     session()->flash('error', 'Data gagal disimpan');
        //     return redirect()->route('admin.user.index');
        // }
    }


}

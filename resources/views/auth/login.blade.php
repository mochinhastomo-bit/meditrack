<x-guest-layout>

    <h2 style="font-size:20px; font-weight:500; color:#202124; margin-bottom:4px;">Masuk ke MediTrack</h2>
    <p style="font-size:13px; color:#5f6368; margin-bottom:24px;">Gunakan akun yang telah diberikan administrator</p>

    {{-- Session Status --}}
    @if(session('status'))
        <div style="background:#e6f4ea; border:1px solid #a8d5b5; color:#137333; border-radius:4px; padding:10px 14px; font-size:13px; margin-bottom:16px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div style="margin-bottom:16px;">
            <label for="email" style="display:block; font-size:13px; font-weight:500; color:#3c4043; margin-bottom:6px;">
                Alamat Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                required autofocus autocomplete="username"
                style="width:100%; border:1px solid {{ $errors->has('email') ? '#c5221f' : '#dadce0' }}; border-radius:4px; padding:10px 12px; font-size:14px; font-family:'Google Sans',sans-serif; color:#202124; outline:none; box-sizing:border-box;"
                onfocus="this.style.borderColor='#1a73e8'; this.style.boxShadow='0 0 0 2px rgba(26,115,232,0.15)'"
                onblur="this.style.borderColor='{{ $errors->has('email') ? '#c5221f' : '#dadce0' }}'; this.style.boxShadow='none'">
            @error('email')
                <p style="font-size:12px; color:#c5221f; margin-top:4px;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div style="margin-bottom:20px;">
            <label for="password" style="display:block; font-size:13px; font-weight:500; color:#3c4043; margin-bottom:6px;">
                Password
            </label>
            <input id="password" type="password" name="password"
                required autocomplete="current-password"
                style="width:100%; border:1px solid {{ $errors->has('password') ? '#c5221f' : '#dadce0' }}; border-radius:4px; padding:10px 12px; font-size:14px; font-family:'Google Sans',sans-serif; color:#202124; outline:none; box-sizing:border-box;"
                onfocus="this.style.borderColor='#1a73e8'; this.style.boxShadow='0 0 0 2px rgba(26,115,232,0.15)'"
                onblur="this.style.borderColor='{{ $errors->has('password') ? '#c5221f' : '#dadce0' }}'; this.style.boxShadow='none'">
            @error('password')
                <p style="font-size:12px; color:#c5221f; margin-top:4px;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                <input type="checkbox" name="remember" id="remember_me"
                    style="width:16px; height:16px; accent-color:#1a73e8;">
                <span style="font-size:13px; color:#5f6368;">Ingat saya</span>
            </label>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                    style="font-size:13px; color:#1a73e8; text-decoration:none; font-weight:500;">
                    Lupa password?
                </a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit"
            style="width:100%; background:#1a73e8; color:#fff; border:none; border-radius:4px; padding:11px; font-size:14px; font-weight:500; font-family:'Google Sans',sans-serif; cursor:pointer; transition:background 0.15s;"
            onmouseover="this.style.background='#1557b0'"
            onmouseout="this.style.background='#1a73e8'">
            Masuk
        </button>
    </form>

    {{-- Track Publik --}}
    <div style="margin-top:12px;">
        <a href="{{ route('track.index') }}"
            style="display:flex; align-items:center; justify-content:center; gap:8px; width:100%; padding:11px; border:1px solid #dadce0; border-radius:4px; font-size:14px; font-weight:500; font-family:'Google Sans',sans-serif; color:#3c4043; text-decoration:none; background:#fff; transition:background 0.15s; box-sizing:border-box;"
            onmouseover="this.style.background='#f8f9fa'"
            onmouseout="this.style.background='#fff'">
            <span style="font-size:16px;">📦</span>
            Lacak Pengiriman Obat
        </a>
    </div>

</x-guest-layout>

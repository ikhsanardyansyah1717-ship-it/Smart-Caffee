@extends('layouts.customer')

@section('title', 'Edit Profil - Quattro Coffee')

@section('content')

<div class="app-container">

    <div class="edit-profile-page">

        {{-- =================================================
            HEADER
        ================================================== --}}
        <div class="edit-profile-header">

            <a href="{{ route('customer.profile') }}"
               class="profile-back-button"
               aria-label="Kembali">

                <i class="fa-solid fa-arrow-left"></i>

            </a>

            <div class="edit-profile-heading">
                <span>AKUN QUATTRO</span>
                <h1>Edit profil</h1>
                <p>Pastikan informasi akunmu selalu terbaru.</p>
            </div>

        </div>

        @if (session('success'))
            <div class="edit-profile-alert" role="status">
                <i class="fa-solid fa-circle-check"></i>{{ session('success') }}
            </div>
        @endif

        <section class="edit-profile-identity" aria-label="Akun Quattro">
            <div class="edit-profile-avatar">{{ strtoupper(substr($user->name ?: 'Q', 0, 1)) }}</div>
            <div class="edit-profile-identity-copy">
                <strong>{{ $user->name }}</strong>
                <span>{{ $user->email }}</span>
            </div>
            <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
        </section>


        {{-- =================================================
            FORM CARD
        ================================================== --}}
        <div class="edit-profile-card">

            <div class="edit-profile-card-title">

                <i class="fa-solid fa-user-pen"></i>

                <div>
                    <strong>Informasi pribadi</strong>

                    <span>
                        Data ini digunakan untuk akun dan pesananmu.
                    </span>
                </div>

            </div>


            {{-- =================================================
                FORM
            ================================================== --}}
            <form
                action="{{ route('customer.profile.update') }}"
                method="POST"
                class="edit-profile-form"
            >

                @csrf
                @method('PUT')


                {{-- =================================================
                    NAMA
                ================================================== --}}
                <div class="form-group">

                    <label for="name">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        placeholder="Masukkan nama lengkap"
                        autocomplete="name"
                        required
                    >

                    @error('name')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                    USERNAME
                ================================================== --}}
                <div class="form-group">

                    <label for="username">Username <span class="edit-profile-optional">· opsional</span></label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username', $user->username) }}"
                        placeholder="Masukkan username"
                        autocomplete="username"
                    >

                    @error('username')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                    EMAIL
                ================================================== --}}
                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        placeholder="Masukkan email"
                        autocomplete="email"
                        required
                    >

                    @error('email')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                    NOMOR HP
                ================================================== --}}
                <div class="form-group">

                    <label for="phone">Nomor HP <span class="edit-profile-optional">· opsional</span></label>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        placeholder="08xxxxxxxxxx"
                        autocomplete="tel"
                    >

                    @error('phone')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                    SAVE BUTTON
                ================================================== --}}
                <button
                    type="submit"
                    class="save-profile-btn"
                >

                    <i class="fa-solid fa-check"></i>

                    <span>Simpan Perubahan</span>

                </button>

            </form>

        </div>

    </div>


    {{-- =====================================================
        BOTTOM NAVIGATION
    ====================================================== --}}
</div>

@endsection

@push('styles')
<style>
    .edit-profile-page { max-width: 560px; margin: 0 auto; padding: 8px 0 28px; }
    .edit-profile-header { gap: 14px; margin-bottom: 20px; }
    .edit-profile-heading span { color: #a35b29; font-size: 9px; font-weight: 800; letter-spacing: .18em; }
    .edit-profile-heading h1 { margin: 3px 0 2px; color: #25170e; font-size: 22px; line-height: 1.2; font-weight: 800; }
    .edit-profile-heading p { margin: 0; color: #9b806c; font-size: 11px; }
    .edit-profile-identity { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; padding: 14px 16px; border: 1px solid #efe2d3; border-radius: 16px; background: linear-gradient(120deg, #fffaf4, #f7ecdf); }
    .edit-profile-avatar { width: 44px; height: 44px; display: grid; place-items: center; flex: 0 0 44px; border-radius: 14px; background: #7a3e08; color: #fff; font-size: 18px; font-weight: 800; }
    .edit-profile-identity-copy { min-width: 0; display: flex; flex: 1; flex-direction: column; gap: 3px; }
    .edit-profile-identity-copy strong { overflow: hidden; color: #2a1b10; font-size: 13px; text-overflow: ellipsis; white-space: nowrap; }
    .edit-profile-identity-copy span { overflow: hidden; color: #947d69; font-size: 10px; text-overflow: ellipsis; white-space: nowrap; }
    .edit-profile-identity > i { color: #996332; font-size: 15px; }
    .edit-profile-card { padding: 20px; border: 1px solid #efe5db; border-radius: 18px; box-shadow: 0 8px 24px rgba(66, 42, 20, .06); }
    .edit-profile-card-title { margin-bottom: 22px; }
    .edit-profile-form .form-group { margin-bottom: 17px; }
    .edit-profile-form .form-group label { margin-bottom: 8px; font-size: 11px; }
    .edit-profile-optional { color: #a28c78; font-size: 10px; font-weight: 500; }
    .edit-profile-form .form-group input { height: 48px; border-radius: 13px; background: #fffdfb; }
    .edit-profile-form .form-group input:focus { background: #fff; }
    .edit-profile-form .save-profile-btn { min-height: 48px; border-radius: 13px; }
    .edit-profile-alert { display: flex; align-items: center; gap: 9px; margin: 0 0 14px; padding: 12px 14px; border: 1px solid #d8e8d9; border-radius: 12px; background: #f3faf3; color: #347044; font-size: 12px; }
    @media (max-width: 480px) { .edit-profile-page { padding-top: 5px; } .edit-profile-header { margin-bottom: 18px; } .edit-profile-heading h1 { font-size: 20px; } .edit-profile-card { padding: 17px; } }
</style>
@endpush

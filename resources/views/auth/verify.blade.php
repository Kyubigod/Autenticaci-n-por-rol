<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('verify.post') }}">
        @csrf

        <!-- Email Address -->
<p class="text-white text--800">Hemos enviado un código de verificación a su correo</p>
<br/> 

        <div>
            <x-input-label for="code" :value="__('Two Factor Code')" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>
        

        @session("error")
            <div class="alert alert-danger mt-4">
                {{ session('error') }}
            </div>
        @endsession

        
        <div class="flex items-center justify-end mt-4">
           

            <x-primary-button class="ms-3">
                {{ __('Verify') }}
            </x-primary-button>
        </div>
    </form>
    <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
    @csrf
    <button type="submit" class="underline text-sm text-gray-400 hover:text-gray-200">
        {{ __('Cerrar sesión') }}
    </button>
</form>
</x-guest-layout>

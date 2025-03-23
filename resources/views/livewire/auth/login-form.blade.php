<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header d-flex justify-content-center">
                    <img src="{{ asset('img/logo2.png') }}" alt="Logo" width="200" height="200">
                </div>

                <div class="card-body">
                    <form wire:submit.prevent="submit">
                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputEmail" type="text" wire:model="username"/>
                            <label for="inputEmail">{{ __('Username') }}</label>
                            @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputPassword" type="password" wire:model="password"/>
                            <label for="inputPassword">{{ __('Password') }}</label>
                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

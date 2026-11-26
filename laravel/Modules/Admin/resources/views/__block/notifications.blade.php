@if (session('success'))
    <div class="alert-box success">
        <div class="alert-content">
            <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <span class="alert-message">{{ session('success') }}</span>
        </div>
        <button class="alert-close" onclick="this.parentElement.remove();">&times;</button>
    </div>
@endif

@if (session('error'))
    <div class="alert-box error">
        <div class="alert-content">
            <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <span class="alert-message">{{ session('error') }}</span>
        </div>
        <button class="alert-close" onclick="this.parentElement.remove();">&times;</button>
    </div>
@endif
@if ($errors->any())
    <div class="alert-box error-list">
        <div class="alert-content">
            <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <div class="alert-messages-wrapper">
                <strong class="alert-title">Заполните поля корректно:</strong>
                <ul class="errors-ul">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button class="alert-close" onclick="this.parentElement.remove();">&times;</button>
    </div>
@endif

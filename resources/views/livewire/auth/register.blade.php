<div>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Essential CSS Files for Livewire Auth (cPanel Compatible with Cache-Busting) -->
    <link rel="stylesheet" href="{{ asset('css/button-fixes.css') }}?v={{ filemtime(public_path('css/button-fixes.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/mobile-auth.css') }}?v={{ filemtime(public_path('css/mobile-auth.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/global-fixes.css') }}?v={{ filemtime(public_path('css/global-fixes.css')) }}">
    
    <!-- JavaScript Files for Livewire Auth -->
    <script src="{{ asset('js/global-fixes.js') }}?v={{ filemtime(public_path('js/global-fixes.js')) }}"></script>
    <script src="{{ asset('js/auth-enhanced.js') }}?v={{ filemtime(public_path('js/auth-enhanced.js')) }}"></script>
    
    <style>
        /* Override body styles for Livewire component */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            height: 100vh;
            max-height: 100vh;
            overflow: hidden !important;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            position: relative;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        /* Animated background mesh gradient */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(147, 51, 234, 0.2) 0%, transparent 50%);
            animation: meshMove 15s ease-in-out infinite;
            z-index: 0;
        }
        
        @keyframes meshMove {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(20px, 20px) scale(1.05); }
        }
        
        /* Enhanced container with premium styling */
        .mobile-auth-container {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(30px) !important;
            border-radius: 28px !important;
            padding: 1.75rem 1.5rem !important;
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.1),
                inset 0 1px 0 0 rgba(255, 255, 255, 0.6) !important;
            position: relative;
            z-index: 1;
            max-height: calc(100vh - 1rem);
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .mobile-auth-container::-webkit-scrollbar {
            display: none;
        }
        
        /* Premium brand header */
        .brand-header {
            margin-bottom: 0.75rem;
            text-align: center;
        }
        
        .brand-logo {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 3s ease-in-out infinite;
        }
        
        @keyframes shimmer {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .brand-header p {
            color: #6b7280;
            font-size: 0.85rem;
            font-weight: 500;
            margin-top: 0.35rem;
        }
        
        /* Enhanced illustration with 3D effect */
        .auth-illustration.register-illustration {
            width: 90px;
            height: 90px;
            margin: 0.5rem auto 0.75rem;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.15));
            transition: transform 0.3s ease;
        }
        
        .auth-illustration.register-illustration:hover {
            transform: scale(1.05) rotate(2deg);
        }
        
        /* Premium button styling */
        .btn-google {
            background: linear-gradient(135deg, #4285f4 0%, #357ae8 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 14px !important;
            padding: 0.8rem 1.15rem !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            box-shadow: 
                0 4px 14px rgba(66, 133, 244, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative;
            overflow: hidden;
        }
        
        .btn-google::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-google:hover::before {
            left: 100%;
        }
        
        .btn-google:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 6px 20px rgba(66, 133, 244, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
        }
        
        .btn-google:active {
            transform: translateY(0);
        }
        
        /* Enhanced divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 1rem 0;
            color: #9ca3af;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
        }
        
        .divider span {
            padding: 0 1rem;
        }
        
        /* Premium form inputs */
        .form-group {
            margin-bottom: 0.85rem;
            position: relative;
        }
        
        .form-input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.65rem !important;
            border: 2px solid #e5e7eb !important;
            border-radius: 14px !important;
            background: #f9fafb !important;
            font-size: 0.875rem !important;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            outline: none !important;
        }
        
        .form-input:focus {
            border-color: #6366f1 !important;
            background: #ffffff !important;
            box-shadow: 
                0 0 0 4px rgba(99, 102, 241, 0.1),
                0 2px 8px rgba(0, 0, 0, 0.05) !important;
            transform: translateY(-1px);
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.95rem;
            z-index: 2;
            transition: color 0.3s ease;
        }
        
        .form-input:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: #6366f1;
        }
        
        /* Premium primary button */
        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 14px !important;
            padding: 0.95rem 1.5rem !important;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            letter-spacing: 0.3px;
            box-shadow: 
                0 4px 14px rgba(99, 102, 241, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative;
            overflow: hidden;
            margin-top: 0.5rem;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 6px 20px rgba(99, 102, 241, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        /* Auth link styling */
        .auth-link {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.875rem;
        }
        
        .auth-link span {
            color: #6b7280;
            font-weight: 500;
        }
        
        .auth-link a {
            color: #6366f1 !important;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .auth-link a:hover {
            color: #4f46e5 !important;
            text-decoration: underline;
        }
        
        /* Terms section */
        .mobile-auth-container > div:last-child {
            margin-top: 1rem;
            font-size: 0.7rem;
            line-height: 1.5;
        }
        
        .mobile-auth-container > div:last-child a {
            font-weight: 600;
            transition: color 0.2s ease;
        }
        
        .mobile-auth-container > div:last-child a:hover {
            text-decoration: underline;
        }
        
        /* Enhanced alerts */
        .alert {
            padding: 0.9rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            font-size: 0.825rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            animation: slideDown 0.3s ease-out;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 2px solid #6ee7b7;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 2px solid #fca5a5;
        }
        
        /* Responsive adjustments */
        @media (max-height: 700px) {
            .mobile-auth-container { padding: 1.25rem 1.35rem !important; }
            .auth-illustration.register-illustration { width: 70px; height: 70px; margin: 0.35rem auto 0.65rem; }
            .brand-logo { font-size: 1.5rem; }
            .form-group { margin-bottom: 0.7rem; }
        }
        
        @media (max-height: 600px) {
            .mobile-auth-container { padding: 1rem 1.15rem !important; }
            .auth-illustration.register-illustration { width: 55px; height: 55px; margin: 0.25rem auto 0.5rem; }
            .brand-logo { font-size: 1.35rem; }
            .brand-header p { font-size: 0.75rem; }
            .mobile-auth-container > div:last-child { font-size: 0.65rem; margin-top: 0.75rem; }
        }
        
        @media (min-width: 768px) {
            .mobile-auth-container {
                max-width: 440px;
                padding: 2.25rem 2rem !important;
            }
            .auth-illustration.register-illustration { width: 110px; height: 110px; margin: 0.75rem auto 1rem; }
            .brand-logo { font-size: 2rem; }
            .form-group { margin-bottom: 0.95rem; }
        }
    </style>

    <div class="mobile-auth-container">
        <!-- Brand Header -->
        <div class="brand-header">
            <div class="brand-logo">
                <span class="h-text">MCQ</span><span class="care-text">Pro</span>
            </div>
            <p style="color: #6b7280; font-size: 0.9rem; margin-top: 0.5rem;">Create your account to get started</p>
        </div>

        <!-- Illustration -->
        <div class="auth-illustration register-illustration">
            <div class="illustration-bg">
                <div class="mcq-book">
                    <div class="book-cover">
                        <div class="book-title">MCQ</div>
                        <div class="book-subtitle">Pro</div>
                        <div class="book-decoration">
                            <div class="question-mark">?</div>
                            <div class="answer-options">
                                <div class="option option-a">A</div>
                                <div class="option option-b">B</div>
                                <div class="option option-c">C</div>
                                <div class="option option-d">D</div>
                            </div>
                        </div>
                    </div>
                    <div class="book-spine"></div>
                    <div class="book-shadow"></div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if($error)
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ $error }}</span>
            </div>
        @endif
        
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Social Login -->
        <div class="social-login">
            <a href="{{ route('auth.google') }}" class="btn btn-google">
                <svg class="social-icon" viewBox="0 0 24 24">
                    <path fill="white" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="white" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="white" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="white" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continue with Google
            </a>
        </div>

        <!-- Divider -->
        <div class="divider">
            <span>or register with email</span>
        </div>
        
        <!-- Registration Form -->
        <form wire:submit.prevent="register" class="auth-form">
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" 
                           class="form-input" 
                           wire:model="name"
                           placeholder="Full name" 
                           required>
                </div>
                @error('name')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" 
                           class="form-input" 
                           wire:model="email"
                           placeholder="Email address" 
                           required 
                           autocomplete="email">
                </div>
                @error('email')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" 
                           class="form-input" 
                           wire:model="password"
                           placeholder="Password (min. 6 characters)" 
                           required 
                           autocomplete="new-password">
                </div>
                @error('password')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" 
                           class="form-input" 
                           wire:model="password_confirmation"
                           placeholder="Confirm password" 
                           required 
                           autocomplete="new-password">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="register">
                    <i class="fas fa-user-plus" style="margin-right: 0.5rem;"></i>
                    Create Account
                </span>
                <span wire:loading wire:target="register">
                    <i class="fas fa-spinner fa-spin" style="margin-right: 0.5rem;"></i>
                    Creating account...
                </span>
            </button>
        </form>
        
        <!-- Auth Link -->
        <div class="auth-link">
            <span style="color: #6b7280;">Already have an account?</span>
            <a href="{{ route('login') }}" wire:navigate style="margin-left: 0.25rem;">Sign in</a>
        </div>
        
        <!-- Terms -->
        <div style="text-align: center; margin-top: 1.5rem; font-size: 0.75rem; color: #9ca3af; line-height: 1.5;">
            By creating an account, you agree to our<br>
            <a href="#" style="color: #6366f1; text-decoration: none;">Terms of Service</a> and 
            <a href="#" style="color: #6366f1; text-decoration: none;">Privacy Policy</a>
        </div>
    </div>
</div>

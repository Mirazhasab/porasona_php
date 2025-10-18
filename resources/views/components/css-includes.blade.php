{{-- Comprehensive CSS Inclusion Component for MCQ PRO --}}
{{-- Usage: @include('components.css-includes') --}}
{{-- Note: This component should only be used for standalone pages that don't extend layouts --}}

<!-- Essential CSS Files (Multiple Loading Methods for cPanel Compatibility) -->
<!-- Button Fixes - Critical for button visibility -->
<link rel="stylesheet" href="{{ asset('css/button-fixes.css') }}">
<link rel="stylesheet" href="{{ url('css/button-fixes.css') }}">
<link rel="stylesheet" href="/css/button-fixes.css">

<!-- Global Fixes - Universal styling fixes -->
<link rel="stylesheet" href="{{ asset('css/global-fixes.css') }}">
<link rel="stylesheet" href="{{ url('css/global-fixes.css') }}">
<link rel="stylesheet" href="/css/global-fixes.css">

<!-- Dashboard Spacing - Layout spacing -->
<link rel="stylesheet" href="{{ asset('css/dashboard-spacing.css') }}">
<link rel="stylesheet" href="{{ url('css/dashboard-spacing.css') }}">
<link rel="stylesheet" href="/css/dashboard-spacing.css">

<!-- MCQ Options - MCQ specific styling -->
<link rel="stylesheet" href="{{ asset('css/mcq-options-fullwidth.css') }}">
<link rel="stylesheet" href="{{ url('css/mcq-options-fullwidth.css') }}">
<link rel="stylesheet" href="/css/mcq-options-fullwidth.css">

<!-- Universal Card Fix - Card styling fixes -->
<link rel="stylesheet" href="{{ asset('css/universal-card-fix.css') }}">
<link rel="stylesheet" href="{{ url('css/universal-card-fix.css') }}">
<link rel="stylesheet" href="/css/universal-card-fix.css">

<!-- Critical Inline CSS for Emergency Fallback -->
<style>
/* CRITICAL BUTTON STYLES - ALWAYS VISIBLE */
.btn, button, [type="button"], [type="submit"], 
a[class*="btn"], a[href*="exam"]:not([href*="view"]) {
    background-color: #2563eb !important;
    color: #ffffff !important;
    border: 2px solid #2563eb !important;
    padding: 12px 24px !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
    min-height: 44px !important;
}

.btn:hover, button:hover, [type="button"]:hover, [type="submit"]:hover,
a[class*="btn"]:hover, a[href*="exam"]:not([href*="view"]):hover {
    background-color: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
    color: #ffffff !important;
    text-decoration: none !important;
}

/* Button Variants */
.btn-primary { background-color: #2563eb !important; border-color: #2563eb !important; }
.btn-secondary { background-color: #6b7280 !important; border-color: #6b7280 !important; }
.btn-success { background-color: #059669 !important; border-color: #059669 !important; }
.btn-danger { background-color: #dc2626 !important; border-color: #dc2626 !important; }

/* Icon Colors */
.btn i, button i, [type="button"] i, [type="submit"] i,
a[class*="btn"] i, a[href*="exam"] i {
    color: #ffffff !important;
}

/* Card Styles */
.card, .bg-white {
    background-color: #ffffff !important;
    border-radius: 12px !important;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid #e5e7eb !important;
}

/* Essential Utilities */
.flex { display: flex !important; }
.items-center { align-items: center !important; }
.justify-center { justify-content: center !important; }
.text-white { color: #ffffff !important; }
.bg-blue-600 { background-color: #2563eb !important; }
.rounded-lg { border-radius: 8px !important; }
.p-4 { padding: 16px !important; }
.mb-4 { margin-bottom: 16px !important; }
.w-full { width: 100% !important; }

/* Mobile Responsive */
@media (max-width: 768px) {
    .btn, button, [type="button"], [type="submit"] {
        min-height: 48px !important;
        padding: 14px 20px !important;
    }
}
</style>

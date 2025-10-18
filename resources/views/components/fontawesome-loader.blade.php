@once
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-vi+Ar3G5kZZgwyXR0vbJIUKGwEuITdSb9VjA36TObgGJE0E7E5Wdl66iRS0LlwM651c01qmPvvrL1jAU6RzX6A=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer"
          onerror="(function(el){el.onerror=null;el.href='https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css';})(this)">
    <script>
        (function () {
            function fontAwesomeLoaded() {
                try {
                    var probe = document.createElement('i');
                    probe.className = 'fas fa-check';
                    probe.style.position = 'absolute';
                    probe.style.opacity = '0';
                    probe.style.pointerEvents = 'none';
                    document.body.appendChild(probe);
                    var before = window.getComputedStyle(probe, '::before');
                    var fontFamily = before && before.fontFamily ? before.fontFamily : '';
                    probe.remove();
                    return fontFamily.indexOf('Font Awesome') !== -1;
                } catch (error) {
                    return false;
                }
            }

            function injectFallback() {
                if (document.querySelector('link[data-fa-fallback]')) {
                    return;
                }
                var fallback = document.createElement('link');
                fallback.rel = 'stylesheet';
                fallback.href = 'https://use.fontawesome.com/releases/v5.15.4/css/all.css';
                fallback.setAttribute('data-fa-fallback', 'true');
                fallback.onerror = function () {
                    fallback.remove();
                    if (!document.querySelector('style[data-fa-inline]')) {
                        var inline = document.createElement('style');
                        inline.setAttribute('data-fa-inline', 'true');
                        inline.textContent = '.fa, .fas, .far, .fal, .fad, .fab { font-family: \"Font Awesome 5 Free\", \"Font Awesome 5 Brands\", \"Font Awesome 6 Free\"; font-weight: 900; }';
                        document.head.appendChild(inline);
                    }
                };
                document.head.appendChild(fallback);
            }

            function ensure() {
                if (!fontAwesomeLoaded()) {
                    injectFallback();
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', ensure);
            } else {
                ensure();
            }
        })();
    </script>
@endonce

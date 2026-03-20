<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="MediaHub - Gestor de medios multimedia">
    <meta name="theme-color" content="#0d6efd">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MediaHub">
    <link rel="apple-touch-icon" href="{{ asset('icon-192.svg') }}">
    
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('icon-192.svg') }}">
    <title>@yield('title', 'MediaHub')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --touch-target-size: 44px;
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-tertiary: #e9ecef;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --border-color: #dee2e6;
            --card-bg: #ffffff;
            --navbar-bg: #ffffff;
            --footer-bg: #f8f9fa;
            --shadow: rgba(0, 0, 0, 0.1);
            --shadow-hover: rgba(0, 0, 0, 0.15);
            --input-bg: #ffffff;
            --input-color: #212529;
            --input-border: #ced4da;
        }
        [data-theme="dark"] {
            --bg-primary: #1a1d21;
            --bg-secondary: #212529;
            --bg-tertiary: #2d3238;
            --text-primary: #f8f9fa;
            --text-secondary: #adb5bd;
            --border-color: #495057;
            --card-bg: #212529;
            --navbar-bg: #1a1d21;
            --footer-bg: #1a1d21;
            --shadow: rgba(0, 0, 0, 0.3);
            --shadow-hover: rgba(0, 0, 0, 0.5);
            --input-bg: #2d3238;
            --input-color: #f8f9fa;
            --input-border: #495057;
        }
        * { -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body>
    <div id="offline-indicator" class="offline-indicator">
        <i class="bi bi-wifi-off"></i> Sin conexión a internet
    </div>
    
    <div id="toast-container"></div>
    
    <nav class="navbar navbar-expand-lg shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-collection-play"></i> MediaHub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="bi bi-house"></i> <span class="d-none d-lg-inline">Inicio</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('medios.index') }}">
                            <i class="bi bi-collection"></i> <span class="d-none d-lg-inline">Medios</span>
                        </a>
                    </li>
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> <span class="d-none d-lg-inline">Dashboard</span>
                        </a>
                    </li>
                    @endauth
                </ul>
                <ul class="navbar-nav align-items-center gap-2">
                    @guest
                    <li>
                        <button class="theme-toggle" id="themeToggle" title="Cambiar tema">
                            <i class="bi bi-moon-fill moon-icon"></i>
                            <i class="bi bi-sun-fill sun-icon"></i>
                        </button>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> <span class="d-none d-lg-inline">Iniciar Sesión</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm text-white" href="{{ route('register') }}">
                            <i class="bi bi-person-plus"></i> <span class="d-none d-lg-inline">Registrarse</span>
                        </a>
                    </li>
                    @else
                    <li>
                        <button class="theme-toggle" id="themeToggle" title="Cambiar tema">
                            <i class="bi bi-moon-fill moon-icon"></i>
                            <i class="bi bi-sun-fill sun-icon"></i>
                        </button>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" id="notificationBtn">
                            <i class="bi bi-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge" style="display: none;">
                                0
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="width: 320px; max-height: 400px; overflow-y: auto;" id="notificationDropdown">
                            <li class="dropdown-header d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-bell"></i> Notificaciones</span>
                                <a href="#" class="text-decoration-none small" onclick="markAllNotificationsRead()">Marcar todas como leídas</a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li id="notificationList">
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-bell-slash fs-4"></i>
                                    <p class="mb-0 small">No hay notificaciones</p>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <span class="d-none d-lg-inline">{{ Auth::user()->name }}</span>
                            <span class="badge bg-{{ Auth::user()->isAdmin() ? 'danger' : (Auth::user()->isEditor() ? 'primary' : 'secondary') }} ms-1">
                                {{ Auth::user()->getRoleLabel() }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                                <i class="bi bi-person"></i> Mi Perfil
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('favoritos.index') }}">
                                <i class="bi bi-heart-fill text-danger"></i> Mis Favoritos
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a></li>
                            @if(Auth::user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('users.index') }}">
                                <i class="bi bi-people"></i> Gestionar Usuarios
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('trash.index') }}">
                                <i class="bi bi-trash3"></i> Papelera
                            </a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="#" class="dropdown-item" onclick="event.preventDefault(); logoutUser();">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-3 page-content" data-flash-success="{{ session('success') }}" data-flash-error="{{ session('error') }}" data-flash-warning="{{ session('warning') }}" data-flash-info="{{ session('info') }}">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container text-center">
            <p class="text-muted mb-0 small">
                &copy; {{ date('Y') }} MediaHub
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
    
    <script>
        const Theme = {
            key: 'mediahub-theme',
            
            init() {
                const saved = localStorage.getItem(this.key);
                if (saved) {
                    document.documentElement.setAttribute('data-theme', saved);
                } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.setAttribute('data-theme', 'dark');
                }
                
                const toggle = document.getElementById('themeToggle');
                if (toggle) {
                    toggle.addEventListener('click', () => this.toggle());
                }
            },
            
            toggle() {
                const current = document.documentElement.getAttribute('data-theme');
                const next = current === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', next);
                localStorage.setItem(this.key, next);
                
                gsap.fromTo('#themeToggle', 
                    { scale: 0.8, rotate: -180 },
                    { scale: 1, rotate: 0, duration: 0.4, ease: 'back.out(1.7)' }
                );
            }
        };
        
        const Toast = {
            container: document.getElementById('toast-container'),
            
            icons: {
                success: 'check-lg',
                error: 'x-lg',
                warning: 'exclamation-lg',
                info: 'info-lg'
            },
            
            titles: {
                success: '¡Éxito!',
                error: '¡Error!',
                warning: '¡Atención!',
                info: 'Información'
            },
            
            show(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `toast-notification ${type}`;
                toast.innerHTML = `
                    <div class="toast-icon">
                        <i class="bi bi-${this.icons[type] || this.icons.info}"></i>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">${this.titles[type] || this.titles.info}</div>
                        <div class="toast-message">${message}</div>
                    </div>
                    <button class="toast-close" onclick="Toast.hide(this.parentElement)">
                        <i class="bi bi-x"></i>
                    </button>
                    <div class="toast-progress"></div>
                `;
                
                this.container.appendChild(toast);
                
                gsap.fromTo(toast, 
                    { x: 100, opacity: 0, scale: 0.8 },
                    { x: 0, opacity: 1, scale: 1, duration: 0.5, ease: 'back.out(1.7)' }
                );
                
                setTimeout(() => this.hide(toast), 4000);
                toast.addEventListener('click', () => this.hide(toast));
            },
            
            hide(toast) {
                gsap.to(toast, {
                    x: 100, opacity: 0, scale: 0.8, duration: 0.3, ease: 'power2.in',
                    onComplete: () => toast.remove()
                });
            },
            
            success(message) { this.show(message, 'success'); },
            error(message) { this.show(message, 'error'); },
            warning(message) { this.show(message, 'warning'); },
            info(message) { this.show(message, 'info'); }
        };
        
        document.addEventListener('DOMContentLoaded', () => {
            Theme.init();
            ScrollAnimations.init();
            PageAnimations.init();
            
            const main = document.querySelector('main[data-flash-success]');
            if (main) {
                const success = main.dataset.flashSuccess;
                const error = main.dataset.flashError;
                const warning = main.dataset.flashWarning;
                const info = main.dataset.flashInfo;
                
                if (success) Toast.success(success);
                if (error) Toast.error(error);
                if (warning) Toast.warning(warning);
                if (info) Toast.info(info);
            }
        });
        
        // ========================================
        // SCROLL ANIMATIONS
        // ========================================
        const ScrollAnimations = {
            init() {
                const reveals = document.querySelectorAll('.reveal');
                
                if (reveals.length === 0) return;
                
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });
                
                reveals.forEach(reveal => observer.observe(reveal));
            },
            
            // Parallax effect
            parallax() {
                const parallaxElements = document.querySelectorAll('.parallax-bg');
                parallaxElements.forEach(el => {
                    const speed = el.dataset.speed || 0.5;
                    const yPos = -(window.pageYOffset * speed);
                    gsap.to(el, { y: yPos, duration: 0 });
                });
            }
        };
        
        // Parallax on scroll
        window.addEventListener('scroll', () => {
            ScrollAnimations.parallax();
        });
        
        // ========================================
        // PAGE ANIMATIONS (simplified)
        // ========================================
        const PageAnimations = {
            init() {
                if (typeof gsap === 'undefined') return;
                
                gsap.fromTo('.navbar', 
                    { y: -50, opacity: 0 },
                    { y: 0, opacity: 1, duration: 0.4, ease: 'power2.out' }
                );
                
                gsap.fromTo('main', 
                    { opacity: 0 },
                    { opacity: 1, duration: 0.3, delay: 0.1 }
                );
            }
        };
        
        PageAnimations.init();
        
        // Smooth scroll for anchor links (optional - can be removed if slow)
        // document.querySelectorAll('a[href^="#"]').forEach(anchor => { ... });
        
        // Hover animations for cards (simplified)
        document.querySelectorAll('.card-media').forEach(card => {
            card.addEventListener('mouseenter', () => card.classList.add('hovered'));
            card.addEventListener('mouseleave', () => card.classList.remove('hovered'));
        });
        
        // Animate numbers (counters) - simplified without GSAP
        document.querySelectorAll('[data-counter]').forEach(el => {
            el.textContent = el.dataset.counter;
        });
        
        // ========================================
        // SKELETON LOADERS (simplified)
        // ========================================
        document.querySelectorAll('.skeleton-wrapper').forEach(wrapper => {
            wrapper.classList.add('loaded');
        });
        
        // ========================================
        // MICROINTERACCIONES
        // ========================================
        const MicroInteractions = {
            init() {
                this.rippleEffect();
                this.inputAnimations();
                this.cardHover();
                this.likeButton();
            },
            
            // Ripple effect para botones
            rippleEffect() {
                document.querySelectorAll('.btn').forEach(btn => {
                    btn.classList.add('btn-ripple');
                });
            },
            
            // Animaciones en inputs
            inputAnimations() {
                const inputs = document.querySelectorAll('.form-control');
                
                inputs.forEach(input => {
                    // Animación al escribir
                    input.addEventListener('input', () => {
                        if (input.value.length > 0) {
                            gsap.fromTo(input, 
                                { scale: 1 },
                                { scale: 1.01, duration: 0.1, yoyo: true, repeat: 1 }
                            );
                        }
                    });
                    
                    // Validación visual
                    input.addEventListener('blur', () => {
                        if (input.classList.contains('is-invalid')) {
                            gsap.fromTo(input, 
                                { x: 0 },
                                { x: [-5, 5, -5, 5, 0], duration: 0.3 }
                            );
                        }
                    });
                });
            },
            
            // Hover mejorado para cards
            cardHover() {
                document.querySelectorAll('.card-media').forEach(card => {
                    card.addEventListener('mouseenter', () => {
                        gsap.to(card, {
                            y: -8,
                            boxShadow: '0 20px 40px var(--shadow-hover)',
                            duration: 0.3,
                            ease: 'power2.out'
                        });
                    });
                    
                    card.addEventListener('mouseleave', () => {
                        gsap.to(card, {
                            y: 0,
                            boxShadow: '0 4px 15px var(--shadow)',
                            duration: 0.3,
                            ease: 'power2.out'
                        });
                    });
                });
            },
            
            // Botón de like con corazón
            likeButton() {
                document.querySelectorAll('.btn-like').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        const icon = this.querySelector('i');
                        const isLiked = this.classList.contains('liked');
                        
                        // Toggle like
                        this.classList.toggle('liked');
                        
                        if (!isLiked) {
                            // Animación de corazón
                            gsap.fromTo(icon,
                                { scale: 1 },
                                { scale: [1, 1.5, 1], duration: 0.4, ease: 'ease.out' }
                            );
                            
                            // Burbuja "+1"
                            this.insertAdjacentHTML('beforeend', '<span class="like-bubble">+1</span>');
                            const bubble = this.querySelector('.like-bubble');
                            gsap.fromTo(bubble,
                                { opacity: 1, y: 0 },
                                { opacity: 0, y: -30, duration: 0.8, ease: 'power2.out',
                                  onComplete: () => bubble.remove()
                                }
                            );
                        }
                    });
                });
            }
        };
        
        // Inicializar microinteracciones
        MicroInteractions.init();
        
        // Animar contadores (simplificado)
        document.querySelectorAll('[data-counter]').forEach(el => {
            el.textContent = el.dataset.counter;
        });
        
        // ========================================
        // LIGHTBOX / FANCYBOX
        // ========================================
        const Lightbox = {
            init() {
                if (typeof $.fn.fancybox === 'undefined') return;
                
                $('[data-fancybox]').fancybox({
                    loop: true,
                    thumbs: {
                        autoStart: true,
                        hideOnClose: true
                    },
                    buttons: [
                        'zoom',
                        'slideShow',
                        'fullScreen',
                        'download',
                        'thumbs',
                        'close'
                    ],
                    animationEffect: 'zoom-in-out',
                    transitionEffect: 'tube',
                    gutter: 10,
                    keyboard: true,
                    toolbar: true,
                    infobar: true
                });
                
                this.initVideoLazyLoad();
            },
            
            initVideoLazyLoad() {
                document.querySelectorAll('.video-lazy').forEach(container => {
                    const iframe = container.querySelector('iframe');
                    if (!iframe) return;
                    
                    const src = iframe.src;
                    iframe.src = '';
                    iframe.dataset.src = src;
                    
                    container.classList.add('has-placeholder');
                    
                    container.addEventListener('click', () => {
                        if (iframe.dataset.src) {
                            iframe.src = iframe.dataset.src;
                            container.classList.remove('has-placeholder');
                            container.classList.add('video-loaded');
                        }
                    });
                });
            }
        };
        
        $(document).ready(function() {
            Lightbox.init();
        });
        
        // ========================================
        // FAVORITOS / LIKES
        // ========================================
        function toggleFavorito(medioId) {
            const btn = document.getElementById('btn-favorito');
            const icon = btn.querySelector('i');
            const text = document.getElementById('btn-favorito-text');
            const countEl = document.getElementById('favoritos-count');
            
            fetch(`/favoritos/${medioId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const isLiked = data.liked;
                
                btn.classList.toggle('btn-danger', isLiked);
                btn.classList.toggle('btn-outline-danger', !isLiked);
                icon.classList.toggle('bi-heart', !isLiked);
                icon.classList.toggle('bi-heart-fill', isLiked);
                text.textContent = isLiked ? 'Favorito' : 'Me gusta';
                countEl.textContent = data.count;
                
                gsap.fromTo(icon, 
                    { scale: 1 },
                    { scale: [1, 1.4, 1], duration: 0.4, ease: 'ease.out' }
                );
                
                Toast.success(isLiked ? 'Agregado a favoritos' : 'Removido de favoritos');
            })
            .catch(error => {
                Toast.error('Error al procesar');
            });
        }
        
        // ========================================
        // NOTIFICATIONS
        // ========================================
        const Notifications = {
            badge: null,
            list: null,
            
            init() {
                this.badge = document.getElementById('notificationBadge');
                this.list = document.getElementById('notificationList');
                
                this.loadUnreadCount();
                this.loadNotifications();
                
                setInterval(() => this.loadUnreadCount(), 30000);
            },
            
            loadUnreadCount() {
                fetch('/notifications/unread')
                    .then(r => r.json())
                    .then(data => {
                        const count = data.count;
                        if (count > 0) {
                            this.badge.textContent = count > 99 ? '99+' : count;
                            this.badge.style.display = 'block';
                        } else {
                            this.badge.style.display = 'none';
                        }
                    })
                    .catch(() => {});
            },
            
            loadNotifications() {
                fetch('/notifications')
                    .then(r => r.json())
                    .then(data => {
                        if (data.length === 0) {
                            this.list.innerHTML = `
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-bell-slash fs-4"></i>
                                    <p class="mb-0 small">No hay notificaciones</p>
                                </div>
                            `;
                            return;
                        }
                        
                        this.list.innerHTML = data.map(n => `
                            <li class="dropdown-item ${n.read_at ? '' : 'bg-light'} notification-item" data-id="${n.id}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <i class="bi bi-chat-text text-primary me-2"></i>
                                        <strong>${n.data.user_nombre}</strong> commented on ${n.data.medio_titulo}
                                        <br><small class="text-muted">${n.data.contenido}</small>
                                    </div>
                                    <small class="text-muted">${this.timeAgo(n.created_at)}</small>
                                </div>
                            </li>
                        `).join('');
                        
                        document.querySelectorAll('.notification-item').forEach(item => {
                            item.addEventListener('click', () => {
                                const id = item.dataset.id;
                                fetch(`/notifications/${id}/read`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });
                                item.classList.remove('bg-light');
                                this.loadUnreadCount();
                            });
                        });
                    })
                    .catch(() => {});
            },
            
            timeAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const seconds = Math.floor((now - date) / 1000);
                
                if (seconds < 60) return 'ahora';
                if (seconds < 3600) return Math.floor(seconds / 60) + 'm';
                if (seconds < 86400) return Math.floor(seconds / 3600) + 'h';
                return Math.floor(seconds / 86400) + 'd';
            }
        };
        
        window.markAllNotificationsRead = function() {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(() => {
                Notifications.loadUnreadCount();
                Notifications.loadNotifications();
            });
        };
        
        document.addEventListener('DOMContentLoaded', () => {
            @auth
                Notifications.init();
            @endauth
        });
        
        document.querySelectorAll('.alert').forEach(alert => {
            gsap.fromTo(alert, 
                { y: -20, opacity: 0 },
                { y: 0, opacity: 1, duration: 0.4, ease: 'back.out(1.4)' }
            );
        });
        
        window.addEventListener('online', () => {
            document.getElementById('offline-indicator').classList.remove('show');
        });
        
        window.addEventListener('offline', () => {
            document.getElementById('offline-indicator').classList.add('show');
        });
        
        function logoutUser() {
            fetch('/logout', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect || '/';
                }
            })
            .catch(() => {
                window.location.href = '/';
            });
        }
    </script>
    @stack('scripts')
    
    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('SW registrado:', reg.scope))
            .catch(err => console.error('Error SW:', err));
    }
    </script>
</body>
</html>

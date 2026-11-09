
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initNavbar();
        initMobileMenu();
        initScrollReveal();
        initCounters();
        initCodeTyping();
        initPortfolioFilters();
        initTestimonialSlider();
        initFaqAccordion();
        initBackToTop();
        initContactForm();
        initSmoothAnchors();
    });

    
    function initNavbar() {
        var navbar = document.getElementById('navbar');
        if (!navbar) return;

        function onScroll() {
            if (window.scrollY > 24) {
                navbar.classList.add('is-scrolled');
            } else {
                navbar.classList.remove('is-scrolled');
            }
        }
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    
    function initMobileMenu() {
        var toggle = document.getElementById('navToggle');
        var nav = document.getElementById('navbarNav');
        if (!toggle || !nav) return;

        toggle.addEventListener('click', function () {
            var isOpen = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            toggle.setAttribute('aria-label', isOpen ? 'Cerrar menú' : 'Abrir menú');
        });

        nav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                nav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    
    function initSmoothAnchors() {
        document.querySelectorAll('a[href^="#"]').forEach(function (link) {
            link.addEventListener('click', function (e) {
                var id = link.getAttribute('href');
                if (id.length < 2) return;
                var target = document.querySelector(id);
                if (!target) return;
                e.preventDefault();
                var offset = 80;
                var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({ top: top, behavior: 'smooth' });
            });
        });
    }

    
    function initScrollReveal() {
        var items = document.querySelectorAll('.reveal');
        if (!items.length) return;

        if (!('IntersectionObserver' in window)) {
            items.forEach(function (el) { el.classList.add('is-visible'); });
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

        items.forEach(function (el) { observer.observe(el); });
    }

    
    function initCounters() {
        var counters = document.querySelectorAll('[data-counter]');
        if (!counters.length) return;

        function animateCounter(el) {
            var target = parseInt(el.getAttribute('data-target'), 10) || 0;
            var duration = 1400;
            var start = null;

            function step(timestamp) {
                if (!start) start = timestamp;
                var progress = Math.min((timestamp - start) / duration, 1);
                var eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.floor(eased * target);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    el.textContent = target;
                }
            }
            window.requestAnimationFrame(step);
        }

        if (!('IntersectionObserver' in window)) {
            counters.forEach(animateCounter);
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(function (el) { observer.observe(el); });
    }

    
    function initCodeTyping() {
        var el = document.getElementById('typedCode');
        if (!el) return;

        var lines = [
            '<?php', 'include "header.php"; ?>', '', '<section class="hero">',
            '  <h1>Tu negocio,</h1>', '  <span>en línea.</span>', '</section>', '',
            '// build: publicando sitio', 'status: 200 OK ✓'
        ];

        var lineIndex = 0, charIndex = 0, output = '';
        var typingSpeed = 26;

        function typeNext() {
            if (lineIndex >= lines.length) {
                window.setTimeout(function () {
                    output = '';
                    lineIndex = 0;
                    charIndex = 0;
                    el.textContent = '';
                    typeNext();
                }, 2200);
                return;
            }
            var currentLine = lines[lineIndex];
            if (charIndex < currentLine.length) {
                output += currentLine.charAt(charIndex);
                el.textContent = output;
                charIndex++;
                window.setTimeout(typeNext, typingSpeed);
            } else {
                output += '\n';
                el.textContent = output;
                lineIndex++;
                charIndex = 0;
                window.setTimeout(typeNext, 130);
            }
        }
        typeNext();
    }

    
    function initPortfolioFilters() {
        var buttons = document.querySelectorAll('.filter-btn');
        var cards = document.querySelectorAll('.project-card');
        if (!buttons.length || !cards.length) return;

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                buttons.forEach(function (b) { b.classList.remove('is-active'); });
                btn.classList.add('is-active');

                var filter = btn.getAttribute('data-filter');
                cards.forEach(function (card) {
                    var match = filter === 'all' || card.getAttribute('data-category') === filter;
                    card.classList.toggle('is-hidden', !match);
                });
            });
        });
    }

    
    function initTestimonialSlider() {
        var track = document.getElementById('testimonialTrack');
        if (!track) return;

        var slides = track.querySelectorAll('.testimonial-slide');
        var dotsWrap = document.getElementById('testimonialDots');
        var prevBtn = document.getElementById('testimonialPrev');
        var nextBtn = document.getElementById('testimonialNext');
        var current = 0;
        var autoplayTimer;

        slides.forEach(function (_, i) {
            var dot = document.createElement('button');
            dot.className = 'testimonial-dot' + (i === 0 ? ' is-active' : '');
            dot.setAttribute('aria-label', 'Ver testimonio ' + (i + 1));
            dot.addEventListener('click', function () { goTo(i); });
            dotsWrap.appendChild(dot);
        });
        var dots = dotsWrap.querySelectorAll('.testimonial-dot');

        function goTo(index) {
            slides[current].classList.remove('is-active');
            dots[current].classList.remove('is-active');
            current = (index + slides.length) % slides.length;
            slides[current].classList.add('is-active');
            dots[current].classList.add('is-active');
            resetAutoplay();
        }

        function resetAutoplay() {
            window.clearInterval(autoplayTimer);
            autoplayTimer = window.setInterval(function () { goTo(current + 1); }, 6000);
        }

        prevBtn.addEventListener('click', function () { goTo(current - 1); });
        nextBtn.addEventListener('click', function () { goTo(current + 1); });

        resetAutoplay();
    }

    
    function initFaqAccordion() {
        var items = document.querySelectorAll('.faq-item');
        if (!items.length) return;

        items.forEach(function (item) {
            var question = item.querySelector('.faq-item__question');
            var answer = item.querySelector('.faq-item__answer');

            question.addEventListener('click', function () {
                var isOpen = item.classList.contains('is-open');

                items.forEach(function (other) {
                    other.classList.remove('is-open');
                    other.querySelector('.faq-item__question').setAttribute('aria-expanded', 'false');
                    other.querySelector('.faq-item__answer').style.maxHeight = null;
                });

                if (!isOpen) {
                    item.classList.add('is-open');
                    question.setAttribute('aria-expanded', 'true');
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                }
            });
        });
    }

    
    function initBackToTop() {
        var btn = document.getElementById('backToTop');
        if (!btn) return;

        window.addEventListener('scroll', function () {
            btn.classList.toggle('is-visible', window.scrollY > 500);
        }, { passive: true });

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    
    function initContactForm() {
        var form = document.getElementById('contactForm');
        if (!form) return;

        var rules = {
            nombre: function (v) { return v.trim().length >= 2 ? '' : 'Ingresa tu nombre completo.'; },
            correo: function (v) {
                var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(v.trim()) ? '' : 'Ingresa un correo electrónico válido.';
            },
            telefono: function (v) {
                var re = /^[0-9+\s\-()]{7,20}$/;
                return re.test(v.trim()) ? '' : 'Ingresa un teléfono válido.';
            },
            asunto: function (v) { return v.trim().length >= 3 ? '' : 'El campo Asunto es obligatorio (mín. 3 caracteres).'; },
            mensaje: function (v) { return v.trim().length >= 10 ? '' : 'Cuéntanos un poco más (mín. 10 caracteres).'; }
        };

        function validateField(field) {
            var name = field.name;
            if (!rules[name]) return true;
            var errorMsg = rules[name](field.value);
            var errorEl = form.querySelector('[data-error-for="' + name + '"]');
            var group = field.closest('.form-group');

            if (errorMsg) {
                if (errorEl) errorEl.textContent = errorMsg;
                if (group) group.classList.add('has-error');
                return false;
            } else {
                if (errorEl) errorEl.textContent = '';
                if (group) group.classList.remove('has-error');
                return true;
            }
        }

        Object.keys(rules).forEach(function (name) {
            var field = form.querySelector('[name="' + name + '"]');
            if (field) {
                field.addEventListener('blur', function () { validateField(field); });
            }
        });

        form.addEventListener('submit', function (e) {
            var valid = true;
            Object.keys(rules).forEach(function (name) {
                var field = form.querySelector('[name="' + name + '"]');
                if (field && !validateField(field)) valid = false;
            });
            if (!valid) {
                e.preventDefault();
                var firstError = form.querySelector('.has-error input, .has-error textarea');
                if (firstError) firstError.focus();
            }
        });
    }

})();

import './stimulus_bootstrap.js';
import './styles/app.css';

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

console.log('Premium JS Initializing...');

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM Content Loaded - Initializing Premium Features');
    initChrono();
    initScrollReveal();
    init3DTilt();
    initMobileMenu();
    initHeroSlider();
    initFuturisticHeroScroll();
    initTechRotation();
    initPortfolioFilters();
});

// --- COUNTDOWN LOGIC ---
function initChrono() {
    const daysEl = document.getElementById('chrono-days');
    const hoursEl = document.getElementById('chrono-hours');
    const minsEl = document.getElementById('chrono-minutes');
    const secsEl = document.getElementById('chrono-seconds');
    const statusEl = document.getElementById('chrono-status');

    if (!daysEl) return;

    const startDate = new Date('2026-01-19T00:00:00');
    const endDate = new Date('2026-04-19T00:00:00');

    const updateTimer = () => {
        const now = new Date();
        let targetDate = startDate;
        let isStarted = false;

        if (now >= startDate) {
            targetDate = endDate;
            isStarted = true;
        }

        const diff = targetDate - now;

        if (diff <= 0 && isStarted) {
            if (statusEl) statusEl.innerText = "Défi terminé — Résultats livrés";
            return;
        }

        const s = Math.floor(diff / 1000);
        const m = Math.floor(s / 60);
        const h = Math.floor(m / 60);
        const d = Math.floor(h / 24);

        daysEl.innerText = String(d).padStart(2, '0');
        hoursEl.innerText = String(h % 24).padStart(2, '0');
        minsEl.innerText = String(m % 60).padStart(2, '0');
        secsEl.innerText = String(s % 60).padStart(2, '0');

        if (statusEl) {
            if (!isStarted) {
                statusEl.innerText = "Le défi commence bientôt";
            } else {
                const daysPassed = Math.floor((now - startDate) / (1000 * 60 * 60 * 24)) + 1;
                statusEl.innerText = `Jour ${daysPassed} du défi — En cours...`;
            }
        }
    };

    updateTimer();
    setInterval(updateTimer, 1000);
}

// --- GSAP SCROLL REVEAL ---
function initScrollReveal() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    console.log('Registering ScrollTriggers...');

    gsap.utils.toArray('.reveal').forEach((elem) => {
        // Initial state set via JS (if fails, content is already visible because of CSS override)
        gsap.set(elem, { opacity: 0, y: 30 });

        gsap.to(elem, {
            scrollTrigger: {
                trigger: elem,
                start: 'top 85%',
                toggleActions: 'play none none none'
            },
            opacity: 1,
            y: 0,
            duration: 1,
            ease: 'power3.out'
        });
    });

    // Stagger for cards/logos
    gsap.utils.toArray('.stagger-reveal').forEach((container) => {
        gsap.set(container.children, { opacity: 0, y: 20 });

        gsap.to(container.children, {
            scrollTrigger: {
                trigger: container,
                start: 'top 80%'
            },
            opacity: 1,
            y: 0,
            duration: 0.8,
            stagger: 0.1,
            ease: 'back.out(1.2)'
        });
    });

    // Parallax background
    gsap.to('.parallax-bg', {
        scrollTrigger: {
            trigger: 'body',
            start: 'top top',
            end: 'bottom bottom',
            scrub: true
        },
        y: (i, target) => -target.offsetHeight * 0.1,
        ease: 'none'
    });
}

// --- 3D TILT EFFECT ---
function init3DTilt() {
    const cards = document.querySelectorAll('.tilt-element');

    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;

            gsap.to(card, {
                rotateX: rotateX,
                rotateY: rotateY,
                duration: 0.5,
                ease: 'power2.out'
            });
        });

        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                rotateX: 0,
                rotateY: 0,
                duration: 0.8,
                ease: 'elastic.out(1, 0.3)'
            });
        });
    });
}

// --- MOBILE MENU ---
function initMobileMenu() {
    const btn = document.getElementById('mobile-menu-btn');
    const closeBtn = document.getElementById('close-menu') || document.getElementById('close-mobile-menu');
    const menu = document.getElementById('mobile-menu');
    if (!btn || !menu) return;

    const toggle = (forceClose = false) => {
        const isOpen = menu.getAttribute('data-state') === 'open';
        if (forceClose && !isOpen) return;

        const newState = forceClose ? 'closed' : (isOpen ? 'closed' : 'open');
        menu.setAttribute('data-state', newState);

        if (newState === 'open') {
            document.body.style.overflow = 'hidden';
            gsap.to(menu, {
                y: '0%',
                opacity: 1,
                pointerEvents: 'auto',
                duration: 0.8,
                ease: 'expo.out'
            });
            // Stagger reveal links
            gsap.fromTo(menu.querySelectorAll('.nav-link'),
                { y: 30, opacity: 0 },
                { y: 0, opacity: 1, duration: 0.6, stagger: 0.1, ease: 'back.out(1.2)', delay: 0.2 }
            );
        } else {
            document.body.style.overflow = '';
            gsap.to(menu, {
                y: '100%',
                opacity: 0,
                pointerEvents: 'none',
                duration: 0.6,
                ease: 'expo.in'
            });
        }
    };

    btn.addEventListener('click', () => toggle());
    if (closeBtn) closeBtn.addEventListener('click', () => toggle(true));

    // Close on link click
    menu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => toggle(true));
    });
}

// --- HERO PHOTO SLIDER ---
function initHeroSlider() {
    const slides = document.querySelectorAll('.hero-slide');
    if (slides.length <= 1) return;

    let current = 0;
    const duration = 1.5;
    const delay = 4;

    const playNext = () => {
        const next = (current + 1) % slides.length;

        // Fade out current
        gsap.to(slides[current], {
            opacity: 0,
            scale: 1.1,
            duration: duration,
            ease: 'power2.inOut'
        });

        // Fade in next
        gsap.to(slides[next], {
            opacity: 1,
            scale: 1,
            duration: duration,
            ease: 'power2.inOut',
            onStart: () => {
                // Ensure the next slide is reset for scaling if needed
                gsap.set(slides[next], { scale: 1.2, opacity: 0 });
                gsap.to(slides[next], { opacity: 1, scale: 1, duration: duration });
            }
        });

        current = next;
        setTimeout(playNext, delay * 1000);
    };

    // Initial state: first slide is visible, others are pre-scaled for effect
    gsap.set(slides, { opacity: 0, scale: 1.2 });
    gsap.set(slides[0], { opacity: 1, scale: 1 });

    setTimeout(playNext, delay * 1000);
}

// --- FUTURISTIC HERO SCROLL ---
function initFuturisticHeroScroll() {
    const hero = document.getElementById('hero');
    const content = document.getElementById('hero-content');
    const title = document.querySelector('.hero-title');
    const desc = document.querySelector('.hero-description');
    const actions = document.querySelector('.hero-actions');
    const right = document.querySelector('.hero-right');
    const hudLines = document.querySelectorAll('.hud-line');
    const hudData = document.querySelectorAll('.hud-data');
    const grid = document.querySelector('.tech-grid');

    if (!hero || !content) return;

    const tl = gsap.timeline({
        scrollTrigger: {
            trigger: hero,
            start: 'top top',
            end: 'bottom top',
            scrub: true,
            pin: false // We don't want to pin to show other content
        }
    });

    // Main Content Dissociation & perspective
    tl.to(content, {
        rotateX: 15,
        y: -100,
        opacity: 0,
        scale: 0.9,
        ease: 'none'
    }, 0);

    // Individual Parallax for dissociation
    tl.to(title, { y: -150, x: -50, ease: 'none' }, 0);
    tl.to(desc, { y: -100, x: -30, ease: 'none' }, 0);
    tl.to(actions, { y: -50, x: -10, ease: 'none' }, 0);

    tl.to(right, {
        y: -200,
        x: 100,
        rotateY: -20,
        rotateZ: 5,
        ease: 'none'
    }, 0);

    // HUD Elements fast parallax
    hudLines.forEach((line, i) => {
        tl.to(line, {
            y: i === 0 ? 500 : -500,
            opacity: 0,
            ease: 'none'
        }, 0);
    });

    hudData.forEach((data, i) => {
        tl.to(data, {
            y: i === 0 ? -300 : 300,
            opacity: 0,
            ease: 'none'
        }, 0);
    });

    // Grid depth effect
    tl.to(grid, {
        scale: 1.5,
        opacity: 0,
        ease: 'none'
    }, 0);
}

// --- TECH ROTATION (BADGE) ---
function initTechRotation() {
    const techEl = document.getElementById('hero-badge-tech');
    if (!techEl) return;

    const techs = ['SYMFONY', 'FLUTTER', 'PHP', 'DART', 'FIREBASE', 'DOCKER'];
    let index = 0;

    setInterval(() => {
        gsap.to(techEl, {
            opacity: 0,
            y: -10,
            duration: 0.5,
            onComplete: () => {
                index = (index + 1) % techs.length;
                techEl.innerText = techs[index];
                gsap.to(techEl, {
                    opacity: 1,
                    y: 0,
                    duration: 0.5
                });
            }
        });
    }, 3000);
}

// --- PORTFOLIO FILTERING ---
function initPortfolioFilters() {
    const btns = document.querySelectorAll('.filter-btn');
    const items = document.querySelectorAll('.project-item');
    if (!btns.length) return;

    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            const filter = btn.getAttribute('data-filter');

            // UI Active State
            btns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Filtering Logic with Animation
            items.forEach(item => {
                const category = item.getAttribute('data-category');

                if (filter === 'all' || category === filter) {
                    gsap.to(item, {
                        opacity: 1,
                        scale: 1,
                        duration: 0.5,
                        display: 'block',
                        ease: 'power2.out'
                    });
                } else {
                    gsap.to(item, {
                        opacity: 0,
                        scale: 0.8,
                        duration: 0.5,
                        display: 'none',
                        ease: 'power2.in'
                    });
                }
            });

            // Re-trigger ScrollTrigger to refresh positions if needed
            ScrollTrigger.refresh();
        });
    });
}

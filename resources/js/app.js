// Sticky Header scroll behavior
document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.site-header');
    if (header) {
        window.addEventListener('scroll', () => {
            header.classList.toggle('scrolled', window.scrollY > 50);
        });
        // Trigger on load in case page is already scrolled
        header.classList.toggle('scrolled', window.scrollY > 50);
    }

    // Mobile nav toggle
    const navToggle = document.querySelector('.nav-toggle');
    const mainNav = document.querySelector('.main-nav');
    if (navToggle && mainNav) {
        navToggle.addEventListener('click', () => {
            mainNav.classList.toggle('open');
        });
    }

    // Accordion
    document.querySelectorAll('.accordion-trigger').forEach(trigger => {
        trigger.addEventListener('click', () => {
            const item = trigger.closest('.accordion-item');
            const content = item.querySelector('.accordion-content');
            const isOpen = item.classList.contains('open');

            // Close all
            document.querySelectorAll('.accordion-item').forEach(ai => {
                ai.classList.remove('open');
                ai.querySelector('.accordion-content').style.maxHeight = null;
            });

            if (!isOpen) {
                item.classList.add('open');
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        });
    });

    // Scroll animations (intersection observer)
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
});

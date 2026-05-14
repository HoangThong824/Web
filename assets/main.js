// Simple Carousel Interactivity
document.addEventListener('DOMContentLoaded', () => {
    console.log('Khô Đặc Sản JS Loaded');
    
    // Add scroll effect to header
    const header = document.querySelector('header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.style.padding = '10px 0';
                header.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
            } else {
                header.style.padding = '15px 0';
                header.style.backgroundColor = '#fff';
            }
        });
    }

    // Mobile Menu Logic
    console.log('Initializing Mobile Menu Logic');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const closeMenuBtn = document.getElementById('close-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

    if (!mobileMenuBtn) console.error('Mobile menu button NOT found!');
    if (!mobileMenu) console.error('Mobile menu drawer NOT found!');

    function toggleMenu() {
        console.log('Toggle Menu Triggered');
        if (!mobileMenu || !mobileMenuOverlay) {
            console.error('Menu elements missing during toggle');
            return;
        }

        const isOpen = !mobileMenu.classList.contains('-translate-x-full');
        console.log('Current Menu State: ' + (isOpen ? 'Open' : 'Closed'));
        
        if (isOpen) {
            // Close
            mobileMenu.classList.add('-translate-x-full');
            mobileMenuOverlay.classList.add('opacity-0');
            setTimeout(() => {
                mobileMenuOverlay.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        } else {
            // Open
            mobileMenuOverlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                mobileMenuOverlay.classList.remove('opacity-0');
                mobileMenu.classList.remove('-translate-x-full');
            }, 10);
        }
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', (e) => {
            e.preventDefault();
            toggleMenu();
        });
    }
    if (closeMenuBtn) closeMenuBtn.addEventListener('click', toggleMenu);
    if (mobileMenuOverlay) mobileMenuOverlay.addEventListener('click', toggleMenu);

    // Close menu on ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileMenu && !mobileMenu.classList.contains('-translate-x-full')) {
            toggleMenu();
        }
    });

    // Client-side form validation helper
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'red';
                } else {
                    field.style.borderColor = '#ddd';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Vui lòng điền đầy đủ thông tin!');
            }
        });
    });
});

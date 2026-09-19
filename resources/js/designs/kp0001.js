const page = document.querySelector('[data-kp0001]');

if (page) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const music = document.getElementById('bgMusic');
    const musicControl = document.getElementById('musicControl');
    let opened = false;

    window.AOS?.init({ duration: 1000, once: true, easing: 'ease-out-quad', disable: reducedMotion.matches });

    function syncMusicControl() {
        const playing = !music.paused;
        musicControl.classList.toggle('playing', playing);
        musicControl.setAttribute('aria-pressed', String(playing));
        musicControl.setAttribute('aria-label', playing ? 'Jeda muzik' : 'Main muzik');
    }

    async function playMusic() {
        try {
            await music.play();
        } catch {
            // Playback may be unavailable or blocked by the browser.
            syncMusicControl();
        }
    }

    music.addEventListener('play', syncMusicControl);
    music.addEventListener('pause', syncMusicControl);
    musicControl.addEventListener('click', () => music.paused ? playMusic() : music.pause());

    document.querySelector('[data-open-invitation]').addEventListener('click', () => {
        if (opened) return;
        opened = true;
        page.style.overflow = 'auto';
        page.style.height = 'auto';
        const button = document.getElementById('btn-main');
        button.classList.add('btn-out');
        window.setTimeout(() => { button.hidden = true; }, 600);
        document.getElementById('text-bottom').classList.add('show-text');
        document.getElementById('home').classList.add('border-b-8', 'border-green-main');
        document.querySelectorAll('.bunga-daun').forEach(flower => flower.classList.add('bunga-muncul'));
        const navigation = document.getElementById('bottom-nav');
        navigation.classList.remove('translate-y-full', 'opacity-0');
        navigation.classList.add('nav-show');
        musicControl.classList.remove('opacity-0', 'pointer-events-none');
        musicControl.classList.add('opacity-100');
        playMusic();
        window.setTimeout(() => window.AOS?.refresh(), 100);
    });

    document.querySelectorAll('#bottom-nav a').forEach(link => {
        link.addEventListener('click', event => {
            const target = document.getElementById(link.hash.slice(1));
            if (!target) return;
            event.preventDefault();
            document.querySelectorAll('#bottom-nav a').forEach(item => item.classList.toggle('active-menu', item === link));
            target.scrollIntoView({ behavior: reducedMotion.matches ? 'instant' : 'smooth' });
        });
    });

    const weddingDate = Date.parse(page.dataset.weddingDate);
    function updateCountdown() {
        const seconds = Math.max(0, Math.floor((weddingDate - Date.now()) / 1000));
        const values = { days: Math.floor(seconds / 86400), hours: Math.floor(seconds / 3600) % 24, mins: Math.floor(seconds / 60) % 60, secs: seconds % 60 };
        Object.entries(values).forEach(([id, value]) => {
            document.getElementById(id).textContent = String(value).padStart(2, '0');
        });
    }
    updateCountdown();
    window.setInterval(updateCountdown, 1000);

    const leaves = document.createElement('div');
    leaves.className = 'leaf-container';
    leaves.setAttribute('aria-hidden', 'true');
    page.appendChild(leaves);
    function createLeaf() {
        if (document.hidden || reducedMotion.matches) return;
        const leaf = document.createElement('div');
        const size = `${Math.random() * 10 + 10}px`;
        const duration = Math.random() * 5 + 7;
        leaf.className = 'leaf';
        Object.assign(leaf.style, {
            width: size, height: size, left: `${Math.random() * 120 - 20}%`,
            backgroundColor: ['#556b2f', '#6b8e23', '#4b5d20'][Math.floor(Math.random() * 3)],
            transform: 'translateY(-150px)', animation: `fall ${duration}s linear`, opacity: Math.random() * 0.4 + 0.2,
        });
        leaves.appendChild(leaf);
        window.setTimeout(() => leaf.remove(), duration * 1000);
    }
    window.setInterval(createLeaf, 800);

    const slider = document.getElementById('slider');
    const dots = document.querySelectorAll('.dot');
    let slideIndex = 0;
    function updateSlider() {
        slider.style.transform = `translateX(-${slideIndex * 100}%)`;
        dots.forEach((dot, index) => dot.classList.toggle('bg-white', index === slideIndex));
    }
    updateSlider();
    window.setInterval(() => {
        if (document.hidden || reducedMotion.matches) return;
        slideIndex = (slideIndex + 1) % slider.children.length;
        updateSlider();
    }, 3000);

    const wishes = [
        ['Aiman', 'Semoga perkahwinan ini dipenuhi dengan kebahagiaan dan keberkatan hingga ke syurga.'],
        ['Nurul', 'Selamat pengantin baru! Semoga kekal bahagia dan saling melengkapi.'],
        ['Hakim', 'Semoga ikatan ini membawa rahmat dan kebahagiaan berpanjangan.'],
        ['Siti', 'Doa terbaik buat kalian berdua, semoga sentiasa dalam lindungan-Nya.'],
        ['Farhan', 'Tahniah! Semoga perjalanan baru ini penuh cinta dan kebahagiaan.'],
        ['Aisyah', 'Semoga perkahwinan ini menjadi permulaan hidup penuh barakah.'],
    ];
    const wishesContainer = document.getElementById('ucapanContainer');
    let wishIndex = 0;
    function createWish([name, message]) {
        const card = document.createElement('div');
        card.className = 'bg-white/90 p-4 rounded-2xl border border-[#556b2f]/10 shadow-sm';
        const heading = document.createElement('div');
        heading.className = 'text-sm font-semibold text-[#1f3d2b] mb-1';
        heading.textContent = name;
        const text = document.createElement('div');
        text.className = 'text-xs text-stone-600 italic leading-snug';
        text.textContent = `“${message}”`;
        card.append(heading, text);
        return card;
    }
    wishes.slice(0, 3).forEach(wish => wishesContainer.appendChild(createWish(wish)));
    window.setInterval(() => {
        if (document.hidden || reducedMotion.matches) return;
        const distance = wishesContainer.firstElementChild.offsetHeight + 12;
        wishesContainer.style.transition = 'transform 0.5s ease';
        wishesContainer.style.transform = `translateY(-${distance}px)`;
        window.setTimeout(() => {
            wishIndex = (wishIndex + 1) % wishes.length;
            wishesContainer.firstElementChild.remove();
            wishesContainer.appendChild(createWish(wishes[(wishIndex + 2) % wishes.length]));
            wishesContainer.style.transition = 'none';
            wishesContainer.style.transform = 'translateY(0)';
        }, 500);
    }, 3000);

    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
    window.scrollTo(0, 0);
}

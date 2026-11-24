document.querySelectorAll('form[data-kirim]').forEach((formulir) => {
    formulir.addEventListener('submit', (kejadian) => {
        if (formulir.getAttribute('aria-busy') === 'true') {
            kejadian.preventDefault();
            return;
        }

        const tombol = formulir.querySelector('button[type="submit"]');

        if (tombol) {
            tombol.dataset.teksSemula = tombol.textContent;
            tombol.kontenSemula = [...tombol.childNodes];
            tombol.textContent = tombol.dataset.teksProses || tombol.textContent;
            tombol.disabled = true;
            formulir.setAttribute('aria-busy', 'true');
        }
    });
});

window.addEventListener('pageshow', () => {
    document.querySelectorAll('button[data-teks-semula]').forEach((tombol) => {
        if (tombol.kontenSemula) {
            tombol.replaceChildren(...tombol.kontenSemula);
        } else {
            tombol.textContent = tombol.dataset.teksSemula;
        }
        tombol.disabled = false;
        delete tombol.dataset.teksSemula;
        tombol.closest('form').removeAttribute('aria-busy');
    });
});

document.querySelector('[data-kesalahan-formulir]')?.focus();

const navigasiAkun = document.querySelector('[data-navigasi-akun]');

if (navigasiAkun) {
    const tautanAkun = [...navigasiAkun.querySelectorAll('a[href^="#"]')];
    const panelAkun = [...document.querySelectorAll('[data-panel-akun]')];

    const tampilkanBagianAkun = (hash, perbaruiAlamat = false) => {
        const bagianAktif = panelAkun.find((panel) => `#${panel.id}` === hash)
            ?? panelAkun.find((panel) => panel.classList.contains('bagian-pengaturan-validasi'))
            ?? panelAkun[0];

        panelAkun.forEach((panel) => {
            panel.hidden = panel !== bagianAktif;
        });
        tautanAkun.forEach((tautan) => {
            if (tautan.hash === `#${bagianAktif.id}`) {
                tautan.setAttribute('aria-current', 'location');
            } else {
                tautan.removeAttribute('aria-current');
            }
        });

        if (perbaruiAlamat && window.location.hash !== `#${bagianAktif.id}`) {
            window.history.replaceState(null, '', `#${bagianAktif.id}`);
        }
    };

    tautanAkun.forEach((tautan) => {
        tautan.addEventListener('click', () => tampilkanBagianAkun(tautan.hash));
    });
    window.addEventListener('hashchange', () => tampilkanBagianAkun(window.location.hash));
    window.addEventListener('pageshow', () => tampilkanBagianAkun(window.location.hash));
    tampilkanBagianAkun(window.location.hash, !window.location.hash);
}

const modalLowongan = document.getElementById('modal-lowongan');

if (modalLowongan instanceof HTMLDialogElement) {
    const bukaModal = () => {
        modalLowongan.showModal();
    };

    document.querySelectorAll('[data-buka-modal]').forEach((pemicu) => {
        pemicu.addEventListener('click', (kejadian) => {
            kejadian.preventDefault();
            bukaModal();
        });
    });
    modalLowongan.querySelectorAll('[data-tutup-modal]').forEach((tombol) => {
        tombol.addEventListener('click', () => modalLowongan.close());
    });

    if (modalLowongan.hasAttribute('data-terbuka')) {
        bukaModal();
    }
}

document.querySelectorAll('select[data-kirim-otomatis]').forEach((saringan) => {
    saringan.addEventListener('change', () => saringan.form?.requestSubmit());
});

document.querySelectorAll('[data-lihat-sandi]').forEach((tombol) => {
    const isian = document.getElementById(tombol.getAttribute('aria-controls'));
    const keterangan = tombol.getAttribute('aria-label');

    tombol.hidden = false;
    tombol.addEventListener('click', () => {
        const terlihat = isian.type === 'password';
        isian.type = terlihat ? 'text' : 'password';
        tombol.setAttribute('aria-pressed', String(terlihat));
        tombol.setAttribute('aria-label', terlihat ? keterangan.replace('Tampilkan', 'Sembunyikan') : keterangan);
    });
});

document.querySelectorAll('form:has([data-bagian-akun])').forEach((formulir) => {
    const perbaruiJenisAkun = () => {
        const jenisAkun = formulir.querySelector('input[name="role"]:checked')?.value;
        formulir.querySelectorAll('[data-bagian-akun]').forEach((bagian) => {
            const aktif = bagian.dataset.bagianAkun === jenisAkun;
            bagian.hidden = !aktif;
            bagian.disabled = !aktif;
            bagian.querySelectorAll('[data-wajib]').forEach((isian) => {
                isian.required = aktif && isian.dataset.wajib === 'true';
            });
        });
        formulir.querySelector('[data-label-nama]').textContent = jenisAkun === 'perusahaan' ? 'Nama perusahaan' : 'Nama lengkap';
        formulir.querySelector('#name').autocomplete = jenisAkun === 'perusahaan' ? 'organization' : 'name';
    };

    formulir.querySelectorAll('input[name="role"]').forEach((pilihan) => pilihan.addEventListener('change', perbaruiJenisAkun));
    window.addEventListener('pageshow', perbaruiJenisAkun);
    perbaruiJenisAkun();
});
document.querySelectorAll('[data-toggle-menu]').forEach((tombol) => {
    const panel = tombol.closest('[data-sidebar]');
    const menu = panel.querySelector('[data-menu-peran]');
    const label = tombol.querySelector('[data-sidebar-label]');

    if (panel.hasAttribute('data-menu-dropdown')) {
        const perbaruiDropdown = (terbuka) => {
            tombol.setAttribute('aria-expanded', String(terbuka));
            tombol.setAttribute('aria-label', terbuka ? 'Tutup navigasi' : 'Buka navigasi');
            label.textContent = tombol.getAttribute('aria-label');
            menu.hidden = !terbuka;
        };

        tombol.addEventListener('click', () => perbaruiDropdown(tombol.getAttribute('aria-expanded') !== 'true'));
        document.addEventListener('click', (kejadian) => {
            if (!panel.contains(kejadian.target)) {
                perbaruiDropdown(false);
            }
        });
        document.addEventListener('keydown', (kejadian) => {
            if (kejadian.key === 'Escape' && tombol.getAttribute('aria-expanded') === 'true') {
                perbaruiDropdown(false);
                tombol.focus();
            }
        });

        return;
    }

    const layarPonsel = window.matchMedia('(max-width: 780px)');

    const perbaruiMenu = (ditutup) => {
        tombol.setAttribute('aria-expanded', String(!ditutup));
        tombol.setAttribute('aria-label', ditutup ? 'Buka navigasi' : 'Tutup navigasi');
        label.textContent = tombol.getAttribute('aria-label');
        panel.toggleAttribute('data-collapsed', ditutup);
        menu.hidden = layarPonsel.matches && ditutup;
    };

    tombol.addEventListener('click', () => {
        perbaruiMenu(tombol.getAttribute('aria-expanded') === 'true');
    });

    layarPonsel.addEventListener('change', (kejadian) => perbaruiMenu(kejadian.matches));
    perbaruiMenu(layarPonsel.matches);
});


const ikatFormulirKirim = (akar = document) => {
    akar.querySelectorAll('form[data-kirim]').forEach((formulir) => {
        if (formulir.dataset.kirimTerikat === '1') {
            return;
        }
        formulir.dataset.kirimTerikat = '1';

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
};

ikatFormulirKirim();

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

const ikatNavigasiAkun = (akar = document) => {
    const navigasiAkun = akar.querySelector('[data-navigasi-akun]');
    if (!navigasiAkun || navigasiAkun.dataset.akunTerikat === '1') {
        return;
    }
    navigasiAkun.dataset.akunTerikat = '1';

    const tautanAkun = [...navigasiAkun.querySelectorAll('a[href^="#"]')];
    const panelAkun = [...akar.querySelectorAll('[data-panel-akun]')];

    const tampilkanBagianAkun = (hash, perbaruiAlamat = false) => {
        const bagianAktif = panelAkun.find((panel) => `#${panel.id}` === hash)
            ?? panelAkun.find((panel) => panel.classList.contains('bagian-pengaturan-validasi'))
            ?? panelAkun[0];

        if (!bagianAktif) {
            return;
        }

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
        tautan.addEventListener('click', (kejadian) => {
            kejadian.preventDefault();
            tampilkanBagianAkun(tautan.hash, true);
        });
    });
    window.addEventListener('hashchange', () => tampilkanBagianAkun(window.location.hash));
    window.addEventListener('pageshow', () => tampilkanBagianAkun(window.location.hash));
    tampilkanBagianAkun(window.location.hash, !window.location.hash);
};

ikatNavigasiAkun();

const ikatModalLowongan = (akar = document) => {
    const modalLowongan = akar.querySelector('#modal-lowongan');
    if (!(modalLowongan instanceof HTMLDialogElement) || modalLowongan.dataset.modalTerikat === '1') {
        return;
    }
    modalLowongan.dataset.modalTerikat = '1';

    const bukaModal = () => {
        modalLowongan.showModal();
    };

    akar.querySelectorAll('[data-buka-modal]').forEach((pemicu) => {
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
};

ikatModalLowongan();

const ikatLihatSandi = (akar = document) => {
    akar.querySelectorAll('[data-lihat-sandi]').forEach((tombol) => {
        if (tombol.dataset.sandiTerikat === '1') {
            return;
        }
        tombol.dataset.sandiTerikat = '1';

        const isian = document.getElementById(tombol.getAttribute('aria-controls'));
        const keterangan = tombol.getAttribute('aria-label') || 'Tampilkan kata sandi';
        if (!isian) {
            return;
        }

        tombol.hidden = false;
        tombol.addEventListener('click', () => {
            const terlihat = isian.type === 'password';
            isian.type = terlihat ? 'text' : 'password';
            tombol.setAttribute('aria-pressed', String(terlihat));
            tombol.setAttribute('aria-label', terlihat ? keterangan.replace('Tampilkan', 'Sembunyikan') : keterangan);
        });
    });
};

ikatLihatSandi();

document.querySelectorAll('select[data-kirim-otomatis]').forEach((saringan) => {
    saringan.addEventListener('change', () => saringan.form?.requestSubmit());
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

const formulirPencarian = document.querySelector('[data-pencarian-langsung]');

if (formulirPencarian) {
    const inputKataKunci = formulirPencarian.querySelector('#kata-kunci');
    const pilihProvinsi = formulirPencarian.querySelector('#pilih-provinsi');
    const pilihLokasi = formulirPencarian.querySelector('#pilih-lokasi');
    const pilihKategori = formulirPencarian.querySelector('#pilih-kategori');
    const inputTipeTersembunyi = formulirPencarian.querySelector('input[name="tipe"]');
    const inputGajiTersembunyi = formulirPencarian.querySelector('input[name="gaji_min"]');
    const inputUrutkanTersembunyi = formulirPencarian.querySelector('input[name="urutkan"]');
    const pilihTipe = document.querySelector('#pilih-tipe');
    const pilihGaji = document.querySelector('#pilih-gaji');
    const pilihUrutkan = document.querySelector('#pilih-urutkan');
    const formulirTipe = pilihTipe?.closest('form');
    const daftarLowonganEl = document.querySelector('[data-daftar-lowongan]');
    const jumlahHasilEl = document.querySelector('[data-jumlah-hasil]');
    const penyaringAktifEl = document.querySelector('[data-penyaring-aktif]');
    const paginasiEl = document.querySelector('.paginasi-katalog');

    const apiLowonganUrl = formulirPencarian.dataset.apiLowongan;
    const apiKabupatenUrl = formulirPencarian.dataset.apiKabupaten;
    const homeUrl = formulirPencarian.dataset.homeUrl;
    const bisaLamar = daftarLowonganEl?.dataset.bisaLamar === '1';
    const csrfToken = daftarLowonganEl?.dataset.csrf ?? '';
    const formatRupiah = new Intl.NumberFormat('id-ID');

    let pengontrolLowongan = null;
    let pengontrolWilayah = null;
    let pengaturWaktuKetik = null;

    const buatSvg = (atribut, jalur) => {
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('viewBox', '0 0 24 24');
        svg.setAttribute('fill', 'none');
        svg.setAttribute('stroke', 'currentColor');
        svg.setAttribute('stroke-width', '1.8');
        svg.setAttribute('aria-hidden', 'true');
        jalur.forEach(([tag, attrs]) => {
            const el = document.createElementNS('http://www.w3.org/2000/svg', tag);
            Object.entries(attrs).forEach(([k, v]) => el.setAttribute(k, v));
            svg.appendChild(el);
        });
        Object.entries(atribut).forEach(([k, v]) => svg.setAttribute(k, v));
        return svg;
    };

    const buatKartuLowongan = (item) => {
        const artikel = document.createElement('article');
        artikel.className = 'flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-5 transition hover:border-[#0888E8] hover:shadow-md';

        const atas = document.createElement('div');
        const identitas = document.createElement('div');
        identitas.className = 'flex min-w-0 items-start gap-3';

        const namaPerusahaan = item.perusahaan?.nama ?? '';
        const inisial = document.createElement('span');
        inisial.className = 'grid size-11 shrink-0 place-items-center rounded-full border border-slate-200 bg-slate-50 text-sm font-extrabold text-[#0888E8]';
        inisial.setAttribute('aria-hidden', 'true');
        inisial.textContent = namaPerusahaan.replace(/^PT\s+/i, '').slice(0, 1).toUpperCase();

        const info = document.createElement('div');
        info.className = 'min-w-0';
        const pPerusahaan = document.createElement('p');
        pPerusahaan.className = 'truncate text-xs font-medium text-[#0888E8]';
        pPerusahaan.textContent = namaPerusahaan;

        const h3 = document.createElement('h3');
        h3.className = 'mt-0.5 text-base font-bold leading-snug text-slate-900';
        if (item.detail_url) {
            const tautanJudul = document.createElement('a');
            tautanJudul.className = 'text-slate-900 hover:text-[#0888E8]';
            tautanJudul.href = item.detail_url;
            tautanJudul.textContent = item.judul;
            h3.appendChild(tautanJudul);
        } else {
            h3.textContent = item.judul;
        }

        const pLokasi = document.createElement('p');
        pLokasi.className = 'mt-1 flex items-center gap-1 truncate text-xs text-slate-500';
        pLokasi.appendChild(buatSvg({ class: 'size-3.5 shrink-0 text-slate-400' }, [
            ['path', { d: 'M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z' }],
            ['circle', { cx: '12', cy: '10', r: '3' }],
        ]));
        const spanLokasi = document.createElement('span');
        spanLokasi.className = 'truncate';
        spanLokasi.textContent = item.lokasi;
        pLokasi.appendChild(spanLokasi);

        info.append(pPerusahaan, h3, pLokasi);
        identitas.append(inisial, info);

        const rincian = document.createElement('div');
        rincian.className = 'mt-3.5 flex flex-wrap gap-1.5';
        const badgeTipe = document.createElement('span');
        badgeTipe.className = 'rounded-md bg-[#E6F2FE] px-2.5 py-1 text-xs font-medium text-slate-700';
        badgeTipe.textContent = item.tipe_kerja;
        const badgeKategori = document.createElement('span');
        badgeKategori.className = 'rounded-md bg-[#E6F2FE] px-2.5 py-1 text-xs font-medium text-slate-700';
        badgeKategori.textContent = item.kategori?.nama ?? '';
        rincian.append(badgeTipe, badgeKategori);
        if (item.sumber === 'remotive') {
            const badgeRemotive = document.createElement('span');
            badgeRemotive.className = 'rounded-md bg-[#E6F2FE] px-2.5 py-1 text-xs font-medium text-slate-700';
            badgeRemotive.textContent = 'Remotive';
            rincian.appendChild(badgeRemotive);
        }

        atas.append(identitas, rincian);

        const bagianBawah = document.createElement('div');
        bagianBawah.className = 'mt-4 flex items-center justify-between gap-2 border-t border-slate-100 pt-3.5 text-xs';

        const pGaji = document.createElement('p');
        pGaji.className = 'flex items-center gap-1.5 truncate font-semibold text-slate-700';
        pGaji.appendChild(buatSvg({ class: 'size-4 shrink-0 text-slate-400' }, [
            ['rect', { x: '3', y: '7', width: '18', height: '13', rx: '2' }],
            ['path', { d: 'M7 7V4h10v3M3 12h18M10 12v3h4v-3' }],
        ]));
        pGaji.appendChild(document.createTextNode(
            (item.gaji_min && item.gaji_max)
                ? `Rp ${formatRupiah.format(item.gaji_min)}–${formatRupiah.format(item.gaji_max)}`
                : 'Gaji sesuai ketentuan'
        ));

        const pWaktu = document.createElement('span');
        pWaktu.className = 'shrink-0 text-slate-400';
        pWaktu.textContent = item.diposting_relatif ?? (item.batas_lamaran_format ? `Batas ${item.batas_lamaran_format}` : 'Aktif');

        bagianBawah.append(pGaji, pWaktu);
        artikel.append(atas, bagianBawah);

        return artikel;
    };

    const kumpulkanParameter = () => {
        const params = new URLSearchParams();
        const kataKunci = inputKataKunci?.value.trim() ?? '';
        const provinsi = pilihProvinsi?.value ?? '';
        const lokasi = pilihLokasi?.value ?? '';
        const kategori = pilihKategori?.value ?? '';
        const tipe = pilihTipe?.value ?? inputTipeTersembunyi?.value ?? '';
        const gajiMin = pilihGaji?.value ?? inputGajiTersembunyi?.value ?? '';
        const urutkan = pilihUrutkan?.value ?? inputUrutkanTersembunyi?.value ?? '';

        if (kataKunci) {
            params.set('kata_kunci', kataKunci);
        }
        if (provinsi) {
            params.set('provinsi', provinsi);
        }
        if (lokasi) {
            params.set('lokasi', lokasi);
        }
        if (kategori) {
            params.set('kategori', kategori);
        }
        if (tipe) {
            params.set('tipe', tipe);
        }
        if (gajiMin) {
            params.set('gaji_min', gajiMin);
        }
        if (urutkan && urutkan !== 'terbaru') {
            params.set('urutkan', urutkan);
        }

        return params;
    };

    const perbaruiLowongan = async () => {
        if (!daftarLowonganEl || !apiLowonganUrl) {
            return;
        }

        const params = kumpulkanParameter();
        if (inputTipeTersembunyi && pilihTipe) {
            inputTipeTersembunyi.value = pilihTipe.value;
        }
        if (inputGajiTersembunyi && pilihGaji) {
            inputGajiTersembunyi.value = pilihGaji.value;
        }
        if (inputUrutkanTersembunyi && pilihUrutkan) {
            inputUrutkanTersembunyi.value = pilihUrutkan.value;
        }

        pengontrolLowongan?.abort();
        pengontrolLowongan = new AbortController();

        daftarLowonganEl.setAttribute('aria-busy', 'true');

        try {
            const url = `${apiLowonganUrl}?${params.toString()}`;
            const respons = await fetch(url, {
                headers: { Accept: 'application/json' },
                signal: pengontrolLowongan.signal,
            });

            if (!respons.ok) {
                return;
            }

            const hasil = await respons.json();
            const daftar = Array.isArray(hasil.data) ? hasil.data : [];
            const total = hasil.meta?.total ?? daftar.length;

            daftarLowonganEl.replaceChildren();

            if (daftar.length === 0) {
                const judulKosong = document.createElement('h3');
                judulKosong.textContent = 'Belum ada lowongan yang sesuai dengan pencarianmu.';
                const ketKosong = document.createElement('p');
                ketKosong.textContent = 'Coba ubah kata kunci atau filter untuk melihat lowongan lainnya.';
                const tautanSemua = document.createElement('a');
                tautanSemua.className = 'tombol tombol-sekunder';
                tautanSemua.href = homeUrl;
                tautanSemua.textContent = 'Lihat semua lowongan';
                daftarLowonganEl.append(judulKosong, ketKosong, tautanSemua);
            } else {
                daftar.forEach((item) => {
                    daftarLowonganEl.appendChild(buatKartuLowongan(item));
                });
            }

            if (jumlahHasilEl) {
                jumlahHasilEl.textContent = `${total} lowongan tersedia`;
            }

            if (penyaringAktifEl) {
                penyaringAktifEl.hidden = params.toString() === '';
            }

            if (paginasiEl) {
                paginasiEl.hidden = total <= daftar.length;
            }

            const alamatBaru = params.toString() ? `${homeUrl}?${params.toString()}` : homeUrl;
            window.history.replaceState(null, '', alamatBaru);
        } catch (kesalahan) {
            if (kesalahan.name !== 'AbortError') {
                formulirPencarian.submit();
            }
        } finally {
            daftarLowonganEl.removeAttribute('aria-busy');
        }
    };

    const muatKabupaten = async (provinsiId) => {
        if (!pilihLokasi || !apiKabupatenUrl) {
            return;
        }

        pengontrolWilayah?.abort();
        pengontrolWilayah = new AbortController();

        pilihLokasi.replaceChildren();
        const opsiAwal = document.createElement('option');
        opsiAwal.value = '';

        if (!provinsiId) {
            opsiAwal.textContent = 'Pilih kota/kabupaten';
            pilihLokasi.appendChild(opsiAwal);
            return;
        }

        opsiAwal.textContent = 'Memuat kota/kabupaten...';
        pilihLokasi.appendChild(opsiAwal);
        pilihLokasi.disabled = true;

        try {
            const respons = await fetch(`${apiKabupatenUrl}?provinsi_id=${encodeURIComponent(provinsiId)}`, {
                headers: { Accept: 'application/json' },
                signal: pengontrolWilayah.signal,
            });

            if (!respons.ok) {
                opsiAwal.textContent = 'Semua kota/kabupaten';
                return;
            }

            const hasil = await respons.json();
            const daftar = Array.isArray(hasil.data) ? hasil.data : [];

            pilihLokasi.replaceChildren();
            const opsiSemua = document.createElement('option');
            opsiSemua.value = '';
            opsiSemua.textContent = 'Semua kota/kabupaten';
            pilihLokasi.appendChild(opsiSemua);

            daftar.forEach((kab) => {
                const opsi = document.createElement('option');
                opsi.value = kab.nama;
                opsi.textContent = kab.nama;
                pilihLokasi.appendChild(opsi);
            });
        } catch (kesalahan) {
            if (kesalahan.name !== 'AbortError') {
                opsiAwal.textContent = 'Semua kota/kabupaten';
            }
        } finally {
            pilihLokasi.disabled = false;
        }
    };

    pilihProvinsi?.addEventListener('change', async () => {
        await muatKabupaten(pilihProvinsi.value);
        await perbaruiLowongan();
    });

    pilihLokasi?.addEventListener('change', perbaruiLowongan);
    pilihKategori?.addEventListener('change', perbaruiLowongan);
    pilihTipe?.addEventListener('change', perbaruiLowongan);
    pilihGaji?.addEventListener('change', perbaruiLowongan);
    pilihUrutkan?.addEventListener('change', perbaruiLowongan);

    inputKataKunci?.addEventListener('input', () => {
        window.clearTimeout(pengaturWaktuKetik);
        pengaturWaktuKetik = window.setTimeout(perbaruiLowongan, 300);
    });

    formulirPencarian.addEventListener('submit', (kejadian) => {
        kejadian.preventDefault();
        perbaruiLowongan();
    });

    formulirTipe?.addEventListener('submit', (kejadian) => {
        kejadian.preventDefault();
        perbaruiLowongan();
    });
}

// Instant Admin Navigation + Hover/Touch Prefetch + Collapsible Dock
(() => {
    if (!document.querySelector('[data-admin-shell]')) {
        return;
    }

    const kunciDock = 'admin_dock_open';

    const terapkanStatusDock = (akar = document) => {
        const terbuka = window.localStorage.getItem(kunciDock) === '1';
        const grid = akar.querySelector('[data-dock-grid]');
        if (!grid) {
            return;
        }

        grid.classList.toggle('lg:grid-cols-[56px_minmax(0,1fr)]', !terbuka);
        grid.classList.toggle('lg:grid-cols-[156px_minmax(0,1fr)]', terbuka);

        grid.querySelectorAll('[data-dock-capsule]').forEach((kapsul) => {
            kapsul.classList.toggle('rounded-full', !terbuka);
            kapsul.classList.toggle('rounded-3xl', terbuka);
        });

        grid.querySelectorAll('[data-dock-item]').forEach((item) => {
            item.classList.toggle('w-10', !terbuka);
            item.classList.toggle('justify-center', !terbuka);
            item.classList.toggle('w-full', terbuka);
            item.classList.toggle('justify-start', terbuka);
            item.classList.toggle('px-3', terbuka);
            item.classList.toggle('gap-2.5', terbuka);
        });

        grid.querySelectorAll('[data-dock-label]').forEach((label) => {
            label.classList.toggle('hidden', !terbuka);
        });

        const tombolToggle = grid.querySelector('[data-toggle-dock]');
        const ikonPanah = grid.querySelector('[data-dock-chevron]');
        if (tombolToggle) {
            tombolToggle.setAttribute('aria-expanded', String(terbuka));
        }
        if (ikonPanah) {
            ikonPanah.classList.toggle('rotate-180', terbuka);
        }
    };

    terapkanStatusDock();

    document.addEventListener('click', (kejadian) => {
        const tombolToggle = kejadian.target?.closest?.('[data-toggle-dock]');
        if (!tombolToggle) {
            return;
        }
        const sekarangTerbuka = window.localStorage.getItem(kunciDock) === '1';
        window.localStorage.setItem(kunciDock, sekarangTerbuka ? '0' : '1');
        terapkanStatusDock();
    });

    const cacheHalaman = new Map();
    const sedangAmbil = new Map();
    const masaCacheMs = 15000;
    let pengontrolNavigasi = null;

    const bisaNavigasiCepat = (tautan) => {
        if (!(tautan instanceof HTMLAnchorElement)) {
            return false;
        }
        if (tautan.target && tautan.target !== '_self') {
            return false;
        }
        if (tautan.hasAttribute('download')) {
            return false;
        }
        const href = tautan.getAttribute('href') || '';
        if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) {
            return false;
        }
        const url = new URL(tautan.href, window.location.origin);
        if (url.origin !== window.location.origin) {
            return false;
        }
        if (url.pathname.includes('/legalitas')) {
            return false;
        }
        return true;
    };

    const ambilHtml = async (url, signal) => {
        const sekarang = Date.now();
        const tersimpan = cacheHalaman.get(url);
        if (tersimpan && sekarang - tersimpan.waktu < masaCacheMs) {
            return tersimpan.html;
        }

        if (!signal && sedangAmbil.has(url)) {
            return sedangAmbil.get(url);
        }

        const janji = fetch(url, {
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal,
        })
            .then(async (respons) => {
                if (!respons.ok || respons.redirected) {
                    const tujuan = respons.redirected ? respons.url : null;
                    return { redirect: tujuan };
                }
                const tipe = respons.headers.get('content-type') || '';
                if (!tipe.includes('text/html')) {
                    return { redirect: url };
                }
                const html = await respons.text();
                cacheHalaman.set(url, { html, waktu: Date.now() });
                return html;
            })
            .finally(() => {
                sedangAmbil.delete(url);
            });

        if (!signal) {
            sedangAmbil.set(url, janji);
        }

        return janji;
    };

    const muatHalamanAdmin = async (url, tambahRiwayat = true) => {
        const shellSekarang = document.querySelector('[data-admin-shell]');
        if (!shellSekarang) {
            window.location.href = url;
            return;
        }

        pengontrolNavigasi?.abort();
        pengontrolNavigasi = new AbortController();

        try {
            const hasil = await ambilHtml(url, pengontrolNavigasi.signal);
            if (typeof hasil !== 'string') {
                window.location.href = hasil?.redirect || url;
                return;
            }

            const dokumenBaru = new DOMParser().parseFromString(hasil, 'text/html');
            const shellBaru = dokumenBaru.querySelector('[data-admin-shell]');
            if (!shellBaru) {
                window.location.href = url;
                return;
            }

            document.title = dokumenBaru.title || document.title;
            shellSekarang.replaceWith(shellBaru);
            ikatFormulirKirim(shellBaru);
            ikatNavigasiAkun(shellBaru);
            ikatModalLowongan(shellBaru);
            ikatLihatSandi(shellBaru);
            terapkanStatusDock(shellBaru);
            prefetchSemuaMenuDock();

            if (tambahRiwayat && window.location.href !== url) {
                window.history.pushState({ adminInstant: true }, '', url);
            }

            window.scrollTo({ top: 0, behavior: 'instant' });
        } catch (kesalahan) {
            if (kesalahan.name !== 'AbortError') {
                window.location.href = url;
            }
        }
    };

    const prefetchSemuaMenuDock = () => {
        document.querySelectorAll('[data-dock-aside] a[href]').forEach((tautan) => {
            if (bisaNavigasiCepat(tautan) && tautan.href !== window.location.href) {
                ambilHtml(tautan.href).catch(() => {});
            }
        });
    };

    window.setTimeout(prefetchSemuaMenuDock, 150);

    const prefetchTautan = (kejadian) => {
        const tautan = kejadian.target?.closest?.('[data-admin-shell] a[href]');
        if (!tautan || !bisaNavigasiCepat(tautan)) {
            return;
        }
        ambilHtml(tautan.href).catch(() => {});
    };

    document.addEventListener('pointerenter', prefetchTautan, true);
    document.addEventListener('touchstart', prefetchTautan, { passive: true, capture: true });

    document.addEventListener('click', (kejadian) => {
        if (kejadian.defaultPrevented || kejadian.button !== 0 || kejadian.metaKey || kejadian.ctrlKey || kejadian.shiftKey || kejadian.altKey) {
            return;
        }
        const tautan = kejadian.target?.closest?.('[data-admin-shell] a[href]');
        if (!tautan || !bisaNavigasiCepat(tautan)) {
            return;
        }
        kejadian.preventDefault();
        muatHalamanAdmin(tautan.href, true);
    });

    document.addEventListener('submit', (kejadian) => {
        const formulir = kejadian.target;
        if (!formulir?.closest?.('[data-admin-shell]')) {
            return;
        }
        if ((formulir.method || 'GET').toUpperCase() === 'GET') {
            kejadian.preventDefault();
            const aksi = formulir.getAttribute('action') || window.location.pathname;
            const url = new URL(aksi, window.location.origin);
            const data = new FormData(formulir);
            url.search = '';
            data.forEach((nilai, kunci) => {
                if (typeof nilai === 'string' && nilai !== '') {
                    url.searchParams.append(kunci, nilai);
                }
            });
            muatHalamanAdmin(url.toString(), true);
            return;
        }
        cacheHalaman.clear();
    });

    window.addEventListener('popstate', () => {
        if (document.querySelector('[data-admin-shell]')) {
            muatHalamanAdmin(window.location.href, false);
        }
    });

    window.bukaDetailLokerAdmin = (data) => {
        const modal = document.getElementById('modal-detail-loker-admin');
        if (!modal) {
            return;
        }
        document.getElementById('modal-loker-inisial').textContent = data.inisial || 'L';
        document.getElementById('modal-loker-judul').textContent = data.judul || '';
        document.getElementById('modal-loker-perusahaan').textContent = data.perusahaan || '';
        document.getElementById('modal-loker-tipe').textContent = data.tipe || '';
        document.getElementById('modal-loker-kategori').textContent = data.kategori || '';
        document.getElementById('modal-loker-lokasi').textContent = data.lokasi || '';
        document.getElementById('modal-loker-gaji').textContent = data.gaji || '';
        document.getElementById('modal-loker-batas').textContent = data.batas || '';
        document.getElementById('modal-loker-waktu').textContent = data.waktu || '';
        document.getElementById('modal-loker-deskripsi').textContent = data.deskripsi || 'Tidak ada deskripsi.';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.tutupDetailLokerAdmin = () => {
        const modal = document.getElementById('modal-detail-loker-admin');
        if (!modal) {
            return;
        }
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };
})();


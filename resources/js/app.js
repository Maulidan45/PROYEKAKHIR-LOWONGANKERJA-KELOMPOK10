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
        artikel.className = 'kartu-lowongan kartu-lowongan-publik';

        const identitas = document.createElement('div');
        identitas.className = 'identitas-lowongan';

        const namaPerusahaan = item.perusahaan?.nama ?? '';
        const inisial = document.createElement('span');
        inisial.className = 'inisial-perusahaan';
        inisial.setAttribute('aria-hidden', 'true');
        inisial.textContent = namaPerusahaan.replace(/^PT\s+/i, '').slice(0, 1).toUpperCase();

        const info = document.createElement('div');
        const pPerusahaan = document.createElement('p');
        pPerusahaan.className = 'perusahaan-lowongan';
        pPerusahaan.textContent = namaPerusahaan;

        const h3 = document.createElement('h3');
        if (item.detail_url) {
            const tautanJudul = document.createElement('a');
            tautanJudul.className = 'tautan-lowongan';
            tautanJudul.href = item.detail_url;
            tautanJudul.textContent = item.judul;
            h3.appendChild(tautanJudul);
        } else {
            h3.textContent = item.judul;
        }

        const pLokasi = document.createElement('p');
        pLokasi.className = 'lokasi-lowongan';
        pLokasi.appendChild(buatSvg({}, [
            ['path', { d: 'M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z' }],
            ['circle', { cx: '12', cy: '10', r: '3' }],
        ]));
        pLokasi.appendChild(document.createTextNode(item.lokasi));

        info.append(pPerusahaan, h3, pLokasi);
        identitas.append(inisial, info);

        const rincian = document.createElement('ul');
        rincian.className = 'rincian-lowongan';
        rincian.setAttribute('aria-label', 'Informasi posisi');
        const liTipe = document.createElement('li');
        liTipe.textContent = item.tipe_kerja;
        const liKategori = document.createElement('li');
        liKategori.textContent = item.kategori?.nama ?? '';
        rincian.append(liTipe, liKategori);

        const bagianBawah = document.createElement('div');
        bagianBawah.className = 'bagian-bawah-lowongan';

        const pGaji = document.createElement('p');
        pGaji.className = 'gaji-lowongan';
        pGaji.appendChild(buatSvg({}, [
            ['rect', { x: '3', y: '7', width: '18', height: '13', rx: '2' }],
            ['path', { d: 'M7 7V4h10v3M3 12h18M10 12v3h4v-3' }],
        ]));
        pGaji.appendChild(document.createTextNode(`Rp ${formatRupiah.format(item.gaji_min)}–${formatRupiah.format(item.gaji_max)}`));
        const periode = document.createElement('span');
        periode.className = 'periode-gaji';
        periode.textContent = '/ bulan';
        pGaji.appendChild(periode);

        const batas = document.createElement('span');
        batas.className = 'kode-lowongan';
        batas.textContent = item.diposting_relatif ?? `Batas ${item.batas_lamaran_format ?? item.batas_lamaran}`;

        bagianBawah.append(pGaji, batas);
        artikel.append(identitas, rincian, bagianBawah);

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


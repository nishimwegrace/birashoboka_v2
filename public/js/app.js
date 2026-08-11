/* Minimal SPA using fetch to call existing API endpoints
 - Routes: /, /about, /programs, /program/:id, /posts, /posts/:id, /partners, /contact
 - Uses simple client-side routing with hash
*/

const API_BASE = window.__API_BASE__ || '/api';

function el(tag, cls, text) {
  const e = document.createElement(tag);
  if (cls) e.className = cls;
  if (text) e.textContent = text;
  return e;
}

async function fetchJson(path) {
  const res = await fetch(API_BASE + path);
  if (!res.ok) throw new Error(res.status + ' ' + res.statusText);
  return res.json();
}

function formatCount(value) {
  return value?.toLocaleString?.() || 0;
}

function closeMobileMenu() {
  const off = document.getElementById('offcanvas');
  const overlay = document.getElementById('pageOverlay');
  off?.classList.remove('translate-x-0');
  off?.classList.add('translate-x-full');
  overlay?.classList.add('hidden');
}

function openMobileMenu() {
  const off = document.getElementById('offcanvas');
  const overlay = document.getElementById('pageOverlay');
  off?.classList.remove('translate-x-full');
  off?.classList.add('translate-x-0');
  overlay?.classList.remove('hidden');
}

function navigateTo(hash) {
  location.hash = hash;
}

function renderHome() {
  const container = el('div', 'space-y-16');

  const banner = el('section', 'grid gap-6 rounded-[32px] bg-slate-900 px-6 py-14 text-white shadow-xl sm:grid-cols-[1.1fr_0.9fr] sm:items-center sm:px-14');
  banner.innerHTML = `
    <div>
      <span class="inline-flex rounded-full bg-sky-500/20 px-3 py-1 text-sm font-semibold text-sky-200">Accueil</span>
      <h1 class="mt-6 text-4xl font-semibold leading-tight sm:text-5xl">Birashoboka aide les jeunes et les familles à réussir.</h1>
      <p class="mt-4 max-w-2xl text-slate-200">Programmes d'accompagnement, formations et actualités pour amplifier l'impact social.</p>
      <div class="mt-8 flex flex-col gap-3 sm:flex-row">
        <a href="#/about" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-900 shadow hover:bg-slate-100">En savoir plus</a>
        <a href="#/programs" class="inline-flex items-center justify-center rounded-full border border-white/20 bg-white/10 px-6 py-3 text-sm font-semibold text-white hover:bg-white/10">Voir les programmes</a>
      </div>
    </div>
    <div class="rounded-[28px] bg-slate-800/80 p-6 text-slate-100 shadow-xl">
      <p class="text-sm uppercase tracking-[0.24em] text-sky-300">Nouveautés</p>
      <h2 class="mt-4 text-2xl font-semibold">Formation en cours</h2>
      <p class="mt-3 text-slate-300">Appel à candidatures pour les prochaines sessions de renforcement de compétences.</p>
      <div class="mt-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-3xl bg-slate-900/70 p-4">
          <p class="text-xs uppercase text-slate-400">Candidature</p>
          <p class="mt-2 text-lg font-semibold">En cours</p>
        </div>
        <div class="rounded-3xl bg-slate-900/70 p-4">
          <p class="text-xs uppercase text-slate-400">Prochaine session</p>
          <p class="mt-2 text-lg font-semibold">Bientôt ouverte</p>
        </div>
      </div>
    </div>
  `;
  container.appendChild(banner);

  const about = el('section', 'grid gap-6 rounded-[32px] bg-white px-6 py-10 shadow-sm sm:grid-cols-[0.9fr_0.8fr] sm:px-10');
  about.innerHTML = `
    <div>
      <p class="text-sm font-semibold uppercase tracking-[0.3em] text-sky-500">À propos</p>
      <h2 class="mt-4 text-3xl font-semibold text-slate-900">Nous construisons des réussites concrètes.</h2>
      <p class="mt-4 text-slate-600">Birashoboka accompagne les jeunes, les femmes et les communautés à travers des programmes pratiques, des formations et un appui structuré.</p>
      <p class="mt-4 text-slate-600">Notre approche est basée sur l'inclusion, l'excellence et la collaboration avec les partenaires locaux.</p>
      <a href="#/about" class="mt-6 inline-flex items-center rounded-full bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-sky-700">Lire notre histoire</a>
    </div>
    <div class="grid gap-4">
      <div class="rounded-3xl bg-slate-50 p-6">
        <p class="text-sm uppercase tracking-[0.24em] text-slate-400">Notre vision</p>
        <p class="mt-3 text-slate-700">Un Rwanda où chaque jeune et chaque femme a les moyens de réussir.</p>
      </div>
      <div class="rounded-3xl bg-slate-50 p-6">
        <p class="text-sm uppercase tracking-[0.24em] text-slate-400">Notre mission</p>
        <p class="mt-3 text-slate-700">Renforcer les capacités locales par l'éducation, la formation et les opportunités d'insertion.</p>
      </div>
    </div>
  `;
  container.appendChild(about);

  const postsSection = el('section', 'space-y-6');
  postsSection.innerHTML = '<div class="flex items-center justify-between"><h2 class="text-2xl font-semibold text-slate-900">Actualités récentes</h2><a href="#/posts" class="text-sm font-semibold text-sky-600 hover:text-sky-700">Voir tout</a></div><div id="postsGrid" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">Chargement...</div>';
  container.appendChild(postsSection);

  fetchJson('/posts')
    .then(response => {
      const grid = document.getElementById('postsGrid');
      grid.innerHTML = '';
      const items = response.data?.items || [];
      items.slice(0, 6).forEach(post => {
        const card = el('article', 'group overflow-hidden rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md');
        card.innerHTML = `<div class="mb-4 inline-flex rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-sky-700">${post.volet?.name || 'Autres'}</div><h3 class="text-xl font-semibold text-slate-900">${post.title || ''}</h3><p class="mt-3 text-slate-600">${(post.description || '').slice(0, 130)}</p>`;
        card.addEventListener('click', () => navigateTo('#/posts/' + post.id));
        grid.appendChild(card);
      });
    })
    .catch(() => {
      const grid = document.getElementById('postsGrid');
      if (grid) grid.textContent = 'Impossible de charger les actualités.';
    });

  const impact = el('section', 'grid gap-6 rounded-[32px] bg-white px-6 py-10 shadow-sm sm:grid-cols-3 sm:px-10');
  impact.innerHTML = `
    <div class="rounded-[28px] bg-slate-50 p-6 text-center">
      <p class="text-sm uppercase tracking-[0.24em] text-slate-400">Programmes</p>
      <p id="programCount" class="mt-4 text-4xl font-semibold text-slate-900">...</p>
    </div>
    <div class="rounded-[28px] bg-slate-50 p-6 text-center">
      <p class="text-sm uppercase tracking-[0.24em] text-slate-400">Participants</p>
      <p id="peopleCount" class="mt-4 text-4xl font-semibold text-slate-900">...</p>
    </div>
    <div class="rounded-[28px] bg-slate-50 p-6 text-center">
      <p class="text-sm uppercase tracking-[0.24em] text-slate-400">Partenaires</p>
      <p id="partnerCount" class="mt-4 text-4xl font-semibold text-slate-900">...</p>
    </div>
  `;
  container.appendChild(impact);

  Promise.all([fetchJson('/volets').catch(() => null), fetchJson('/inscriptions').catch(() => null), fetchJson('/partners').catch(() => null)])
    .then(([volets, inscriptions, partners]) => {
      const programCount = document.getElementById('programCount');
      const peopleCount = document.getElementById('peopleCount');
      const partnerCount = document.getElementById('partnerCount');
      if (programCount) programCount.textContent = formatCount(volets?.data?.pagination?.total || 0);
      if (peopleCount) peopleCount.textContent = formatCount(inscriptions?.data?.pagination?.total || 0);
      if (partnerCount) partnerCount.textContent = formatCount(partners?.data?.pagination?.total || 0);
    });

  const testimonials = el('section', 'space-y-6');
  testimonials.innerHTML = '<h2 class="text-2xl font-semibold text-slate-900">Témoignages</h2><div id="testiList" class="grid gap-5 md:grid-cols-2">Chargement...</div>';
  container.appendChild(testimonials);

  fetchJson('/testimonials')
    .then(response => {
      const wrap = document.getElementById('testiList');
      if (!wrap) return;
      wrap.innerHTML = '';
      (response.data?.items || []).slice(0, 4).forEach(item => {
        const block = el('blockquote', 'rounded-[28px] border border-slate-200 bg-white p-6 text-slate-700 shadow-sm');
        block.textContent = item.content || '';
        wrap.appendChild(block);
      });
    })
    .catch(() => {
      const wrap = document.getElementById('testiList');
      if (wrap) wrap.textContent = 'Aucun témoignage disponible.';
    });

  const trending = el('section', 'space-y-6');
  trending.innerHTML = '<div class="flex items-center justify-between"><h2 class="text-2xl font-semibold text-slate-900">Évènements en cours</h2><a href="#/programs" class="text-sm font-semibold text-sky-600 hover:text-sky-700">Voir les programmes</a></div><div id="trendingList" class="grid gap-5 md:grid-cols-2">Chargement...</div>';
  container.appendChild(trending);

  fetchJson('/campaigns')
    .then(response => {
      const list = document.getElementById('trendingList');
      if (!list) return;
      list.innerHTML = '';
      (response.data?.items || []).slice(0, 4).forEach(campaign => {
        const card = el('div', 'rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm');
        card.innerHTML = `<p class="text-sm uppercase tracking-[0.24em] text-sky-500">Évènement</p><h3 class="mt-3 text-xl font-semibold text-slate-900">${campaign.title || ''}</h3><p class="mt-3 text-slate-600">${(campaign.description || '').slice(0, 120)}</p>`;
        list.appendChild(card);
      });
    })
    .catch(() => {
      const list = document.getElementById('trendingList');
      if (list) list.textContent = 'Aucun évènement trouvé.';
    });

  const partners = el('section', 'space-y-6');
  partners.innerHTML = '<h2 class="text-2xl font-semibold text-slate-900">Partenaires</h2><div id="partnersList" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">Chargement...</div>';
  container.appendChild(partners);

  fetchJson('/partners')
    .then(response => {
      const list = document.getElementById('partnersList');
      if (!list) return;
      list.innerHTML = '';
      (response.data?.items || []).forEach(partner => {
        const card = el('div', 'rounded-[28px] border border-slate-200 bg-white p-6 text-slate-700 shadow-sm');
        card.textContent = partner.name || '';
        list.appendChild(card);
      });
    })
    .catch(() => {
      const list = document.getElementById('partnersList');
      if (list) list.textContent = 'Aucun partenaire trouvé.';
    });

  return container;
}

function renderAbout() {
  const section = el('section', 'space-y-10');
  section.innerHTML = `
    <div class="space-y-4 rounded-[32px] bg-white p-8 shadow-sm">
      <div class="text-sm font-semibold uppercase tracking-[0.3em] text-sky-600">A propos</div>
      <h1 class="text-4xl font-semibold text-slate-900">Notre histoire et nos ambitions</h1>
      <p class="max-w-3xl text-slate-600">Birashoboka Center est né pour répondre aux besoins des jeunes et des femmes dans notre communauté. Nous proposons des solutions concrètes, des programmes de formation et un environnement de partage pour construire un avenir plus inclusif.</p>
    </div>
    <div class="grid gap-6 lg:grid-cols-2">
      <div class="rounded-[32px] bg-white p-8 shadow-sm">
        <h2 class="text-2xl font-semibold text-slate-900">Vision</h2>
        <p class="mt-4 text-slate-600">Un Rwanda solidaire où chaque participant trouve des opportunités de développement et de réussite.</p>
      </div>
      <div class="rounded-[32px] bg-white p-8 shadow-sm">
        <h2 class="text-2xl font-semibold text-slate-900">Mission</h2>
        <p class="mt-4 text-slate-600">Offrir des programmes éducatifs, des formations professionnelles et un accompagnement durable à ceux qui veulent progresser.</p>
      </div>
    </div>
    <div class="grid gap-6 lg:grid-cols-2">
      <div class="rounded-[32px] bg-white p-8 shadow-sm">
        <h2 class="text-2xl font-semibold text-slate-900">Historique</h2>
        <p class="mt-4 text-slate-600">Depuis sa création, Birashoboka a accompagné des centaines de bénéficiaires dans plusieurs volets d'action, en renforçant leurs capacités et en facilitant l'accès aux partenaires.</p>
      </div>
      <div class="rounded-[32px] bg-white p-8 shadow-sm">
        <h2 class="text-2xl font-semibold text-slate-900">Direction</h2>
        <p class="mt-4 text-slate-600">Notre board de direction rassemble des experts locaux engagés, des formateurs et des partenaires qui veillent à l'impact social de nos programmes.</p>
      </div>
    </div>
  `;
  return section;
}

function renderPrograms() {
  const wrapper = el('section', 'space-y-8');
  wrapper.innerHTML = `
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-sky-600">Programmes</p>
        <h1 class="mt-3 text-4xl font-semibold text-slate-900">Nos volets d'accompagnement</h1>
      </div>
      <p class="text-slate-600 max-w-xl">Découvrez les programmes développés pour chaque groupe, avec des outils pratiques et des activités adaptées.</p>
    </div>
    <div id="voletsList" class="grid gap-6 md:grid-cols-2 xl:grid-cols-3"></div>
  `;

  fetchJson('/volets')
    .then(response => {
      const list = document.getElementById('voletsList');
      if (!list) return;
      list.innerHTML = '';
      (response.data?.items || []).forEach(volet => {
        const card = el('button', 'group flex h-full flex-col rounded-[32px] border border-slate-200 bg-white p-6 text-left shadow-sm transition hover:-translate-y-1 hover:border-sky-300 hover:bg-sky-50');
        card.innerHTML = `<span class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">${volet.place || 'Programme'}</span><h2 class="mt-4 text-2xl font-semibold text-slate-900">${volet.name || ''}</h2><p class="mt-3 text-slate-600">${(volet.subtitle || volet.slogan || '').slice(0, 100)}</p><p class="mt-4 text-slate-500">${(volet.description || '').slice(0, 120)}</p>`;
        card.addEventListener('click', () => navigateTo('#/program/' + volet.id));
        list.appendChild(card);
      });
    })
    .catch(() => {
      const list = document.getElementById('voletsList');
      if (list) list.textContent = 'Impossible de charger les programmes.';
    });

  return wrapper;
}

function renderVoletDetail(id) {
  const section = el('section', 'space-y-10');
  section.innerHTML = '<div class="rounded-[32px] bg-white p-10 shadow-sm">Chargement...</div>';

  fetchJson('/volets/' + id)
    .then(response => {
      const volet = response.data;
      section.innerHTML = `
        <div class="grid gap-10 lg:grid-cols-[0.9fr_0.7fr]">
          <div class="space-y-6 rounded-[32px] bg-white p-10 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-sky-600">Programme</p>
            <h1 class="text-4xl font-semibold text-slate-900">${volet.name || ''}</h1>
            <p class="text-xl font-medium text-slate-700">${volet.subtitle || volet.slogan || ''}</p>
            <p class="text-slate-600">${volet.description || 'Description à venir.'}</p>
            <div class="grid gap-4 sm:grid-cols-2">
              <div class="rounded-3xl bg-slate-50 p-6">
                <h3 class="font-semibold text-slate-900">Cible</h3>
                <p class="mt-2 text-slate-600">${volet.target || 'Tous'}</p>
              </div>
              <div class="rounded-3xl bg-slate-50 p-6">
                <h3 class="font-semibold text-slate-900">Zone</h3>
                <p class="mt-2 text-slate-600">${volet.place || 'Non défini'}</p>
              </div>
            </div>
          </div>
          <div class="space-y-6">
            <div class="rounded-[32px] bg-white p-8 shadow-sm">
              <h2 class="text-xl font-semibold text-slate-900">Activités proposées</h2>
              <ul class="mt-4 space-y-3 text-slate-600">${(volet.activities || []).map(activity => `<li class="rounded-2xl bg-slate-50 p-4">${activity.title}</li>`).join('') || '<li>Aucune activité enregistrée.</li>'}</ul>
            </div>
            <div class="rounded-[32px] bg-white p-8 shadow-sm">
              <h2 class="text-xl font-semibold text-slate-900">Actualités du programme</h2>
              <div id="voletPosts" class="mt-4 space-y-4">Chargement...</div>
            </div>
          </div>
        </div>
      `;
      return fetchJson('/volets/' + id + '/posts');
    })
    .then(response => {
      const postsNode = document.getElementById('voletPosts');
      if (!postsNode) return;
      postsNode.innerHTML = '';
      const posts = response.data?.items || [];
      if (!posts.length) {
        postsNode.innerHTML = '<div class="text-slate-500">Aucune actualité pour ce programme.</div>';
        return;
      }
      posts.forEach(post => {
        const card = el('div', 'rounded-3xl bg-slate-50 p-4');
        card.innerHTML = `<h3 class="text-lg font-semibold text-slate-900">${post.title}</h3><p class="mt-2 text-slate-600">${(post.description || '').slice(0, 120)}</p>`;
        card.addEventListener('click', () => navigateTo('#/posts/' + post.id));
        postsNode.appendChild(card);
      });
    })
    .catch(() => {
      const postsNode = document.getElementById('voletPosts');
      if (postsNode) postsNode.textContent = 'Impossible de charger les actualités.';
    });

  return section;
}

function renderPosts() {
  const wrapper = el('section', 'space-y-8');
  wrapper.innerHTML = `
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-sky-600">A la une</p>
        <h1 class="mt-3 text-4xl font-semibold text-slate-900">Actualités et publications</h1>
      </div>
      <div class="flex flex-wrap gap-3">
        <select id="postFilter" class="rounded-full border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-sky-500 focus:outline-none">
          <option value="">Tous les programmes</option>
        </select>
      </div>
    </div>
    <div id="postsGrid" class="grid gap-6 lg:grid-cols-2"></div>
  `;

  let allPosts = [];

  const renderList = (posts) => {
    const grid = document.getElementById('postsGrid');
    if (!grid) return;
    grid.innerHTML = '';
    if (!posts.length) {
      grid.textContent = 'Aucun article trouvé.';
      return;
    }
    posts.forEach(post => {
      const card = el('article', 'rounded-[32px] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md');
      card.innerHTML = `
        <div class="mb-4 inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-slate-600">${post.volet?.name || 'Autre'}</div>
        <h2 class="text-2xl font-semibold text-slate-900">${post.title}</h2>
        <p class="mt-3 text-slate-600">${(post.description || '').slice(0, 180)}</p>
      `;
      card.addEventListener('click', () => navigateTo('#/posts/' + post.id));
      grid.appendChild(card);
    });
  };

  Promise.all([fetchJson('/posts'), fetchJson('/volets')])
    .then(([postsResponse, voletsResponse]) => {
      allPosts = postsResponse.data?.items || [];
      const filter = document.getElementById('postFilter');
      const volets = voletsResponse.data?.items || [];
      volets.forEach(volet => {
        const option = document.createElement('option');
        option.value = volet.id;
        option.textContent = volet.name;
        filter.appendChild(option);
      });
      filter.addEventListener('change', () => {
        const value = filter.value;
        if (!value) {
          renderList(allPosts);
          return;
        }
        renderList(allPosts.filter(post => String(post.volet_id) === String(value) || String(post.volet?.id) === String(value)));
      });
      renderList(allPosts);
    })
    .catch(() => {
      const grid = document.getElementById('postsGrid');
      if (grid) grid.textContent = 'Impossible de charger les posts.';
    });

  return wrapper;
}

function renderPostDetail(id) {
  const section = el('section', 'space-y-8');
  section.innerHTML = '<div class="rounded-[32px] bg-white p-10 shadow-sm">Chargement...</div>';

  fetchJson('/posts/' + id)
    .then(response => {
      const post = response.data;
      section.innerHTML = `
        <div class="grid gap-10 lg:grid-cols-[0.9fr_0.6fr]">
          <div class="space-y-6 rounded-[32px] bg-white p-10 shadow-sm">
            <h1 class="text-4xl font-semibold text-slate-900">${post.title || ''}</h1>
            <div class="rounded-[28px] bg-slate-50 p-6 text-slate-700">${post.description || ''}</div>
          </div>
          <aside class="space-y-6 rounded-[32px] bg-white p-8 shadow-sm">
            <div>
              <p class="text-sm uppercase tracking-[0.24em] text-slate-400">Programme</p>
              <p class="mt-3 text-xl font-semibold text-slate-900">${post.volet?.name || 'Non défini'}</p>
            </div>
            <div>
              <p class="text-sm uppercase tracking-[0.24em] text-slate-400">Description</p>
              <p class="mt-3 text-slate-600">${(post.volet?.description || '').slice(0, 120) || 'Aucune description de programme.'}</p>
            </div>
          </aside>
        </div>
      `;
    })
    .catch(() => {
      section.innerHTML = '<div class="rounded-[32px] bg-white p-10 shadow-sm text-slate-700">Article introuvable.</div>';
    });

  return section;
}

function renderPartners() {
  const section = el('section', 'space-y-8');
  section.innerHTML = `
    <div>
      <p class="text-sm font-semibold uppercase tracking-[0.3em] text-sky-600">Partenaires</p>
      <h1 class="mt-3 text-4xl font-semibold text-slate-900">Nos partenaires</h1>
      <p class="max-w-3xl text-slate-600">Nous travaillons avec des organisations et des structures engagées pour enrichir nos programmes.</p>
    </div>
    <div id="partnersGrid" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"></div>
  `;

  fetchJson('/partners')
    .then(response => {
      const grid = document.getElementById('partnersGrid');
      if (!grid) return;
      grid.innerHTML = '';
      (response.data?.items || []).forEach(partner => {
        const lines = [`<p class="text-lg font-semibold text-slate-900">${partner.name || ''}</p>`];
        if (partner.volet?.name) {
          lines.push(`<p class="mt-3 text-slate-600">Volet: ${partner.volet.name}</p>`);
        }
        const card = el('div', 'rounded-[32px] border border-slate-200 bg-white p-6 shadow-sm');
        card.innerHTML = lines.join('');
        grid.appendChild(card);
      });
    })
    .catch(() => {
      const grid = document.getElementById('partnersGrid');
      if (grid) grid.textContent = 'Impossible de charger les partenaires.';
    });

  return section;
}

function renderContact() {
  const section = el('section', 'space-y-8');
  section.innerHTML = `
    <div class="rounded-[32px] bg-white p-10 shadow-sm">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <p class="text-sm font-semibold uppercase tracking-[0.3em] text-sky-600">Contact</p>
          <h1 class="mt-3 text-4xl font-semibold text-slate-900">Restons en contact</h1>
        </div>
      </div>
      <p class="mt-4 max-w-3xl text-slate-600">Envoyez-nous un message et nous reviendrons vers vous bientôt. Le formulaire sera bientôt connecté au backend.</p>
      <div class="mt-8 space-y-4">
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
          <p class="font-semibold text-slate-900">Email</p>
          <p class="mt-2 text-slate-600">contact@birashobokacenter.org</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
          <p class="font-semibold text-slate-900">Téléphone</p>
          <p class="mt-2 text-slate-600">+250 78 123 4567</p>
        </div>
      </div>
    </div>
  `;
  return section;
}

function router() {
  const hash = location.hash || '#/';
  const main = document.getElementById('app');
  main.innerHTML = '';
  const parts = hash.slice(2).split('/').filter(Boolean);

  if (parts.length === 0) {
    main.appendChild(renderHome());
  } else if (parts[0] === 'about') {
    main.appendChild(renderAbout());
  } else if (parts[0] === 'programs') {
    main.appendChild(renderPrograms());
  } else if (parts[0] === 'program' && parts[1]) {
    main.appendChild(renderVoletDetail(parts[1]));
  } else if (parts[0] === 'posts' && parts[1]) {
    main.appendChild(renderPostDetail(parts[1]));
  } else if (parts[0] === 'posts') {
    main.appendChild(renderPosts());
  } else if (parts[0] === 'partners') {
    main.appendChild(renderPartners());
  } else if (parts[0] === 'contact') {
    main.appendChild(renderContact());
  } else {
    main.innerHTML = '<div class="rounded-[32px] bg-white p-10 shadow-sm text-slate-700">Page introuvable.</div>';
  }
}

function bindMobileMenu() {
  const navToggle = document.getElementById('navToggle');
  const offcanvas = document.getElementById('offcanvas');
  const overlay = document.getElementById('pageOverlay');
  const closeBtn = document.getElementById('closeOffcanvas');

  navToggle?.addEventListener('click', () => {
    if (offcanvas.classList.contains('translate-x-0')) {
      closeMobileMenu();
    } else {
      openMobileMenu();
    }
  });

  closeBtn?.addEventListener('click', closeMobileMenu);
  overlay?.addEventListener('click', closeMobileMenu);

  document.querySelectorAll('#offcanvas a').forEach(link => {
    link.addEventListener('click', closeMobileMenu);
  });
}

window.addEventListener('hashchange', () => {
  closeMobileMenu();
  router();
});
window.addEventListener('load', () => {
  document.getElementById('currentYear').textContent = new Date().getFullYear();
  bindMobileMenu();
  router();
});

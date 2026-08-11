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

function renderHeader() {
  // topbar is static in index.html
}

function renderHome() {
  const container = el('div', 'home');
  // banner
  const banner = el('section', 'banner');
  banner.innerHTML = '<h1>Bienvenue à Birashoboka</h1><p>Nous accompagnons la réussite.</p>';
  container.appendChild(banner);

  // about short
  const about = el('section', 'about-short');
  about.innerHTML = '<h2>A propos</h2><p>Notre mission: appuyer les jeunes et les femmes...</p><p><a href="#/about">En savoir plus</a></p>';
  container.appendChild(about);

  // recent posts (mix volets & activities via posts and activities endpoints)
  const postsSec = el('section', 'recent-posts');
  postsSec.innerHTML = '<h2>A la une</h2><div class="posts-grid" id="postsGrid">Chargement...</div>';
  container.appendChild(postsSec);

  fetchJson('/posts')
    .then(data => {
      const grid = document.getElementById('postsGrid');
      grid.innerHTML = '';
      (data.data.items || []).slice(0,8).forEach(p => {
        const card = el('article','post-card');
        const flag = el('div','volet-flag', p.data?.volet?.name || (p.volet && p.volet.name) || '');
        card.appendChild(flag);
        const title = el('h3',null,p.title || p.data?.title || '');
        card.appendChild(title);
        const desc = el('p',null, (p.description || p.data?.description || '').slice(0,120));
        card.appendChild(desc);
        card.addEventListener('click', ()=>navigateTo('#/posts/'+(p.id||p.data?.id)));
        grid.appendChild(card);
      });
    })
    .catch(err => { document.getElementById('postsGrid').textContent = 'Impossible de charger.'; console.error(err); });

  // impact numbers: count students/inscriptions or use totals from API by querying endpoints
  const impact = el('section','impact');
  impact.innerHTML = '<h2>Notre impact</h2><div class="numbers" id="impactNums">Chargement...</div>';
  container.appendChild(impact);
  Promise.all([fetchJson('/students').catch(()=>null), fetchJson('/inscriptions').catch(()=>null)])
    .then(([s,i]) => {
      const target = document.getElementById('impactNums');
      target.innerHTML = '';
      const people = el('div','num');
      people.innerHTML = `<div class="big">${(i && i.data && i.data.pagination)? i.data.pagination.total : (s && s.data && s.data.pagination ? s.data.pagination.total : 0)}</div><div>Participants inscrits</div>`;
      target.appendChild(people);
    });

  // testimonials
  const tes = el('section','testimonials');
  tes.innerHTML = '<h2>Témoignages</h2><div id="testiList">Chargement...</div>';
  container.appendChild(tes);
  fetchJson('/testimonials').then(r=>{
    const wrap = document.getElementById('testiList'); wrap.innerHTML='';
    (r.data.items||[]).slice(0,6).forEach(t=>{
      const node = el('blockquote','testi'); node.textContent = t.content || t.data?.content || ''; wrap.appendChild(node);
    });
  }).catch(()=>{});

  // trending (campaigns)
  const trend = el('section','trending'); trend.innerHTML = '<h2>Évènements en cours</h2><div id="trendingList">Chargement...</div>'; container.appendChild(trend);
  fetchJson('/campaigns').then(r=>{ const list=document.getElementById('trendingList'); list.innerHTML=''; (r.data.items||[]).slice(0,4).forEach(c=>{ const d=el('div','campaign'); d.innerHTML=`<h4>${c.title||c.data?.title}</h4><p>${(c.description||'').slice(0,140)}</p>`; list.appendChild(d); }); }).catch(()=>{});

  // partners
  const part = el('section','partners'); part.innerHTML = '<h2>Partenaires</h2><div id="partnersList">Chargement...</div>'; container.appendChild(part);
  fetchJson('/partners').then(r=>{ const list=document.getElementById('partnersList'); list.innerHTML=''; (r.data.items||[]).forEach(p=>{ const d=el('div','partner'); d.textContent = p.name || p.data?.name; list.appendChild(d); }); }).catch(()=>{});

  return container;
}

function renderAbout() {
  const c = el('div','about-page');
  c.innerHTML = '<h1>A propos</h1><p>Histoire, vision, mission...</p>';
  return c;
}

function renderPrograms() {
  const c=el('div','programs'); c.innerHTML='<h1>Programmes</h1><div id="voletsList">Chargement...</div>';
  fetchJson('/volets').then(r=>{ const wrap=document.getElementById('voletsList'); wrap.innerHTML=''; (r.data.items||[]).forEach(v=>{ const card=el('div','volet-card'); card.innerHTML=`<h3>${v.name||v.data?.name}</h3><p class="slogan">${v.slogan||v.data?.slogan||''}</p><p>${(v.description||'').slice(0,140)}</p>`; card.addEventListener('click',()=>navigateTo('#/program/'+(v.id||v.data?.id))); wrap.appendChild(card); }); }).catch(()=>{});
  return c;
}

function renderVoletDetail(id) {
  const c=el('div','volet-detail'); c.innerHTML='<div>Chargement...</div>';
  fetchJson('/volets/'+id).then(r=>{ const v=r.data; c.innerHTML=`<h1>${v.name}</h1><p class="slogan">${v.slogan||''}</p><img src="/storage/uploads/${v.image||''}" alt="" onerror="this.style.display='none'"/><div>${v.description||''}</div><h3>Activités</h3><ul>${(v.activities||[]).map(a=>`<li>${a.title}</li>`).join('')}</ul><h3>Actualités</h3><div id="voletPosts">Chargement...</div>`; fetchJson('/volets/'+id+'/posts').then(p=>{ const node=document.getElementById('voletPosts'); node.innerHTML=''; (p.data.items||[]).forEach(it=>{ const li=document.createElement('div'); li.className='post'; li.innerHTML=`<h4>${it.title}</h4><p>${(it.description||'').slice(0,140)}</p>`; node.appendChild(li); }); }).catch(()=>{}); }).catch(()=>{ c.innerHTML='<div>Introuvable</div>' });
  return c;
}

function renderPosts() {
  const c=el('div','posts-page'); c.innerHTML='<h1>A la une</h1><div id="postsGrid">Chargement...</div>';
  fetchJson('/posts').then(r=>{ const g=document.getElementById('postsGrid'); g.innerHTML=''; (r.data.items||[]).forEach(p=>{ const card=el('div','post'); card.innerHTML=`<h3>${p.title}</h3><p>${(p.description||'').slice(0,200)}</p>`; card.addEventListener('click',()=>navigateTo('#/posts/'+p.id)); g.appendChild(card); }); }).catch(()=>{});
  return c;
}

function renderPostDetail(id) {
  const c=el('div','post-detail'); c.innerHTML='Chargement...';
  fetchJson('/posts/'+id).then(r=>{ const p=r.data; c.innerHTML=`<h1>${p.title}</h1><div>${p.description}</div><div>Volet: ${p.volet?.name||''}</div>`; }).catch(()=>{ c.innerHTML='Introuvable'; });
  return c;
}

function renderPartners() { const c=el('div','partners-page'); c.innerHTML='<h1>Partenaires</h1><div id="ps">Chargement...</div>'; fetchJson('/partners').then(r=>{ const ps=document.getElementById('ps'); ps.innerHTML=''; (r.data.items||[]).forEach(p=>{ const d=el('div','partner'); d.textContent=p.name; ps.appendChild(d); }); }).catch(()=>{}); return c; }
function renderContact(){ const c=el('div','contact'); c.innerHTML='<h1>Contact</h1><p>Formulaire à venir.</p>'; return c; }

function navigateTo(hash) { location.hash = hash; }

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
    main.innerHTML = '<h1>Page introuvable</h1>';
  }
}

// offcanvas behavior
(function(){
  const navToggle = document.getElementById('navToggle');
  const off = document.getElementById('offcanvas');
  const overlay = document.getElementById('pageOverlay');
  const closeBtn = document.getElementById('closeOffcanvas');
  navToggle.addEventListener('click', ()=>{ off.classList.toggle('open'); overlay.classList.toggle('show'); });
  closeBtn.addEventListener('click', ()=>{ off.classList.remove('open'); overlay.classList.remove('show'); });
  overlay.addEventListener('click', ()=>{ off.classList.remove('open'); overlay.classList.remove('show'); });
})();

window.addEventListener('hashchange', router);
window.addEventListener('load', router);

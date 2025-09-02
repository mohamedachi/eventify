function validateLogin(f){ return f.email.value && f.password.value.length>=6; }
function validateRegister(f){ return f.name.value && f.email.value && f.password.value.length>=6; }
function validateEvent(f){ return f.title.value && f.description.value && f.location.value && f.event_date.value && (+f.price.value)>=0; }
function validateUser(f){ return f.name.value && f.email.value; }

function filterEvents() {
  let q = (document.querySelector('#searchEvents')?.value || '').toLowerCase();
  let status = (document.querySelector('#statusFilter')?.value || '');
  document.querySelectorAll('.grid article.card').forEach(c => {
    let text = c.innerText.toLowerCase();
    let s = c.getAttribute('data-status') || '';
    let ok = (!q || text.includes(q)) && (!status || status===s);
    c.style.display = ok ? 'block' : 'none';
  });
}
const form=document.querySelector('#offerte-form');
if(form){
 const result=document.querySelector('#offerte-result');
 const summary=document.querySelector('#offerte-summary');
 const whatsapp=document.querySelector('#offerte-whatsapp');
 const mail=document.querySelector('#offerte-send-email');
 form.addEventListener('submit',event=>{
  event.preventDefault();
  if(!form.reportValidity())return;
  const data=new FormData(form);
  const text=key=>String(data.get(key)||'').trim();
  if(!text('naam')||!text('plaats')||text('bericht').length<20){
   document.querySelector('#offerte-bericht').setCustomValidity('Beschrijf uw tuinwerken in minstens 20 tekens.');
   form.reportValidity();return;
  }
  const message=`Offerteaanvraag via tuinmanindebuurt.be\n\nNaam: ${text('naam')}\nGemeente/deelgemeente: ${text('plaats')}\nDienst: ${text('dienst')}\nTelefoon: ${text('telefoon')||'Niet opgegeven'}\nE-mail: ${text('email')||'Niet opgegeven'}\nGewenste periode: ${text('periode')||'In overleg'}\n\nMijn tuinproject:\n${text('bericht')}`;
  summary.textContent=message;
  whatsapp.href='https://wa.me/32478040128?text='+encodeURIComponent(message);
  mail.href='mailto:diovanswalm@outlook.be?subject='+encodeURIComponent('Offerteaanvraag tuinwerken — '+text('plaats'))+'&body='+encodeURIComponent(message);
  result.hidden=false;
  result.scrollIntoView({behavior:window.matchMedia('(prefers-reduced-motion: reduce)').matches?'auto':'smooth',block:'center'});
 });
 form.addEventListener('input',()=>{
  document.querySelector('#offerte-bericht').setCustomValidity('');
  result.hidden=true;
  whatsapp.removeAttribute('href');mail.removeAttribute('href');
 });
}

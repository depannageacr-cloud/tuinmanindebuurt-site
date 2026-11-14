const form=document.querySelector('#offerte-form');
if(form){
 const result=document.querySelector('#offerte-result');
 const summary=document.querySelector('#offerte-summary');
 const whatsapp=document.querySelector('#offerte-whatsapp');
 const mail=document.querySelector('#offerte-send-email');
 const status=document.querySelector('#offerte-status');
 const submit=form.querySelector('button[type="submit"]');
 let requestId=crypto.randomUUID();
 let sending=false;
 let sent=false;
 form.addEventListener('submit',async event=>{
  event.preventDefault();
  if(sending||sent||!form.reportValidity())return;
  const data=new FormData(form);
  const value=key=>String(data.get(key)||'').trim();
  const emailInput=document.querySelector('#offerte-email');
  if(!value('telefoon')&&!value('email')){
   emailInput.setCustomValidity('Vul een telefoonnummer of e-mailadres in zodat DVS u kan antwoorden.');
   form.reportValidity();return;
  }
  const message=`Offerteaanvraag via tuinmanindebuurt.be\n\nNaam: ${value('naam')}\nGemeente/deelgemeente: ${value('plaats')}\nDienst: ${value('dienst')}\nTelefoon: ${value('telefoon')||'Niet opgegeven'}\nE-mail: ${value('email')||'Niet opgegeven'}\nGewenste periode: ${value('periode')||'In overleg'}\n\nMijn tuinproject:\n${value('bericht')}`;
  summary.textContent=message;
  whatsapp.href='https://wa.me/32478040128?text='+encodeURIComponent(message);
  mail.href='mailto:diovanswalm@outlook.be?subject='+encodeURIComponent('Offerteaanvraag tuinwerken — '+value('plaats'))+'&body='+encodeURIComponent(message);
  sending=true;submit.disabled=true;submit.textContent='Aanvraag versturen…';
  const payload=JSON.stringify({...Object.fromEntries(data),request_id:requestId});
  const fields=[...form.querySelectorAll('input,select,textarea')];
  fields.forEach(field=>field.disabled=true);
  status.textContent='Uw aanvraag wordt verzonden. Even geduld.';
  result.hidden=true;
  try{
   const response=await fetch(form.dataset.endpoint,{
    method:'POST',headers:{'Content-Type':'application/json'},
    body:payload,
    signal:AbortSignal.timeout(20000)
   });
   const answer=await response.json();
   if(!response.ok||answer.ok!==true||!answer.reference)throw new Error(answer.error||'De verzending kon niet worden bevestigd.');
   sent=true;submit.textContent='Aanvraag verzonden ✓';
   status.textContent=`Uw aanvraag is per e-mail verstuurd naar DVS Tuinen. Referentie: ${answer.reference}. DVS neemt persoonlijk contact met u op. Prijs en planning worden afzonderlijk afgesproken.`;
  }catch(error){
   status.textContent=(error.name==='TimeoutError'||error.name==='TypeError')?'We konden de verzending niet bevestigen. Probeer opnieuw: dezelfde aanvraag wordt niet dubbel verzonden. U kunt ook hieronder rechtstreeks mailen of WhatsApp gebruiken.':error.message;
   submit.disabled=false;submit.textContent='Probeer opnieuw ↗';
   result.hidden=false;
  }finally{sending=false;fields.forEach(field=>field.disabled=false);}
 });
 form.addEventListener('input',()=>{
  document.querySelector('#offerte-email').setCustomValidity('');
  if(sending)return;
  requestId=crypto.randomUUID();sent=false;
  submit.disabled=false;submit.textContent='Verstuur mijn offerteaanvraag ↗';
  status.textContent='';result.hidden=true;
 });
}

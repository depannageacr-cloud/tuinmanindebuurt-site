# Tuinman in de buurt — DVS Tuinen

Zelfstandige statische website voor https://tuinmanindebuurt.be, met rechtstreeks contact met DVS Tuinen uit Zwalm.

## Bouwen

Voer `node build.mjs` uit. Het resultaat staat in `dist/`. Geen afhankelijkheden nodig.

## Publicatie bij OVH

Upload de **inhoud** van `dist/` naar een eigen webmap voor dit domein. Koppel zowel tuinmanindebuurt.be als www.tuinmanindebuurt.be aan die map. Activeer eerst een geldig SSL-certificaat en HTTPS voor beide namen. De meegeleverde `.htaccess` verwijst daarna naar https://tuinmanindebuurt.be en geeft een eigen 404-pagina.

## Zoekmachines

Sitemap: https://tuinmanindebuurt.be/sitemap.xml. Verifieer na publicatie het domein in Google Search Console en dien de sitemap in. Vraag vervolgens indexering van de homepage en belangrijke diensten aan. Dit is een aanvraag, geen garantie op opname of posities.

## Inhoud en privacy

Bedrijfsgegevens en diensten zijn gecontroleerd op https://www.dvstuinen.be/ op 14 november 2026. Telefoon: 0478 04 01 28. E-mail: diovanswalm@outlook.be. Beelden zijn van de bestaande DVS-site overgenomen en als illustratief aangeduid. Geen verzonnen projecten, reviews, prijsclaims of plaatselijke vestigingen.

Contact loopt via telefoon, e-mail en WhatsApp. Het offerteformulier verstuurt aanvragen via HTTPS naar `https://offerte.tuinmanindebuurt.be/`, een PHP-endpoint op de bestaande OVH Pro-hosting. De server gebruikt OVH's PHP-mailfunctie, met een vaste ontvanger (`diovanswalm@outlook.be`) en afzender op het eigen domein. Een opgegeven bezoekeradres wordt alleen als Reply-To gebruikt. Een succesvolle reactie betekent dat de mailserver het bericht heeft aangenomen; inboxbezorging is een afzonderlijke controle. Bij een fout blijft de aanvraag beschikbaar voor rechtstreeks mailen of WhatsApp.

De backend staat in `backend/tuinman-api/index.php`. OVH publiceert de afzonderlijke GitHub-branch `offerte-backend` naar map `tuinman-offerte`; het endpoint staat daar als `index.php` in de hoofdmap. Backendwijzigingen in main moeten ook naar deze branch worden overgenomen en in OVH opnieuw worden uitgerold. De statische website blijft op Sites met het eigen domein. Gebruik geen PHP-bestanden in de statische publicatiemap.

Beveiliging: vaste ontvanger, controle van velden en toegestane diensten, headerinjectiepreventie, honeypot, groottebeperking, oorsprongcontrole, maximaal vijf aanvragen per netwerkadres per uur, maximaal 200 per dag en aanvraagcodes tegen dubbel versturen bij herhaalde verzoeken. Alleen technische tellers, gehashte netwerkadressen en aanvraagcodes worden tijdelijk in een afgeschermd serverbestand bijgehouden; geen berichtinhoud. De site gebruikt geen analytics of advertentiecookies. Er zijn geen SMTP-wachtwoorden of andere geheimen in de broncode nodig.

## Uitbreiding

50 inhoudelijke pagina’s, waaronder 26 regiopagina’s en een aparte pagina over bomen vellen en kappen. Vaste bel- en WhatsAppknoppen op desktop en mobiel. Het offerteformulier bevat ook bomen snoeien en bomen vellen en kappen.

